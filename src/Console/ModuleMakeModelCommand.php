<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules\Console;

class ModuleMakeModelCommand extends ModuleLayerCommand
{
    /**
     * @var string
     */
    protected $signature = 'module:make:model {module : O nome do módulo} {name? : O nome da classe}';

    /**
     * @var string
     */
    protected $description = 'Cria um model em um módulo existente.';

    /**
     * @return string
     */
    protected function layer(): string
    {
        return 'model';
    }
}
