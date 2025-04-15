<?php

namespace Laminas\Http\Header;

use Laminas\Uri\Http as HttpUri;
use Laminas\Uri\UriInterface;

/**
 * Content-Location Header
 *
 * @link       http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.36
 */
final class Referer extends AbstractLocation
{
    /**
     * Set the URI/URL for this header
     * according to RFC Referer URI should not have fragment
     *
     * @param  string|HttpUri $uri
     * @return $this
     */
    public function setUri($uri)
    {
        parent::setUri($uri);
        if ($this->uri instanceof UriInterface) {
            $this->uri->setFragment('');
        }

        return $this;
    }

    /**
     * Return header name
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'Referer';
    }
}
