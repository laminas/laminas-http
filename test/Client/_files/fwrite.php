<?php

namespace Laminas\Http\Client\Adapter;

/**
 * This is a stub for PHP's `fwrite` function. It
 * allows us to check that a write operation to a
 * socket producing a returned "0 bytes" written
 * is actually valid.
 */
function fwrite($socket, $request)
{
    return 0;
}
