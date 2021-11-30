<?php

declare(strict_types=1);

namespace App\Commands\Traits;

use Illuminate\Support\Arr;

trait InteractsWithNpm
{
    protected function getPackageJson(string $path = '')
    {
        if (! empty($path)) {
            $path .= '/';
        }

        return json_decode(file_get_contents("{$path}package.json"), true);
    }

    protected function writePackageJson(array $contents, string $path = '')
    {
        if (! empty($path)) {
            $path .= '/';
        }

        file_put_contents("{$path}package.json", json_encode($contents, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    protected function mergePackageJsonConfig(array &$package, string $key, string $data): array
    {
        if (Arr::has($package, $key)) {
            return $package;
        }

        Arr::set($package, $key, $data);

        return $package;
    }
}
