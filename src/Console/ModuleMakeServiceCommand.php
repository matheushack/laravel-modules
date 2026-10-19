<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules\Console;

class ModuleMakeServiceCommand extends ModuleLayerCommand
{
    /**
     * @var string
     */
    protected $signature = 'module:make:service {module : O nome do módulo} {name? : O nome da classe}';

    /**
     * @var string
     */
    protected $description = 'Cria um service em um módulo existente.';

    /**
     * @return string
     */
    protected function layer(): string
    {
        return 'service';
    }
}
