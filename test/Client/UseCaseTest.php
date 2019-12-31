<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Client;

use Laminas\Http\Client\Adapter;
use Laminas\Http\Client\Adapter\Exception as AdapterException;
use Laminas\Http\Client as HTTPClient;
use Laminas\Http\Request;
use Laminas\Http\Response;

/**
 * This are the test for the prototype of Laminas\Http\Client
 *
 * @category   Laminas
 * @package    Laminas\Http\Client
 * @subpackage UnitTests
 * @group      Laminas_Http
 * @group      Laminas_Http_Client
 */
class UseCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The bast URI for this test, containing all files in the files directory
     * Should be set in TestConfiguration.php or TestConfiguration.php.dist
     *
     * @var string
     */
    protected $baseuri;

    /**
     * Common HTTP client
     *
     * @var \Laminas\Http\Client
     */
    protected $client = null;

    /**
     * Common HTTP client adapter
     *
     * @var \Laminas\Http\Client\Adapter\AdapterInterface
     */
    protected $adapter = null;

    /**
     * Configuration array
     *
     * @var array
     */
    protected $config = array(
        'adapter'     => 'Laminas\Http\Client\Adapter\Socket'
    );

    /**
     * Set up the test case
     */
    protected function setUp()
    {
        if (defined('TESTS_LAMINAS_HTTP_CLIENT_BASEURI')
            && (TESTS_LAMINAS_HTTP_CLIENT_BASEURI != false)
        ) {
            $this->baseuri = TESTS_LAMINAS_HTTP_CLIENT_BASEURI;
            $this->client  = new HTTPClient($this->baseuri);
        } else {
            // Skip tests
            $this->markTestSkipped("Laminas_Http_Client dynamic tests are not enabled in TestConfiguration.php");
        }
    }

    /**
     * Clean up the test environment
     *
     */
    protected function tearDown()
    {
        $this->client = null;
    }

    public function testHttpGet()
    {
        $this->client->setMethod(Request::METHOD_GET);
        $response= $this->client->send();
        $this->assertTrue($response->isSuccess());
    }

    public function testStaticHttpGet()
    {
//        $response= HTTPClient::get($this->baseuri);
//        $this->assertTrue($response->isSuccess());
    }

    public function testRequestHttpGet()
    {
        $client= new HTTPClient();
        $request= new Request();
        $request->setUri($this->baseuri);
        $request->setMethod(Request::METHOD_GET);
        $response= $client->send($request);
        $this->assertTrue($response->isSuccess());
    }

}
