<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Etag;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use PHPUnit\Framework\TestCase;

class EtagTest extends TestCase
{
    public function testEtagFromStringCreatesValidEtagHeader()
    {
        $etagHeader = Etag::fromString('Etag: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $etagHeader);
        $this->assertInstanceOf(Etag::class, $etagHeader);
    }

    public function testEtagGetFieldNameReturnsHeaderName()
    {
        $etagHeader = new Etag();
        $this->assertEquals('Etag', $etagHeader->getFieldName());
    }

    public function testEtagGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Etag needs to be completed');

        $etagHeader = new Etag();
        $this->assertEquals('xxx', $etagHeader->getFieldValue());
    }

    public function testEtagToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Etag needs to be completed');

        $etagHeader = new Etag();

        // @todo set some values, then test output
        $this->assertEmpty('Etag: xxx', $etagHeader->toString());
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
        Etag::fromString("Etag: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     *
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new Etag("xxx\r\n\r\nevilContent");
    }
}
