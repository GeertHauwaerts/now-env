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

use NowEnv\Exception\InvalidCallbackException;
use NowEnv\Exception\ValidationException;

/**
 * The Validator class.
 *
 * @category  NowEnv
 * @package   Validator
 * @author    Geert Hauwaerts <geert@hauwaerts.be>
 * @author    Vance Lucas <vance@vancelucas.com>
 * @copyright 2018 Geert Hauwaerts, Vance Lucas
 * @license   BSD 3-Clause License
 * @version   Release: @package_version@
 * @link      https://github.com/GeertHauwaerts/now-env now-env
 */
class Validator
{
    /**
     * The variables to validate.
     *
     * @var array
     */
    protected $variables;

    /**
     * The Loader instance.
     *
     * @var \NowEnv\Loader
     */
    protected $loader;

    /**
     * Create a new Validator instance.
     *
     * @param array          $variables The required variables.
     * @param \NowEnv\Loader $loader    The Loader instance.
     *
     * @return void
     */
    public function __construct(array $variables, Loader $loader)
    {
        $this->variables = $variables;
        $this->loader = $loader;

        $this->assertCallback(
            function ($value) {
                return $value !== null;
            },
            'is missing'
        );
    }

    /**
     * Assert that each variable is not empty.
     *
     * @return \NowEnv\Validator
     */
    public function notEmpty()
    {
        return $this->assertCallback(
            function ($value) {
                return strlen(trim($value)) > 0;
            },
            'is empty'
        );
    }

    /**
     * Assert that each specified variable is an integer.
     *
     * @return \NowEnv\Validator
     */
    public function isInteger()
    {
        return $this->assertCallback(
            function ($value) {
                return ctype_digit($value);
            },
            'is not an integer'
        );
    }

    /**
     * Assert that each specified variable is a boolean.
     *
     * @return \NowEnv\Validator
     */
    public function isBoolean()
    {
        return $this->assertCallback(
            function ($value) {
                if ($value === '') {
                    return false;
                }

                return (filter_var(
                    $value,
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                ) !== null);
            },
            'is not a boolean'
        );
    }

    /**
     * Assert that each variable is amongst the given choices.
     *
     * @param string[] $choices The allowed choices.
     *
     * @return \NowEnv\Validator
     */
    public function allowedValues(array $choices)
    {
        return $this->assertCallback(
            function ($value) use ($choices) {
                return in_array($value, $choices);
            },
            'is not an allowed value'
        );
    }

    /**
     * Assert that the callback returns true for each variable.
     *
     * @param callable $callback The callback function.
     * @param string   $message  The failure message.
     *
     * @throws \NowEnv\Exception\InvalidCallbackException|\NowEnv\Exception\ValidationException
     *
     * @return \NowEnv\Validator
     */
    protected function assertCallback(
        $callback,
        $message = 'failed callback assertion'
    ) {
        if (!is_callable($callback)) {
            throw new InvalidCallbackException(
                'The provided callback must be callable.'
            );
        }

        $variablesFailingAssertion = [];

        foreach ($this->variables as $variableName) {
            $variableValue = $this->loader->getEnvironmentVariable($variableName);

            if (call_user_func($callback, $variableValue) === false) {
                $variablesFailingAssertion[] = $variableName . ' ' . $message;
            }
        }

        if (count($variablesFailingAssertion) > 0) {
            throw new ValidationException(
                sprintf(
                    'One or more environment variables failed assertions: %s.',
                    implode(', ', $variablesFailingAssertion)
                )
            );
        }

        return $this;
    }
}
