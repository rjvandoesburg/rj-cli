<?php

declare(strict_types=1);

namespace App\Commands\Traits;

use Symfony\Component\Process\Process;

trait RunsShellCommands
{
    public function exec(string $command, array $parameters = [], ?bool $quiet = null): Process
    {
        if ($quiet === null) {
            $quiet = ! $this->getOutput()->isVerbose();
        }

        $didAnything = false;

        $process = $this->buildProcess($command);
        $process->run(function ($type, $buffer) use ($quiet, &$didAnything) {
            if (empty($buffer) || $buffer === PHP_EOL || $quiet) {
                return;
            }

            $this->output->writeLn($this->formatMessage($buffer, $type === process::ERR));
            $didAnything = true;
        }, $parameters);

        if ($didAnything) {
            $this->output->writeLn("\n");
        }

        return $process;
    }

    public function buildProcess(string $command): Process
    {
        $process = Process::fromShellCommandline($command);
        $process->setTimeout(null);

        return $process;
    }

    public function formatMessage(string $buffer, $isError = false): string
    {
        $pre = $isError ? '<bg=red;fg=white> ERR </> %s' : '<bg=green;fg=white> OUT </> %s';

        return rtrim(collect(explode("\n", trim($buffer)))->reduce(function (&$carry, $line) use ($pre) {
            return $carry .= trim(sprintf($pre, $line)) . "\n";
        }, ''));
    }
}
