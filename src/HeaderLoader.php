<?php

namespace Laminas\Http;

use Laminas\Http\Exception\InvalidArgumentException;

use function array_key_exists;
use function class_exists;

/**
 * Plugin Class Loader implementation for HTTP headers
 */
final class HeaderLoader
{
    /** @var array<non-empty-string, class-string> */
    private array $plugins = [
        'accept'                  => Header\Accept::class,
        'acceptcharset'           => Header\AcceptCharset::class,
        'acceptencoding'          => Header\AcceptEncoding::class,
        'acceptlanguage'          => Header\AcceptLanguage::class,
        'acceptranges'            => Header\AcceptRanges::class,
        'age'                     => Header\Age::class,
        'allow'                   => Header\Allow::class,
        'authenticationinfo'      => Header\AuthenticationInfo::class,
        'authorization'           => Header\Authorization::class,
        'cachecontrol'            => Header\CacheControl::class,
        'connection'              => Header\Connection::class,
        'contentdisposition'      => Header\ContentDisposition::class,
        'contentencoding'         => Header\ContentEncoding::class,
        'contentlanguage'         => Header\ContentLanguage::class,
        'contentlength'           => Header\ContentLength::class,
        'contentlocation'         => Header\ContentLocation::class,
        'contentmd5'              => Header\ContentMD5::class,
        'contentrange'            => Header\ContentRange::class,
        'contentsecuritypolicy'   => Header\ContentSecurityPolicy::class,
        'contenttransferencoding' => Header\ContentTransferEncoding::class,
        'contenttype'             => Header\ContentType::class,
        'cookie'                  => Header\Cookie::class,
        'date'                    => Header\Date::class,
        'etag'                    => Header\Etag::class,
        'expect'                  => Header\Expect::class,
        'expires'                 => Header\Expires::class,
        'featurepolicy'           => Header\FeaturePolicy::class,
        'from'                    => Header\From::class,
        'host'                    => Header\Host::class,
        'ifmatch'                 => Header\IfMatch::class,
        'ifmodifiedsince'         => Header\IfModifiedSince::class,
        'ifnonematch'             => Header\IfNoneMatch::class,
        'ifrange'                 => Header\IfRange::class,
        'ifunmodifiedsince'       => Header\IfUnmodifiedSince::class,
        'keepalive'               => Header\KeepAlive::class,
        'lastmodified'            => Header\LastModified::class,
        'location'                => Header\Location::class,
        'maxforwards'             => Header\MaxForwards::class,
        'origin'                  => Header\Origin::class,
        'pragma'                  => Header\Pragma::class,
        'proxyauthenticate'       => Header\ProxyAuthenticate::class,
        'proxyauthorization'      => Header\ProxyAuthorization::class,
        'range'                   => Header\Range::class,
        'referer'                 => Header\Referer::class,
        'refresh'                 => Header\Refresh::class,
        'retryafter'              => Header\RetryAfter::class,
        'server'                  => Header\Server::class,
        'setcookie'               => Header\SetCookie::class,
        'te'                      => Header\TE::class,
        'trailer'                 => Header\Trailer::class,
        'transferencoding'        => Header\TransferEncoding::class,
        'upgrade'                 => Header\Upgrade::class,
        'useragent'               => Header\UserAgent::class,
        'vary'                    => Header\Vary::class,
        'via'                     => Header\Via::class,
        'warning'                 => Header\Warning::class,
        'wwwauthenticate'         => Header\WWWAuthenticate::class,
    ];

    /** @return class-string|null */
    public function loader(string $header): string|null
    {
        if (! array_key_exists($header, $this->plugins)) {
            return null;
        }

        return $this->plugins[$header];
    }

    /**
     * @param non-empty-string $header
     * @param class-string $class
     */
    public function registerPlugin(string $header, string $class): void
    {
        if (array_key_exists($header, $this->plugins)) {
            throw new InvalidArgumentException("Plugin already exists");
        }

        if (! class_exists($class)) {
            throw new InvalidArgumentException("Class " . $class . ' does not exist');
        }

        $this->plugins[$header] = $class;
    }
}
