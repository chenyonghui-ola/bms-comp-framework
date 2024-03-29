<?php

namespace Tests;

use Phalcon\Di;
use Phalcon\Test\UnitTestCase as PhalconTestCase;
use PHPUnit\Framework\TestCase as UnitTestCase;

abstract class TestCase extends UnitTestCase
{
    /**
     * @var bool
     */
    // private $_loaded = false;

    public function setUp()
    {
        parent::setUp();

        // Load any additional services that might be required during testing
        // $di = Di::getDefault();

        // // Get any DI components here. If you have a config, be sure to pass it to the parent

        // $this->setDi($di);

        // $this->_loaded = true;
    }

    /**
     * Check if the test case is setup properly
     *
     * @throws \PHPUnit_Framework_IncompleteTestError;
     */
    public function __destruct()
    {
        // if (!$this->_loaded) {
        //     throw new \PHPUnit_Framework_IncompleteTestError(
        //         "Please run parent::setUp()."
        //     );
        // }
    }
}
