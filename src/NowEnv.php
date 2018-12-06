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

use NowEnv\Exception\InvalidPathException;

/**
 * The NowEnv class.
 *
 * @category  NowEnv
 * @package   NowEnv
 * @author    Geert Hauwaerts <geert@hauwaerts.be>
 * @author    Vance Lucas <vance@vancelucas.com>
 * @copyright 2018 Geert Hauwaerts, Vance Lucas
 * @license   BSD 3-Clause License
 * @version   Release: @package_version@
 * @link      https://github.com/GeertHauwaerts/now-env now-env
 */
class NowEnv
{
    /**
     * The file path.
     *
     * @var string
     */
    protected $filePath;

    /**
     * The Loader instance.
     *
     * @var \NowEnv\Loader|null
     */
    protected $loader;

    /**
     * Create a new NowEnv instance.
     *
     * @param string $path The file path.
     * @param string $file The file name.
     *
     * @return void
     */
    public function __construct($path, $file = 'now.json')
    {
        $this->filePath = $this->getFilePath($path, $file);
        $this->loader = new Loader($this->filePath, true);
    }

    /**
     * Load the environment file in the given directory.
     *
     * @return array
     */
    public function load()
    {
        return $this->loadData();
    }

    /**
     * Load the environment file in the given directory, suppress
     * InvalidPathException.
     *
     * @return array
     */
    public function safeLoad()
    {
        try {
            return $this->loadData();
        } catch (InvalidPathException $e) {
            return [];
        }
    }

    /**
     * Load the environment file in the given directory, overwrite existing
     * environment variables values.
     *
     * @return array
     */
    public function overload()
    {
        return $this->loadData(true);
    }

    /**
     * Return the full path to the file.
     *
     * @param string $path The file path.
     * @param string $file The file name.
     *
     * @return string
     */
    protected function getFilePath($path, $file)
    {
        if (!is_string($file)) {
            $file = 'now.json';
        }

        return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * Load the data.
     *
     * @param bool $overload Overwrite existing environment variables values.
     *
     * @return array
     */
    protected function loadData($overload = false)
    {
        if ($this->loader->getEnvironmentVariable('NOW_REGION') !== null) {
            return [];
        }

        return $this->loader->setImmutable(!$overload)->load();
    }

    /**
     * Required ensures that the specified variables exist, and returns a new
     * validator object.
     *
     * @param string|string[] $variable The required variables.
     *
     * @return \NowEnv\Validator
     */
    public function required($variable)
    {
        return new Validator((array) $variable, $this->loader);
    }

    /**
     * Get the list of environment variables declared inside the `now.json` file.
     *
     * @return array
     */
    public function getEnvironmentVariableNames()
    {
        return $this->loader->variableNames;
    }
}
