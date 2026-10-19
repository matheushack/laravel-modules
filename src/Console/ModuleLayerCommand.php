<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Matheushack\LaravelModules\LaravelModules;

abstract class ModuleLayerCommand extends Command
{
    /**
     * @return string
     */
    abstract protected function layer(): string;

    /**
     * @return array<int, class-string<ModuleLayerCommand>>
     */
    public static function commands(): array
    {
        return [
            ModuleMakeControllerCommand::class,
            ModuleMakeServiceCommand::class,
            ModuleMakeRepositoryCommand::class,
            ModuleMakeFilterCommand::class,
            ModuleMakeModelCommand::class,
            ModuleMakeResourceCommand::class,
            ModuleMakeRequestCommand::class,
            ModuleMakeEnumCommand::class,
            ModuleMakeFactoryCommand::class,
            ModuleMakeSeederCommand::class,
            ModuleMakeJobCommand::class,
            ModuleMakeConfigCommand::class,
            ModuleMakeWebRoutesCommand::class,
        ];
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        /** @var LaravelModules $modules */
        $modules = $this->laravel->make(LaravelModules::class);
        $module = Str::studly((string) $this->argument('module'));

        if (!$modules->exists($module)) {
            $this->error("O módulo {$module} não existe. Crie com module:make {$module}.");

            return;
        }

        $layer = $modules->layerDefinition($module, $this->layer());
        $name = $this->getDefinition()->hasArgument('name') ? $this->argument('name') : null;
        $class = Str::studly((string) ($name ?: ($layer['default'] ?? $module)));

        $destination = $modules->layerPath($module, $this->layer(), $class);

        if (is_file($destination)) {
            $this->error("O arquivo já existe: {$destination}");

            return;
        }

        $modules->write($module, $class, $destination, (string) $layer['stub']);
        $this->info("Classe criada: {$destination}");
    }
}
