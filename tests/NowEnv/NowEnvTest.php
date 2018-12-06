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

use NowEnv\NowEnv;
use PHPUnit\Framework\TestCase;

/**
 * The NowEnvTest class.
 *
 * @category  TestCase
 * @package   NowEnvTest
 * @author    Geert Hauwaerts <geert@hauwaerts.be>
 * @author    Vance Lucas <vance@vancelucas.com>
 * @copyright 2018 Geert Hauwaerts, Vance Lucas
 * @license   BSD 3-Clause License
 * @version   Release: @package_version@
 * @link      https://github.com/GeertHauwaerts/now-env now-env
 */
class NowEnvTest extends TestCase
{
    /**
     * The location of the test config files.
     *
     * @var string
     */
    private $_fixturesFolder;

    /**
     * Setup the PHPUnit framework.
     *
     * @return void
     */
    public function setUp()
    {
        $this->_fixturesFolder = dirname(__DIR__) . '/fixtures';
    }

    /**
     * Test if InvalidPathException is thrown.
     *
     * @expectedException        \NowEnv\Exception\InvalidPathException
     * @expectedExceptionMessage Unable to read the environment file at
     *
     * @return void
     */
    public function testNowEnvThrowsExceptionIfUnableToLoadFile()
    {
        $nowenv = new NowEnv(__DIR__);
        $nowenv->load();
    }

    /**
     * Test if `safeLoad()` is skipped when `now.json` does not exist.
     *
     * @return void
     */
    public function testNowEnvSkipsLoadingIfFileIsMissing()
    {
        $nowenv = new NowEnv(__DIR__);
        $this->assertEmpty($nowenv->safeLoad());
    }

    /**
     * Test if `load()` and `overload()` are skipped when running inside
     * a Now container.
     *
     * @return void
     */
    public function testNowEnvSkipsLoadingIfRunningOnNow()
    {
        putenv('NOW_REGION=bru1');

        $nowenv = new NowEnv($this->_fixturesFolder, 'now-normal.json');
        $this->assertEmpty($nowenv->load());
        $this->assertEmpty($nowenv->overload());

        putenv('NOW_REGION');
    }

    /**
     * Test if the variables are loaded.
     *
     * @return void
     */
    public function testNowEnvLoadsEnvironmentVars()
    {
        $nowenv = new NowEnv($this->_fixturesFolder, 'now-normal.json');
        $nowenv->load();

        $this->assertSame('Kitties go Meow', getenv('KITTIES'));
        $this->assertSame('Doggies go Woof', getenv('DOGGIES'));
        $this->assertSame('Hiding in the Woods', getenv('FOXES'));
        $this->assertEmpty(getenv('NULL'));
    }

    /**
     * Test if escaped variables are loaded..
     *
     * @return void
     */
    public function testQuotedNowEnvLoadsEnvironmentVars()
    {
        $nowenv = new NowEnv($this->_fixturesFolder, 'now-escaped.json');
        $nowenv->load();

        $this->assertSame(
            'Test some escaped characters like a quote (") or maybe a backslash' .
            ' (\\)',
            getenv('ESCAPED')
        );
    }

    /**
     * Test if the variables are loaded into $_SERVER.
     *
     * @return void
     */
    public function testNowEnvLoadsServerGlobals()
    {
        $nowenv = new NowEnv($this->_fixturesFolder, 'now-normal.json');
        $nowenv->load();

        $this->assertSame('Kitties go Meow', $_SERVER['KITTIES']);
        $this->assertSame('Doggies go Woof', $_SERVER['DOGGIES']);
        $this->assertSame('Hiding in the Woods', $_SERVER['FOXES']);
        $this->assertEmpty($_SERVER['NULL']);
    }

    /**
     * Test if the variables are loaded into $_ENV.
     *
     * @return void
     */
    public function testNowEnvLoadsEnvGlobals()
    {
        $nowenv = new NowEnv($this->_fixturesFolder, 'now-normal.json');
        $nowenv->load();

        $this->assertSame('Kitties go Meow', $_ENV['KITTIES']);
        $this->assertSame('Doggies go Woof', $_ENV['DOGGIES']);
        $this->assertSame('Hiding in the Woods', $_ENV['FOXES']);
        $this->assertEmpty($_ENV['NULL']);
    }

