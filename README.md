# coreutils-php

[![CI](https://github.com/thenexxuz/coreutils-php/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/thenexxuz/coreutils-php/actions)

A PHP-based shim of many GNU coreutils commands. This project aims to provide
lightweight PHP implementations of common command-line utilities so you can run
them from a single PHP dispatcher or via symlinked command names.

Minimum requirements
- PHP 8.1 or newer
- Composer to install dev dependencies and run tests

Optional (recommended for full parity)
- `pcntl` and `posix` extensions — required for full-featured process control
	(used by `nohup`, `nice`, `timeout`, `env` when executing other programs).

Usage
- Run commands via the dispatcher:

```
php bin/coreutils echo hello world
php bin/coreutils pwd
php bin/coreutils cat README.md
```

- Or install the dispatcher and create symlinks so each command name invokes the
	dispatcher automatically (example):

```
chmod +x bin/coreutils
ln -s $(pwd)/bin/coreutils /usr/local/bin/echo
echo hello
```

Project layout
- `bin/coreutils` — CLI dispatcher that locates `Coreutils\Commands\*` classes
- `bin/new-command` — helper to scaffold new command stubs
- `src/Commands/` — command implementations; commands implement
	`Coreutils\CommandInterface::run(array $argv): int`
- `tests/` — PHPUnit tests

Status
- Many common utilities have working implementations (file utilities, text
	processing, hashing, and various small system helpers). Some commands aim for
	GNU-like behavior; others implement pragmatic, well-tested subsets.

Testing & static analysis
- Run tests locally:

```bash
composer install --no-interaction
vendor/bin/phpunit
```

- Run PHPStan static analysis:

```bash
vendor/bin/phpstan analyse --memory-limit=1G
```

- Note: several tests and commands depend on the `pcntl`/`posix` extensions and
	will be skipped if those extensions are not available in the runtime.

Continuous integration
- A GitHub Actions workflow is included at `.github/workflows/ci.yml` that runs
	linting, PHPStan, and PHPUnit across a matrix of PHP versions. There is a
	separate `static-analysis` job which posts PHPStan annotations to pull
	requests using reviewdog.

Contributing
- To add a new command, create a PSR-4 class under `src/Commands/` named
	`XxxCommand` that implements the `CommandInterface` and add tests in
	`tests/`.
- Follow the existing style and add PHPUnit tests; CI will run PHPStan and
	PHPUnit on your PR.

License
- See [LICENSE.md](LICENSE.md)

Enjoy — if you want help implementing specific GNU utilities to a closer
parity, tell me which commands to prioritize.

