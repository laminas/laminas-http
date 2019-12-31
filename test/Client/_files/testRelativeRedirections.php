<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

if (! isset($_GET['redirect'])) {
    $_GET['redirect'] = null;
}

switch ($_GET['redirect']) {
    case 'abpath':
        header("Location: /path/to/fake/file.ext?redirect=abpath");
        break;

    case 'relpath':
        header("Location: path/to/fake/file.ext?redirect=relpath");
        break;

    default:
        echo "Redirections done.";
        break;
}
