<?php

namespace Eoads\StarterKit\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'eoads:install
                            {--force : Overwrite existing files}';

    protected $description = 'Install the EO-ADS starter kit — scaffolds all project structure, docs, and AI context files';

    private string $stubsPath;
    private bool   $force;
    private array  $vars = [];

    public function handle(): int
    {
        $this->stubsPath = dirname(__DIR__, 2) . '/stubs';
        $this->force     = $this->option('force');

        $this->components->info('Installing EO-ADS Starter Kit...');
        $this->newLine();

        $this->collectProjectInfo();
        $this->newLine();

        $this->publishStubs();
        $this->ensureDirs();

        $this->newLine();
        $this->components->success('EO-ADS Starter Kit installed successfully.');
        $this->newLine();

        $this->components->twoColumnDetail('<fg=green>Project</>',      $this->vars['PROJECT_NAME']);
        $this->components->twoColumnDetail('<fg=green>Team</>',         $this->vars['TEAM_NAME']);
        $this->components->twoColumnDetail('<fg=green>module:make</>',  'available — use AI or run directly');
        $this->components->twoColumnDetail('<fg=green>CLAUDE.md</>',    'backend/.claude/CLAUDE.md');
        $this->components->twoColumnDetail('<fg=green>Architecture</>', 'backend/.docs/ARCHITECTURE.md');
        $this->components->twoColumnDetail('<fg=green>Sprint 01</>',    'backend/.docs/sprints/sprint-01.md');
        $this->components->twoColumnDetail('<fg=green>Design</>',       'backend/.design/DESIGN-SYSTEM.md');

        $this->newLine();
        $this->line('  <fg=cyan>Structure:</>');
        $this->line('  project-root/');
        $this->line('  ├── backend/   ← Laravel app (you are here)');
        $this->line('  └── frontend/  ← Vue SPA');
        $this->newLine();
        $this->line('  <fg=cyan>Onboarding steps:</>');
        $this->line('  1. Open the project root in Claude Code: <comment>claude ..</comment>');
        $this->line('  2. Say: <comment>"I want to create a module for [your feature]"</comment>');
        $this->line('  3. The AI scaffolds backend + frontend together.');
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
            'PROJECT_NAME'    => $this->ask('Project name', $appName),
            'PROJECT_DESC'    => $this->ask('Project description', 'EO-ADS application'),
            'TEAM_NAME'       => $this->ask('Team / department name', 'A&D Department'),
            'SPRINT_NUMBER'   => $this->ask('First sprint number', '01'),
            'SPRINT_TITLE'    => $this->ask('First sprint title', 'Foundation & Auth'),
            'SPRINT_PIC'      => $this->ask('Sprint PIC (person in charge)', '—'),
            'SPRINT_ETC'      => $this->ask('Sprint ETC (estimated completion)', '—'),
            'YEAR'            => date('Y'),
        ];

        $this->vars['SPRINT_PADDED'] = str_pad($this->vars['SPRINT_NUMBER'], 2, '0', STR_PAD_LEFT);
    }

    // ─── Publish stubs ────────────────────────────────────────────────────────

    private function publishStubs(): void
    {
        foreach ($this->stubMap() as $stub => $destination) {
            $src  = "{$this->stubsPath}/{$stub}";
            // destination is relative to project root (one level above backend/)
            $dest = base_path("../{$destination}");

            if (! file_exists($src)) {
                $this->components->warn("Stub not found: {$stub}");
                continue;
            }

            if (file_exists($dest) && ! $this->force) {
                $this->components->twoColumnDetail("<fg=yellow>SKIP</>  {$destination}", 'already exists');
                continue;
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
    }

    private function replacePlaceholders(string $content): string
    {
        $search  = array_map(fn ($k) => "{{$k}}", array_keys($this->vars));
        $replace = array_values($this->vars);
        return str_replace($search, $replace, $content);
    }

    private function ensureDirs(): void
    {
        $dirs = [
            // backend dirs (relative to project root)
            'backend/.docs/sprints/archive',
            'backend/.design/assets',
            'backend/.design/preview',
            // frontend dirs
            'frontend/resources/js/modules',
            'frontend/resources/js/plugins/router',
            'frontend/resources/js/stores',
            'frontend/resources/js/layouts/components',
        ];

        foreach ($dirs as $dir) {
            $path = base_path("../{$dir}");
            if (! is_dir($path)) {
                mkdir($path, 0755, true);
                $this->components->twoColumnDetail("<fg=green>CREATE</> {$dir}/", 'done');
            }
        }
    }

    // ─── Stub map ─────────────────────────────────────────────────────────────

    private function stubMap(): array
    {
        $sprintPadded = str_pad($this->vars['SPRINT_NUMBER'], 2, '0', STR_PAD_LEFT);

        return [
            // backend — docs, AI context, design
            '.claude/CLAUDE.md'                               => 'backend/.claude/CLAUDE.md',
            '.claude/settings.local.json'                    => 'backend/.claude/settings.local.json',
            'AGENTS.md'                                      => 'backend/AGENTS.md',
            '.docs/ARCHITECTURE.md'                          => 'backend/.docs/ARCHITECTURE.md',
            '.docs/TEMPLATE-ADAPTATION.md'                   => 'backend/.docs/TEMPLATE-ADAPTATION.md',
            '.docs/app-blueprint.md'                         => 'backend/.docs/app-blueprint.md',
            '.docs/sprints/sprint-roadmap.md'                => 'backend/.docs/sprints/sprint-roadmap.md',
            '.docs/sprints/sprint-01.md'                     => "backend/.docs/sprints/sprint-{$sprintPadded}.md",
            '.skills/test-driven-development/SKILL.md'       => 'backend/.skills/test-driven-development/SKILL.md',
            '.skills/systematic-debugging/SKILL.md'          => 'backend/.skills/systematic-debugging/SKILL.md',
            '.skills/writing-plans/SKILL.md'                 => 'backend/.skills/writing-plans/SKILL.md',
            '.skills/verification-before-completion/SKILL.md'=> 'backend/.skills/verification-before-completion/SKILL.md',
            '.design/README.md'                              => 'backend/.design/README.md',
            '.design/SKILL.md'                               => 'backend/.design/SKILL.md',
            '.design/DESIGN-SYSTEM.md'                       => 'backend/.design/DESIGN-SYSTEM.md',
            '.design/colors_and_type.css'                    => 'backend/.design/colors_and_type.css',
            'dev-agent.sh'                                   => 'backend/dev-agent.sh',
            // frontend — base JS files
            'resources/js/plugins/axios.js'                  => 'frontend/resources/js/plugins/axios.js',
            'resources/js/plugins/router/routes.js'          => 'frontend/resources/js/plugins/router/routes.js',
            'resources/js/stores/toastStore.js'              => 'frontend/resources/js/stores/toastStore.js',
            'resources/js/layouts/components/NavItems.vue'   => 'frontend/resources/js/layouts/components/NavItems.vue',
        ];
    }
}
