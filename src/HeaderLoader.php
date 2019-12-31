<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Http;

use Laminas\Loader\PluginClassLoader;

/**
 * Plugin Class Loader implementation for HTTP headers
 *
 * @category   Laminas
 * @package    Laminas_Http
 */
class HeaderLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased Header plugins
     */
    protected $plugins = array(
        'accept'             => 'Laminas\Http\Header\Accept',
        'acceptcharset'      => 'Laminas\Http\Header\AcceptCharset',
        'acceptencoding'     => 'Laminas\Http\Header\AcceptEncoding',
        'acceptlanguage'     => 'Laminas\Http\Header\AcceptLanguage',
        'acceptranges'       => 'Laminas\Http\Header\AcceptRanges',
        'age'                => 'Laminas\Http\Header\Age',
        'allow'              => 'Laminas\Http\Header\Allow',
        'authenticationinfo' => 'Laminas\Http\Header\AuthenticationInfo',
        'authorization'      => 'Laminas\Http\Header\Authorization',
        'cachecontrol'       => 'Laminas\Http\Header\CacheControl',
        'connection'         => 'Laminas\Http\Header\Connection',
        'contentdisposition' => 'Laminas\Http\Header\ContentDisposition',
        'contentencoding'    => 'Laminas\Http\Header\ContentEncoding',
        'contentlanguage'    => 'Laminas\Http\Header\ContentLanguage',
        'contentlength'      => 'Laminas\Http\Header\ContentLength',
        'contentlocation'    => 'Laminas\Http\Header\ContentLocation',
        'contentmd5'         => 'Laminas\Http\Header\ContentMD5',
        'contentrange'       => 'Laminas\Http\Header\ContentRange',
        'contenttype'        => 'Laminas\Http\Header\ContentType',
        'cookie'             => 'Laminas\Http\Header\Cookie',
        'date'               => 'Laminas\Http\Header\Date',
        'etag'               => 'Laminas\Http\Header\Etag',
        'expect'             => 'Laminas\Http\Header\Expect',
        'expires'            => 'Laminas\Http\Header\Expires',
        'from'               => 'Laminas\Http\Header\From',
        'host'               => 'Laminas\Http\Header\Host',
        'ifmatch'            => 'Laminas\Http\Header\IfMatch',
        'ifmodifiedsince'    => 'Laminas\Http\Header\IfModifiedSince',
        'ifnonematch'        => 'Laminas\Http\Header\IfNoneMatch',
        'ifrange'            => 'Laminas\Http\Header\IfRange',
        'ifunmodifiedsince'  => 'Laminas\Http\Header\IfUnmodifiedSince',
        'keepalive'          => 'Laminas\Http\Header\KeepAlive',
        'lastmodified'       => 'Laminas\Http\Header\LastModified',
        'location'           => 'Laminas\Http\Header\Location',
        'maxforwards'        => 'Laminas\Http\Header\MaxForwards',
        'pragma'             => 'Laminas\Http\Header\Pragma',
        'proxyauthenticate'  => 'Laminas\Http\Header\ProxyAuthenticate',
        'proxyauthorization' => 'Laminas\Http\Header\ProxyAuthorization',
        'range'              => 'Laminas\Http\Header\Range',
        'referer'            => 'Laminas\Http\Header\Referer',
        'refresh'            => 'Laminas\Http\Header\Refresh',
        'retryafter'         => 'Laminas\Http\Header\RetryAfter',
        'server'             => 'Laminas\Http\Header\Server',
        'setcookie'          => 'Laminas\Http\Header\SetCookie',
        'te'                 => 'Laminas\Http\Header\TE',
        'trailer'            => 'Laminas\Http\Header\Trailer',
        'transferencoding'   => 'Laminas\Http\Header\TransferEncoding',
        'upgrade'            => 'Laminas\Http\Header\Upgrade',
        'useragent'          => 'Laminas\Http\Header\UserAgent',
        'vary'               => 'Laminas\Http\Header\Vary',
        'via'                => 'Laminas\Http\Header\Via',
        'warning'            => 'Laminas\Http\Header\Warning',
        'wwwauthenticate'    => 'Laminas\Http\Header\WWWAuthenticate'
    );
}
