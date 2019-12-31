<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\TE;

class TETest extends \PHPUnit_Framework_TestCase
{
    public function testTEFromStringCreatesValidTEHeader()
    {
        $tEHeader = TE::fromString('TE: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $tEHeader);
        $this->assertInstanceOf('Laminas\Http\Header\TE', $tEHeader);
    }

    public function testTEGetFieldNameReturnsHeaderName()
    {
        $tEHeader = new TE();
        $this->assertEquals('TE', $tEHeader->getFieldName());
    }

    public function testTEGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('TE needs to be completed');

        $tEHeader = new TE();
        $this->assertEquals('xxx', $tEHeader->getFieldValue());
    }

    public function testTEToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('TE needs to be completed');

        $tEHeader = new TE();

        // @todo set some values, then test output
        $this->assertEmpty('TE: xxx', $tEHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = TE::fromString("TE: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = new TE("xxx\r\n\r\nevilContent");
    }
}
