# The Response Class

`Laminas\Http\Response` is responsible for providing a fluent API that allows a
developer to interact with all the various parts of an HTTP response.

A typical HTTP Response looks like this:

```text
| VERSION | CODE | REASON |
|        HEADERS          |
|         BODY            |
```

The first line of the response consists of the HTTP version, status code, and
the reason string for the provided status code; this is called the Response
Line. Next is a set of zero or more headers.  The remainder of the response is
the response body, which is typically a string of HTML that will render on the
client's browser, but which can also be a place for request/response payload
data typical of an AJAX request. More information on the structure and
specification of an HTTP response can be found in
[RFC-2616 on the W3.org site](http://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html).

## Quick Start

Response objects can either be created from the provided `fromString()` factory,
or, if you wish to have a completely empty object to start with, by
instantiating the `Laminas\Http\Response` class with no arguments.

```php
use Laminas\Http\Response;
$response = Response::fromString(<<<EOS
HTTP/1.0 200 OK
HeaderField1: header-field-value
HeaderField2: header-field-value2

<html>
<body>
    Hello World
</body>
</html>
EOS);

// OR

$response = new Response();
$response->setStatusCode(Response::STATUS_CODE_200);
$response->getHeaders()->addHeaders([
    'HeaderField1' => 'header-field-value',
    'HeaderField2' => 'header-field-value2',
]);
$response->setContent(<<<EOS
<html>
<body>
    Hello World
</body>
</html>
EOS
);
```

## Configuration Options

No configuration options are available.

## Available Methods

The following table details available methods, their signatures, and a brief
description. Note that the following references refer to the following
fully qualified class names and/or namespaces:

- `Headers`: `Laminas\Http\Headers`
- `Response`: `Laminas\Http\Response`

Method signature                                                       | Description
---------------------------------------------------------------------- | -----------
`static fromString(string $string) : Response`                         | Populate object from string.
`renderStatusLine() : string`                                          | Render the status line header
`setHeaders(Headers $headers) : self`                                  | Provide an alternate Parameter Container implementation for headers in this object. (This is NOT the primary API for value setting; for that, see `getHeaders()`.)
`getHeaders() : Headers`                                               | Return the container responsible for storing HTTP headers. This container exposes the primary API for manipulating headers set in the HTTP response. See the section on [Headers](headers.md) for more information.
`setVersion(string $version) : self`                                   | Set the HTTP version for this object, one of 1.0, 1.1 or 2 (`Response::VERSION_10`, `Response::VERSION_11`, `Response::VERSION_2`). HTTP/2 support was added in laminas-http 2.10.0.
`getVersion() : string`                                                | Return the HTTP version for this response.
`setStatusCode(int $code) : self`                                      | Set HTTP status code.
`getStatusCode() : int`                                                | Retrieve HTTP status code.
`setReasonPhrase(string $reasonPhrase) : self`                         | Set custom HTTP status message.
`getReasonPhrase() : string`                                           | Get HTTP status message.
`isClientError() : bool`                                               | Does the status code indicate a client error?
`isForbidden() : bool`                                                 | Is the request forbidden due to ACLs?
`isInformational() : bool`                                             | Is the current status "informational"?
`isNotFound() : bool`                                                  | Does the status code indicate the resource is not found?
`isOk() : bool`                                                        | Do we have a normal, OK response?
`isServerError() : bool`                                               | Does the status code reflect a server error?
`isRedirect() : bool`                                                  | Do we have a redirect?
`isSuccess() : bool`                                                   | Was the response successful?
`decodeChunkedBody(string $body) : string`                             | Decode a "chunked" transfer-encoded body and return the decoded text.
`decodeGzip(string $body) : string`                                    | Decode a gzip encoded message (when `Content-Encoding` indicates gzip). Currently requires PHP with zlib support.
`decodeDeflate(string $body) : string`                                 | Decode a zlib deflated message (when `Content-Encoding` indicates deflate). Currently requires PHP with zlib support.
`setMetadata(string|int|array|Traversable $spec, mixed $value) : self` | Non-destructive setting of message metadata; always adds to the metadata, never overwrites the entire metadata container.
`getMetadata(null|string|int $key, null|mixed $default) : mixed`       | Retrieve all metadata or a single metadatum as specified by key.
`setContent(mixed $value) : self`                                      | Set message content.
`getContent() : mixed`                                                 | Get raw message content.
`getBody() : mixed`                                                    | Get decoded message content.
`toString() : string`                                                  | Returns string representation of response.

## Examples

### Generating a Response object from a string

```php
use Laminas\Http\Response;
$request = Response::fromString(<<<EOS
HTTP/1.0 200 OK
HeaderField1: header-field-value
HeaderField2: header-field-value2

<html>
<body>
    Hello World
</body>
</html>
EOS);
```

### Generating a formatted HTTP Response from a Response object

```php
use Laminas\Http\Response;
$response = new Response();
$response->setStatusCode(Response::STATUS_CODE_200);
$response->getHeaders()->addHeaders([
    'HeaderField1' => 'header-field-value',
    'HeaderField2' => 'header-field-value2',
]);
$response->setContent(<<<EOS
<html>
<body>
    Hello World
</body>
</html>
EOS);
```

### Handle "Headers already sent" errors

> Available since version 2.13.0

By default, laminas-http's `Laminas\Http\PhpEnvironment\Response` class, which
is used in laminas-mvc applications, tests to see if PHP has already emitted
HTTP headers and started emitting content before it attempts to send headers
from the response object. If it has, it silently ignores this fact.

If you would like to capture that information (e.g., to log when it happens and
which headers were not emitted, or to raise an exception), you can provide a
callable to the `Response::setHeadersSentHandler()`, per the following example:

```php
use Laminas\Http\PhpEnvironment\Response;

// On a new instance
$response = new Response();
$response->setHeadersSentHandler(function ($response): void {
    throw new RuntimeException('Cannot send headers, headers already sent');
});
```

If you are using laminas-mvc, we recommend creating a delegator factory for
purposes of such injection. The delegator factory would look like this:

```php
// in module/Application/src/InjectSendHeadersHandlerDelegator.php:

namespace Application;

use Interop\Container\ContainerInterface;
use Laminas\Http\PhpEnvironment\Response;
use Laminas\ServiceManager\Factory\DelegatorFactoryInterface;
use RuntimeException;

class InjectSendHeadersHandlerDelegator implements DelegatorFactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory
    ); Response {
        /** @var Response $response */
        $response = $factory();
        $response->setHeadersSentHandler(function (Response $response): void {
            throw new RuntimeException('Cannot send headers, headers already sent');
        });

        return $response;
    }
}
```

You would then configure your container to use this instance with the following
configuration:

```php
// in config/autoload/dependencies.global.php

use Application\InjectSendHeadersHandlerDelegator;
use Laminas\Http\PhpEnvironment\Response;

return [
    'service_manager' => [
        'delegators' => [
            Response::class => [
                InjectSendHeadersHandlerDelegator::class,
            ],
        ],
    ],
];
```
