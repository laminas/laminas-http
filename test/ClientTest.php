<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http;

use Laminas\Http\Client;
use Laminas\Http\Exception;
use Laminas\Http\Header\SetCookie;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testClientRetrievesUppercaseHttpMethodFromRequestObject()
    {
        $client = new Client;
        $client->setMethod('post');
        $this->assertEquals(Client::ENC_URLENCODED, $client->getEncType());
    }

    public function testIfZeroValueCookiesCanBeSet()
    {
        $client = new Client();
        $client->addCookie("test", 0);
        $client->addCookie("test2", "0");
        $client->addCookie("test3", false);
    }

    /**
    * @expectedException Laminas\Http\Exception\InvalidArgumentException
    */
    public function testIfNullValueCookiesThrowsException()
    {
        $client = new Client();
        $client->addCookie("test", null);
    }

    public function testIfCookieHeaderCanBeSet()
    {
        $header = new SetCookie('foo');

        $client = new Client();
        $client->addCookie($header);

        $cookies = $client->getCookies();
        $this->assertEquals(1, count($cookies));
        $this->assertEquals($header, $cookies['foo']);
    }

    public function testIfArrayOfHeadersCanBeSet()
    {
        $headers = array(
            new SetCookie('foo'),
            new SetCookie('bar')
        );

        $client = new Client();
        $client->addCookie($headers);

        $cookies = $client->getCookies();
        $this->assertEquals(2, count($cookies));
    }

    public function testIfArrayIteratorOfHeadersCanBeSet()
    {
        $headers = new \ArrayIterator(array(
            new SetCookie('foo'),
            new SetCookie('bar')
        ));

        $client = new Client();
        $client->addCookie($headers);

        $cookies = $client->getCookies();
        $this->assertEquals(2, count($cookies));
    }
}
