<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Http\PhpEnvironment;

use Laminas\Http\Header\MultipleHeaderInterface;
use Laminas\Http\Response as HttpResponse;

/**
 * HTTP Response for current PHP environment
 *
 * @category   Laminas
 * @package    Laminas_Http
 */
class Response extends HttpResponse
{
    /**
     * @var bool
     */
    protected $headersSent = false;

    /**
     * @var bool
     */
    protected $contentSent = false;

    /**
     * @return bool
     */
    public function headersSent()
    {
        return $this->headersSent;
    }

    /**
     * @return bool
     */
    public function contentSent()
    {
        return $this->contentSent;
    }

    /**
     * Send HTTP headers
     *
     * @return Response
     */
    public function sendHeaders()
    {
        if ($this->headersSent()) {
            return $this;
        }

        $status  = $this->renderStatusLine();
        header($status);

        /** @var \Laminas\Http\Header\HeaderInterface $header */
        foreach ($this->getHeaders() as $header) {
            if ($header instanceof MultipleHeaderInterface) {
                header($header->toString(), false);
                continue;
            }
            header($header->toString());
        }

        $this->headersSent = true;
        return $this;
    }

    /**
     * Send content
     *
     * @return Response
     */
    public function sendContent()
    {
        if ($this->contentSent()) {
            return $this;
        }

        echo $this->getContent();
        $this->contentSent = true;
        return $this;
    }

    /**
     * Send HTTP response
     *
     * @return Response
     */
    public function send()
    {
        $this->sendHeaders()
             ->sendContent();
        return $this;
    }
}