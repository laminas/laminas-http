<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderValue;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class HeaderValueTest extends TestCase
{
    /**
     * Data for filter value
     *
     * @psalm-return array<array-key, array{0: string, 1: string}>
     */
    public static function getFilterValues(): array
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

    #[DataProvider('getFilterValues')]
    #[Group('ZF2015-04')]
    public function testFiltersValuesPerRfc7230(string $value, string $expected): void
    {
        $this->assertEquals($expected, HeaderValue::filter($value));
    }

    /** @psalm-return array<array-key, array{0: string, 1: string}> */
    public static function validateValues(): array
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

    #[DataProvider('validateValues')]
    #[Group('ZF2015-04')]
    public function testValidatesValuesPerRfc7230(string $value, string $assertion): void
    {
        $this->{$assertion}(HeaderValue::isValid($value));
    }

    /** @psalm-return array<array-key, array{0: string}> */
    public static function assertValues(): array
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

    #[DataProvider('assertValues')]
    #[Group('ZF2015-04')]
    public function testAssertValidRaisesExceptionForInvalidValue(string $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        HeaderValue::assertValid($value);
    }
}
