<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Http\Header;

/**
 * Accept Ranges Header
 *
 * @category   Laminas
 * @package    Laminas\Http\Header
 * @see        http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.5
 */
class AcceptRanges implements HeaderInterface
{

    protected $rangeUnit = null;

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = explode(': ', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'accept-ranges') {
            throw new Exception\InvalidArgumentException('Invalid header line for Accept-Ranges string');
        }

        $header->rangeUnit = trim($value);

        return $header;
    }

    public function getFieldName()
    {
        return 'Accept-Ranges';
    }

    public function getFieldValue()
    {
        return $this->getRangeUnit();
    }

    public function setRangeUnit($rangeUnit)
    {
        $this->rangeUnit = $rangeUnit;
        return $this;
    }

    public function getRangeUnit()
    {
        return $this->rangeUnit;
    }

    public function toString()
    {
        return 'Accept-Ranges: ' . $this->getFieldValue();
    }
}
