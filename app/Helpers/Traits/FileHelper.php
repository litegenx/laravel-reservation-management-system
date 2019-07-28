<?php
declare(strict_types=1);

namespace App\Helpers\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Trait FileHelper
 * @package App\Helpers\Traits
 */
trait FileHelper
{
    /**
     * @param  string  $path
     * @param  string|null  $name
     *
     * @return string
     */
    protected function getStorageUrl(string $path, ?string $name = null): string
    {
        if (Str::startsWith($path, 'http')) {
            return $path;
        }

        return Storage::disk($name)->url($path);
    }
}
