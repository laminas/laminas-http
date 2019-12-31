<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http;

use Laminas\Http\Cookies;
use Laminas\Http\Header\SetCookie;
use Laminas\Http\Headers;
use Laminas\Http\Response;

class CookiesTest extends \PHPUnit_Framework_TestCase
{
    public function testFromResponseInSetCookie()
    {
        $response = new Response();
        $headers = new Headers();
        $header = new SetCookie("foo", "bar");
        $header->setDomain("www.zend.com");
        $header->setPath("/");
        $headers->addHeader($header);
        $response->setHeaders($headers);

        $response = Cookies::fromResponse($response, "https://www.zend.com");
        $this->assertSame($header, $response->getCookie('https://www.zend.com', 'foo'));
    }

    public function testFromResponseInCookie()
    {
        $response = new Response();
        $headers = new Headers();
        $header = new SetCookie("foo", "bar");
        $header->setDomain("www.zend.com");
        $header->setPath("/");
        $headers->addHeader($header);
        $response->setHeaders($headers);

        $response = Cookies::fromResponse($response, "https://www.zend.com");
        $this->assertSame($header, $response->getCookie('https://www.zend.com', 'foo'));
    }
}
