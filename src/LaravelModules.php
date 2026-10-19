<?php

declare(strict_types=1);

namespace Matheushack\LaravelModules;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LaravelModules
{
    /**
     * @var array<string, bool>
     */
    protected array $runtime = [];

    /**
     * @var array<string, array<string, mixed>>
     */
    protected array $configs = [];

    /**
     * @param string|null $module
     * @return string
     */
    public function path(?string $module = null): string
    {
        $root = base_path((string) config('laravel-modules.path'));

        if ($module === null || $module === '') {
            return $root;
        }

        return $root.'/'.Str::studly($module);
    }

    /**
     * @param string $module
     * @return bool
     */
    public function exists(string $module): bool
    {
        return File::isDirectory($this->path($module));
    }

    /**
     * @return array<int, string>
     */
    public function modules(): array
    {
        if (!File::isDirectory($this->path())) {
            return [];
        }

        return array_values(array_map(
            static fn (string $directory): string => basename($directory),
            File::directories($this->path())
        ));
    }

    /**
     * @param string $module
     * @return string
     */
    public function namespace(string $module): string
    {
        return config('laravel-modules.namespace').'\\'.Str::studly($module);
    }

    /**
     * Liga o módulo só durante esta execução.
     * Rotas já registradas no boot não são removidas.
     *
     * @param string $module
     * @return void
     */
    public function enable(string $module): void
    {
        $this->runtime[Str::studly($module)] = true;
    }

    /**
     * Desliga o módulo só durante esta execução.
     * Para desligar de forma permanente, use config specific ou o config.php do módulo.
     *
     * @param string $module
     * @return void
     */
    public function disable(string $module): void
    {
        $this->runtime[Str::studly($module)] = false;
    }

    /**
     * @param string $module
     * @return bool
     */
    public function enabled(string $module): bool
    {
        $module = Str::studly($module);

        if (!$this->exists($module)) {
            return false;
        }

        if (array_key_exists($module, $this->runtime)) {
            return $this->runtime[$module];
        }

        return (bool) ($this->moduleConfig($module)['enabled'] ?? true);
    }

    /**
     * @param string $module
     * @return bool
     */
    public function disabled(string $module): bool
    {
        return !$this->enabled($module);
    }

    /**
     * @param string $module
     * @return void
     */
    public function registerModuleConfig(string $module): void
    {
        $module = Str::studly($module);
        $fromFile = [];
        $file = $this->path($module).'/config.php';

        if (File::exists($file)) {
            $loaded = require $file;
            $fromFile = is_array($loaded) ? $loaded : [];
        }

        $specific = config("laravel-modules.specific.{$module}", []);
        $merged = array_replace_recursive($fromFile, is_array($specific) ? $specific : []);
        $this->configs[$module] = $merged;

        config([
            "laravel-modules.specific.{$module}" => $merged,
            $module => $merged,
        ]);
    }

    /**
     * @param string $module
     * @return array<string, mixed>
     */
    public function moduleConfig(string $module): array
    {
        $module = Str::studly($module);

        if (!array_key_exists($module, $this->configs)) {
            $this->registerModuleConfig($module);
        }

        return $this->configs[$module];
    }

    /**
     * @param string $module
     * @param string $key
     * @return string
     */
    public function folder(string $module, string $key): string
    {
        $specific = $this->moduleConfig($module)['structure'][$key] ?? null;

        if (is_string($specific)) {
            return $specific;
        }

        $folder = config("laravel-modules.structure.{$key}");

        if (is_string($folder)) {
            return $folder;
        }

        if ($key === 'migrations') {
            return (string) config('laravel-modules.migrations.path', 'resources/database/migrations');
        }

        return '';
    }

    /**
     * @param string $module
     * @param string $folderKey
     * @return string
     */
    public function classNamespace(string $module, string $folderKey): string
    {
        $base = rtrim($this->namespace($module), '\\');
        $folder = trim(str_replace('/', '\\', $this->folder($module, $folderKey)), '\\');

        return $folder === '' ? $base : $base.'\\'.$folder;
    }

    /**
     * @param string $module
     * @return array<string, array<string, mixed>>
     */
    public function routing(string $module): array
    {
        $channels = config('laravel-modules.routing');

        if (!is_array($channels) || $channels === []) {
            $legacy = config('laravel-modules.routes');
            $channels = is_array($legacy) ? [
                'api' => array_replace([
                    'enabled' => true,
                    'prefix' => 'api',
                    'middleware' => ['api'],
                    'file' => 'resources/routes/api.php',
                ], $legacy),
            ] : [];
        }

        $override = $this->moduleConfig($module)['routing'] ?? [];
        $resolved = [];

        foreach ($channels as $name => $channel) {
            if (!is_array($channel)) {
                continue;
            }

            $channel = array_replace($channel, is_array($override[$name] ?? null) ? $override[$name] : []);

            if (empty($channel['enabled'])) {
                continue;
            }

            $resolved[(string) $name] = $channel;
        }

        return $resolved;
    }

    /**
     * @param string $module
     * @param string $layer
     * @return array<string, mixed>
     */
    public function layerDefinition(string $module, string $layer): array
    {
        $default = (array) config("laravel-modules.layers.{$layer}", []);
        $specific = (array) ($this->moduleConfig($module)['layers'][$layer] ?? []);

        return array_replace($default, $specific);
    }

