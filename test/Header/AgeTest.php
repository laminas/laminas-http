<?php

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Age;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use PHPUnit\Framework\TestCase;

class AgeTest extends TestCase
{
    public function testAgeFromStringCreatesValidAgeHeader()
    {
        $ageHeader = Age::fromString('Age: 12');
        $this->assertInstanceOf(HeaderInterface::class, $ageHeader);
        $this->assertInstanceOf(Age::class, $ageHeader);
        $this->assertEquals('12', $ageHeader->getDeltaSeconds());
    }

    public function testAgeGetFieldNameReturnsHeaderName()
    {
        $ageHeader = new Age();
        $this->assertEquals('Age', $ageHeader->getFieldName());
    }

    public function testAgeGetFieldValueReturnsProperValue()
    {
        $ageHeader = new Age();
        $ageHeader->setDeltaSeconds('12');
        $this->assertEquals('12', $ageHeader->getFieldValue());
    }

    public function testAgeToStringReturnsHeaderFormattedString()
    {
        $ageHeader = new Age();
        $ageHeader->setDeltaSeconds('12');
        $this->assertEquals('Age: 12', $ageHeader->toString());
    }

    public function testAgeCorrectsDeltaSecondsOverflow()
    {
        $ageHeader = new Age();
        $ageHeader->setDeltaSeconds(PHP_INT_MAX);
        $this->assertEquals('Age: 2147483648', $ageHeader->toString());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->expectException(InvalidArgumentException::class);
        $header = Age::fromString("Age: 100\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        $header = new Age("100\r\n\r\nevilContent");
    }
}
