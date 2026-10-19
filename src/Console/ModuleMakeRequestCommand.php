<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules\Console;

class ModuleMakeRequestCommand extends ModuleLayerCommand
{
    /**
     * @var string
     */
    protected $signature = 'module:make:request {module : O nome do módulo} {name? : O nome da classe}';

    /**
     * @var string
     */
    protected $description = 'Cria um form request em um módulo existente. O nome padrão é StoreRequest.';

    /**
     * @return string
     */
    protected function layer(): string
    {
        return 'request';
    }
}
