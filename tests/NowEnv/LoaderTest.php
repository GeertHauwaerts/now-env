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

use NowEnv\Loader;
use PHPUnit\Framework\TestCase;

/**
 * The LoaderTest class.
 *
 * @category  TestCase
 * @package   LoaderTest
 * @author    Geert Hauwaerts <geert@hauwaerts.be>
 * @author    Vance Lucas <vance@vancelucas.com>
 * @copyright 2018 Geert Hauwaerts, Vance Lucas
 * @license   BSD 3-Clause License
 * @version   Release: @package_version@
 * @link      https://github.com/GeertHauwaerts/now-env now-env
 */
class LoaderTest extends TestCase
{
    /**
     * The immutable Loader.
     *
     * @var \NowEnv\Loader
     */
    private $_immutableLoader;

    /**
     * The mutable Loader.
     *
     * @var \NowEnv\Loader
     */
    private $_mutableLoader;

    /**
     * The key/value for testing.
     *
     * @var array
     */
    protected $keyVal;

    /**
     * Setup the PHPUnit framework.
     *
     * @return void
     */
    public function setUp()
    {
        $folder = dirname(__DIR__) . '/fixtures';

        $this->keyVal(true);
        $this->_mutableLoader = new Loader($folder);
        $this->_immutableLoader = new Loader($folder, true);
    }

    /**
     * Generate a new key/value pair or return the previous one.
     *
     * @param bool $reset If true, a new pair will be generated. If false,
     *                    the last returned pair will be returned.
     *
     * @return array
     */
    protected function keyVal($reset = false)
    {
        if (!isset($this->keyVal) || $reset) {
            $this->keyVal = [uniqid() => uniqid()];
        }

        return $this->keyVal;
    }

    /**
     * Return the key from keyVal(), without reset.
     *
     * @return string
     */
    protected function key()
    {
        $value = $this->keyVal();
        return key($value);
    }

    /**
     * Return the value from keyVal(), without reset.
     *
     * @return string
     */
    protected function value()
    {
        $value = $this->keyVal();
        return reset($value);
    }

    /**
     * Test if a mutable loader can be changed into immutable.
     *
     * @return void
     */
    public function testMutableLoaderSetUnsetImmutable()
    {
        $immutable = $this->_mutableLoader->getImmutable();

        $this->_mutableLoader->setImmutable(!$immutable);
        $this->assertSame(!$immutable, $this->_mutableLoader->getImmutable());

        $this->_mutableLoader->setImmutable($immutable);
        $this->assertSame($immutable, $this->_mutableLoader->getImmutable());
    }

    /**
     * Test if a mutable variable can be set and cleared.
     *
     * @return void
     */
    public function testMutableLoaderClearsEnvironmentVars()
    {
        $this->_mutableLoader->setEnvironmentVariable($this->key(), $this->value());
        $this->_mutableLoader->clearEnvironmentVariable($this->key());

        $this->assertSame(
            null,
            $this->_mutableLoader->getEnvironmentVariable($this->key())
        );

        $this->assertSame(false, getenv($this->key()));
        $this->assertSame(false, isset($_ENV[$this->key()]));
        $this->assertSame(false, isset($_SERVER[$this->key()]));

        $this->assertTrue(is_array($this->_mutableLoader->variableNames));
        $this->assertFalse(empty($this->_mutableLoader->variableNames));

    }

    /**
     * Test if an immutable loader can be changed into mutable.
     *
     * @return void
     */
    public function testImmutableLoaderSetUnsetImmutable()
    {
        $immutable = $this->_immutableLoader->getImmutable();

        $this->_immutableLoader->setImmutable(!$immutable);
        $this->assertSame(!$immutable, $this->_immutableLoader->getImmutable());

        $this->_immutableLoader->setImmutable($immutable);
        $this->assertSame($immutable, $this->_immutableLoader->getImmutable());
    }

    /**
     * Test if an immutable is in fact immutable.
     *
     * @return void
     */
    public function testImmutableLoaderCannotClearEnvironmentVars()
    {
        $this->_immutableLoader->setEnvironmentVariable(
            $this->key(),
            $this->value()
        );

        $this->_immutableLoader->clearEnvironmentVariable($this->key());

        $this->assertSame(
            $this->value(),
            $this->_immutableLoader->getEnvironmentVariable($this->key())
        );

        $this->assertSame($this->value(), getenv($this->key()));
        $this->assertSame(true, isset($_ENV[$this->key()]));
        $this->assertSame(true, isset($_SERVER[$this->key()]));

        $this->assertTrue(is_array($this->_immutableLoader->variableNames));
        $this->assertFalse(empty($this->_immutableLoader->variableNames));
    }
}
