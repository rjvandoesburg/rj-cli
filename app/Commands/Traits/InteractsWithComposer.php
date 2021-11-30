<?php

declare(strict_types=1);

namespace App\Commands\Traits;

use Illuminate\Support\Arr;

trait InteractsWithComposer
{
    protected function getComposer(string $path = '')
    {
        if (! empty($path)) {
            $path .= '/';
        }

        return json_decode(file_get_contents("{$path}composer.json"), true);
    }

    protected function writeComposer(array $contents, string $path = ''): bool
    {
        if (! empty($path)) {
            $path .= '/';
        }

        return file_put_contents("{$path}composer.json", json_encode($contents, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false;
    }

    protected function mergeComposerConfig(array &$composer, string $key, array $data): array
    {
        $original = Arr::get($composer, $key, []);

        Arr::set($composer, $key, array_unique(array_merge($original, $data), SORT_REGULAR));

        return $composer;
    }
}
