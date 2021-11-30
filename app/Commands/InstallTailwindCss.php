<?php

declare(strict_types=1);

namespace App\Commands;

use App\Commands\Traits\InteractsWithGit;
use App\Commands\Traits\IsDirectoryAware;
use App\Commands\Traits\RunsShellCommands;

class InstallTailwindCss extends BaseCommand
{
    use RunsShellCommands;
    use InteractsWithGit;
    use IsDirectoryAware;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'tailwind:install {name?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Install Tailwind CSS';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->line('# Tailwind CSS', 'fg=cyan');

        $this->checkCurrentWorkingDirectory();

        $this
            ->installNpmPackages()
            ->generateTailwindConfig()
            ->generateStylesheet()
            ->updateWebpack()
            ->runFixer();

        if ($this->shouldCommit()) {
            $this->gitAdd();
            $this->gitCommit('Install Tailwind CSS');
        }

        $this->returnToOriginalDir();
    }

    protected function installNpmPackages(): self
    {
        $this->task('Installing required npm packages', function () {
            return $this->exec('npm install -D tailwindcss@latest postcss@latest autoprefixer@latest @tailwindcss/forms @tailwindcss/line-clamp @tailwindcss/typography --save-prod')
                ->isSuccessful();
        });

        return $this;
    }

    protected function generateTailwindConfig(): self
    {
        $this->task('Initializing Tailwind', function () {
            return $this->exec('npx tailwindcss init')
                ->isSuccessful();
        });

        $this->task('Configuring Tailwind', function () {
            if (! $content = file_get_contents('tailwind.config.js')) {
                return false;
            }

            $content = "/* eslint-disable global-require */\n{$content}";

            // Update purge paths and add JIT
            $content = str_replace('purge: [],', <<<'PURGE'
            mode: 'jit',
              purge: [
                './app/**/*.php',
                './resources/**/*.blade.php',
                './resources/**/*.js',
              ],
            PURGE, $content);

            // Update plugins
            $content = str_replace('plugins: [],', <<<'PLUGINS'
            plugins: [
                  require('@tailwindcss/typography'),
                  require('@tailwindcss/forms'),
                  require('@tailwindcss/line-clamp'),
              ],
            PLUGINS, $content);

            return file_put_contents('tailwind.config.js', $content) !== false;
        });

        return $this;
    }

    protected function generateStylesheet(): self
    {
        $this->task('Updating app.css', function () {
            $result = file_put_contents('resources/css/app.css', <<<'CSS'
            @tailwind base;

            @tailwind components;

            @tailwind utilities;
            CSS);

            return $result !== false;
        });

        return $this;
    }

    protected function updateWebpack(): self
    {
        $this->task('Updating webpack.mix.js', function () {
            $content = file_get_contents('webpack.mix.js');

            $content = "/* eslint-disable global-require */\n{$content}";

            // We need the newline below the require!
            $config = <<<'CONFIG'
                    require('tailwindcss'),

            CONFIG;
            $content = preg_replace('#(postCss\(.*\[\n)\s+//\n(.*)#s', "$1{$config}$2", $content);

            $content .= "\n" . <<<'CONFIG'
            // Disable mix success notifications
            mix.disableSuccessNotifications();

            // Version our CSS and JS files
            mix.version();

            // Add sourcemaps to dev files
            mix.sourceMaps(false, 'eval');
            CONFIG;

            return file_put_contents('webpack.mix.js', $content) !== false;
        });

        return $this;
    }

    protected function runFixer(): self
    {
        $this->task('Running fixer', function () {
            return $this->exec('npm run cs')
                ->isSuccessful();
        });

        return $this;
    }
}
