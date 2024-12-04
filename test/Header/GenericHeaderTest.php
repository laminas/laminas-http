<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\GenericHeader;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function ord;

class GenericHeaderTest extends TestCase
{
    #[DataProvider('validFieldNameChars')]
    public function testValidFieldName(string $name): void
    {
        try {
            new GenericHeader($name);
        } catch (InvalidArgumentException $e) {
            $this->assertEquals(
                $e->getMessage(),
                'Header name must be a valid RFC 7230 (section 3.2) field-name.'
            );
            $this->fail('Allowed char rejected: ' . ord($name)); // For easy debug
        }
    }

    #[DataProvider('invalidFieldNameChars')]
    public function testInvalidFieldName(string $name): void
    {
        try {
            new GenericHeader($name);
            $this->fail('Invalid char allowed: ' . ord($name)); // For easy debug
        } catch (InvalidArgumentException $e) {
            $this->assertEquals(
                $e->getMessage(),
                'Header name must be a valid RFC 7230 (section 3.2) field-name.'
            );
        }
    }

    #[Group('ZF#7295')]
    public function testDoesNotReplaceUnderscoresWithDashes(): void
    {
        $header = new GenericHeader('X_Foo_Bar');
        $this->assertEquals('X_Foo_Bar', $header->getFieldName());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaFromString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        GenericHeader::fromString("X_Foo_Bar: Bar\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new GenericHeader('X_Foo_Bar', "Bar\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testProtectsFromCRLFAttackViaSetFieldName(): void
    {
        $header = new GenericHeader();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('valid');
        $header->setFieldName("\rX-\r\nFoo-\nBar");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testProtectsFromCRLFAttackViaSetFieldValue(): void
    {
        $header = new GenericHeader();
        $this->expectException(InvalidArgumentException::class);
        $header->setFieldValue("\rSome\r\nCLRF\nAttack");
    }

    /**
     * Valid field name characters.
     *
     * @return string[]
     */
    public static function validFieldNameChars(): array
    {
        return [
            ['!'],
            ['#'],
            ['$'],
            ['%'],
            ['&'],
            ["'"],
            ['*'],
            ['+'],
            ['-'],
            ['.'],
            ['0'], // Begin numeric range
            ['9'], // End numeric range
            ['A'], // Begin upper range
            ['Z'], // End upper range
            ['^'],
            ['_'],
            ['`'],
            ['a'], // Begin lower range
            ['z'], // End lower range
            ['|'],
            ['~'],
        ];
    }

    /**
     * Invalid field name characters.
     *
     * @return string[]
     */
    public static function invalidFieldNameChars(): array
    {
        return [
            ["\x00"], // Min CTL invalid character range.
            ["\x1F"], // Max CTL invalid character range.
            ['('],
            [')'],
            ['<'],
            ['>'],
            ['@'],
            [','],
            [';'],
            [':'],
            ['\\'],
            ['"'],
            ['/'],
            ['['],
            [']'],
            ['?'],
            ['='],
            ['{'],
            ['}'],
            [' '],
            ["\t"],
            ["\x7F"], // DEL CTL invalid character.
        ];
    }
}
