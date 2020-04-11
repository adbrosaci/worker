# Worker
Simple interactive worker for doing hard jobs. Inspired by [symfony/maker-bundle](https://github.com/symfony/maker-bundle).

## Installation
```shell
composer require adbros/worker --dev
```

## Configuration

```yaml
extensions:
    worker: Adbros\Worker\DI\WorkerExtension
```

## Usage

```shell
# Generate command for symfony/command package
php bin/console worker:command [options] [--] [<name>]

# Generate model for nextras/orm package
php bin/console worker:orm [options] [--] [<entity> [<repository>]]

# Generate presenter and default template
php bin/console worker:presenter [options] [--] [<name>]
```

## Create your own jobs
Just create a class that implements ```Adbros\Worker\IJob``` and register it to DI container. That's it.

## Examples

### ormJob
![ormJob](.docs/ormJob.png)

### presenterJob
![presenterJob](.docs/presenterJob.png)
