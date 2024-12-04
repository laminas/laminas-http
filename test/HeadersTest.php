<?php

declare(strict_types=1);

namespace LaminasTest\Http;

use ArrayIterator;
use Countable;
use Iterator;
use Laminas\Http\Exception\InvalidArgumentException;
use Laminas\Http\Exception\RuntimeException;
use Laminas\Http\Header;
use Laminas\Http\Header\GenericHeader;
use Laminas\Http\Header\GenericMultiHeader;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\HeaderLoader;
use Laminas\Http\Headers;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function implode;
use function sprintf;

class HeadersTest extends TestCase
{
    public function testHeadersImplementsProperClasses(): void
    {
        $headers = new Headers();
        $this->assertInstanceOf(Iterator::class, $headers);
        $this->assertInstanceOf(Countable::class, $headers);
    }

    public function testHeadersCanGetPluginClassLoader(): void
    {
        $headers = new Headers();
        $this->assertInstanceOf(HeaderLoader::class, $headers->getPluginClassLoader());
    }

    public function testHeadersFromStringFactoryCreatesSingleObject(): void
    {
        $headers = Headers::fromString('Fake: foo-bar');
        $this->assertEquals(1, $headers->count());

        $header = $headers->get('fake');
        $this->assertInstanceOf(GenericHeader::class, $header);
        $this->assertEquals('Fake', $header->getFieldName());
        $this->assertEquals('foo-bar', $header->getFieldValue());
    }

    public function testHeadersFromStringFactoryCreatesSingleObjectWithHeaderBreakLine(): void
    {
        $headers = Headers::fromString("Fake: foo-bar\r\n\r\n");
        $this->assertEquals(1, $headers->count());

        $header = $headers->get('fake');
        $this->assertInstanceOf(GenericHeader::class, $header);
        $this->assertEquals('Fake', $header->getFieldName());
        $this->assertEquals('foo-bar', $header->getFieldValue());
    }

    public function testHeadersFromStringFactoryCreatesSingleObjectWithHeaderFolding(): void
    {
        $headers = Headers::fromString("Fake: foo\r\n -bar");
        $this->assertEquals(1, $headers->count());

        $header = $headers->get('fake');
        $this->assertInstanceOf(GenericHeader::class, $header);
        $this->assertEquals('Fake', $header->getFieldName());
        $this->assertEquals('foo-bar', $header->getFieldValue());
    }

    public function testHeadersFromStringFactoryThrowsExceptionOnMalformedHeaderLine(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('does not match');
        Headers::fromString("Fake = foo-bar\r\n\r\n");
    }

    public function testHeadersFromStringFactoryCreatesMultipleObjects(): void
    {
        $headers = Headers::fromString("Fake: foo-bar\r\nAnother-Fake: boo-baz");
        $this->assertEquals(2, $headers->count());

        $header = $headers->get('fake');
        $this->assertInstanceOf(GenericHeader::class, $header);
        $this->assertEquals('Fake', $header->getFieldName());
        $this->assertEquals('foo-bar', $header->getFieldValue());

        $this->assertFalse($headers->get('anotherfake'));

        $header = $headers->get('another-fake');
        $this->assertInstanceOf(GenericHeader::class, $header);
        $this->assertEquals('Another-Fake', $header->getFieldName());
        $this->assertEquals('boo-baz', $header->getFieldValue());

        $this->assertSame($header, $headers->get('another fake'));
        $this->assertSame($header, $headers->get('another_fake'));
        $this->assertSame($header, $headers->get('another.fake'));
    }

    public function testHeadersFromStringMultiHeaderWillAggregateLazyLoadedHeaders(): void
    {
        $headers = new Headers();
        $pcl     = $headers->getPluginClassLoader();
        $pcl->registerPlugin('foo', GenericMultiHeader::class);
        $headers->addHeaderLine('foo: bar1,bar2,bar3');
        $headers->forceLoading();
        $this->assertEquals(3, $headers->count());
    }

    public function testHeadersHasAndGetWorkProperly(): void
    {
        $headers = new Headers();
        $headers->addHeaders([
            $f = new Header\GenericHeader('Foo', 'bar'),
            new Header\GenericHeader('Baz', 'baz'),
        ]);
        $this->assertFalse($headers->has('foobar'));
        $this->assertTrue($headers->has('foo'));
        $this->assertTrue($headers->has('Foo'));
        $this->assertSame($f, $headers->get('foo'));
    }

