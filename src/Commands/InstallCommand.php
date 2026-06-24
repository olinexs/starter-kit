<?php

namespace Eoads\StarterKit\Commands;

use Illuminate\Console\Command;
use ZipArchive;

class InstallCommand extends Command
{
    protected $signature = 'eoads:install
                            {--force : Overwrite existing files}';

    protected $description = 'Install the EOADS starter kit — scaffolds project structure, docs, AI context, frontend template, and auth system';

    private string $stubsPath;
    private bool   $force;
    private array  $vars = [];

    private array  $selectedTemplate = [];
    private string $lang             = 'js';
    private string $version          = 'starter-kit';
    private string $auth             = 'local';

    public function handle(): int
    {
        $this->stubsPath = dirname(__DIR__, 2) . '/stubs';
        $this->force     = $this->option('force');

        // Pre-flight: the frontend template is shipped as a zip and extracted
        // with ZipArchive. Fail fast with a clear fix instead of crashing
        // mid-install if the PHP zip extension is not enabled.
        if (! class_exists(\ZipArchive::class)) {
            $this->components->error('The PHP "zip" extension is required but not enabled.');
            $this->line('  Enable it in your php.ini (uncomment <comment>extension=zip</comment>),');
            $this->line('  then restart your terminal and re-run <comment>php artisan eoads:install</comment>.');
            $this->line('  Check your php.ini path with: <comment>php --ini</comment>');

            return self::FAILURE;
        }

        $this->components->info('Installing EO-ADS Starter Kit...');
        $this->newLine();

        $this->collectProjectInfo();
        $this->newLine();

        $this->selectTemplate();
        $this->newLine();

        $this->selectAuth();
        $this->newLine();

        $this->publishCommonStubs();
        $this->publishAuthStubs();
        $this->publishAuthModule();
        $this->extractFrontendTemplate();
        $this->publishAuthFrontend();
        $this->ensureBackendDirs();

        $this->newLine();
        $this->components->success('EO-ADS Starter Kit installed successfully.');
        $this->newLine();

        $this->components->twoColumnDetail('<fg=green>Project</>',    $this->vars['PROJECT_NAME']);
        $this->components->twoColumnDetail('<fg=green>Team</>',       $this->vars['TEAM_NAME']);
        $this->components->twoColumnDetail('<fg=green>Template</>',   $this->selectedTemplate['name'] . ' · ' . strtoupper($this->lang) . ' · ' . $this->version);
        $this->components->twoColumnDetail('<fg=green>Auth</>',       strtoupper($this->auth));
        $this->components->twoColumnDetail('<fg=green>CLAUDE.md</>',  'backend/.claude/CLAUDE.md');
        $this->components->twoColumnDetail('<fg=green>Architecture</>', 'backend/.docs/ARCHITECTURE.md');
        $this->components->twoColumnDetail('<fg=green>Sprint 01</>',  'backend/.docs/sprints/sprint-01.md');
        $this->components->twoColumnDetail('<fg=green>Design</>',     'backend/.design/DESIGN-SYSTEM.md');

        $this->newLine();
        $this->line('  <fg=cyan>Structure:</>');
        $this->line('  project-root/');
        $this->line('  ├── backend/   ← Laravel app (you are here)');
        $this->line('  └── frontend/  ← ' . $this->selectedTemplate['name'] . ' SPA');
        $this->newLine();
        $this->line('  <fg=cyan>Next steps:</>');
        $this->line('  1. Open project root in Claude Code: <comment>claude ..</comment>');
        $this->line('  2. Fill in <comment>backend/.env</comment> and run <comment>php artisan migrate</comment>');
        $this->line('  3. Say: <comment>"I want to create a module for [your feature]"</comment>');
        $this->newLine();
        $this->line('  <fg=cyan>Or scaffold manually:</>');
        $this->line('  <comment>php artisan module:make YourModuleName</comment>');

        return self::SUCCESS;
    }

    // ─── Collect project info ─────────────────────────────────────────────────

