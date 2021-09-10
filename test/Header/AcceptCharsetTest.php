<?php

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\AcceptCharset;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use PHPUnit\Framework\TestCase;

use function array_shift;

class AcceptCharsetTest extends TestCase
{
    public function testAcceptCharsetFromStringCreatesValidAcceptCharsetHeader()
    {
        $acceptCharsetHeader = AcceptCharset::fromString('Accept-Charset: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $acceptCharsetHeader);
        $this->assertInstanceOf(AcceptCharset::class, $acceptCharsetHeader);
    }

    public function testAcceptCharsetGetFieldNameReturnsHeaderName()
    {
        $acceptCharsetHeader = new AcceptCharset();
        $this->assertEquals('Accept-Charset', $acceptCharsetHeader->getFieldName());
    }

    public function testAcceptCharsetGetFieldValueReturnsProperValue()
    {
        $acceptCharsetHeader = AcceptCharset::fromString('Accept-Charset: xxx');
        $this->assertEquals('xxx', $acceptCharsetHeader->getFieldValue());
    }

    public function testAcceptCharsetGetFieldValueReturnsProperValueWithTrailingSemicolon()
    {
        $acceptCharsetHeader = AcceptCharset::fromString('Accept-Charset: xxx;');
        $this->assertEquals('xxx', $acceptCharsetHeader->getFieldValue());
    }

    public function testAcceptCharsetGetFieldValueReturnsProperValueWithSemicolonWithoutEqualSign()
    {
        $acceptCharsetHeader = AcceptCharset::fromString('Accept-Charset: xxx;yyy');
        $this->assertEquals('xxx;yyy', $acceptCharsetHeader->getFieldValue());
    }

    public function testAcceptCharsetToStringReturnsHeaderFormattedString()
    {
        $acceptCharsetHeader = new AcceptCharset();
        $acceptCharsetHeader->addCharset('iso-8859-5', 0.8)
                            ->addCharset('unicode-1-1', 1);

        $this->assertEquals('Accept-Charset: iso-8859-5;q=0.8, unicode-1-1', $acceptCharsetHeader->toString());
    }

    // Implementation specific tests here

    // phpcs:ignore Squiz.Commenting.FunctionComment.WrongStyle
    public function testCanParseCommaSeparatedValues()
    {
        $header = AcceptCharset::fromString('Accept-Charset: iso-8859-5;q=0.8,unicode-1-1');
        $this->assertTrue($header->hasCharset('iso-8859-5'));
        $this->assertTrue($header->hasCharset('unicode-1-1'));
    }

    public function testPrioritizesValuesBasedOnQParameter()
    {
        $header   = AcceptCharset::fromString('Accept-Charset: iso-8859-5;q=0.8,unicode-1-1,*;q=0.4');
        $expected = [
            'unicode-1-1',
            'iso-8859-5',
            '*',
        ];

        foreach ($header->getPrioritized() as $type) {
            $this->assertEquals(array_shift($expected), $type->getCharset());
        }
    }

    public function testWildcharCharset()
    {
        $acceptHeader = new AcceptCharset();
        $acceptHeader->addCharset('iso-8859-5', 0.8)
                     ->addCharset('*', 0.4);

        $this->assertTrue($acceptHeader->hasCharset('iso-8859-5'));
        $this->assertTrue($acceptHeader->hasCharset('unicode-1-1'));
        $this->assertEquals('Accept-Charset: iso-8859-5;q=0.8, *;q=0.4', $acceptHeader->toString());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     *
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->expectException(InvalidArgumentException::class);
        AcceptCharset::fromString("Accept-Charset: iso-8859-5\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     *
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaSetters()
    {
        $header = new AcceptCharset();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('valid type');
        $header->addCharset("\niso\r-8859-\r\n5");
    }
}
