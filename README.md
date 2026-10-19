# Laravel Modules

[![Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Frepo.packagist.org%2Fp2%2Fmatheushack%2Flaravel-modules.json&query=%24.packages%5B%22matheushack%2Flaravel-modules%22%5D%5B0%5D.version&label=version&color=blue&style=flat-square)](https://packagist.org/packages/matheushack/laravel-modules)
[![Downloads](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fpackagist.org%2Fpackages%2Fmatheushack%2Flaravel-modules%2Fstats.json&query=%24.downloads.total&label=downloads&color=brightgreen&style=flat-square)](https://packagist.org/packages/matheushack/laravel-modules)
[![Monthly Downloads](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fpackagist.org%2Fpackages%2Fmatheushack%2Flaravel-modules%2Fstats.json&query=%24.downloads.monthly&label=downloads%2Fmonth&color=brightgreen&style=flat-square)](https://packagist.org/packages/matheushack/laravel-modules)
[![PHP Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Frepo.packagist.org%2Fp2%2Fmatheushack%2Flaravel-modules.json&query=%24.packages%5B%22matheushack%2Flaravel-modules%22%5D%5B0%5D.require.php&label=php&logo=php&logoColor=white&color=777BB4&style=flat-square)](https://packagist.org/packages/matheushack/laravel-modules)
[![Laravel](https://img.shields.io/badge/laravel-11%20%7C%2012-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://packagist.org/packages/matheushack/laravel-modules)
[![License](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Frepo.packagist.org%2Fp2%2Fmatheushack%2Flaravel-modules.json&query=%24.packages%5B%22matheushack%2Flaravel-modules%22%5D%5B0%5D.license%5B0%5D&label=license&color=blue&style=flat-square)](https://packagist.org/packages/matheushack/laravel-modules)

Gerador e carregador de módulos para Laravel. Cada módulo fica em `app/Modules` e traz controller, service, repository, filter, model, resource, requests, rotas e provider. O pacote registra os módulos ligados, carrega rotas e migrations e expõe comandos Artisan para criar o restante sob demanda.

## Requisitos

- PHP 8.2 ou superior
- Laravel 11 ou 12

## Instalação

```bash
composer require matheushack/laravel-modules
```

O service provider entra pelo package discovery do Laravel. Para publicar a configuração:

```bash
php artisan vendor:publish --tag=laravel-modules-config
```

Para sobrescrever os stubs gerados:

```bash
php artisan vendor:publish --tag=laravel-modules-stubs
```

Os stubs publicados ficam em `stubs/laravel-modules`.

## Uso

Cria um módulo com as camadas padrão:

```bash
php artisan module:make Blog
```

O comando gera a estrutura em `app/Modules/Blog`, com namespace `App\Modules\Blog`.

Lista os módulos encontrados:

```bash
php artisan module:list
```

Executa o seeder principal dos módulos ligados, ou de um módulo específico:

```bash
php artisan module:seed
php artisan module:seed Blog
```

### Comandos por camada

Estes comandos criam o arquivo em um módulo que já existe:

| Comando | O que cria |
| --- | --- |
| `module:make:controller {module} {name?}` | Controller |
| `module:make:request {module} {name?}` | Form request (padrão: `StoreRequest`) |
| `module:make:resource {module} {name?}` | API resource |
| `module:make:model {module} {name?}` | Model |
| `module:make:repository {module} {name?}` | Repository |
| `module:make:filter {module} {name?}` | Filter |
| `module:make:service {module} {name?}` | Service |
| `module:make:enum {module} {name}` | Enum |
| `module:make:factory {module} {name?}` | Factory |
| `module:make:seeder {module} {name?}` | Seeder |
| `module:make:job {module} {name?}` | Job |
| `module:make:config {module}` | `config.php` na raiz do módulo |
| `module:make:migration {module} {name}` | Migration |
| `module:make:web {module}` | Arquivo de rotas web |

`module:migration` é um alias de `module:make:migration`.

```bash
php artisan module:make:enum Blog PostStatus
php artisan module:make:migration Blog create_posts_table
php artisan module:make:factory Blog
php artisan module:make:seeder Blog
php artisan module:make:job Blog PublishPost
```

## O que o `module:make` gera

Por padrão, o scaffold inclui controller, store request, update request, resource, model, repository, filter, service, provider, rotas da API e README.

Enum, factory, seeder, job, config, request avulso e rotas web ficam de fora do scaffold e são criados pelos comandos da tabela acima. A chave `generate` em `config/laravel-modules.php` liga ou desliga cada camada na criação do módulo. O comando da camada continua disponível mesmo quando ela está desligada no scaffold.

## Estrutura

```text
app/Modules/Blog/
├── BlogServiceProvider.php
├── README.md
├── config.php                          # opcional
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
├── Models/
├── Services/
├── Repositories/
├── Filters/
├── Enums/
├── Jobs/
├── Database/
│   ├── Factories/
│   └── Seeders/
└── resources/
    ├── routes/
    │   ├── api.php
    │   └── web.php                     # opcional
    └── database/
        └── migrations/
```

Pastas, namespace e canais de rota são configuráveis em `config/laravel-modules.php`.

## Rotas

O canal `api` começa ligado, com prefixo `api` e middleware `api`. O canal `web` existe, mas permanece desligado: `module:make:web` só cria o arquivo. Para carregá-lo:

```php
'routing' => [
    'web' => [
        'enabled' => true,
    ],
],
```

A mesma chave pode ser definida só para um módulo em `specific` ou no `config.php` da raiz desse módulo.

## Ligar e desligar um módulo

`enabled` com `false` impede o registro do provider, das rotas e das migrations.

No config publicado:

```php
'specific' => [
    'Blog' => [
        'enabled' => false,
    ],
],
```

Ou no `config.php` do próprio módulo. Em runtime, só durante a execução atual:

```php
use Matheushack\LaravelModules\Facades\LaravelModules;

LaravelModules::disable('Blog');
LaravelModules::enable('Blog');
```

`disable()` e `enable()` não removem rotas que já foram registradas no boot.

## Facade

O alias `LaravelModules` aponta para `Matheushack\LaravelModules\Facades\LaravelModules`. Além de `enable`, `disable` e `enabled`, a facade expõe caminho, namespace, lista de módulos, configuração e geração de arquivos.

## Licença

MIT. Veja [LICENSE](LICENSE).
