<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules\Console;

class ModuleMakeRepositoryCommand extends ModuleLayerCommand
{
    /**
     * @var string
     */
    protected $signature = 'module:make:repository {module : O nome do módulo} {name? : O nome da classe}';

    /**
     * @var string
     */
    protected $description = 'Cria um repository em um módulo existente.';

    /**
     * @return string
     */
    protected function layer(): string
    {
        return 'repository';
    }
}
