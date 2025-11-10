<?php

namespace Laminas\Http\Header;

interface MultipleHeaderInterface extends HeaderInterface
{
    /**
     * Convert multiple headers to string representation
     *
     * @param array $headers Array of header instances
     * @return string
     */
    public function toStringMultipleHeaders(array $headers);
}
