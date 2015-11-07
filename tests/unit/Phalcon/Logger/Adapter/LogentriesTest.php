<?php
/**
 * LogentriesTest.php
 * \Phalcon\Tests\Logger\Adapter\LogentriesTest
 *
 * Tests the Phalcon\Logger\Adapter\Logentries component
 *
 * Phalcon Framework
 *
 * @copyright (c) 2011-2015 Phalcon Team
 * @link      http://www.phalconphp.com
 * @author    Serghei Iakovlev <andres@phalconphp.com>
 *
 * The contents of this file are subject to the New BSD License that is
 * bundled with this package in the file docs/LICENSE.txt
 *
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world-wide-web, please send an email to license@phalconphp.com
 * so that we can send you a copy immediately.
 */

namespace Phalcon\Tests\Logger\Adapter;

use Codeception\TestCase\Test as TestCase;
use Phalcon\Logger\Adapter\Logentries;

/**
 * Logentries Test
 * @package Phalcon\Tests\Logger\Adapter
 */
class LogentriesTest extends TestCase
{
    /**
     * UnitTester Object
     * @var \UnitTester
     */
    protected $tester;

    /**
     * executed before each test
     */
    protected function _before()
    {
    }

    /**
     * executed after each test
     */
    protected function _after()
    {
    }

    protected function setUp()
    {
        if (!extension_loaded('phalcon')) {
            $this->markTestSkipped('The phalcon module is not available.');
        }

        parent::setUp();
    }

    public function testShouldReturnLogentriesInstanceWithTokenParam()
    {
        $logger = new Logentries(['token' => 'ad43g-dfd34-df3ed-3d3d3']);
        $this->assertInstanceOf('Phalcon\Logger\Adapter\Logentries', $logger);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Logentries Token was not provided
     */
    public function testShouldThrowsLoggerExceptionWhenTokenWasNotPassed()
    {
        new Logentries();
    }
}