    private function collectProjectInfo(): void
    {
        $appName = config('app.name', 'My App');

        $this->vars = [
            'PROJECT_NAME'  => $this->ask('Project name', $appName),
            'PROJECT_DESC'  => $this->ask('Project description', 'EO-ADS application'),
            'TEAM_NAME'     => $this->ask('Team / department name', 'A&D Department'),
            'SPRINT_NUMBER' => $this->ask('First sprint number', '01'),
            'SPRINT_TITLE'  => $this->ask('First sprint title', 'Foundation & Auth'),
            'SPRINT_PIC'    => $this->ask('Sprint PIC (person in charge)', '—'),
            'SPRINT_ETC'    => $this->ask('Sprint ETC (estimated completion)', '—'),
            'YEAR'          => date('Y'),
        ];

        $this->vars['SPRINT_PADDED'] = str_pad($this->vars['SPRINT_NUMBER'], 2, '0', STR_PAD_LEFT);

        // Frontend language placeholders — defaults for docs; refined in
        // selectTemplate() once the developer picks JS or TS.
        $this->setLangVars();
    }

    /**
     * Populate the frontend-language doc placeholders from $this->lang.
     * Used by CLAUDE.md and other common stubs so paths/extensions match
     * the chosen language (resources/js + .js  vs  resources/ts + .ts).
     */
    private function setLangVars(): void
    {
        $isTs = $this->lang === 'ts';

        $this->vars['SRC_DIR']   = $this->srcDir();
        $this->vars['EXT']       = $this->lang;
        $this->vars['TS_ATTR']   = $isTs ? ' lang="ts"' : '';
        $this->vars['CODE_LANG'] = $isTs ? 'ts' : 'js';
    }

    // ─── Template selection ───────────────────────────────────────────────────

    private function selectTemplate(): void
    {
        $templates = $this->discoverTemplates();

        if (empty($templates)) {
            $this->components->warn('No templates found in stubs/templates/ — skipping frontend.');
            $this->selectedTemplate = ['key' => '', 'name' => 'None', 'zip' => '', 'versions' => []];
            return;
        }

        // Step 1 — pick template
        $labels  = array_map(fn ($t) => $t['name'] . ' — ' . $t['description'], $templates);
        $chosen  = $this->choice('Which frontend template?', $labels, 0);
        $index   = array_search($chosen, $labels);
        $this->selectedTemplate = $templates[$index];

        // Step 2 — JS or TS
        $this->lang = strtolower($this->choice('JavaScript or TypeScript?', ['JavaScript', 'TypeScript'], 0));
        $this->lang = $this->lang === 'typescript' ? 'ts' : 'js';

        // Step 3 — Starter Kit or Full Version
        $versions     = $this->selectedTemplate['versions'] ?? [];
        $versionKeys  = array_keys($versions);
        $versionLabels = array_map(fn ($k) => $versions[$k]['label'] ?? $k, $versionKeys);

        $chosenVersion  = $this->choice('Which version?', $versionLabels, 0);
        $versionIndex   = array_search($chosenVersion, $versionLabels);
        $this->version  = $versionKeys[$versionIndex];

        $this->vars['LANG']     = $this->lang;
        $this->vars['VERSION']  = $this->version;

        // Now that the language is known, refresh the doc placeholders.
        $this->setLangVars();

        $this->components->twoColumnDetail('<fg=cyan>Template</>', $this->selectedTemplate['name'] . ' · ' . strtoupper($this->lang) . ' · ' . $this->version);
    }

    private function discoverTemplates(): array
    {
        $dir = $this->stubsPath . '/templates';

        if (! is_dir($dir)) {
            return [];
        }

        $templates = [];

        foreach (scandir($dir) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $manifest = $dir . '/' . $entry . '/template.json';

            if (! file_exists($manifest)) {
                continue;
            }

            $data = json_decode(file_get_contents($manifest), true);

            if (! $data || empty($data['name'])) {
                continue;
            }

            $templates[] = array_merge($data, ['key' => $entry]);
        }

        return $templates;
    }

    // ─── Auth selection ───────────────────────────────────────────────────────