    public function testHeadersGetReturnsLastAddedHeaderValue(): void
    {
        $headers = new Headers();
        $headers->addHeaders([
            new Header\GenericHeader('Foo', 'bar'),
        ]);
        $headers->addHeader(new Header\GenericHeader('Foo', $value = 'baz'));

        $this->assertEquals($value, $headers->get('foo')->getFieldValue());
    }

    public function testHeadersAggregatesHeaderObjects(): void
    {
        $fakeHeader = new Header\GenericHeader('Fake', 'bar');
        $headers    = new Headers();
        $headers->addHeader($fakeHeader);
        $this->assertEquals(1, $headers->count());
        $this->assertSame($fakeHeader, $headers->get('Fake'));
    }

    public function testHeadersAggregatesHeaderThroughAddHeader(): void
    {
        $headers = new Headers();
        $headers->addHeader(new Header\GenericHeader('Fake', 'bar'));
        $this->assertEquals(1, $headers->count());
        $this->assertInstanceOf(GenericHeader::class, $headers->get('Fake'));
    }

    public function testHeadersAggregatesHeaderThroughAddHeaderLine(): void
    {
        $headers = new Headers();
        $headers->addHeaderLine('Fake', 'bar');
        $this->assertEquals(1, $headers->count());
        $this->assertInstanceOf(GenericHeader::class, $headers->get('Fake'));
    }

