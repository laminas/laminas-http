<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

if (! isset($_GET['redirection'])) {
    $_GET['redirection'] = 0;

    /**
     * Create session cookie, but only on first redirect
     */
    setcookie('laminastestSessionCookie', 'positive');

    /**
     * Create a long living cookie
     */
    setcookie('laminastestLongLivedCookie', 'positive', time() + 2678400);

    /**
     * Create a cookie that should be invalid on arrival
     */
    setcookie('laminastestExpiredCookie', 'negative', time() - 2400);
}

$_GET['redirection']++;
$https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';

if (! isset($_GET['redirection']) || $_GET['redirection'] < 4) {
    $target = 'http' . ($https ? 's://' : '://')  . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    header('Location: ' . $target . '?redirection=' . $_GET['redirection']);
} else {
    var_dump($_GET);
    var_dump($_POST);
}
