<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Matheushack\LaravelModules\LaravelModules;

class ModuleSeedCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'module:seed {module? : O nome do módulo. Sem argumento, percorre os módulos ligados.}';

    /**
     * @var string
     */
    protected $description = 'Executa o seeder principal dos módulos ligados.';

    /**
     * @return void
     */
    public function handle(): void
    {
        /** @var LaravelModules $modules */
        $modules = $this->laravel->make(LaravelModules::class);
        $argument = $this->argument('module');
        $names = $argument === null || $argument === ''
            ? $modules->modules()
            : [Str::studly((string) $argument)];
        $ran = 0;

        foreach ($names as $name) {
            if (!$modules->exists($name)) {
                $this->error("O módulo {$name} não existe.");

                continue;
            }

            if (!$modules->enabled($name)) {
                $this->line("Módulo {$name} desligado. Seeder ignorado.");

                continue;
            }

            $class = $modules->seeder($name);

            if ($class === null) {
                $this->line("Módulo {$name} não possui seeder principal.");

                continue;
            }

            $this->info("Seeding: {$class}");
            $seeder = $this->laravel->make($class);

            if (is_object($seeder) && method_exists($seeder, 'setContainer')) {
                $seeder->setContainer($this->laravel);
            }

            if (is_object($seeder) && method_exists($seeder, 'setCommand')) {
                $seeder->setCommand($this);
            }

            $seeder->__invoke();
            $ran++;
        }

        if ($ran === 0) {
            $this->warn('Nenhum seeder executado.');
        }
    }
}
