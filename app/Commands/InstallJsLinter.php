<?php

declare(strict_types=1);

namespace App\Commands;

use App\Commands\Traits\InteractsWithGit;
use App\Commands\Traits\InteractsWithNpm;
use App\Commands\Traits\IsDirectoryAware;
use App\Commands\Traits\HandlesLocalFiles;
use App\Commands\Traits\RunsShellCommands;
use App\Commands\Traits\InteractsWithComposer;

class InstallJsLinter extends BaseCommand
{
    use InteractsWithComposer;
    use RunsShellCommands;
    use InteractsWithGit;
    use IsDirectoryAware;
    use HandlesLocalFiles;
    use InteractsWithNpm;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'js-linter:install {name?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Install js-linter';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->line('# JS linter', 'fg=cyan');

        $this->checkCurrentWorkingDirectory();

        $this->copyFiles()
            ->updateDefaultDependencies()
            ->addEslinPackages()
            ->updatePackageJson();

        $this->returnToOriginalDir();
    }

    protected function copyFiles(): self
    {
        $this->copyFilesFrom('js_linter');

        return $this;
    }

    protected function updateDefaultDependencies(): self
    {
        $this->task('Updating npm packages', function () {
            return $this->exec('npm i lodash axios laravel-mix --save-prod')
                ->isSuccessful();
        });

        if ($this->shouldCommit()) {
            $this->gitAdd();
            $this->gitCommit('Update default npm dependencies');
        }

        return $this;
    }

    protected function addEslinPackages(): self
    {
        $this->task('Installing required npm packages', function () {
            return $this->exec('npm i eslint eslint-config-airbnb-base eslint-plugin-import eslint-import-resolver-webpack stylelint stylelint-config-standard --save-dev')
                ->isSuccessful();
        });

        if ($this->shouldCommit()) {
            $this->gitAdd();
            $this->gitCommit('Install eslint');
        }

        return $this;
    }

    protected function updatePackageJson(): self
    {
        $this->task('Add fixer script to package.json', function () {
            $npm = $this->getPackageJson();
            $npm = $this->mergePackageJsonConfig($npm, 'scripts.cs', 'npx stylelint --fix resources/css && npx eslint --fix resources/js webpack.mix.js tailwind.config.js');

            $this->writePackageJson($npm);
        });

        if ($this->shouldCommit()) {
            $this->gitAdd();
            $this->gitCommit('Add fixer script to package.json');
        }

        return $this;
    }
}
