<?php

/**
 * Load the `now.json` variables into your development environment.
 *
 * With the help of this package, you can easily use your Now environment variables
 * for the use in development.
 *
 * If you're already using a `now.json` file, the `env` sub property will be assigned
 * to `$_ENV`, `$_SERVER`, and `getenv()` automatically.
 *
 * PHP version 7.2
 *
 * @category  GeertHauwaerts
 * @package   NowEnv
 * @author    Geert Hauwaerts <geert@hauwaerts.be>
 * @author    Vance Lucas <vance@vancelucas.com>
 * @copyright 2018 Geert Hauwaerts, Vance Lucas
 * @license   BSD 3-Clause License
 * @link      https://github.com/GeertHauwaerts/now-env now-env
 */

namespace NowEnv;

use NowEnv\Exception\InvalidFileException;
use NowEnv\Exception\InvalidPathException;

/**
 * The Loader class.
 *
 * @category  NowEnv
 * @package   Loader
 * @author    Geert Hauwaerts <geert@hauwaerts.be>
 * @author    Vance Lucas <vance@vancelucas.com>
 * @copyright 2018 Geert Hauwaerts, Vance Lucas
 * @license   BSD 3-Clause License
 * @version   Release: @package_version@
 * @link      https://github.com/GeertHauwaerts/now-env now-env
 */
class Loader
{
    /**
     * The file path.
     *
     * @var string
     */
    protected $filePath;

    /**
     * Are we immutable?
     *
     * @var bool
     */
    protected $immutable;

    /**
     * The list of environment variables declared inside the `now.json` file.
     *
     * @var array
     */
    public $variableNames = [];

    /**
     * Create a new loader instance.
     *
     * @param string $filePath  The file path.
     * @param bool   $immutable Are we immutable?
     *
     * @return void
     */
    public function __construct($filePath, $immutable = false)
    {
        $this->filePath = $filePath;
        $this->immutable = $immutable;
    }

    /**
     * Set immutable value.
     *
     * @param bool $immutable Are we immutable?
     *
     * @return $this
     */
    public function setImmutable($immutable = false)
    {
        $this->immutable = $immutable;
        return $this;
    }

    /**
     * Get immutable value.
     *
     * @return bool
     */
    public function getImmutable()
    {
        return $this->immutable;
    }

    /**
     * Load the `now.json` file in the given directory.
     *
     * @return void
     */
    public function load()
    {
        $this->ensureFileIsReadable();

        foreach ($this->parseJSON() as $name => $value) {
            $this->setEnvironmentVariable($name, $value);
        }
    }

    /**
     * Ensures the given filePath is readable.
     *
     * @throws \NowEnv\Exception\InvalidPathException
     *
     * @return void
     */
    protected function ensureFileIsReadable()
    {
        if (!is_readable($this->filePath) || !is_file($this->filePath)) {
            throw new InvalidPathException(
                sprintf(
                    'Unable to read the environment file at %s.',
                    $this->filePath
                )
            );
        }
    }

    /**
     * Parse the JSON information.
     *
     * @throws \NowEnv\Exception\InvalidFileException
     *
     * @return void
     */
    protected function parseJSON()
    {
        $json = @json_decode(file_get_contents($this->filePath), true);

        if ($json === null || !is_array($json['env'])) {
            throw new InvalidFileException(
                sprintf(
                    'Unable to find the environment variables in %s.',
                    $this->filePath
                )
            );
        }

        return $json['env'];
    }

    /**
     * Search the different places for environment variables and return the
     * first value found.
     *
     * @param string $name The variable name.
     *
     * @return string|null
     */
    public function getEnvironmentVariable($name)
    {
        switch (true) {
        case array_key_exists($name, $_ENV):
            return $_ENV[$name];
            break;
        case array_key_exists($name, $_SERVER):
            return $_SERVER[$name];
            break;
        default:
            $value = getenv($name);
            return $value === false ? null : $value;
            break;
        }
    }

    /**
     * Set an environment variable.
     *
     * @param string $name  The variable name.
     * @param string $value The variable value.
     *
     * @return void
     */
    public function setEnvironmentVariable($name, $value)
    {
        $this->variableNames[] = $name;

        if ($this->immutable && $this->getEnvironmentVariable($name) !== null) {
            return;
        }

        if (function_exists('apache_getenv')
            && function_exists('apache_setenv')
            && apache_getenv($name) !== false
        ) {
            apache_setenv($name, $value);
        }

        if (function_exists('putenv')) {
            putenv($name . '=' . $value);
        }

        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }

    /**
     * Clear an environment variable.
     *
     * @param string $name The variable name.
     *
     * @return void
     */
    public function clearEnvironmentVariable($name)
    {
        if ($this->immutable) {
            return;
        }

        if (function_exists('putenv')) {
            putenv($name);
        }

        unset($_ENV[$name], $_SERVER[$name]);
    }
}
