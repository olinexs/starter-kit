<?php

namespace Eoads\StarterKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

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
        $this->ensureFrontendDirs();

        $this->newLine();
        $this->components->success('EO-ADS Starter Kit installed successfully.');
        $this->newLine();

        $this->components->twoColumnDetail('<fg=green>Project</>',      $this->vars['PROJECT_NAME']);
        $this->components->twoColumnDetail('<fg=green>Team</>',         $this->vars['TEAM_NAME']);
        $this->components->twoColumnDetail('<fg=green>module:make</>',  'available — use AI or run directly');
        $this->components->twoColumnDetail('<fg=green>CLAUDE.md</>',    '.claude/CLAUDE.md');
        $this->components->twoColumnDetail('<fg=green>Architecture</>', '.docs/ARCHITECTURE.md');
        $this->components->twoColumnDetail('<fg=green>Sprint 01</>',    '.docs/sprints/sprint-01.md');
        $this->components->twoColumnDetail('<fg=green>Design<//>',      '.design/DESIGN-SYSTEM.md');

        $this->newLine();
        $this->line('  <fg=cyan>Onboarding steps:</>');
        $this->line('  1. Open this project in Claude Code');
        $this->line('  2. Say: <comment>"I want to create a module for [your feature]"</comment>');
        $this->line('  3. The AI scaffolds, implements, and wires everything.');
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
            $dest = base_path($destination);

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

    private function ensureFrontendDirs(): void
    {
        $dirs = [
            'resources/js/modules',
            'resources/js/plugins/router',
            'resources/js/stores',
            'resources/js/layouts/components',
            '.design/assets',
            '.design/preview',
            '.docs/sprints/archive',
        ];

        foreach ($dirs as $dir) {
            $path = base_path($dir);
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
            '.claude/CLAUDE.md'                               => '.claude/CLAUDE.md',
            '.claude/settings.local.json'                    => '.claude/settings.local.json',
            'AGENTS.md'                                      => 'AGENTS.md',
            '.docs/ARCHITECTURE.md'                          => '.docs/ARCHITECTURE.md',
            '.docs/TEMPLATE-ADAPTATION.md'                   => '.docs/TEMPLATE-ADAPTATION.md',
            '.docs/app-blueprint.md'                         => '.docs/app-blueprint.md',
            '.docs/sprints/sprint-roadmap.md'                => '.docs/sprints/sprint-roadmap.md',
            '.docs/sprints/sprint-01.md'                     => ".docs/sprints/sprint-{$sprintPadded}.md",
            '.skills/test-driven-development/SKILL.md'        => '.skills/test-driven-development/SKILL.md',
            '.skills/systematic-debugging/SKILL.md'           => '.skills/systematic-debugging/SKILL.md',
            '.skills/writing-plans/SKILL.md'                  => '.skills/writing-plans/SKILL.md',
            '.skills/verification-before-completion/SKILL.md' => '.skills/verification-before-completion/SKILL.md',
            '.design/README.md'                              => '.design/README.md',
            '.design/SKILL.md'                               => '.design/SKILL.md',
            '.design/DESIGN-SYSTEM.md'                       => '.design/DESIGN-SYSTEM.md',
            '.design/colors_and_type.css'                    => '.design/colors_and_type.css',
            'resources/js/plugins/axios.js'                  => 'resources/js/plugins/axios.js',
            'resources/js/plugins/router/routes.js'          => 'resources/js/plugins/router/routes.js',
            'resources/js/stores/toastStore.js'              => 'resources/js/stores/toastStore.js',
            'resources/js/layouts/components/NavItems.vue'   => 'resources/js/layouts/components/NavItems.vue',
            'dev-agent.sh'                                   => 'dev-agent.sh',
        ];
    }
}
