<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules\Console;

class ModuleMakeFilterCommand extends ModuleLayerCommand
{
    /**
     * @var string
     */
    protected $signature = 'module:make:filter {module : O nome do módulo} {name? : O nome da classe}';

    /**
     * @var string
     */
    protected $description = 'Cria um filter em um módulo existente.';

    /**
     * @return string
     */
    protected function layer(): string
    {
        return 'filter';
    }
}