    private function selectAuth(): void
    {
        $options = [
            'local'     => 'Local (Laravel Sanctum — email + password)',
            'ldap'      => 'LDAP (Active Directory)',
            'keycloak'  => 'Keycloak (SSO)',
        ];

        $labels     = array_values($options);
        $keys       = array_keys($options);
        $chosen     = $this->choice('Which authentication system?', $labels, 0);
        $this->auth = $keys[array_search($chosen, $labels)];

        $this->vars['AUTH'] = strtoupper($this->auth);

        // Auth-derived placeholders for docs/templates.
        $authMeta = [
            'local' => [
                'label'      => 'Local (Laravel Sanctum — email + password)',
                'middleware' => 'auth:sanctum',
            ],
            'ldap' => [
                'label'      => 'LDAP (Active Directory) + Sanctum tokens',
                'middleware' => 'auth:sanctum',
            ],
            'keycloak' => [
                'label'      => 'Keycloak (SSO) + Sanctum tokens',
                'middleware' => 'auth:sanctum',
            ],
        ];

        $this->vars['AUTH_LABEL']      = $authMeta[$this->auth]['label'];
        $this->vars['AUTH_MIDDLEWARE'] = $authMeta[$this->auth]['middleware'];

        $this->components->twoColumnDetail('<fg=cyan>Auth</>', $options[$this->auth]);
    }

    // ─── Publish common stubs ─────────────────────────────────────────────────

    private function publishCommonStubs(): void
    {
        $sprintPadded = $this->vars['SPRINT_PADDED'];

        $map = [
            'common/.claude/CLAUDE.md'                               => 'backend/.claude/CLAUDE.md',
            'common/.claude/settings.local.json'                     => 'backend/.claude/settings.local.json',
            'common/AGENTS.md'                                       => 'backend/AGENTS.md',
            'common/.docs/ARCHITECTURE.md'                           => 'backend/.docs/ARCHITECTURE.md',
            'common/.docs/TEMPLATE-ADAPTATION.md'                    => 'backend/.docs/TEMPLATE-ADAPTATION.md',
            'common/.docs/app-blueprint.md'                          => 'backend/.docs/app-blueprint.md',
            'common/.docs/sprints/sprint-roadmap.md'                 => 'backend/.docs/sprints/sprint-roadmap.md',
            'common/.docs/sprints/sprint-01.md'                      => "backend/.docs/sprints/sprint-{$sprintPadded}.md",
            'common/.skills/test-driven-development/SKILL.md'        => 'backend/.skills/test-driven-development/SKILL.md',
            'common/.skills/systematic-debugging/SKILL.md'           => 'backend/.skills/systematic-debugging/SKILL.md',
            'common/.skills/writing-plans/SKILL.md'                  => 'backend/.skills/writing-plans/SKILL.md',
            'common/.skills/verification-before-completion/SKILL.md' => 'backend/.skills/verification-before-completion/SKILL.md',
            'common/.design/README.md'                               => 'backend/.design/README.md',
            'common/.design/SKILL.md'                                => 'backend/.design/SKILL.md',
            'common/.design/DESIGN-SYSTEM.md'                        => 'backend/.design/DESIGN-SYSTEM.md',
            'common/.design/colors_and_type.css'                     => 'backend/.design/colors_and_type.css',
            'common/dev-agent.sh'                                    => 'backend/dev-agent.sh',
            'common/setup.sh'                                        => 'setup.sh',
            'common/setup.bat'                                       => 'setup.bat',
        ];

        foreach ($map as $stub => $destination) {
            $this->publishFile($stub, $destination);
        }
    }

    // ─── Publish auth stubs ───────────────────────────────────────────────────

