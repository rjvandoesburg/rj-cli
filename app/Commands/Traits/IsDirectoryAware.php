<?php

declare(strict_types=1);

namespace App\Commands\Traits;

trait IsDirectoryAware
{
    /**
     * The current working directory.
     *
     * @var string
     */
    protected $currentWorkingDirectory;

    /**
     * The execution path.
     *
     * @var string
     */
    protected $projectDir;

    /**
     * The name of the website (if provided).
     *
     * @var string
     */
    protected $websiteName;

    /**
     * Check if the user provided a name, if so move to it.
     */
    protected function checkCurrentWorkingDirectory(): void
    {
        $this->projectDir = $this->currentWorkingDirectory = getcwd();

        if (($name = $this->argument('name')) !== null) {
            $this->projectDir = getcwd() . DIRECTORY_SEPARATOR . $name;
            $this->websiteName = $name;
        }

        if (empty($name)) {
            $dir = explode('/', $this->projectDir);
            $this->websiteName = end($dir);
        }

        chdir($this->projectDir);
    }

    /**
     * Return to the original current working directory.
     */
    protected function returnToOriginalDir(): void
    {
        chdir($this->currentWorkingDirectory);
    }
}
