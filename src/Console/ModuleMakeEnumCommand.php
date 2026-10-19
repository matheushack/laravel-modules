<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules\Console;

class ModuleMakeEnumCommand extends ModuleLayerCommand
{
    /**
     * @var string
     */
    protected $signature = 'module:make:enum {module : O nome do módulo} {name : O nome do enum}';

    /**
     * @var string
     */
    protected $description = 'Cria um enum dentro de um módulo existente.';

    /**
     * @return string
     */
    protected function layer(): string
    {
        return 'enum';
    }
}