    private function publishAuthStubs(): void
    {
        $authDir = $this->stubsPath . '/auth/' . $this->auth;

        if (! is_dir($authDir)) {
            $this->components->warn("Auth stubs not found for: {$this->auth}");
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($authDir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            $relativePath = ltrim(str_replace($authDir, '', $file->getPathname()), '/\\');
            $relativePath = str_replace('\\', '/', $relativePath);

            // Modules/ and frontend/ are published by dedicated methods.
            if (strpos($relativePath, 'Modules/') === 0 || strpos($relativePath, 'frontend/') === 0) {
                continue;
            }

            $stub        = 'auth/' . $this->auth . '/' . $relativePath;
            $destination = 'backend/' . $relativePath;

            $this->publishFile($stub, $destination);
        }
    }

    // ─── Publish Auth module ──────────────────────────────────────────────────

    private function publishAuthModule(): void
    {
        $moduleDir = $this->stubsPath . '/auth/' . $this->auth . '/Modules/Auth';

        if (! is_dir($moduleDir)) {
            $this->components->warn("Auth module stubs not found for: {$this->auth}");
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($moduleDir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            $relative    = ltrim(str_replace($moduleDir, '', $file->getPathname()), '/\\');
            $relative    = str_replace('\\', '/', $relative);
            $stub        = 'auth/' . $this->auth . '/Modules/Auth/' . $relative;
            $destination = 'backend/Modules/Auth/' . $relative;

            $this->publishFile($stub, $destination);
        }

        // Register in backend composer.json autoload
        $this->registerAuthModuleNamespace();
    }

    private function registerAuthModuleNamespace(): void
    {
        $composerPath = base_path('../backend/composer.json');

        if (! file_exists($composerPath)) {
            return;
        }

        $composer = json_decode(file_get_contents($composerPath), true);

        if (! is_array($composer)) {
            return;
        }

        $key  = 'Modules\\Auth\\';
        $val  = 'Modules/Auth/app/';
        $psr4 = $composer['autoload']['psr-4'] ?? [];

        if (isset($psr4[$key])) {
            return;
        }

        $composer['autoload']['psr-4'][$key] = $val;

        file_put_contents(
            $composerPath,
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n"
        );

        $this->components->twoColumnDetail('<fg=green>UPDATE</> backend/composer.json', 'Auth namespace registered');
    }

    // ─── Publish auth-specific frontend (login page + auth module) ─────────────

    private function publishAuthFrontend(): void
    {
        // Frontend stubs are split by language (js/ and ts/) — pick the one
        // matching the selected language so a TS project gets .ts files.
        $authFrontend = $this->stubsPath . '/auth/' . $this->auth . '/frontend/' . $this->lang;

        if (! is_dir($authFrontend)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($authFrontend, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            $relative = ltrim(str_replace($authFrontend, '', $file->getPathname()), '/\\');
            $relative = str_replace('\\', '/', $relative);

            $stub        = 'auth/' . $this->auth . '/frontend/' . $this->lang . '/' . $relative;
            $destination = 'frontend/' . $relative;

            $this->publishFile($stub, $destination);
        }

        if ($this->auth === 'keycloak') {
            $this->appendKeycloakFrontendEnv();
        }
    }

    private function appendKeycloakFrontendEnv(): void
    {
        $block = <<<'ENV'

# ─── Keycloak (SSO) ───────────────────────────────────────────────
VITE_KEYCLOAK_URL=https://keycloak.example.com
VITE_KEYCLOAK_REALM=my-realm
VITE_KEYCLOAK_CLIENT_ID=my-spa-client
VITE_KEYCLOAK_REDIRECT_URI=http://localhost:5173/login
ENV;

        foreach (['frontend/.env.example', 'frontend/.env'] as $relative) {
            $path = base_path('../' . $relative);

            if (! file_exists($path)) {
                continue;
            }

            if (strpos(file_get_contents($path), 'VITE_KEYCLOAK_URL') !== false) {
                continue; // already present
            }

            file_put_contents($path, rtrim(file_get_contents($path)) . "\n" . $block . "\n");
            $this->components->twoColumnDetail("<fg=green>UPDATE</> {$relative}", 'Keycloak vars appended');
        }
    }

    // ─── Extract frontend template from zip ───────────────────────────────────

    /**
     * Folders inside the template's src/ that are USABLE infrastructure.
     * These are extracted to frontend/resources/js/<folder>/ and used directly.
     */
    private array $usableSrcDirs = [
        '@core',
        '@layouts',
        'assets',
        'navigation',
        'composables',
    ];

    /**
     * Plugins (relative to src/plugins/) that are USABLE — extracted to
     * resources/js/plugins/ and auto-registered by @core/utils/plugins.js.
     * The file-based router (1.router/) is deliberately excluded; our own
     * router plugin (from common stubs) replaces it.
     */
    private array $usablePlugins = [
        '2.pinia.js',
        'vuetify',
        'iconify',
        'layouts.js',
        'webfontloader.js',
    ];

    private function extractFrontendTemplate(): void
    {
        $tKey = $this->selectedTemplate['key'] ?? '';

        if ($tKey === '') {
            return;
        }

        $zipFile = $this->ensureTemplateZip($tKey, $this->selectedTemplate['zip'] ?? '');

        if ($zipFile === null) {
            $this->components->warn('Frontend template could not be obtained — skipping frontend.');
            return;
        }

        $versions  = $this->selectedTemplate['versions'] ?? [];
        $langPath  = $versions[$this->version][$this->lang] ?? null;

        if (! $langPath) {
            $this->components->warn("No path defined for version={$this->version}, lang={$this->lang}");
            return;
        }

        $frontendDest = base_path('../frontend');
        $jsDest       = $frontendDest . '/' . $this->srcDir();

        foreach ([$frontendDest, $jsDest, $jsDest . '/.template'] as $d) {
            if (! is_dir($d)) {
                mkdir($d, 0755, true);
            }
        }

        $this->newLine();
        $this->components->info('Extracting frontend template...');

        $zip = new ZipArchive();

        if ($zip->open($zipFile) !== true) {
            $this->components->error("Could not open zip: {$zipFile}");
            return;
        }

        $prefix = rtrim($langPath, '/') . '/';
        $stats  = ['root' => 0, 'core' => 0, 'template' => 0];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);

            if (strpos($name, $prefix) !== 0) {
                continue;
            }

            $relative = substr($name, strlen($prefix));

            if ($relative === '' || substr($relative, -1) === '/') {
                continue; // skip directory entries
            }

            $dest = $this->resolveFrontendTarget($relative, $frontendDest, $jsDest, $stats);

            if ($dest === null) {
                continue;
            }

            $dir = dirname($dest);
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            if (file_exists($dest) && ! $this->force) {
                continue;
            }

            file_put_contents($dest, $zip->getFromIndex($i));
        }

        $zip->close();

        $src = $this->srcDir();
        $this->components->twoColumnDetail('<fg=green>EXTRACT</> frontend/ (root)',                 "{$stats['root']} files");
        $this->components->twoColumnDetail("<fg=green>EXTRACT</> {$src}/ (usable core)",            "{$stats['core']} files");
        $this->components->twoColumnDetail("<fg=green>EXTRACT</> {$src}/.template/ (ref)",          "{$stats['template']} files");

        // Overlay our working stubs (overrides template defaults).
        $this->publishFrontendStubs($tKey);

        $this->installFrontendDependencies($frontendDest);
    }

    /**
     * Decide where a zip entry (path relative to the version root) lands:
     *   - non-src/ files                  → frontend/                 (root)
     *   - src/<usableSrcDir>/...          → resources/js/<dir>/...    (core)
     *   - src/plugins/<usablePlugin>      → resources/js/plugins/...  (core)
     *   - everything else under src/      → resources/js/.template/   (reference)
     * Returns the absolute destination path, or null to skip.
     */
    private function resolveFrontendTarget(string $relative, string $frontendDest, string $jsDest, array &$stats): ?string
    {
        $relative = str_replace('\\', '/', $relative);

        // Root-level files (not under src/) → frontend/ root, verbatim.
        if (strpos($relative, 'src/') !== 0) {
            $stats['root']++;
            return $frontendDest . '/' . $relative;
        }

        $inner = substr($relative, strlen('src/')); // path inside src/

        // Usable core folders → resources/js/<folder>/...
        foreach ($this->usableSrcDirs as $dir) {
            if ($inner === $dir || strpos($inner, $dir . '/') === 0) {
                $stats['core']++;
                return $jsDest . '/' . $inner;
            }
        }

        // Usable plugins → resources/js/plugins/... (router excluded).
        if (strpos($inner, 'plugins/') === 0) {
            $pluginPath = substr($inner, strlen('plugins/'));
            $segment    = explode('/', $pluginPath)[0];

            if (in_array($segment, $this->usablePlugins, true)) {
                $stats['core']++;
                return $jsDest . '/plugins/' . $pluginPath;
            }
        }

        // Everything else under src/ → read-only AI reference.
        $stats['template']++;
        return $jsDest . '/.template/' . $inner;
    }

    /**
     * Overlay working frontend code on top of the extracted template:
     *   - common/frontend/** (main.js, App.vue, plugins/router, plugins/axios,
     *     stores, layouts) — our hand-written working code
     *   - templates/<key>/vite.config.js + index.html — remapped to resources/js
     */
    private function publishFrontendStubs(string $tKey): void
    {
        // Common overlay is split by language (js/ and ts/) — pick the one
        // matching the selected language so a TS project gets .ts files.
        $commonFrontend = $this->stubsPath . '/common/frontend/' . $this->lang;

        if (is_dir($commonFrontend)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($commonFrontend, \RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                $relative = ltrim(str_replace($commonFrontend, '', $file->getPathname()), '/\\');
                $relative = str_replace('\\', '/', $relative);

                $this->publishFile('common/frontend/' . $this->lang . '/' . $relative, 'frontend/' . $relative);
            }
        }

        // Template-specific root override: vite config (extension follows the
        // chosen language so a TS project gets vite.config.ts).
        $viteConfig = $this->lang === 'ts' ? 'vite.config.ts' : 'vite.config.js';
        $viteStub   = "templates/{$tKey}/{$viteConfig}";
        if (file_exists("{$this->stubsPath}/{$viteStub}")) {
            $this->publishFile($viteStub, "frontend/{$viteConfig}");
        }

        // index.html — the entry <script src> must point at the right entry
        // file (main.ts for TypeScript, main.js for JavaScript).
        $this->publishIndexHtml($tKey);

        // Write the .template/ guide for AI agents.
        $this->writeTemplateReadme();
    }

    /**
     * Publish the template's index.html, rewriting the entry <script src> to
     * point at the language-appropriate entry file (main.ts for TS projects).
     */
    private function publishIndexHtml(string $tKey): void
    {
        $stub = "templates/{$tKey}/index.html";
        $src  = "{$this->stubsPath}/{$stub}";

        if (! file_exists($src)) {
            return;
        }

        $dest = base_path('../frontend/index.html');

        if (file_exists($dest) && ! $this->force) {
            $this->components->twoColumnDetail('<fg=yellow>SKIP</>  frontend/index.html', 'already exists');
            return;
        }

        $content = $this->replacePlaceholders(file_get_contents($src));

        if ($this->lang === 'ts') {
            $content = str_replace('/resources/js/main.js', '/resources/ts/main.ts', $content);
        }

        $dir = dirname($dest);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($dest, $content);
        $this->components->twoColumnDetail('<fg=green>CREATE</> frontend/index.html', 'done');
    }

    private function writeTemplateReadme(): void
    {
        $src  = $this->srcDir();          // resources/js or resources/ts
        $ext  = $this->lang;              // js or ts
        $dest = base_path("../frontend/{$src}/.template/README.md");

        if (file_exists($dest) && ! $this->force) {
            return;
        }

        $content = <<<MD
# .template/ — Read-Only AI Reference

This folder holds the **original** template source (pages, components, views,
layouts, stores, the file-based router, etc.) extracted from the vendor theme.

## Rules

- **Do NOT import from `.template/` in working code.** It is reference only.
- **Do NOT edit files here.** Treat it as read-only documentation.
- When building a feature, **copy the relevant pattern** out of `.template/`
  into the working tree (`{$src}/modules/`, `{$src}/pages/`, etc.),
  then adapt it.

## Where working code lives

| Concern        | Working location                          |
|----------------|-------------------------------------------|
| Entry point    | `{$src}/main.{$ext}`                       |
| Router         | `{$src}/plugins/router/`                   |
| HTTP client    | `{$src}/plugins/axios.{$ext}`              |
| Feature module | `{$src}/modules/<name>/`                   |
| Shared core    | `{$src}/@core/`, `@layouts/`               |

The vendor `@core/` and `@layouts/` are usable directly — only the demo
content (pages/components/views) is parked here for reference.
MD;

        file_put_contents($dest, $content . "\n");
        $this->components->twoColumnDetail("<fg=green>CREATE</> {$src}/.template/README.md", 'done');
    }

    /**
     * Locate the chosen template's zip, downloading it on demand.
     *
     * To keep `composer require` small the heavy template archives are
     * export-ignored from the package dist (see .gitattributes), so a normal
     * install ships without them. The first time a template is chosen we fetch
     * just that one zip from its `download` URL and cache it under the user's
     * home dir so re-installs reuse it. When working from a git checkout the
     * zip is present locally and used directly. Returns the absolute path, or
     * null if it could not be obtained.
     */
    private function ensureTemplateZip(string $tKey, string $zipName): ?string
    {
        if ($zipName === '') {
            return null;
        }

        // 1. Bundled / git checkout — already on disk.
        $local = $this->stubsPath . '/templates/' . $tKey . '/' . $zipName;
        if (file_exists($local)) {
            return $local;
        }

        // 2. Previously downloaded into the per-user cache.
        $cached = $this->templateCacheDir() . '/' . $tKey . '/' . $zipName;
        if (file_exists($cached)) {
            return $cached;
        }

        // 3. Download just this one template.
        $url = $this->selectedTemplate['download'] ?? '';
        if ($url === '') {
            $this->components->error("Template '{$zipName}' is not bundled and template.json has no \"download\" URL.");
            return null;
        }

        $this->newLine();
        $this->components->info("Downloading {$this->selectedTemplate['name']} template (one-time, cached for re-use)...");

        if (! is_dir(dirname($cached))) {
            mkdir(dirname($cached), 0755, true);
        }

        if (! $this->downloadFile($url, $cached)) {
            $this->components->error("Failed to download template from: {$url}");
            return null;
        }

        $this->components->twoColumnDetail("<fg=green>DOWNLOAD</> {$zipName}", 'done');
        return $cached;
    }

    private function templateCacheDir(): string
    {
        $home = getenv('HOME') ?: getenv('USERPROFILE') ?: sys_get_temp_dir();
        return rtrim(str_replace('\\', '/', $home), '/') . '/.eoads/templates';
    }

    /**
     * Download a URL to a local path atomically (writes to a .part file, then
     * renames). Prefers cURL for reliable HTTPS + redirect handling; falls back
     * to the stream wrapper when the cURL extension is unavailable.
     */
    private function downloadFile(string $url, string $dest): bool
    {
        $tmp = $dest . '.part';
        @unlink($tmp);

        // Try each method in turn; the first that lands a non-empty file wins.
        // Order is chosen for reliability behind corporate networks/AV: the
        // system curl binary (Windows 10+/macOS/Linux) uses the OS certificate
        // store and is the most robust, so it leads.
        $errors = [];

        foreach (['systemCurl', 'phpCurl', 'streamCopy'] as $method) {
            @unlink($tmp);
            $err = $this->{'download' . ucfirst($method)}($url, $tmp);

            if ($err === null && file_exists($tmp) && filesize($tmp) > 0) {
                return rename($tmp, $dest);
            }

            if ($err !== null) {
                $errors[] = "{$method}: {$err}";
            }
        }

        @unlink($tmp);

        $this->newLine();
        $this->line('  <fg=red>Could not download the template.</> Tried:');
        foreach ($errors as $e) {
            $this->line("    - {$e}");
        }
        $this->line('  <comment>Recovery:</> download the zip manually and drop it in:');
        $this->line("    <comment>{$dest}</comment>");
        $this->line('  then re-run <comment>php artisan eoads:install --force</comment>.');

        return false;
    }

    /** Download via the system curl binary (uses the OS cert store). */
    private function downloadSystemCurl(string $url, string $tmp): ?string
    {
        if (! $this->commandExists('curl')) {
            return 'curl binary not on PATH';
        }

        $cmd = 'curl -fsSL --retry 3 --connect-timeout 30 -A eoads-starter-kit '
             . '-o ' . escapeshellarg($tmp) . ' ' . escapeshellarg($url) . ' 2>&1';
        exec($cmd, $out, $code);

        return $code === 0 ? null : ('exit ' . $code . ' ' . trim(implode(' ', $out)));
    }

    /** Download via the PHP cURL extension. */
    private function downloadPhpCurl(string $url, string $tmp): ?string
    {
        if (! function_exists('curl_init')) {
            return 'php-curl extension not available';
        }

        $fh = fopen($tmp, 'wb');
        if ($fh === false) {
            return 'cannot open temp file';
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_FILE           => $fh,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_FAILONERROR    => true,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT        => 600,
            CURLOPT_USERAGENT      => 'eoads-starter-kit',
        ]);
        $ok  = curl_exec($ch) !== false;
        $msg = $ok ? null : ('cURL error ' . curl_errno($ch) . ': ' . curl_error($ch));
        curl_close($ch);
        fclose($fh);

        return $msg;
    }

