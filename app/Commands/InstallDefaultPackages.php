<?php

declare(strict_types=1);

namespace App\Commands;

use App\Commands\Traits\InteractsWithGit;
use App\Commands\Traits\IsDirectoryAware;
use App\Commands\Traits\HandlesLocalFiles;
use App\Commands\Traits\RunsShellCommands;
use App\Commands\Traits\InteractsWithComposer;

class InstallDefaultPackages extends BaseCommand
{
    use RunsShellCommands;
    use InteractsWithComposer;
    use InteractsWithGit;
    use IsDirectoryAware;
    use HandlesLocalFiles;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'default-packages:install {name?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Install default composer packages';

    protected $packages = [
        'barryvdh/laravel-debugbar:"*"',
        'barryvdh/laravel-ide-helper:"*"',
        'beyondcode/laravel-dump-server',
        'roave/security-advisories:dev-master',
        'friendsofphp/php-cs-fixer',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->line('# Default Packages', 'fg=cyan');

        $this->checkCurrentWorkingDirectory();

        $this
            ->installDefaultPackages()
            ->addIdeHelpers()
            ->installPhpCs()
            ->runPhpCs();

        $this->returnToOriginalDir();
    }

    protected function installDefaultPackages(): self
    {
        $this->task('Installing default packages', function () {
            return $this->exec('composer require -W --dev ' . implode(' ', $this->packages))
                ->isSuccessful();
        });

        return $this;
    }

    protected function addIdeHelpers(): self
    {
        $this->task('Updating composer.json', function () {
            $composer = $this->getComposer($this->projectDir);

            $this->mergeComposerConfig($composer, 'scripts.post-update-cmd', [
                '@php artisan ide-helper:generate',
                '@php artisan ide-helper:meta',
            ]);

            $this->mergeComposerConfig($composer, 'scripts', [
                'test' => 'vendor/bin/phpunit --stop-on-failure',
                'cs' => 'vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php',
            ]);

            return $this->writeComposer($composer, $this->projectDir);
        });

        if ($this->shouldCommit()) {
            $this->gitAdd();
            $this->gitCommit('Update composer.json config');
        }

        return $this;
    }

    protected function installPhpCs(): self
    {
        $this->copyFilesFrom('default_packages');

        if ($this->shouldCommit()) {
            $this->gitAdd();
            $this->gitCommit('Add PHPCS files');
        }

        return $this;
    }

    protected function runPhpCs(): self
    {
        $this->task('Running PHPCS', function () {
            return $this->exec('composer cs')
                ->isSuccessful();
        });

        if ($this->shouldCommit()) {
            $this->gitAdd();
            $this->gitCommit('Run first PHPCS');
        }

        return $this;
    }
}
