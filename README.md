# Phalcon Logentries

[![Software License](https://img.shields.io/badge/license-BSD--3-brightgreen.svg?style=flat-square)](https://github.com/phalcon/phalcon-logentries/blob/master/LICENSE.txt)
[![Build Status](https://img.shields.io/travis/phalcon/phalcon-logentries/master.svg?style=flat-square)](https://travis-ci.org/phalcon/phalcon-logentries)
[![Total Downloads](https://img.shields.io/packagist/dt/phalcon/logentries.svg?style=flat-square)](https://packagist.org/packages/phalcon/logentries)

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
* PHP >= 5.5
* Latest stable [Phalcon Framework release][3] extension enabled

Development requirements:

* [Codeception][4]
* [PHP_CodeSniffer][5]

### Installation

Install composer in a common location or in your project:

```sh
$ curl -s http://getcomposer.org/installer | php
```

Create the composer.json file as follows:

```json
{
    "require": {
        "phalcon/logentries": "~1.2"
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
use Phalcon\Logger\Adapter\Logentries;

$di->set('logger', function() {
    $logger = new Logentries([
        'token' => getenv('LOGENTRIES_TOKEN'),
        // optional parameters
    ]);
    
    return $logger;
});
```

`LOGENTRIES_TOKEN` is the token we copied earlier from the Logentries UI.
It associates that logger with the log file on Logentries.

### Adding a Custom Host Name and Host ID sent in your PHP log events

To Set a custom host name that will appear in your PHP log events as Key / Value pairs
pass to the `Logentries::__constructor` the following parameters:

- **host_name_enabled**
- **host_name**
- **host_id**

For example:

```php
use Phalcon\Logger\Adapter\Logentries;

$di->set('logger', function() {
    $logger = new Logentries([
        'token'             => getenv('LOGENTRIES_TOKEN'),
        'host_name_enabled' => true,
        'host_name'         => 'Custom_host_name_here',
        'host_id'           => 'Custom_ID_here_12345'
    ]);

    return $logger;
});
```

The `host_name` param can be left as an empty string, and the Logentries component will automatically attempt to
assign a host name from your local host machine and use that as the custom host name.

To set a custom Host ID that will appear in your PHP log events as Key / Value pairs:
* Enter a value instead of the empty string in `host_id => ''`;
* If no `host_id` is set and the empty string is left unaltered, no Host ID or Key / Value pairing will appear in your PHP logs.

## Creating a Log

The example below shows how to create a log and add messages to it:

```php
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Logentries as LeAdapter;

$logger = new LeAdapter(['token' => 'ad43g-dfd34-df3ed-3d3d3']);

// These are the different log levels available:
$logger->critical('This is a critical message');
$logger->emergency('This is an emergency message');
$logger->debug('This is a debug message');
$logger->error('This is an error message');
$logger->info('This is an info message');
$logger->notice('This is a notice message');
$logger->warning('This is a warning message');
$logger->alert('This is an alert message');


// You can also use the log() method with a Logger constant:
$logger->log('This is another error message', Logger::ERROR);

// If no constant is given, DEBUG is assumed.
$logger->log('This is a message');

// Closes the logger
$logger->close();
```

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
[7]: https://github.com/phalcon/phalcon-logentries/blob/master/LICENSE.txt
