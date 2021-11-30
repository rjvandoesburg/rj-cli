<?php

declare(strict_types=1);

namespace App\Commands;

use App\Commands\Traits\InteractsWithGit;
use App\Commands\Traits\IsDirectoryAware;
use App\Commands\Traits\HandlesLocalFiles;
use App\Commands\Traits\RunsShellCommands;
use App\Commands\Traits\InteractsWithComposer;

class InstallPreCommit extends BaseCommand
{
    use InteractsWithComposer;
    use RunsShellCommands;
    use InteractsWithGit;
    use IsDirectoryAware;
    use HandlesLocalFiles;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'pre-commit:install {name?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Install pre-commit & pre-push';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->line('# Pre-commit', 'fg=cyan');

        $this->checkCurrentWorkingDirectory();

        $this->copyFiles()
            ->updateComposer()
            ->installPreCommit();

        $this->returnToOriginalDir();
    }

    protected function copyFiles(): self
    {
        $this->copyFilesFrom('pre_commit');

        return $this;
    }

    protected function updateComposer(): self
    {
        $this->task('Adding pre-commit to composer.json', function () {
            $composer = $this->getComposer();

            $this->mergeComposerConfig($composer, 'scripts.post-install-cmd', [
                'pre-commit install > /dev/null 2>&1 || true',
            ]);

            return $this->writeComposer($composer);
        });

        if ($this->shouldCommit()) {
            $this->gitAdd();
            $this->gitCommit('Add pre-commit files and update composer.json');
        }

        return $this;
    }

    protected function installPreCommit(): self
    {
        $this->task('Installing pre-commit', function () {
            return $this->exec('composer install')->isSuccessful();
        });

        return $this;
    }
}
