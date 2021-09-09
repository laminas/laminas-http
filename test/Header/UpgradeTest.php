<?php

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\Upgrade;
use PHPUnit\Framework\TestCase;

class UpgradeTest extends TestCase
{
    public function testUpgradeFromStringCreatesValidUpgradeHeader()
    {
        $upgradeHeader = Upgrade::fromString('Upgrade: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $upgradeHeader);
        $this->assertInstanceOf(Upgrade::class, $upgradeHeader);
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

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     *
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->expectException(InvalidArgumentException::class);
        Upgrade::fromString("Upgrade: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     *
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new Upgrade("xxx\r\n\r\nevilContent");
    }
}