    public function testHeadersAddHeaderLineThrowsExceptionOnMissingFieldValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('without a field');
        $headers = new Headers();
        $headers->addHeaderLine('Foo');
    }

    public function testHeadersAggregatesHeadersThroughAddHeaders(): void
    {
        $headers = new Headers();
        $headers->addHeaders([new Header\GenericHeader('Foo', 'bar'), new Header\GenericHeader('Baz', 'baz')]);
        $this->assertEquals(2, $headers->count());
        $this->assertInstanceOf(GenericHeader::class, $headers->get('Foo'));
        $this->assertEquals('bar', $headers->get('foo')->getFieldValue());
        $this->assertEquals('baz', $headers->get('baz')->getFieldValue());

        $headers = new Headers();
        $headers->addHeaders(['Foo: bar', 'Baz: baz']);
        $this->assertEquals(2, $headers->count());
        $this->assertInstanceOf(GenericHeader::class, $headers->get('Foo'));
        $this->assertEquals('bar', $headers->get('foo')->getFieldValue());
        $this->assertEquals('baz', $headers->get('baz')->getFieldValue());

        $headers = new Headers();
        $headers->addHeaders([['Foo' => 'bar'], ['Baz' => 'baz']]);
        $this->assertEquals(2, $headers->count());
        $this->assertInstanceOf(GenericHeader::class, $headers->get('Foo'));
        $this->assertEquals('bar', $headers->get('foo')->getFieldValue());
        $this->assertEquals('baz', $headers->get('baz')->getFieldValue());

        $headers = new Headers();
        $headers->addHeaders([['Foo', 'bar'], ['Baz', 'baz']]);
        $this->assertEquals(2, $headers->count());
        $this->assertInstanceOf(GenericHeader::class, $headers->get('Foo'));
        $this->assertEquals('bar', $headers->get('foo')->getFieldValue());
        $this->assertEquals('baz', $headers->get('baz')->getFieldValue());

        $headers = new Headers();
        $headers->addHeaders(['Foo' => 'bar', 'Baz' => 'baz']);
        $this->assertEquals(2, $headers->count());
        $this->assertInstanceOf(GenericHeader::class, $headers->get('Foo'));
        $this->assertEquals('bar', $headers->get('foo')->getFieldValue());
        $this->assertEquals('baz', $headers->get('baz')->getFieldValue());
    }

    public function testHeadersAddHeadersThrowsExceptionOnInvalidArguments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected array or Trav');
        $headers = new Headers();
        $headers->addHeaders('foo');
    }

    public function testHeadersCanRemoveHeader(): void
    {
        $headers = new Headers();
        $headers->addHeaders(['Foo' => 'bar', 'Baz' => 'baz']);
        $header = $headers->get('foo');
        $this->assertEquals(2, $headers->count());
        $headers->removeHeader($header);
        $this->assertEquals(1, $headers->count());
        $this->assertFalse($headers->get('foo'));
    }

    public function testHeadersCanClearAllHeaders(): void
    {
        $headers = new Headers();
        $headers->addHeaders(['Foo' => 'bar', 'Baz' => 'baz']);
        $this->assertEquals(2, $headers->count());
        $headers->clearHeaders();
        $this->assertEquals(0, $headers->count());
    }

    public function testHeadersCanBeIterated(): void
    {
        $headers = new Headers();
        $headers->addHeaders(['Foo' => 'bar', 'Baz' => 'baz']);
        $iterations = 0;

        /** @var HeaderInterface $header */
        foreach ($headers as $index => $header) {
            $iterations++;
            $this->assertInstanceOf(GenericHeader::class, $header);
            switch ($index) {
                case 0:
                    $this->assertEquals('bar', $header->getFieldValue());
                    break;
                case 1:
                    $this->assertEquals('baz', $header->getFieldValue());
                    break;
                default:
                    $this->fail('Invalid index returned from iterator');
            }
        }
        $this->assertEquals(2, $iterations);
    }

    public function testHeadersCanBeCastToString(): void
    {
        $headers = new Headers();
        $headers->addHeaders(['Foo' => 'bar', 'Baz' => 'baz']);
        $this->assertEquals('Foo: bar' . "\r\n" . 'Baz: baz' . "\r\n", $headers->toString());
    }

    public function testHeadersCanBeCastToArray(): void
    {
        $headers = new Headers();
        $headers->addHeaders(['Foo' => 'bar', 'Baz' => 'baz']);
        $this->assertEquals(['Foo' => 'bar', 'Baz' => 'baz'], $headers->toArray());
    }

    public function testCastingToArrayReturnsMultiHeadersAsArrays(): void
    {
        $headers = new Headers();
        $cookie1 = new Header\SetCookie('foo', 'bar');
        $cookie2 = new Header\SetCookie('bar', 'baz');
        $headers->addHeader($cookie1);
        $headers->addHeader($cookie2);
        $array    = $headers->toArray();
        $expected = [
            'Set-Cookie' => [
                $cookie1->getFieldValue(),
                $cookie2->getFieldValue(),
            ],
        ];
        $this->assertEquals($expected, $array);
    }

    public function testCastingToStringReturnsAllMultiHeaderValues(): void
    {
        $headers = new Headers();
        $cookie1 = new Header\SetCookie('foo', 'bar');
        $cookie2 = new Header\SetCookie('bar', 'baz');
        $headers->addHeader($cookie1);
        $headers->addHeader($cookie2);
        $string   = $headers->toString();
        $expected = [
            'Set-Cookie: ' . $cookie1->getFieldValue(),
            'Set-Cookie: ' . $cookie2->getFieldValue(),
        ];
        $expected = implode("\r\n", $expected) . "\r\n";
        $this->assertEquals($expected, $string);
    }

    public function testZeroIsAValidHeaderValue(): void
    {
        $headers = Headers::fromString('Fake: 0');
        $this->assertSame('0', $headers->get('Fake')->getFieldValue());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testCRLFAttack(): void
    {
        $this->expectException(RuntimeException::class);
        Headers::fromString("Fake: foo-bar\r\n\r\nevilContent");
    }

    public function testAddHeaderLineMultipleHeadersGet(): void
    {
        $headers = new Headers();
        $headers->addHeaderLine('Set-Cookie: cookie1=value1');
        $headers->addHeaderLine('Set-Cookie', 'cookie2=value2');

        $result = $headers->get('Set-Cookie');
        self::assertInstanceOf(ArrayIterator::class, $result);
        self::assertCount(2, $result);
        self::assertContainsOnlyInstancesOf(Header\SetCookie::class, $result);
    }

    public function testAddHeaderLineMultipleHeadersToString(): void
    {
        $headers = new Headers();
        $headers->addHeaderLine('Set-Cookie: cookie1=value1');
        $headers->addHeaderLine('Set-Cookie', 'cookie2=value2');

        self::assertSame(
            'Set-Cookie: cookie1=value1' . "\r\n"
            . 'Set-Cookie: cookie2=value2' . "\r\n",
            $headers->toString()
        );
    }

    public function testAddHeaderMultipleHeadersGet(): void
    {
        $headers = new Headers();
        $headers->addHeader(new Header\SetCookie('cookie1', 'value1'));
        $headers->addHeader(new Header\SetCookie('cookie2', 'value2'));

        $result = $headers->get('Set-Cookie');
        self::assertInstanceOf(ArrayIterator::class, $result);
        self::assertCount(2, $result);
        self::assertContainsOnlyInstancesOf(Header\SetCookie::class, $result);
    }

    public function testAddHeaderMultipleHeadersToString(): void
    {
        $headers = new Headers();
        $headers->addHeader(new Header\SetCookie('cookie1', 'value1'));
        $headers->addHeader(new Header\SetCookie('cookie2', 'value2'));

        self::assertSame(
            'Set-Cookie: cookie1=value1' . "\r\n"
            . 'Set-Cookie: cookie2=value2' . "\r\n",
            $headers->toString()
        );
    }

    public function testAddHeadersMultipleHeadersGet(): void
    {
        $headers = new Headers();
        $headers->addHeaders([
            new Header\SetCookie('cookie1', 'value1'),
            ['Set-Cookie', 'cookie2=value2'],
            ['Set-Cookie' => 'cookie3=value3'],
            'Set-Cookie: cookie4=value4',
            'Set-Cookie' => 'cookie5=value5',
        ]);

        $result = $headers->get('Set-Cookie');
        self::assertInstanceOf(ArrayIterator::class, $result);
        self::assertCount(5, $result);
        self::assertContainsOnlyInstancesOf(Header\SetCookie::class, $result);
    }

    public function testAddHeadersMultipleHeadersToString(): void
    {
        $headers = new Headers();
        $headers->addHeaders([
            new Header\SetCookie('cookie1', 'value1'),
            ['Set-Cookie', 'cookie2=value2'],
            ['Set-Cookie' => 'cookie3=value3'],
            'Set-Cookie: cookie4=value4',
            'Set-Cookie' => 'cookie5=value5',
        ]);

        self::assertSame(
            'Set-Cookie: cookie1=value1' . "\r\n"
            . 'Set-Cookie: cookie2=value2' . "\r\n"
            . 'Set-Cookie: cookie3=value3' . "\r\n"
            . 'Set-Cookie: cookie4=value4' . "\r\n"
            . 'Set-Cookie: cookie5=value5' . "\r\n",
            $headers->toString()
        );
    }

    public function testFromStringMultipleHeadersGet(): void
    {
        $headers = Headers::fromString(
            'Set-Cookie: cookie1=value1' . "\r\n"
            . 'Set-Cookie: cookie2=value2'
        );

        $result = $headers->get('Set-Cookie');
        self::assertInstanceOf(ArrayIterator::class, $result);
        self::assertCount(2, $result);
        self::assertContainsOnlyInstancesOf(Header\SetCookie::class, $result);
    }

    public function testFromStringHeadersToString(): void
    {
        $headers = Headers::fromString(
            'Set-Cookie: cookie1=value1' . "\r\n"
            . 'Set-Cookie: cookie2=value2'
        );

        self::assertSame(
            'Set-Cookie: cookie1=value1' . "\r\n"
            . 'Set-Cookie: cookie2=value2' . "\r\n",
            $headers->toString()
        );
    }

    public function testThrowExceptionOnInvalidHeader(): void
    {
        $headers = new Headers();
        $headers->addHeaderLine('Location', "/mail\r\ntest");

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid header value detected');
        $headers->get('Location');
    }

    public function testToArrayCanHandleIteratorExtensionForMultipleHeaderValue(): void
    {
        $headerValue = 'cookie1=value1; Expires=Sun, 02-Jan-2022 08:54:16 GMT; Domain=.example.org; Path=/;'
        . ' Secure; SameSite=Lax, cookie2=value2; Expires=Sun, 02-Jan-2022 08:54:16 GMT; Domain=.example.org; Path=/;'
        . ' Secure; SameSite=Lax, cookie3=value3; Expires=Sun, 02-Jan-2022 08:54:16 GMT; Domain=.example.org; Path=/;'
        . ' Secure; SameSite=Lax';
        $headers     = Headers::fromString(sprintf('Set-Cookie: %s', $headerValue));

        $headersArray = $headers->toArray();
        self::assertCount(1, $headersArray);
        self::assertArrayHasKey('Set-Cookie', $headersArray);
        self::assertEquals($headerValue, implode(', ', $headersArray['Set-Cookie']));
    }
}
