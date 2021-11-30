<?php

declare(strict_types=1);

namespace App\Commands\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

trait HandlesLocalFiles
{
    public function copyFilesFrom(string $directory, bool $overwrite = false): bool
    {
        $return = true;

        $this->line('Copying files');

        foreach (Storage::allFiles($directory) as $file) {
            $destination = str_replace("{$directory}/", '', $file);
            $result = $this->task(
                sprintf('- %s', $destination),
                function () use ($file, $destination, $overwrite) {
                    if (file_exists($destination) && ! $overwrite) {
                        return false;
                    }

                    $this->createDirsIfNeeded($destination);

                    return copy(Storage::path($file), $destination);
                }
            );

            if (! $result) {
                $return = false;
            }
        }

        return $return;
    }

    protected function createDirsIfNeeded(mixed $destination): void
    {
        if (! str_contains($destination, '/')) {
            return;
        }

        if (! is_dir($dir = Str::beforeLast($destination, '/'))) {
            mkdir($dir, 0755, true);
        }
    }
}
