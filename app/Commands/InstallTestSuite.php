<?php

declare(strict_types=1);

namespace App\Commands;

use App\Commands\Traits\InteractsWithGit;
use App\Commands\Traits\IsDirectoryAware;
use App\Commands\Traits\HandlesLocalFiles;

class InstallTestSuite extends BaseCommand
{
    use InteractsWithGit;
    use IsDirectoryAware;
    use HandlesLocalFiles;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'test-suite:install {name?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Install a new test suite into a repository';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->line('# Test Suite', 'fg=cyan');

        $this->checkCurrentWorkingDirectory();

        $this->copyFilesFrom('test_suite', true);

        if ($this->shouldCommit()) {
            $this->gitAdd();
            $this->gitCommit('Install default test suite');
        }

        $this->returnToOriginalDir();
    }
}
