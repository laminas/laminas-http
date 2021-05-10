<?php

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\AcceptEncoding;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use PHPUnit\Framework\TestCase;

class AcceptEncodingTest extends TestCase
{
    public function testAcceptEncodingFromStringCreatesValidAcceptEncodingHeader()
    {
        $acceptEncodingHeader = AcceptEncoding::fromString('Accept-Encoding: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $acceptEncodingHeader);
        $this->assertInstanceOf(AcceptEncoding::class, $acceptEncodingHeader);
    }

    public function testAcceptEncodingGetFieldNameReturnsHeaderName()
    {
        $acceptEncodingHeader = new AcceptEncoding();
        $this->assertEquals('Accept-Encoding', $acceptEncodingHeader->getFieldName());
    }

    public function testAcceptEncodingGetFieldValueReturnsProperValue()
    {
        $acceptEncodingHeader = AcceptEncoding::fromString('Accept-Encoding: xxx');
        $this->assertEquals('xxx', $acceptEncodingHeader->getFieldValue());
    }

    public function testAcceptEncodingGetFieldValueReturnsProperValueWithTrailingSemicolon()
    {
        $acceptEncodingHeader = AcceptEncoding::fromString('Accept-Encoding: xxx;');
        $this->assertEquals('xxx', $acceptEncodingHeader->getFieldValue());
    }

    public function testAcceptEncodingGetFieldValueReturnsProperValueWithSemicolonWithoutEqualSign()
    {
        $acceptEncodingHeader = AcceptEncoding::fromString('Accept-Encoding: xxx;yyy');
        $this->assertEquals('xxx;yyy', $acceptEncodingHeader->getFieldValue());
    }

    public function testAcceptEncodingToStringReturnsHeaderFormattedString()
    {
        $acceptEncodingHeader = new AcceptEncoding();
        $acceptEncodingHeader->addEncoding('compress', 0.5)
                             ->addEncoding('gzip', 1);

        $this->assertEquals('Accept-Encoding: compress;q=0.5, gzip', $acceptEncodingHeader->toString());
    }

    /** Implementation specific tests here */

    public function testCanParseCommaSeparatedValues()
    {
        $header = AcceptEncoding::fromString('Accept-Encoding: compress;q=0.5,gzip');
        $this->assertTrue($header->hasEncoding('compress'));
        $this->assertTrue($header->hasEncoding('gzip'));
    }

    public function testPrioritizesValuesBasedOnQParameter()
    {
        $header   = AcceptEncoding::fromString('Accept-Encoding: compress;q=0.8,gzip,*;q=0.4');
        $expected = [
            'gzip',
            'compress',
            '*',
        ];

        $test = [];
        foreach ($header->getPrioritized() as $type) {
            $this->assertEquals(array_shift($expected), $type->getEncoding());
        }
    }

    public function testWildcharEncoder()
    {
        $acceptHeader = new AcceptEncoding();
        $acceptHeader->addEncoding('compress', 0.8)
                     ->addEncoding('*', 0.4);

        $this->assertTrue($acceptHeader->hasEncoding('compress'));
        $this->assertTrue($acceptHeader->hasEncoding('gzip'));
        $this->assertEquals('Accept-Encoding: compress;q=0.8, *;q=0.4', $acceptHeader->toString());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->expectException(InvalidArgumentException::class);
        $header = AcceptEncoding::fromString("Accept-Encoding: compress\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaSetters()
    {
        $header = new AcceptEncoding();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('valid type');

        $header->addEncoding("\nc\rom\r\npress");
    }
}
