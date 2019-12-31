<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\TransferEncoding;

class TransferEncodingTest extends \PHPUnit_Framework_TestCase
{
    public function testTransferEncodingFromStringCreatesValidTransferEncodingHeader()
    {
        $transferEncodingHeader = TransferEncoding::fromString('Transfer-Encoding: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $transferEncodingHeader);
        $this->assertInstanceOf('Laminas\Http\Header\TransferEncoding', $transferEncodingHeader);
    }

    public function testTransferEncodingGetFieldNameReturnsHeaderName()
    {
        $transferEncodingHeader = new TransferEncoding();
        $this->assertEquals('Transfer-Encoding', $transferEncodingHeader->getFieldName());
    }

    public function testTransferEncodingGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('TransferEncoding needs to be completed');

        $transferEncodingHeader = new TransferEncoding();
        $this->assertEquals('xxx', $transferEncodingHeader->getFieldValue());
    }

    public function testTransferEncodingToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('TransferEncoding needs to be completed');

        $transferEncodingHeader = new TransferEncoding();

        // @todo set some values, then test output
        $this->assertEmpty('Transfer-Encoding: xxx', $transferEncodingHeader->toString());
    }

    /** Implmentation specific tests here */
}
