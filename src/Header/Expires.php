<?php

namespace Laminas\Http\Header;

use DateTime;

use function date;
use function is_int;
use function is_string;

use const DATE_W3C;

/**
 * Expires Header
 *
 * @link       http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.21
 */
final class Expires extends AbstractDate
{
    /**
     * Get header name
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'Expires';
    }

    /**
     * @param int|string|DateTime $date
     * @return static
     */
    public function setDate($date)
    {
        if ($date === '0' || $date === 0) {
            $date = date(DATE_W3C, 0); // Thu, 01 Jan 1970 00:00:00 GMT
        }

        if (is_int($date) || is_string($date)) {
            if (is_int($date)) {
                $date = new DateTime('@' . $date);
            }

            if ($date instanceof DateTime) {
                $date = $date->format(DATE_W3C);
            }
        }

        return parent::setDate($date);
    }
}
