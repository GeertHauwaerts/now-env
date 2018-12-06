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
 * The ValidatorTest class.
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
class ValidatorTest extends TestCase
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
     * List of boolean values.
     *
     * @return array
     */
    public function validBooleanValuesDataProvider()
    {
        return [
            ['BOOL_TRUE'],
            ['BOOL_FALSE'],
            ['NUMBER_ZERO'],
            ['NUMBER_ONE']
        ];
    }

    /**
     * Test if boolean values are matched.
     *
     * @param array $boolean List of valid booleans.
     *
     * @dataProvider validBooleanValuesDataProvider
     *
     * @return void
     */
    public function testCanValidateBooleans($boolean)
    {
        $nowenv = new NowEnv($this->_fixturesFolder, 'now-booleans.json');
        $nowenv->load();
        $nowenv->required($boolean)->isBoolean();

        $this->assertTrue(true);
    }

    /**
     * List of non-boolean values.
     *
     * @return array
     */
    public function invalidBooleanValuesDataProvider()
    {
        return [
            ['NULL'],
            ['STRING_EMPTY'],
            ['STRING_TEXT']
        ];
    }

    /**
     * Test if non-boolean values are rejected.
     *
     * @param array $boolean List of invalid booleans.
     *
     * @dataProvider      invalidBooleanValuesDataProvider
     * @expectedException NowEnv\Exception\ValidationException
     *
     * @return void
     */
    public function testCanInvalidateNonBooleans($boolean)
    {
        $nowenv = new NowEnv($this->_fixturesFolder, 'now-booleans.json');
        $nowenv->load();
        $nowenv->required($boolean)->isBoolean();
    }
}