    /** Download via the stream wrapper (requires allow_url_fopen). */
    private function downloadStreamCopy(string $url, string $tmp): ?string
    {
        if (! ini_get('allow_url_fopen')) {
            return 'allow_url_fopen is disabled';
        }

        $ctx = stream_context_create(['http' => ['user_agent' => 'eoads-starter-kit', 'timeout' => 600]]);

        return @copy($url, $tmp, $ctx) ? null : 'copy() failed';
    }

    private function installFrontendDependencies(string $frontendPath): void
    {
        if (! file_exists("{$frontendPath}/package.json")) {
            return;
        }

        [$installer, $args] = $this->resolveFrontendInstaller($frontendPath);

        $this->newLine();
        $this->components->info("Installing frontend dependencies ({$installer} {$args})...");

        $command = 'cd ' . escapeshellarg($frontendPath) . " && {$installer} {$args}";
        passthru($command, $exitCode);

        if ($exitCode === 0) {
            $this->components->twoColumnDetail("<fg=green>{$installer} {$args}</>", 'done');
        } else {
            $this->components->warn("{$installer} {$args} failed — run it manually in frontend/");
        }
    }

    /**
     * Choose the fastest reproducible install command for the extracted
     * frontend. The Vuetify templates ship a committed pnpm-lock.yaml, so when
     * pnpm is available we install from the frozen lockfile (no resolution,
     * reproducible). If only an npm lockfile is present we use `npm ci` for the
     * same reason. Otherwise we fall back to a plain `npm install`.
     *
     * @return array{0:string,1:string} [installer, args]
     */
    private function resolveFrontendInstaller(string $frontendPath): array
    {
        if (file_exists("{$frontendPath}/pnpm-lock.yaml") && $this->commandExists('pnpm')) {
            return ['pnpm', 'install --frozen-lockfile'];
        }

        if (file_exists("{$frontendPath}/package-lock.json") && $this->commandExists('npm')) {
            return ['npm', 'ci'];
        }

        return ['npm', 'install'];
    }

