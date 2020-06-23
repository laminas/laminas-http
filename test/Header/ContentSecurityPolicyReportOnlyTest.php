<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\ContentSecurityPolicyReportOnly;
use PHPUnit\Framework\TestCase;

class ContentSecurityPolicyReportOnlyTest extends TestCase
{
    public function testContentSecurityPolicyReportOnlyToString()
    {
        $csp = ContentSecurityPolicyReportOnly::fromString(
            "Content-Security-Policy-Report-Only: default-src 'none'; img-src 'self' https://*.gravatar.com;"
        );
        $this->assertEquals(
            "Content-Security-Policy-Report-Only: default-src 'none'; img-src 'self' https://*.gravatar.com;",
            $csp->toString()
        );
    }
}
