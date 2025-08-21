<?php

declare(strict_types=1);

namespace App\Modules\Core\Helpers;

use Illuminate\Support\Arr;
use ReflectionClass;
use ReflectionException;

class Helper
{
    /**
     * @param  class-string|object  $class
     */
    public static function getResourceName($class): string
    {
        try {
            return (new ReflectionClass($class))->getShortName();
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @param  array<string, mixed>  $request
     * @param  string  $key
     */
    public static function checkIfNotNull($request, $key): bool
    {
        return Arr::has($request, $key) && ! is_null(Arr::get($request, $key));
    }

    /**
     * @param  array<string, mixed>  $request
     * @param  string  $key
     */
    public static function checkIfTrue($request, $key): bool
    {
        return Arr::has($request, $key) && (bool) Arr::get($request, $key) === true;
    }
}
