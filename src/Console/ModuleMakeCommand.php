<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Matheushack\LaravelModules\LaravelModules;

class ModuleMakeCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'module:make {name : O nome do módulo}';

    /**
     * @var string
     */
    protected $description = 'Cria um módulo padrão com controller, service, repository, filter, model, resource, requests, rotas e README.';

    /**
     * @return void
     */
    public function handle(): void
    {
        /** @var LaravelModules $modules */
        $modules = $this->laravel->make(LaravelModules::class);
        $name = Str::studly((string) $this->argument('name'));

        if ($modules->exists($name)) {
            $this->error("O módulo {$name} já existe.");

            return;
        }

        foreach ((array) config('laravel-modules.structure', []) as $directory) {
            if (!is_string($directory) || $directory === '') {
                continue;
            }

            File::makeDirectory($modules->path($name).'/'.$directory, 0755, true);
        }

        $generate = (array) config('laravel-modules.generate', []);

        foreach ((array) config('laravel-modules.layers') as $layer => $definition) {
            $scaffold = array_key_exists($layer, $generate)
                ? (bool) $generate[$layer]
                : !empty($definition['scaffold']);

            if (!$scaffold) {
                continue;
            }

            $class = Str::studly((string) ($definition['class'] ?? $name));
            $modules->write(
                $name,
                $class,
                $modules->layerPath($name, (string) $layer, $class),
                (string) $definition['stub']
            );
        }

        $this->info("Módulo {$name} criado em {$modules->path($name)}.");
        $this->line('Camadas padrão: controller, service, repository, filter, model, resource, requests, rotas e README.');
        $this->line("Enum: php artisan module:make:enum {$name} NomeDoEnum");
        $this->line("Migration: php artisan module:make:migration {$name} create_exemplo_table");
        $this->line("Factory: php artisan module:make:factory {$name}");
        $this->line("Seeder: php artisan module:make:seeder {$name}");
        $this->line("Job: php artisan module:make:job {$name} NomeDoJob");
    }
}
