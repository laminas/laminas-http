<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

$putdata = fopen("php://input", "r");
while ($data = fread($putdata, 1024)) {
    echo $data;
}

fclose($putdata);
