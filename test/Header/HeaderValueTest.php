<?php

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderValue;
use PHPUnit\Framework\TestCase;

class HeaderValueTest extends TestCase
{
    /**
     * Data for filter value
     *
     * @psalm-return array<array-key, array{0: string, 1: string}>
     */
    public function getFilterValues(): array
    {
        return [
            ["This is a\n test", 'This is a test'],
            ["This is a\r test", 'This is a test'],
            ["This is a\n\r test", 'This is a test'],
            ["This is a\r\n  test", 'This is a  test'],
            ["This is a \r\ntest", 'This is a test'],
            ["This is a \r\n\n test", 'This is a  test'],
            ["This is a\n\n test", 'This is a test'],
            ["This is a\r\r test", 'This is a test'],
            ["This is a \r\r\n test", 'This is a  test'],
            ["This is a \r\n\r\ntest", 'This is a test'],
            ["This is a \r\n\n\r\n test", 'This is a  test'],
        ];
    }

    /**
     * @group ZF2015-04
     * @dataProvider getFilterValues
     * @param string $value
     * @param string $expected
     */
    public function testFiltersValuesPerRfc7230($value, $expected)
    {
        $this->assertEquals($expected, HeaderValue::filter($value));
    }

    /** @psalm-return array<array-key, array{0: string, 1: string}> */
    public function validateValues(): array
    {
        return [
            ["This is a\n test", 'assertFalse'],
            ["This is a\r test", 'assertFalse'],
            ["This is a\n\r test", 'assertFalse'],
            ["This is a\r\n  test", 'assertFalse'],
            ["This is a \r\ntest", 'assertFalse'],
            ["This is a \r\n\n test", 'assertFalse'],
            ["This is a\n\n test", 'assertFalse'],
            ["This is a\r\r test", 'assertFalse'],
            ["This is a \r\r\n test", 'assertFalse'],
            ["This is a \r\n\r\ntest", 'assertFalse'],
            ["This is a \r\n\n\r\n test", 'assertFalse'],
        ];
    }

    /**
     * @group ZF2015-04
     * @dataProvider validateValues
     * @param string $value
     * @param string $assertion
     */
    public function testValidatesValuesPerRfc7230($value, $assertion)
    {
        $this->{$assertion}(HeaderValue::isValid($value));
    }

    /** @psalm-return array<array-key, array{0: string}> */
    public function assertValues(): array
    {
        return [
            ["This is a\n test"],
            ["This is a\r test"],
            ["This is a\n\r test"],
            ["This is a \r\ntest"],
            ["This is a \r\n\n test"],
            ["This is a\n\n test"],
            ["This is a\r\r test"],
            ["This is a \r\r\n test"],
            ["This is a \r\n\r\ntest"],
            ["This is a \r\n\n\r\n test"],
        ];
    }

    /**
     * @group ZF2015-04
     * @dataProvider assertValues
     * @param string $value
     */
    public function testAssertValidRaisesExceptionForInvalidValue($value)
    {
        $this->expectException(InvalidArgumentException::class);
        HeaderValue::assertValid($value);
    }
}