    private function commandExists(string $command): bool
    {
        $probe = stripos(PHP_OS, 'WIN') === 0 ? "where {$command}" : "command -v {$command}";
        exec($probe . ' 2>&1', $output, $exitCode);

        return $exitCode === 0;
    }

    // ─── Ensure backend directories exist ────────────────────────────────────

    private function ensureBackendDirs(): void
    {
        $dirs = [
            'backend/.docs/sprints/archive',
            'backend/.design/assets',
            'backend/.design/preview',
        ];

        foreach ($dirs as $dir) {
            $path = base_path("../{$dir}");
            if (! is_dir($path)) {
                mkdir($path, 0755, true);
                $this->components->twoColumnDetail("<fg=green>CREATE</> {$dir}/", 'done');
            }
        }
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Frontend source root, relative to frontend/. TypeScript projects use
     * resources/ts so the folder name matches the language; JS uses resources/js.
     */
    private function srcDir(): string
    {
        return $this->lang === 'ts' ? 'resources/ts' : 'resources/js';
    }

    private function publishFile(string $stub, string $destination): void
    {
        $src  = "{$this->stubsPath}/{$stub}";
        $dest = base_path("../{$destination}");

        if (! file_exists($src)) {
            $this->components->warn("Stub not found: {$stub}");
            return;
        }

        if (file_exists($dest) && ! $this->force) {
            $this->components->twoColumnDetail("<fg=yellow>SKIP</>  {$destination}", 'already exists');
            return;
        }

        $dir = dirname($dest);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $content = file_get_contents($src);
        $content = $this->replacePlaceholders($content);

        file_put_contents($dest, $content);
        $this->components->twoColumnDetail("<fg=green>CREATE</> {$destination}", 'done');
    }

    private function replacePlaceholders(string $content): string
    {
        $search  = array_map(fn ($k) => "{{$k}}", array_keys($this->vars));
        $replace = array_values($this->vars);
        return str_replace($search, $replace, $content);
    }
}
