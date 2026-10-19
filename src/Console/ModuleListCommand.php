<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules\Console;

use Illuminate\Console\Command;
use Matheushack\LaravelModules\LaravelModules;

class ModuleListCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'module:list';

    /**
     * @var string
     */
    protected $description = 'Lista os módulos encontrados na aplicação.';

    /**
     * @return void
     */
    public function handle(): void
    {
        /** @var LaravelModules $modules */
        $modules = $this->laravel->make(LaravelModules::class);
        $names = $modules->modules();

        if ($names === []) {
            $this->warn('Nenhum módulo encontrado.');

            return;
        }

        $this->table(['Módulo', 'Status', 'Namespace', 'Caminho'], array_map(
            fn (string $name): array => [
                $name,
                $modules->enabled($name) ? 'Ligado' : 'Desligado',
                $modules->namespace($name),
                $modules->path($name),
            ],
            $names
        ));
    }
}
