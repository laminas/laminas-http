<?php

declare(strict_types=1);

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
