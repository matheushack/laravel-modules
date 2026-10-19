<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules\Console;

class ModuleMakeFactoryCommand extends ModuleLayerCommand
{
    /**
     * @var string
     */
    protected $signature = 'module:make:factory {module : O nome do módulo} {name? : O nome do model}';

    /**
     * @var string
     */
    protected $description = 'Cria uma factory dentro do módulo informado.';

    /**
     * @return string
     */
    protected function layer(): string
    {
        return 'factory';
    }
}
