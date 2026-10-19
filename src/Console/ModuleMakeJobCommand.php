<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules\Console;

class ModuleMakeJobCommand extends ModuleLayerCommand
{
    /**
     * @var string
     */
    protected $signature = 'module:make:job {module : O nome do módulo} {name? : O nome do job}';

    /**
     * @var string
     */
    protected $description = 'Cria um job dentro do módulo informado.';

    /**
     * @return string
     */
    protected function layer(): string
    {
        return 'job';
    }
}
