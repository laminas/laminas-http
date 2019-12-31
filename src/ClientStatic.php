<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Http;

use Laminas\Http\Client;

/**
 * Http static client
 *
 * @category   Laminas
 * @package    Laminas\Http
 */
class ClientStatic
{

    protected static $client;

    /**
     * Get the static HTTP client
     *
     * @return Client
     */
    protected static function getStaticClient()
    {
        if (!isset(static::$client)) {
            static::$client = new Client();
        }
        return static::$client;
    }

    /**
     * HTTP GET METHOD (static)
     *
     * @param  string $url
     * @param  array $query
     * @param  array $headers
     * @param  mixed $body
     * @return Response|bool
     */
    public static function get($url, $query = array(), $headers = array(), $body = null)
    {
        if (empty($url)) {
            return false;
        }

        $request= new Request();
        $request->setUri($url);
        $request->setMethod(Request::METHOD_GET);

        if (!empty($query) && is_array($query)) {
            $request->getQuery()->fromArray($query);
        }

        if (!empty($headers) && is_array($headers)) {
            $request->getHeaders()->addHeaders($headers);
        }

        if (!empty($body)) {
            $request->setBody($body);
        }

        return static::getStaticClient()->send($request);
    }

    /**
     * HTTP POST METHOD (static)
     *
     * @param  string $url
     * @param  array $params
     * @param  array $headers
     * @param  mixed $body
     * @throws Exception\InvalidArgumentException
     * @return Response|bool
     */
    public static function post($url, $params, $headers = array(), $body = null)
    {
        if (empty($url)) {
            return false;
        }

        $request= new Request();
        $request->setUri($url);
        $request->setMethod(Request::METHOD_POST);

        if (!empty($params) && is_array($params)) {
            $request->getPost()->fromArray($params);
        } else {
            throw new Exception\InvalidArgumentException('The array of post parameters is empty');
        }

        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type']= Client::ENC_URLENCODED;
        }

        if (!empty($headers) && is_array($headers)) {
            $request->getHeaders()->addHeaders($headers);
        }

        if (!empty($body)) {
            $request->setContent($body);
        }

        return static::getStaticClient()->send($request);
    }
}