    /**
     * Test if a required variable from a string input is loaded.
     *
     * @depends testNowEnvLoadsEnvironmentVars
     * @depends testNowEnvLoadsServerGlobals
     * @depends testNowEnvLoadsEnvGlobals
     *
     * @return void
     */
    public function testNowEnvRequiredStringEnvironmentVars()
    {
        $nowenv = new NowEnv($this->_fixturesFolder, 'now-normal.json');
        $nowenv->load();
        $nowenv->required('KITTIES');

        $this->assertTrue(true);
    }

    /**
     * Test if a required variable from an array input is loaded.
     *
     * @depends testNowEnvLoadsEnvironmentVars
     * @depends testNowEnvLoadsServerGlobals
     * @depends testNowEnvLoadsEnvGlobals
     *
     * @return void
     */
    public function testNowEnvRequiredArrayEnvironmentVars()
    {
        $nowenv = new NowEnv($this->_fixturesFolder, 'now-normal.json');
        $nowenv->load();
        $nowenv->required(
            [
                'KITTIES',
                'DOGGIES',
                'FOXES'
            ]
        );

        $this->assertTrue(true);
    }

    /**
     * Test if a required variables matches a strict set of values.
     *
     * @depends testNowEnvLoadsEnvironmentVars
     * @depends testNowEnvLoadsServerGlobals
     * @depends testNowEnvLoadsEnvGlobals
     *
     * @return void
     */
    public function testNowEnvAllowedValues()
    {
        $nowenv = new NowEnv($this->_fixturesFolder, 'now-normal.json');
        $nowenv->load();
        $nowenv->required('KITTIES')->allowedValues(
            [
                'Kitties go Meow',
                'Kitties do not go Woof'
            ]
        );

        $this->assertTrue(true);
    }


    /**
     * Test if ValidationException is thrown for invalid values.
     *
     * phpcs:disable Generic.Files.LineLength
     *
     * @depends testNowEnvLoadsEnvironmentVars
     * @depends testNowEnvLoadsServerGlobals
     * @depends testNowEnvLoadsEnvGlobals
     *
     * @expectedException        \NowEnv\Exception\ValidationException
     * @expectedExceptionMessage One or more environment variables failed assertions: KITTIES is not an allowed value.
     *
     * @return void
     *
     * phpcs:enable Generic.Files.LineLength
     */
    public function testNowEnvProhibitedValues()
    {
        $nowenv = new NowEnv($this->_fixturesFolder, 'now-normal.json');
        $nowenv->load();
        $nowenv->required('KITTIES')->allowedValues(
            [
                'Doggies go Woof',
                'Hiding in the Woods'
            ]
        );
    }

    /**
     * Test if ValidationException is thrown for missing variables.
     *
     * phpcs:disable Generic.Files.LineLength
     *
     * @expectedException        \NowEnv\Exception\ValidationException
     * @expectedExceptionMessage One or more environment variables failed assertions: HORSES is missing, TURTLES is missing.
     *
     * @return void
     *
     * phpcs:enable Generic.Files.LineLength
     */
    public function testNowEnvRequiredThrowsRuntimeException()
    {
        $nowenv = new NowEnv($this->_fixturesFolder, 'now-normal.json');
        $nowenv->load();

        $this->assertFalse(getenv('HORSES'));
        $this->assertFalse(getenv('TURTLES'));

        $nowenv->required(
            [
                'HORSES',
                'TURTLES'
            ]
        );
    }

    /**
     * Test if the default file name is used.
     *
     * @return void
     */
    public function testNowEnvNullFileArgumentUsesDefault()
    {
        $nowenv = new NowEnv($this->_fixturesFolder, null);
        $nowenv->load();

        $this->assertSame('VALUE', getenv('NAME'));
    }

    /**
     * Test if existing variables are not overwritten.
     *
     * @return void
     */
    public function testNowEnvLoadDoesNotOverwriteEnv()
    {
        putenv('IMMUTABLE=true');

        $nowenv = new NowEnv($this->_fixturesFolder, 'now-immutable.json');
        $nowenv->load();

        $this->assertSame('true', getenv('IMMUTABLE'));
    }

    /**
     * Test `load()` after using `overload()`.
     *
     * @return void
     */
    public function testNowEnvLoadAfterOverload()
    {
        putenv('IMMUTABLE=true');
        $nowenv = new NowEnv($this->_fixturesFolder, 'now-immutable.json');
        $nowenv->overload();
        $this->assertSame('false', getenv('IMMUTABLE'));

        putenv('IMMUTABLE=true');
        $nowenv->load();
        $this->assertSame('true', getenv('IMMUTABLE'));
    }

