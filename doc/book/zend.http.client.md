# HTTP Client

## Overview

`Laminas\Http\Client` provides an easy interface for performing Hyper-Text Transfer Protocol (HTTP)
requests. `Laminas\Http\Client` supports the most simple features expected from an *HTTP* client, as
well as some more complex features such as *HTTP* authentication and file uploads. Successful
requests (and most unsuccessful ones too) return a `Laminas\Http\Response` object, which provides
access to the response's headers and body (see this
section &lt;laminas.http.response&gt;).

## Quick Start

The class constructor optionally accepts a URL as its first parameter (can be either a string or a
`Laminas\Uri\Http` object), and an array or `Laminas\Config\Config` object containing configuration
options. The `send()` method is used to submit the request to the remote server, and a
`Laminas\Http\Response` object is returned:

```php
use Laminas\Http\Client;

$client = new Client('http://example.org', array(
    'maxredirects' => 0,
    'timeout'      => 30
));
$response = $client->send();
```

Both constructor parameters can be left out, and set later using the setUri() and setConfig()
methods:

```php
use Laminas\Http\Client;

$client = new Client();
$client->setUri('http://example.org');
$client->setOptions(array(
    'maxredirects' => 0,
    'timeout'      => 30
));
$response = $client->send();
```

`Laminas\Http\Client` can also dispatch requests using a separately configured `request` object (see
the Laminas\\\\Http\\\\Request manual page&lt;laminas.http.request&gt; for full details of the methods
available):

```php
use Laminas\Http\Client;
use Laminas\Http\Request;

$request = new Request();
$request->setUri('http://example.org');

$client = new Client();

$response = $client->send($request);
```

> ## Note
`Laminas\Http\Client` uses `Laminas\Uri\Http` to validate URLs. See the Laminas\\\\Uri manual
page&lt;laminas.uri&gt; for more information on the validation process.

## Configuration

The constructor and setOptions() method accepts an associative array of configuration parameters, or
a `Laminas\Config\Config` object. Setting these parameters is optional, as they all have default
values.

The options are also passed to the adapter class upon instantiation, so the same array or
`Laminas\Config\Config` object) can be used for adapter configuration. See the Laminas Http Client adapter
section&lt;laminas.http.client.adapters&gt; for more information on the adapter-specific options
available.

## Examples

### Performing a Simple GET Request

Performing simple *HTTP* requests is very easily done:

```php
use Laminas\Http\Client;

$client = new Client('http://example.org');
$response = $client->send();
```

### Using Request Methods Other Than GET

The request method can be set using `setMethod()`. If no method is specified, the method set by the
last `setMethod()` call is used. If `setMethod()` was never called, the default request method is
`GET`.

```php
use Laminas\Http\Client;

$client = new Client('http://example.org');

// Performing a POST request
$client->setMethod('POST');
$response = $client->send();
```

For convenience, `Laminas\Http\Request` defines all the request methods as class constants,
`Laminas\Http\Request::METHOD_GET`, `Laminas\Http\Request::METHOD_POST` and so on:

```php
use Laminas\Http\Client;
use Laminas\Http\Request;

$client = new Client('http://example.org');

// Performing a POST request
$client->setMethod(Request::METHOD_POST);
$response = $client->send();
```

### Setting GET parameters

Adding `GET` parameters to an *HTTP* request is quite simple, and can be done either by specifying
them as part of the URL, or by using the `setParameterGet()` method. This method takes the `GET`
parameters as an associative array of name =&gt; value `GET` variables.

```php
use Laminas\Http\Client;
$client = new Client();

// This is equivalent to setting a URL in the Client's constructor:
$client->setUri('http://example.com/index.php?knight=lancelot');

// Adding several parameters with one call
$client->setParameterGet(array(
   'first_name'  => 'Bender',
   'middle_name' => 'Bending',
   'last_name'   => 'Rodríguez',
   'made_in'     => 'Mexico',
));
```

### Setting POST Parameters

While `GET` parameters can be sent with every request method, `POST` parameters are only sent in the
body of `POST` requests. Adding `POST` parameters to a request is very similar to adding `GET`
parameters, and can be done with the `setParameterPost()` method, which is identical to the
`setParameterGet()` method in structure.

```php
use Laminas\Http\Client;

$client = new Client();

// Setting several POST parameters, one of them with several values
$client->setParameterPost(array(
    'language'  => 'es',
    'country'   => 'ar',
    'selection' => array(45, 32, 80)
));
```

Note that when sending `POST` requests, you can set both `GET` and `POST` parameters. On the other
hand, setting POST parameters on a non-`POST` request will not trigger an error, rendering it
useless. Unless the request is a `POST` request, `POST` parameters are simply ignored.

### Connecting to SSL URLs

If you are trying to connect to an SSL (https) URL and are using the default
(`Laminas\Http\Client\Adapter\Socket`) adapter, you may need to set the `sslcapath` configuration
option in order to allow PHP to validate the SSL certificate:

```php
use Laminas\Http\Client;

$client = new Client('https://example.org', array(
   'sslcapath' => '/etc/ssl/certs'
));
$response = $client->send();
```

The exact path to use will vary depending on your Operating System. Without this you'll get the
exception "Unable to enable crypto on TCP connection" when trying to connect.

Alternatively, you could switch to the curl adapter, which negotiates SSL connections more
transparently:

```php
use Laminas\Http\Client;

$client = new Client('https://example.org', array(
   'adapter' => 'Laminas\Http\Client\Adapter\Curl'
));
$response = $client->send();
```

### A Complete Example

```php
use Laminas\Http\Client;

$client = new Client();
$client->setUri('http://www.example.com');
$client->setMethod('POST');
$client->setParameterPost(array(
   'foo' => 'bar'
));

$response = $client->send();

if ($response->isSuccess()) {
    // the POST was successful
}
```

or the same thing, using a request object:

```php
use Laminas\Http\Client;
use Laminas\Http\Request;

$request = new Request();
$request->setUri('http://www.example.com');
$request->setMethod('POST');
$request->getPost()->set('foo', 'bar');

$client = new Client();
$response = $client->send($request);

if ($response->isSuccess()) {
    // the POST was successful
}
```
