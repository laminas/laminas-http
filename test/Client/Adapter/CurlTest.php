<?php

declare(strict_types=1);

namespace LaminasTest\Http\Client\Adapter;

use Laminas\Http\Client\Adapter\Curl;
use Laminas\Uri\Uri;
use PHPUnit\Framework\TestCase;

use function curl_getinfo;

use const CURL_HTTP_VERSION_1_0;
use const CURL_HTTP_VERSION_1_1;
use const CURLINFO_HTTP_VERSION;

final class CurlTest extends TestCase
{
    /** @var Curl */
    private $adapter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adapter = new Curl();
    }

    /**
     * @return iterable<non-empty-string,array{0:CURL_HTTP_VERSION_*,1:float}>
     */
    public function floatHttpVersions(): iterable
    {
        yield 'HTTP 1.0' => [CURL_HTTP_VERSION_1_0, 1.0];
        yield 'HTTP 1.1' => [CURL_HTTP_VERSION_1_1, 1.1];
    }

    /**
     * @return iterable<non-empty-string,array{0:CURL_HTTP_VERSION_*,1:float}>
     */
    public function httpVersions(): iterable
    {
        yield 'HTTP 1.0' => [CURL_HTTP_VERSION_1_0, '1.0'];
        yield 'HTTP 1.1' => [CURL_HTTP_VERSION_1_1, '1.1'];
    }

    /**
     * NOTE: This test is only needed for BC compatibility. The {@see \Laminas\Http\Client\Adapter\AdapterInterface}
     *       has a default for "string" but "float" was used in {@see Curl::write()} due to the lack of strict types.
     *
     * @dataProvider floatHttpVersions
     */
    public function testWriteCanHandleFloatHttpVersion(int $expectedCurlOption, float $version): void
    {
        $this->adapter->connect('example.org');
        $this->adapter->write('GET', new Uri('http://example.org:80/'), $version);
        $handle = $this->adapter->getHandle();
        self::assertNotNull($handle);
        self::assertEquals($expectedCurlOption, curl_getinfo($handle, CURLINFO_HTTP_VERSION));
    }

    /**
     * @dataProvider httpVersions
     */
    public function testWriteCanHandleStringHttpVersion(int $expectedCurlOption, string $version): void
    {
        $this->adapter->connect('example.org');
        $this->adapter->write('GET', new Uri('http://example.org:80/'), $version);
        $handle = $this->adapter->getHandle();
        self::assertNotNull($handle);
        self::assertEquals($expectedCurlOption, curl_getinfo($handle, CURLINFO_HTTP_VERSION));
    }
}
