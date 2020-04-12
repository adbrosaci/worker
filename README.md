# Worker
Simple interactive worker for doing hard jobs. Inspired by [symfony/maker-bundle](https://github.com/symfony/maker-bundle).

---

[![Build Status](https://img.shields.io/travis/com/martenb/worker.svg?style=flat-square)](https://travis-ci.com/martenb/worker)
[![Code coverage](https://img.shields.io/coveralls/martenb/worker.svg?style=flat-square)](https://coveralls.io/r/martenb/worker)
[![Licence](https://img.shields.io/packagist/l/adbros/worker.svg?style=flat-square)](https://packagist.org/packages/adbros/worker)
[![Downloads this Month](https://img.shields.io/packagist/dm/adbros/worker.svg?style=flat-square)](https://packagist.org/packages/adbros/google)
[![Downloads total](https://img.shields.io/packagist/dt/adbros/worker.svg?style=flat-square)](https://packagist.org/packages/adbros/worker)
[![Latest stable](https://img.shields.io/packagist/v/adbros/worker.svg?style=flat-square)](https://packagist.org/packages/adbros/worker)

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

# Generate control with factory and template
php bin/console worker:control [options] [--] [<name>]

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