    /**
     * @param string $module
     * @param string $layer
     * @param string $class
     * @return string
     */
    public function layerPath(string $module, string $layer, string $class): string
    {
        $definition = $this->layerDefinition($module, $layer);
        $file = str_replace(
            ['{{class}}', '{{module}}'],
            [Str::studly($class), Str::studly($module)],
            (string) ($definition['path'] ?? '')
        );

        $folder = '';

        if (array_key_exists('folder', $definition)) {
            $folder = $this->folder($module, (string) $definition['folder']);
        }

        return $this->path($module).'/'.trim($folder.'/'.$file, '/');
    }

    /**
     * Chave primária do model do módulo. Sem model correspondente, usa id.
     *
     * @param string $module
     * @param string $class
     * @return string
     */
    public function primaryKey(string $module, string $class): string
    {
        $module = Str::studly($module);
        $candidates = array_values(array_unique(array_filter([
            $class === '' ? '' : Str::studly($class),
            $module,
        ])));

        foreach ($candidates as $candidate) {
            $resolved = $this->resolvePrimaryKey($module, $candidate);

            if ($resolved !== null) {
                return $resolved;
            }
        }

        return 'id';
    }

    /**
     * @param string $module
     * @param string $class
     * @return string|null
     * @throws FileNotFoundException
     */
    private function resolvePrimaryKey(string $module, string $class): ?string
    {
        $modelClass = $this->classNamespace($module, 'models').'\\'.$class;

        try {
            if (class_exists($modelClass)) {
                $model = new $modelClass();

                if (is_object($model) && method_exists($model, 'getKeyName')) {
                    return (string) $model->getKeyName();
                }
            }
        } catch (\Throwable) {
        }

        $path = $this->layerPath($module, 'model', $class);

        if (!is_file($path)) {
            return null;
        }

        $contents = File::get($path);

        if (preg_match('/\$primaryKey\s*=\s*[\'"]([^\'"]+)[\'"]/', $contents, $matches) === 1) {
            return $matches[1];
        }

        return 'id';
    }

    /**
     * @param string $module
     * @return string|null
     */
    public function seeder(string $module): ?string
    {
        $class = $this->classNamespace($module, 'seeders').'\\'.Str::studly($module).'Seeder';

        return class_exists($class) ? $class : null;
    }

    /**
     * @param string $stub
     * @return string
     */
    public function stubPath(string $stub): string
    {
        $override = config('laravel-modules.stubs');

        if (is_string($override) && $override !== '') {
            $published = base_path(trim($override, '/').'/'.$stub);
            if (File::exists($published)) {
                return $published;
            }
        }

        return dirname(__DIR__).'/stubs/'.$stub;
    }

    /**
     * @param string $module
     * @param string $class
     * @param string $destination
     * @param string $stub
     * @param array<string, string> $extra
     * @return void
     */
    public function write(string $module, string $class, string $destination, string $stub, array $extra = []): void
    {
        File::ensureDirectoryExists(dirname($destination));
        File::put($destination, $this->render($module, $class, $stub, $extra));
    }

    /**
     * @param string $module
     * @param string $class
     * @param string $stub
     * @param array<string, string> $extra
     * @return string
     */
    public function render(string $module, string $class, string $stub, array $extra = []): string
    {
        $contents = File::get($this->stubPath($stub));
        $map = $this->replacements($module, $class, $extra);

        if (str_contains($contents, '{{PrimaryKey}}') && !array_key_exists('{{PrimaryKey}}', $map)) {
            $resolvedModule = $module === '' ? '' : Str::studly($module);
            $map['{{PrimaryKey}}'] = $resolvedModule === '' ? 'id' : $this->primaryKey($resolvedModule, $class);
        }

        return strtr($contents, $map);
    }

    /**
     * @param string $module
     * @param string $class
     * @param array<string, string> $extra
     * @return array<string, string>
     */
    public function replacements(string $module, string $class, array $extra = []): array
    {
        $module = $module === '' ? '' : Str::studly($module);
        $class = $class === '' ? '' : Str::studly($class);
        $configured = config('laravel-modules.replacements', []);
        $map = [
            '{{ModuleNamespace}}' => (string) config('laravel-modules.namespace'),
            '{{Name}}' => $module,
            '{{Class}}' => $class,
            '{{ModelClass}}' => $class,
            '{{lowerName}}' => Str::lower($class),
            '{{slugName}}' => Str::slug($class),
            '{{pluralSlugName}}' => Str::slug(Str::plural($class)),
            '{{singularCamel}}' => Str::camel($class),
        ];

        if ($module !== '') {
            foreach ([
                'ProviderNamespace' => 'provider',
                'ControllersNamespace' => 'controllers',
                'RequestsNamespace' => 'requests',
                'ResourcesNamespace' => 'resources',
                'ModelsNamespace' => 'models',
                'ServicesNamespace' => 'services',
                'RepositoriesNamespace' => 'repositories',
                'FiltersNamespace' => 'filters',
                'EnumsNamespace' => 'enums',
                'JobsNamespace' => 'jobs',
                'FactoriesNamespace' => 'factories',
                'SeedersNamespace' => 'seeders',
            ] as $placeholder => $folder) {
                $map['{{'.$placeholder.'}}'] = $this->classNamespace($module, $folder);
            }
        }

        foreach ($configured as $key => $value) {
            $map['{{'.$key.'}}'] = (string) $value;
        }

        foreach ($extra as $key => $value) {
            $map[$key] = $value;
        }

        return $map;
    }
}
