<?php

namespace Phalcon\Tests\Logger\Adapter;

use Codeception\Test\Unit;
use Phalcon\Logger\Adapter\Logentries;

/**
 * \Phalcon\Tests\Logger\Adapter\LogentriesTest
 * Tests the Phalcon\Logger\Adapter\Logentries component
 *
 * Phalcon Framework
 *
 * @copyright (c) 2011-2017 Phalcon Team
 * @link      https://www.phalconphp.com
 * @author    Serghei Iakovlev <serghei@phalconphp.com>
 * @package   Phalcon\Tests\Logger\Adapter
 *
 * The contents of this file are subject to the New BSD License that is
 * bundled with this package in the file docs/LICENSE.txt
 *
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world-wide-web, please send an email to license@phalconphp.com
 * so that we can send you a copy immediately.
 */
class LogentriesTest extends Unit
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
        if (!extension_loaded('phalcon')) {
            $this->markTestSkipped('The phalcon module is not available.');
        }
    }

    public function testShouldReturnLogentriesInstanceWithTokenParam()
    {
        $logger = new Logentries(['token' => 'ad43g-dfd34-df3ed-3d3d3']);
        $this->assertInstanceOf(Logentries::class, $logger);
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
