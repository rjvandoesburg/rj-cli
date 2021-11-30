<?php

declare(strict_types=1);

namespace App\Commands\Traits;

use Symfony\Component\Process\Process;

trait InteractsWithGit
{
    use RunsShellCommands;

    public function initializeInteractsWithGit(): void
    {
        $this->addOption('no-commit', null, null, "Don't automatically commit changes");
    }

    protected function gitInit(): Process
    {
        return $this->exec('git init');
    }

    protected function gitAdd($file = '.'): Process
    {
        if (is_array($file)) {
            $file = implode(' ', $file);
        }

        return $this->exec("git add {$file}");
    }

    protected function gitCommit(string $message): Process
    {
        $message = str_replace("'", "'\''", $message);

        return $this->exec("git commit -m '{$message}' --no-verify");
    }

    protected function shouldCommit(): bool
    {
        return ! $this->option('no-commit') ?? false;
    }
}
