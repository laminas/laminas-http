<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\RetryAfter;

class RetryAfterTest extends \PHPUnit_Framework_TestCase
{
    public function testRetryAfterFromStringCreatesValidRetryAfterHeader()
    {
        $retryAfterHeader = RetryAfter::fromString('Retry-After: 10');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $retryAfterHeader);
        $this->assertInstanceOf('Laminas\Http\Header\RetryAfter', $retryAfterHeader);
        $this->assertEquals('10', $retryAfterHeader->getDeltaSeconds());
    }

    public function testRetryAfterFromStringCreatesValidRetryAfterHeaderFromDate()
    {
        $retryAfterHeader = RetryAfter::fromString('Retry-After: Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('Sun, 06 Nov 1994 08:49:37 GMT', $retryAfterHeader->getDate());
    }

    public function testRetryAfterGetFieldNameReturnsHeaderName()
    {
        $retryAfterHeader = new RetryAfter();
        $this->assertEquals('Retry-After', $retryAfterHeader->getFieldName());
    }

    public function testRetryAfterGetFieldValueReturnsProperValue()
    {
        $retryAfterHeader = new RetryAfter();
        $retryAfterHeader->setDeltaSeconds(3600);
        $this->assertEquals('3600', $retryAfterHeader->getFieldValue());
        $retryAfterHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('Sun, 06 Nov 1994 08:49:37 GMT', $retryAfterHeader->getFieldValue());
    }

    public function testRetryAfterToStringReturnsHeaderFormattedString()
    {
        $retryAfterHeader = new RetryAfter();

        $retryAfterHeader->setDeltaSeconds(3600);
        $this->assertEquals('Retry-After: 3600', $retryAfterHeader->toString());

        $retryAfterHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('Retry-After: Sun, 06 Nov 1994 08:49:37 GMT', $retryAfterHeader->toString());
    }
}
