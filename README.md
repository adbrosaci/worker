# Worker
Simple interactive worker for doing hard jobs. Inspired by [symfony/maker-bundle](https://github.com/symfony/maker-bundle).

---

[![Build Status](https://img.shields.io/travis/com/adbrosaci/worker.svg?style=flat-square)](https://travis-ci.com/adbrosaci/worker)
[![Code coverage](https://img.shields.io/coveralls/adbrosaci/worker.svg?style=flat-square)](https://coveralls.io/r/adbrosaci/worker)
[![Licence](https://img.shields.io/packagist/l/adbros/worker.svg?style=flat-square)](https://packagist.org/packages/adbros/worker)
[![Downloads this Month](https://img.shields.io/packagist/dm/adbros/worker.svg?style=flat-square)](https://packagist.org/packages/adbros/google)
[![Downloads total](https://img.shields.io/packagist/dt/adbros/worker.svg?style=flat-square)](https://packagist.org/packages/adbros/worker)
[![Latest stable](https://img.shields.io/packagist/v/adbros/worker.svg?style=flat-square)](https://packagist.org/packages/adbros/worker)

## Installation
```shell
composer require adbros/worker --dev
```

## Configuration
Create worker.php, worker.json or worker.neon in your root directory

### worker.neon example
```yaml
appDir: app # application root directory
```

## Usage

```shell
# Generate command for symfony/console package
vendor/bin/worker command [options] [--] [<name>]

# Generate control with factory and template
vendor/bin/worker control [options] [--] [<name>]

# Generate model for nextras/orm package
vendor/bin/worker orm [options] [--] [<entity> [<repository>]]

# Generate presenter and default template
vendor/bin/worker presenter [options] [--] [<name>]
```

## Examples

### ormJob
![ormJob](.docs/ormJob.png)

### presenterJob
![presenterJob](.docs/presenterJob.png)
