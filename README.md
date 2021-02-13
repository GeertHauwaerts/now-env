## PHP NowEnv

Load the `now.json` variables into your development environment.

With the help of this package, you can easily use your Now environment variables
for the use in development.

If you're already using a `now.json` file, the `env` sub property will be assigned
to `$_ENV`, `$_SERVER`, and `getenv()` automatically.

This package does absolutely nothing when running in a Now instance.

[![Build Status](https://travis-ci.org/GeertHauwaerts/now-env.svg?branch=master)](https://travis-ci.org/GeertHauwaerts/now-env)

## Installation

Use composer to add the library:

```shell
composer require geerthauwaerts/now-env
```

## Usage

Add the `env` property to your `now.json` file and load the variables in your
application with:

```php
use NowEnv\NowEnv;

$nowenv = new NowEnv(__DIR__);
$nowenv->load();
```

Optionally you can pass in a filename as the second parameter, if you would like to use something other than `now.json`.

```php
use NowEnv\NowEnv;

$nowenv = new NowEnv(__DIR__, 'now-secrets.json');
$nowenv->load();
```

All of the defined variables are now accessible with the `getenv` method, and are
available in the `$_ENV` and `$_SERVER` super-globals.

```php
$example = getenv('EXAMPLE');
$example = $_ENV['EXAMPLE'];
$example = $_SERVER['EXAMPLE'];
```

### Immutability

By default, NowEnv will NOT overwrite existing environment variables that are
already set in the environment.

If you want NowEnv to overwrite existing environment variables, use `overload()`
instead of `load()`:

```php
use NowEnv\NowEnv;

$nowenv = new NowEnv(__DIR__);
$nowenv->overload();
```

### Requiring variables to be set

You can require specific environment variables to be defined by passing a single string:

```php
$nowenv->required('EXAMPLE');
```

Or an array of strings:

```php
$nowenv->required(['EXAMPLE1', 'EXAMPLE2', 'EXAMPLE3']);
```

If any environment variables vars are missing, NowEnv will throw a `RuntimeException`
like this:

```
One or more environment variables failed assertions: EXAMPLE is missing
```

### Empty variables

Beyond simply requiring a variable to be set, you might also need to ensure the
variable is not empty:

```php
$nowenv->required('EXAMPLE')->notEmpty();
```

If the environment variable is empty, you'd get an Exception:

```
One or more environment variables failed assertions: EXAMPLE is empty
```

### Integer variables

You might also need to ensure that the variable is of an integer value. You may do the following:

```php
$nowenv->required('EXAMPLE')->isInteger();
```

If the environment variable is not an integer, you'd get an Exception:

```
One or more environment variables failed assertions: EXAMPLE is not an integer
```

## Boolean variables

You may need to ensure a variable is in the form of a boolean. You may do the following:

```php
$nowenv->required('EXAMPLE')->isBoolean();
```

If the environment variable is not a boolean, you'd get an Exception:

```
One or more environment variables failed assertions: EXAMPLE is not a boolean
```

### Allowed Values

It is also possible to define a set of values that your environment variable
should be. This is especially useful in situations where only a handful of
options or drivers are actually supported by your code:

```php
$nowenv->required('EXAMPLE')->allowedValues(['VALUE1', 'VALUE2']);
```

If the environment variable wasn't in this list of allowed values, you'd get a
similar Exception:

```
One or more environment variables failed assertions: EXAMPLE is not an allowed value
```

## Collaboration

The GitHub repository is used to keep track of all the bugs and feature
requests; I prefer to work exclusively via GitHib and Twitter.

If you have a patch to contribute:

  * Fork this repository on GitHub.
  * Create a feature branch for your set of patches.
  * Commit your changes to Git and push them to GitHub.
  * Submit a pull request.

Shout to [@GeertHauwaerts](https://twitter.com/GeertHauwaerts) on Twitter at
any time :)

## Donations

If you like this project and you want to support the development, please consider to [donate](https://commerce.coinbase.com/checkout/45c6916d-19ae-40c9-8ef7-7fb7ad30f8e2); all donations are greatly appreciated.

* **[Coinbase Commerce](https://commerce.coinbase.com/checkout/45c6916d-19ae-40c9-8ef7-7fb7ad30f8e2)**: *BTC, BCH, DAI, ETH, LTC, USDC*
* **BTC**: *bc1q654z85zv6sujsjqk750sf4j4eahcckdtq0cqrp*
* **ETH**: *0x4d38b4EB5b0726Dc6bd5770F69348e7472954b41*
* **LTC**: *MBEaP6e4zwro6oNP54yjfC29fVqZ881wdF*
* **DOGE**: *D8LypNzP6GayEBWUKCw3KVc7gwbGBaXynT*
