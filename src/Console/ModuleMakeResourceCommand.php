<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules\Console;

class ModuleMakeResourceCommand extends ModuleLayerCommand
{
    /**
     * @var string
     */
    protected $signature = 'module:make:resource {module : O nome do módulo} {name? : O nome da classe}';

    /**
     * @var string
     */
    protected $description = 'Cria um resource em um módulo existente.';

    /**
     * @return string
     */
    protected function layer(): string
    {
        return 'resource';
    }
}
