<?php

declare(strict_types=1);

namespace App\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Commands\Traits\InteractsWithGit;
use Laravel\Installer\Console\NewCommand;
use App\Commands\Traits\RunsShellCommands;

class InstallLaravel extends BaseCommand
{
    use InteractsWithGit;
    use RunsShellCommands;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'laravel:install {name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected string $currentWorkingDirectory;

    protected string $projectPath;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line('# Laravel', 'fg=cyan');

        $this->projectPath = $this->argument('name');
        $this->currentWorkingDirectory = getcwd();

        $this
            ->installLaravel()
            ->initializeGit()
            ->updateGitIgnore()
            ->setDefaultConfig();
    }

    protected function installLaravel(): self
    {
        $this->task('Installing Laravel', function () {
            $this->callSilent(NewCommand::class, [
                'name' => $this->projectPath,
                '--quiet' => true,
            ]);

            chdir($this->projectPath);
            $this->exec('composer require php "^8.0"');
            chdir($this->currentWorkingDirectory);
        });

        return $this;
    }

    protected function initializeGit(): self
    {
        $this->task('Initializing git', function () {
            chdir($this->projectPath);
            $this->gitInit();
            $this->gitAdd();
            $this->gitCommit('Initial commit');
            chdir($this->currentWorkingDirectory);
        });

        return $this;
    }

    protected function updateGitIgnore(): self
    {
        $this->task('Updating .gitignore', function () {
            chdir($this->projectPath);

            $gitIgnore = file_get_contents('.gitignore');

            if (Str::contains($gitIgnore, '# Custom')) {
                return true;
            }

            $gitIgnore .= "\n" . Storage::get('laravel/.gitignore');

            file_put_contents('.gitignore', $gitIgnore);

            $this->gitAdd();
            $this->gitCommit('Update .gitignore');
            chdir($this->currentWorkingDirectory);

            return true;
        });

        return $this;
    }

    protected function setDefaultConfig(): self
    {
        chdir($this->projectPath);

        $config = [
            'app.locale' => <<<'VALUE'
            'nl'
            VALUE,
            'app.faker_locale' => <<<'VALUE'
            'nl_NL'
            VALUE,
            'logging.channels.stack.channels' => <<<'VALUE'
            ['daily']
            VALUE,
        ];

        foreach ($config as $key => $value) {
            $this->task(sprintf('Updating config "%s" to %s', $key, json_encode($value)), function () use ($key, $value) {
                $parts = explode('.', $key);
                $file = Arr::pull($parts, 0);

                $contents = file_get_contents($path = "config/{$file}.php");
                // Check nested level
                if (count($parts) === 1) {
                    $key = current($parts);
                    $contents = preg_replace("#('{$key}'\s=>\s)(.*),$#m", "$1{$value},", $contents);
                } else {
                    $start = array_shift($parts);
                    $key = array_pop($parts);

                    $segments = [];
                    foreach ($parts as $segment) {
                        $segments[] = ".*'{$segment}'(?:.*\\n)*";
                    }

                    $segments = implode($segments);

                    $pattern = "#(^.*'{$start}'(?:.*\\n)*{$segments}.*'{$key}'\s=>\s)(.*),$#m";

                    $contents = preg_replace($pattern, "$1{$value},", $contents);
                }

                file_put_contents($path, $contents);
            });
        }

        if ($this->shouldCommit()) {
            $this->gitAdd();
            $this->gitCommit('Update default config');
        }

        chdir($this->currentWorkingDirectory);

        return $this;
    }
}
