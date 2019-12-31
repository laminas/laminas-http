<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Upgrade;

class UpgradeTest extends \PHPUnit_Framework_TestCase
{

    public function testUpgradeFromStringCreatesValidUpgradeHeader()
    {
        $upgradeHeader = Upgrade::fromString('Upgrade: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $upgradeHeader);
        $this->assertInstanceOf('Laminas\Http\Header\Upgrade', $upgradeHeader);
    }

    public function testUpgradeGetFieldNameReturnsHeaderName()
    {
        $upgradeHeader = new Upgrade();
        $this->assertEquals('Upgrade', $upgradeHeader->getFieldName());
    }

    public function testUpgradeGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Upgrade needs to be completed');

        $upgradeHeader = new Upgrade();
        $this->assertEquals('xxx', $upgradeHeader->getFieldValue());
    }

    public function testUpgradeToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Upgrade needs to be completed');

        $upgradeHeader = new Upgrade();

        // @todo set some values, then test output
        $this->assertEmpty('Upgrade: xxx', $upgradeHeader->toString());
    }

    /** Implmentation specific tests here */

}

