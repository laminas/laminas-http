<?php

namespace Laminas\Http\Header;

interface MultipleHeaderInterface extends HeaderInterface
{
    /**
     * Convert multiple headers to string representation
     *
     * @param array $headers Array of header instances
     */
    public function toStringMultipleHeaders(array $headers): string;
}
