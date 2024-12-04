<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Allow;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class AllowTest extends TestCase
{
    public function testAllowFromStringCreatesValidAllowHeader(): void
    {
        $allowHeader = Allow::fromString('Allow: GET, POST, PUT');
        $this->assertInstanceOf(HeaderInterface::class, $allowHeader);
        $this->assertInstanceOf(Allow::class, $allowHeader);
        $this->assertEquals(['GET', 'POST', 'PUT'], $allowHeader->getAllowedMethods());
    }

    public function testAllowFromStringSupportsExtensionMethods(): void
    {
        $allowHeader = Allow::fromString('Allow: GET, POST, PROCREATE');
        $this->assertTrue($allowHeader->isAllowedMethod('PROCREATE'));
    }

    public function testAllowFromStringWithNonPostMethod(): void
    {
        $allowHeader = Allow::fromString('Allow: GET');
        $this->assertEquals('GET', $allowHeader->getFieldValue());
    }

    public function testAllowGetFieldNameReturnsHeaderName(): void
    {
        $allowHeader = new Allow();
        $this->assertEquals('Allow', $allowHeader->getFieldName());
    }

    public function testAllowListAllDefinedMethods(): void
    {
        $methods     = [
            'OPTIONS' => false,
            'GET'     => true,
            'HEAD'    => false,
            'POST'    => true,
            'PUT'     => false,
            'DELETE'  => false,
            'TRACE'   => false,
            'CONNECT' => false,
            'PATCH'   => false,
        ];
        $allowHeader = new Allow();
        $this->assertEquals($methods, $allowHeader->getAllMethods());
    }

    public function testAllowGetDefaultAllowedMethods(): void
    {
        $allowHeader = new Allow();
        $this->assertEquals(['GET', 'POST'], $allowHeader->getAllowedMethods());
    }

    public function testAllowGetFieldValueReturnsProperValue(): void
    {
        $allowHeader = new Allow();
        $allowHeader->allowMethods(['GET', 'POST', 'TRACE']);
        $this->assertEquals('GET, POST, TRACE', $allowHeader->getFieldValue());
    }

    public function testAllowToStringReturnsHeaderFormattedString(): void
    {
        $allowHeader = new Allow();
        $allowHeader->allowMethods(['GET', 'POST', 'TRACE']);
        $this->assertEquals('Allow: GET, POST, TRACE', $allowHeader->toString());
    }

    public function testAllowChecksAllowedMethod(): void
    {
        $allowHeader = new Allow();
        $allowHeader->allowMethods(['GET', 'POST', 'TRACE']);
        $this->assertTrue($allowHeader->isAllowedMethod('TRACE'));
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaFromString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid header value detected');

        Allow::fromString("Allow: GET\r\n\r\nevilContent");
    }

    /** @psalm-return array<string, array{0: string|string[]}> */
    public static function injectionMethods(): array
    {
        return [
            'string' => ["\rG\r\nE\nT"],
            'array'  => [["\rG\r\nE\nT"]],
        ];
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[DataProvider('injectionMethods')]
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaAllowMethods(array|string $methods): void
    {
        $header = new Allow();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('valid method');

        $header->allowMethods($methods);
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[DataProvider('injectionMethods')]
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaDisallowMethods(array|string $methods): void
    {
        $header = new Allow();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('valid method');

        $header->disallowMethods($methods);
    }
}
