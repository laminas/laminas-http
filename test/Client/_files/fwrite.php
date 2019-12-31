<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

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
