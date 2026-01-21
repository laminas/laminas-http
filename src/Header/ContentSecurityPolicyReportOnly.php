<?php

namespace Laminas\Http\Header;

/**
 * Content Security Policy Level 3 Header
 *
 * @link http://www.w3.org/TR/CSP/
 */
class ContentSecurityPolicyReportOnly extends ContentSecurityPolicy
{
    /**
     * Valid directive names
     */
    protected array $validDirectiveNames = [
        // As per http://www.w3.org/TR/CSP/#directives
        // Fetch directives
        'child-src',
        'connect-src',
        'default-src',
        'font-src',
        'frame-src',
        'img-src',
        'manifest-src',
        'media-src',
        'object-src',
        'script-src',
        'script-src-elem',
        'script-src-attr',
        'style-src',
        'style-src-elem',
        'style-src-attr',
        'worker-src',

        // Document directives
        'base-uri',

        // Navigation directives
        'form-action',
        'frame-ancestors',
        'navigate-to',

        // Reporting directives
        'report-uri',
        'report-to',

        // Other directives
        'require-trusted-types-for',
        'trusted-types',
    ];

    /**
     * Get the header name
     */
    public function getFieldName(): string
    {
        return 'Content-Security-Policy-Report-Only';
    }
}
