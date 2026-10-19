<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Matheushack\LaravelModules\Console\ModuleLayerCommand;
use Matheushack\LaravelModules\Console\ModuleListCommand;
use Matheushack\LaravelModules\Console\ModuleMakeCommand;
use Matheushack\LaravelModules\Console\ModuleMigrationCommand;
use Matheushack\LaravelModules\Console\ModuleSeedCommand;

class LaravelModulesServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-modules.php', 'laravel-modules');

        $this->app->singleton(LaravelModules::class);
        $this->app->alias(LaravelModules::class, 'laravel-modules');

        /** @var LaravelModules $modules */
        $modules = $this->app->make(LaravelModules::class);

        foreach ($modules->modules() as $module) {
            $modules->registerModuleConfig($module);

            if (!$modules->enabled($module)) {
                continue;
            }

            $provider = $modules->namespace($module).'\\'.$module.'ServiceProvider';

            if (class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->registerFactoryResolver();

        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            ModuleMakeCommand::class,
            ModuleListCommand::class,
            ModuleMigrationCommand::class,
            ModuleSeedCommand::class,
            ...ModuleLayerCommand::commands(),
        ]);

        $this->publishes([
            __DIR__.'/../config/laravel-modules.php' => config_path('laravel-modules.php'),
        ], 'laravel-modules-config');

        $this->publishes([
            __DIR__.'/../stubs' => base_path('stubs/laravel-modules'),
        ], 'laravel-modules-stubs');
    }

    /**
     * @return void
     */
    private function registerFactoryResolver(): void
    {
        if (!class_exists(Factory::class)) {
            return;
        }

        Factory::guessFactoryNamesUsing(function (string $modelName): string {
            $namespace = trim((string) config('laravel-modules.namespace'), '\\');
            $prefix = $namespace.'\\';

            if (str_starts_with($modelName, $prefix)) {
                /** @var LaravelModules $modules */
                $modules = $this->app->make(LaravelModules::class);
                $module = Str::before(Str::after($modelName, $prefix), '\\');

                return $modules->classNamespace($module, 'factories').'\\'.class_basename($modelName).'Factory';
            }

            $appNamespace = $this->app->getNamespace();
            $model = str_starts_with($modelName, $appNamespace.'Models\\')
                ? Str::after($modelName, $appNamespace.'Models\\')
                : Str::after($modelName, $appNamespace);

            return $appNamespace.'Database\\Factories\\'.$model.'Factory';
        });
    }
}
