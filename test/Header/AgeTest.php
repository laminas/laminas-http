<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Age;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use const PHP_INT_MAX;

class AgeTest extends TestCase
{
    public function testAgeFromStringCreatesValidAgeHeader(): void
    {
        $ageHeader = Age::fromString('Age: 12');
        $this->assertInstanceOf(HeaderInterface::class, $ageHeader);
        $this->assertInstanceOf(Age::class, $ageHeader);
        $this->assertEquals('12', $ageHeader->getDeltaSeconds());
    }

    public function testAgeGetFieldNameReturnsHeaderName(): void
    {
        $ageHeader = new Age();
        $this->assertEquals('Age', $ageHeader->getFieldName());
    }

    public function testAgeGetFieldValueReturnsProperValue(): void
    {
        $ageHeader = new Age();
        $ageHeader->setDeltaSeconds('12');
        $this->assertEquals('12', $ageHeader->getFieldValue());
    }

    public function testAgeToStringReturnsHeaderFormattedString(): void
    {
        $ageHeader = new Age();
        $ageHeader->setDeltaSeconds('12');
        $this->assertEquals('Age: 12', $ageHeader->toString());
    }

    public function testAgeCorrectsDeltaSecondsOverflow(): void
    {
        $ageHeader = new Age();
        $ageHeader->setDeltaSeconds(PHP_INT_MAX);
        $this->assertEquals('Age: 2147483648', $ageHeader->toString());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaFromString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $header = Age::fromString("Age: 100\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $header = new Age("100\r\n\r\nevilContent");
    }
}
