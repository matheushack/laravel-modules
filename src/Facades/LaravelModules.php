<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string path(string|null $module = null)
 * @method static bool exists(string $module)
 * @method static array<int, string> modules()
 * @method static string namespace(string $module)
 * @method static void enable(string $module)
 * @method static void disable(string $module)
 * @method static bool enabled(string $module)
 * @method static bool disabled(string $module)
 * @method static void registerModuleConfig(string $module)
 * @method static array<string, mixed> moduleConfig(string $module)
 * @method static string folder(string $module, string $key)
 * @method static string classNamespace(string $module, string $folderKey)
 * @method static array<string, array<string, mixed>> routing(string $module)
 * @method static array<string, mixed> layerDefinition(string $module, string $layer)
 * @method static string layerPath(string $module, string $layer, string $class)
 * @method static string primaryKey(string $module, string $class)
 * @method static string|null seeder(string $module)
 * @method static string stubPath(string $stub)
 * @method static void write(string $module, string $class, string $destination, string $stub, array $extra = [])
 * @method static string render(string $module, string $class, string $stub, array $extra = [])
 *
 * @see \Matheushack\LaravelModules\LaravelModules
 */
class LaravelModules extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \Matheushack\LaravelModules\LaravelModules::class;
    }
}