    /**
     * Test `overload()` after using `load()`.
     *
     * @return void
     */
    public function testNowEnvOverloadAfterLoad()
    {
        putenv('IMMUTABLE=true');
        $nowenv = new NowEnv($this->_fixturesFolder, 'now-immutable.json');
        $nowenv->load();
        $this->assertSame('true', getenv('IMMUTABLE'));

        putenv('IMMUTABLE=true');
        $nowenv->overload();
        $this->assertSame('false', getenv('IMMUTABLE'));
    }

    /**
     * Test if special characters are loaded.
     *
     * @return void
     */
    public function testNowEnvAllowsSpecialCharacters()
    {
        $nowenv = new NowEnv($this->_fixturesFolder, 'now-specialchars.json');
        $nowenv->load();

        $this->assertSame('$a6^C7k%zs+e^.jvjXk', getenv('SPVAR1'));
        $this->assertSame('?BUty3koaV3%GA*hMAwH}B', getenv('SPVAR2'));
        $this->assertSame('22222:22#2^{', getenv('SPVAR4'));

        $this->assertSame(
            'jdgEB4{QgEC]HL))&GcXxokB+wqoN+j>xkV7K?m$r',
            getenv('SPVAR3')
        );
        $this->assertSame(
            'test some escaped characters like a quote " or maybe a backslash \\',
            getenv('SPVAR5')
        );
    }

    /**
     * Test if assertions are handled properly.
     *
     * @return void
     */
    public function testNowEnvAssertions()
    {
        $nowenv = new NowEnv($this->_fixturesFolder, 'now-assertions.json');
        $nowenv->load();

        $this->assertSame('VALUE', getenv('AVAR1'));
        $this->assertEmpty(getenv('AVAR2'));
        $this->assertSame('  ', getenv('AVAR3'));
        $this->assertSame('0', getenv('AVAR4'));

        $nowenv->required(
            [
                'AVAR1',
                'AVAR2',
                'AVAR3',
                'AVAR4'
            ]
        );

        $nowenv->required(
            [
                'AVAR1',
                'AVAR4'
            ]
        )->notEmpty();

        $nowenv->required(
            [
                'AVAR1',
                'AVAR4'
            ]
        )->notEmpty()->allowedValues(
            [
                '0',
                'VALUE'
            ]
        );

        $this->assertTrue(true);
    }

    /**
     * Test if ValidationException is thrown.
     *
     * phpcs:disable Generic.Files.LineLength
     *
     * @expectedException        \NowEnv\Exception\ValidationException
     * @expectedExceptionMessage One or more environment variables failed assertions: AVAR2 is empty.
     *
     * @return void
     *
     * phpcs:enabale Generic.Files.LineLength
     */
    public function testNowEnvEmptyThrowsRuntimeException()
    {
        $nowenv = new NowEnv($this->_fixturesFolder, 'now-assertions.json');
        $nowenv->load();

        $this->assertEmpty(getenv('AVAR2'));
        $nowenv->required('AVAR2')->notEmpty();
    }

    /**
     * Test validation without loading the variables.
     *
     * @expectedException        \NowEnv\Exception\ValidationException
     * @expectedExceptionMessage One or more environment variables failed assertions: HORSES is missing.
     *
     * @return void
     */
    public function testNowEnvValidateRequiredWithoutLoading()
    {
        $nowenv = new NowEnv($this->_fixturesFolder, 'now-normal.json');
        $nowenv->required('HORSES');
    }

    /**
     * Test if `required()` can be used without loading the file.
     *
     * @return void
     */
    public function testNowEnvRequiredCanBeUsedWithoutLoadingFile()
    {
        putenv('REQUIRED_VAR=1');

        $nowenv = new NowEnv($this->_fixturesFolder);
        $nowenv->required('REQUIRED_VAR')->notEmpty();

        $this->assertTrue(true);
    }

    /**
     * Test the `getEnvironmentVariablesList()` function call.
     *
     * @return void
     */
    public function testGetEnvironmentVariablesList()
    {
        $nowenv = new NowEnv($this->_fixturesFolder, 'now-normal.json');
        $nowenv->load();

        $this->assertTrue(is_array($nowenv->getEnvironmentVariableNames()));
        $this->assertSame(
            [
                'KITTIES',
                'DOGGIES',
                'FOXES',
                'NULL'
            ],
            $nowenv->getEnvironmentVariableNames()
        );
    }
}
