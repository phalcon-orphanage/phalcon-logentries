# Phalcon Logentries

Phalcon library to connect and make log entries using [Logentries][1].
You can adapt it to your own needs or improve it if you want.

**Work In Progress. Do not use this version in production!**

Please write us if you have any feedback.

Thanks.

## NOTE

The master branch will always contain the latest stable version. If you wish
to check older versions or newer ones currently under development, please
switch to the relevant branch.

## Get Started

### Requirements

To use this library on your machine, you need at least:

* [Composer][2]
* PHP >= 5.4
* Latest stable [Phalcon Framework release][3] extension enabled

Additional requirements:

* [Codeception][4] >= 2.1.x (for testing)
* [PHP_CodeSniffer][5] >= 2.x (for testing)

### Installation

Install composer in a common location or in your project:

```sh
$ curl -s http://getcomposer.org/installer | php
```

Create the composer.json file as follows:

```json
{
    "require": {
        "sergeyklay/phalcon-logentries": "dev-master"
    }
}
```

Run the composer installer:

```sh
$ php composer.phar install
```

## Tests

Phosphorum use [Codeception][6] unit test.

First you need to re-generate base classes for all suites:

```bash
$ vendor/bin/codecept build
```

Execute all test with `run` command:

```bash
$ vendor/bin/codecept run
# OR
$ vendor/bin/codecept run --debug # Detailed output
```

More details about Console Commands see [here][7].

## License

Phalcon Logentries is open-sourced software licensed under the [New BSD License][8]. © Phalcon Framework Team and contributors

[1]: https://logentries.com/
[2]: https://getcomposer.org/
[3]: https://github.com/phalcon/cphalcon/releases
[4]: http://codeception.com/
[5]: https://github.com/squizlabs/PHP_CodeSniffer
[6]: http://codeception.com
[7]: http://codeception.com/docs/reference/Commands
[8]: https://github.com/phalcon/forum/blob/master/docs/LICENSE.md
