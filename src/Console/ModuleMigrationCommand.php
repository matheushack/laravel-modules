<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Matheushack\LaravelModules\LaravelModules;

class ModuleMigrationCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'module:make:migration
        {module : O nome do módulo}
        {name : O nome da migration, por exemplo create_widgets_table}';

    /**
     * @var string
     */
    protected $description = 'Cria uma migration dentro do módulo informado.';

    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->setAliases(['module:migration']);
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        /** @var LaravelModules $modules */
        $modules = $this->laravel->make(LaravelModules::class);
        $module = Str::studly((string) $this->argument('module'));
        $name = Str::snake(trim((string) $this->argument('name')));

        if (!$modules->exists($module)) {
            $this->error("O módulo {$module} não existe. Crie com module:make {$module}.");

            return;
        }

        $directory = $modules->path($module).'/'.$modules->folder($module, 'migrations');
        $destination = $directory.'/'.date('Y_m_d_His')."_{$name}.php";

        if (File::exists($destination)) {
            $this->error("O arquivo já existe: {$destination}");

            return;
        }

        $stub = preg_match('/^create_.+_table$/', $name) === 1 ? 'migrations/create.stub' : 'migrations/update.stub';
        $modules->write($module, '', $destination, $stub, [
            '{{table}}' => $this->tableName($name),
        ]);

        $this->info("Migration criada: {$destination}");
    }

    /**
     * @param string $name
     * @return string
     */
    private function tableName(string $name): string
    {
        if (preg_match('/create_(.+)_table$/', $name, $matches) === 1) {
            return $matches[1];
        }

        if (preg_match('/(?:to|from|in)_(.+)_table$/', $name, $matches) === 1) {
            return $matches[1];
        }

        return 'table_name';
    }
}
