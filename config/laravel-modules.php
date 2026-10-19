<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Local dos módulos
    |--------------------------------------------------------------------------
    */

    'namespace' => 'App\\Modules',

    'path' => 'app/Modules',

    'stubs' => null,

    /*
    |--------------------------------------------------------------------------
    | Pastas
    |--------------------------------------------------------------------------
    |
    | A pasta vazia do provider deixa a classe na raiz do módulo.
    | Factories, seeders e jobs precisam continuar compatíveis com o PSR-4
    | App\ -> app/. O namespace gerado acompanha estas pastas.
    |
    */

    'structure' => [
        'provider' => '',
        'controllers' => 'Http/Controllers',
        'requests' => 'Http/Requests',
        'resources' => 'Http/Resources',
        'models' => 'Models',
        'services' => 'Services',
        'repositories' => 'Repositories',
        'filters' => 'Filters',
        'enums' => 'Enums',
        'jobs' => 'Jobs',
        'routes' => 'resources/routes',
        'migrations' => 'resources/database/migrations',
        'factories' => 'Database/Factories',
        'seeders' => 'Database/Seeders',
    ],

    /*
    |--------------------------------------------------------------------------
    | Canais de rota
    |--------------------------------------------------------------------------
    |
    | O canal web existe para ser ligado depois. Enquanto enabled for false,
    | o arquivo resources/routes/web.php não é carregado, mesmo se existir.
    | O arquivo é só o nome; a pasta vem de structure.routes.
    |
    */

    'routing' => [
        'api' => [
            'enabled' => true,
            'prefix' => 'api',
            'middleware' => ['api'],
            'file' => 'api.php',
        ],
        'web' => [
            'enabled' => false,
            'prefix' => '',
            'middleware' => ['web'],
            'file' => 'web.php',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | O que o module:make gera
    |--------------------------------------------------------------------------
    |
    | false deixa a camada de fora do módulo novo. O comando da camada
    | continua criando o arquivo em um módulo que já existe.
    |
    */

    'generate' => [
        'controller' => true,
        'store-request' => true,
        'update-request' => true,
        'resource' => true,
        'model' => true,
        'repository' => true,
        'filter' => true,
        'service' => true,
        'provider' => true,
        'routes' => true,
        'readme' => true,
        'request' => false,
        'enum' => false,
        'factory' => false,
        'seeder' => false,
        'job' => false,
        'config' => false,
        'web-routes' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuração por módulo
    |--------------------------------------------------------------------------
    |
    | specific.NomeDoModulo sobrescreve o config.php da raiz do módulo.
    | Chaves livres ficam em config('NomeDoModulo.chave') e também em
    | config('laravel-modules.specific.NomeDoModulo').
    | enabled false impede o registro do provider, das rotas e das migrations.
    |
    */

    'specific' => [
        // 'HelloWorld' => [
        //     'enabled' => false,
        //     'routing' => [
        //         'web' => ['enabled' => true],
        //     ],
        //     'structure' => [
        //         'controllers' => 'Http/Controllers',
        //     ],
        // ],
    ],

    'replacements' => [
        'BaseController' => 'Illuminate\\Routing\\Controller',
        'BaseModel' => 'Illuminate\\Database\\Eloquent\\Model',
        'BaseRepository' => 'Illuminate\\Database\\Eloquent\\Model',
        'BaseFilter' => 'Illuminate\\Database\\Eloquent\\Builder',
        'FilterContract' => 'Illuminate\\Contracts\\Support\\Arrayable',
        'ExactFilter' => 'Illuminate\\Database\\Eloquent\\Model',
        'ResourceBase' => 'Illuminate\\Http\\Resources\\Json\\JsonResource',
        'RouteMiddleware' => 'api',
        'ModuleProvider' => 'Matheushack\\LaravelModules\\ModuleServiceProvider',
        'ModelImports' => '',
        'ModelBody' => '',
        'MigrationBlueprint' => "            \$table->id();\n            \$table->timestamps();",
    ],

    'layers' => [
        'controller' => [
            'stub' => 'controller.stub',
            'folder' => 'controllers',
            'path' => '{{class}}Controller.php',
            'scaffold' => true,
        ],
        'store-request' => [
            'stub' => 'requests/store.stub',
            'folder' => 'requests',
            'path' => 'StoreRequest.php',
            'class' => 'StoreRequest',
            'scaffold' => true,
        ],
        'update-request' => [
            'stub' => 'requests/update.stub',
            'folder' => 'requests',
            'path' => 'UpdateRequest.php',
            'class' => 'UpdateRequest',
            'scaffold' => true,
        ],
        'resource' => [
            'stub' => 'resource.stub',
            'folder' => 'resources',
            'path' => '{{class}}Resource.php',
            'scaffold' => true,
        ],
        'model' => [
            'stub' => 'model.stub',
            'folder' => 'models',
            'path' => '{{class}}.php',
            'scaffold' => true,
        ],
        'repository' => [
            'stub' => 'repository.stub',
            'folder' => 'repositories',
            'path' => '{{class}}Repository.php',
            'scaffold' => true,
        ],
        'filter' => [
            'stub' => 'filter.stub',
            'folder' => 'filters',
            'path' => '{{class}}Filter.php',
            'scaffold' => true,
        ],
        'service' => [
            'stub' => 'service.stub',
            'folder' => 'services',
            'path' => '{{class}}Service.php',
            'scaffold' => true,
        ],
        'provider' => [
            'stub' => 'provider.stub',
            'folder' => 'provider',
            'path' => '{{class}}ServiceProvider.php',
            'scaffold' => true,
        ],
        'routes' => [
            'stub' => 'routes.stub',
            'folder' => 'routes',
            'path' => 'api.php',
            'scaffold' => true,
        ],
        'readme' => [
            'stub' => 'readme.stub',
            'path' => 'README.md',
            'scaffold' => true,
        ],
        'web-routes' => [
            'stub' => 'routes-web.stub',
            'folder' => 'routes',
            'path' => 'web.php',
            'scaffold' => false,
        ],
        'request' => [
            'stub' => 'requests/store.stub',
            'folder' => 'requests',
            'path' => '{{class}}.php',
            'default' => 'StoreRequest',
            'scaffold' => false,
        ],
        'enum' => [
            'stub' => 'enum.stub',
            'folder' => 'enums',
            'path' => '{{class}}.php',
            'scaffold' => false,
        ],
        'factory' => [
            'stub' => 'factory.stub',
            'folder' => 'factories',
            'path' => '{{class}}Factory.php',
            'scaffold' => false,
        ],
        'seeder' => [
            'stub' => 'seeder.stub',
            'folder' => 'seeders',
            'path' => '{{class}}Seeder.php',
            'scaffold' => false,
        ],
        'job' => [
            'stub' => 'job.stub',
            'folder' => 'jobs',
            'path' => '{{class}}.php',
            'scaffold' => false,
        ],
        'config' => [
            'stub' => 'config.stub',
            'path' => 'config.php',
            'scaffold' => false,
        ],
    ],

];
