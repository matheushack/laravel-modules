<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules\Console;

class ModuleMakeConfigCommand extends ModuleLayerCommand
{
    /**
     * @var string
     */
    protected $signature = 'module:make:config {module : O nome do módulo}';

    /**
     * @var string
     */
    protected $description = 'Cria o config.php na raiz do módulo informado.';

    /**
     * @return string
     */
    protected function layer(): string
    {
        return 'config';
    }
}
