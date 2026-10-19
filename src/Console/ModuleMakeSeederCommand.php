<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules\Console;

class ModuleMakeSeederCommand extends ModuleLayerCommand
{
    /**
     * @var string
     */
    protected $signature = 'module:make:seeder {module : O nome do módulo} {name? : O nome do seeder}';

    /**
     * @var string
     */
    protected $description = 'Cria um seeder dentro do módulo informado.';

    /**
     * @return string
     */
    protected function layer(): string
    {
        return 'seeder';
    }
}
