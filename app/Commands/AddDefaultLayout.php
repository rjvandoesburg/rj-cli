<?php

declare(strict_types=1);

namespace App\Commands;

use App\Commands\Traits\InteractsWithGit;
use App\Commands\Traits\IsDirectoryAware;
use App\Commands\Traits\HandlesLocalFiles;

class AddDefaultLayout extends BaseCommand
{
    use InteractsWithGit;
    use IsDirectoryAware;
    use HandlesLocalFiles;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'default-layout:install {name?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Add a default layout to the installation';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->line('# Default layout', 'fg=cyan');

        $this->checkCurrentWorkingDirectory();

        $this->copyFilesFrom('default_layout', true);

        if ($this->shouldCommit()) {
            $this->gitAdd();
            $this->gitCommit('Add default layout files');
        }

        $this->returnToOriginalDir();
    }
}
