<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules\Console;

class ModuleMakeControllerCommand extends ModuleLayerCommand
{
    /**
     * @var string
     */
    protected $signature = 'module:make:controller {module : O nome do módulo} {name? : O nome da classe}';

    /**
     * @var string
     */
    protected $description = 'Cria um controller em um módulo existente.';

    /**
     * @return string
     */
    protected function layer(): string
    {
        return 'controller';
    }
}
