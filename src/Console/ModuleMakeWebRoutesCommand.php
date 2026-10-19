<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules\Console;

class ModuleMakeWebRoutesCommand extends ModuleLayerCommand
{
    /**
     * @var string
     */
    protected $signature = 'module:make:web {module : O nome do módulo}';

    /**
     * @var string
     */
    protected $description = 'Cria o arquivo de rotas web do módulo. O canal web continua desligado até a configuração.';

    /**
     * @return string
     */
    protected function layer(): string
    {
        return 'web-routes';
    }
}
