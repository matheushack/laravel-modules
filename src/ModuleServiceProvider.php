<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

abstract class ModuleServiceProvider extends ServiceProvider
{
    /**
     * @return void
     * @throws BindingResolutionException
     */
    final public function boot(): void
    {
        /** @var LaravelModules $modules */
        $modules = $this->app->make(LaravelModules::class);
        $name = $this->moduleName();

        if (!$modules->enabled($name)) {
            return;
        }

        $this->loadModuleRoutes($modules, $name);
        $this->loadModuleMigrations($modules, $name);
        $this->bootModule();
    }

    /**
     * Ponto de extensão do módulo. O carregamento de rotas e migrations fica no boot final.
     *
     * @return void
     */
    protected function bootModule(): void
    {
    }

    /**
     * @return string
     */
    protected function moduleName(): string
    {
        $namespace = trim((string) config('laravel-modules.namespace'), '\\').'\\';
        $relative = Str::startsWith(static::class, $namespace)
            ? Str::after(static::class, $namespace)
            : static::class;

        return Str::before($relative, '\\');
    }

    /**
     * @param LaravelModules $modules
     * @param string $name
     * @return void
     */
    protected function loadModuleRoutes(LaravelModules $modules, string $name): void
    {
        foreach ($modules->routing($name) as $channel) {
            $file = ltrim((string) ($channel['file'] ?? ''), '/');

            if ($file === '') {
                continue;
            }

            if (!str_contains($file, '/')) {
                $file = trim($modules->folder($name, 'routes'), '/').'/'.$file;
            }

            $file = $modules->path($name).'/'.$file;

            if (!File::exists($file)) {
                continue;
            }

            Route::prefix((string) ($channel['prefix'] ?? ''))
                ->middleware($channel['middleware'] ?? [])
                ->group($file);
        }
    }

    /**
     * @param LaravelModules $modules
     * @param string $name
     * @return void
     */
    protected function loadModuleMigrations(LaravelModules $modules, string $name): void
    {
        $path = $modules->path($name).'/'.$modules->folder($name, 'migrations');

        if (File::isDirectory($path)) {
            $this->loadMigrationsFrom($path);
        }
    }
}
