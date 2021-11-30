<?php

declare(strict_types=1);

namespace App\Commands;

class NewProject extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'new {name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a new project';

    protected string $projectName;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->projectName = $this->argument('name');

        $commands = [
            InstallLaravel::class,
            InstallDefaultPackages::class,
            InstallJsLinter::class,
            InstallPreCommit::class,
            InstallTailwindCss::class,
            InstallTestSuite::class,
            AddDefaultLayout::class,
        ];

        foreach ($commands as $command) {
            $this->call($command, [
                'name' => $this->projectName,
            ]);

            $this->line('');
        }

        $this->line("<fg=green>New project installed in</fg=green> <fg=yellow>{$this->projectName}</fg=yellow>");
    }
}
