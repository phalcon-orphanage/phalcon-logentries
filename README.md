# Phalcon Logentries

[![Build Status](https://img.shields.io/travis/sergeyklay/phalcon-logentries/master.svg?style=flat-square)](https://travis-ci.org/sergeyklay/phalcon-logentries)
[![Software License](https://img.shields.io/badge/license-BSD--3-brightgreen.svg?style=flat-square)](LICENSE.md)

Phalcon library to connect and make log entries using [Logentries][1].
You can adapt it to your own needs or improve it if you want.

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

## Setup

When you have made your account on Logentries. Log in and create a new host with a name that best represents your app.
Then, click on your new host and inside that, create a new log file with a name that represents what you are logging,
example: `myerrors`. Bear in mind, these names are purely for your own benefit. Under source type, select Token TCP
and click Register. You will notice a token appear beside the name of the log, these is a unique identifier that the logging
library will use to access that logfile. You can copy and paste this now or later.

Then create adapter instance:

```php
$di->set('logger', function() {
    $logger = new \Phalcon\Logger\Adapter\Logentries([
        'token' => getenv('LOGENTRIES_TOKEN'),
        // optional parameters
    ]);
});
```

`LOGENTRIES_TOKEN` is the token we copied earlier from the Logentries UI.
It associates that logger with the log file on Logentries.

## Tests

Phosphorum use [Codeception][4] unit test.

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

More details about Console Commands see [here][6].

## License

Phalcon Logentries is open-sourced software licensed under the [New BSD License][7].
© Phalcon Framework Team and contributors

[1]: https://logentries.com/
[2]: https://getcomposer.org/
[3]: https://github.com/phalcon/cphalcon/releases
[4]: http://codeception.com/
[5]: https://github.com/squizlabs/PHP_CodeSniffer
[6]: http://codeception.com/docs/reference/Commands
[7]: https://github.com/phalcon/forum/blob/master/docs/LICENSE.md
