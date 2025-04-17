<?php

namespace Laminas\Http\Header;

use function array_key_exists;
use function implode;
use function is_bool;
use function ksort;
use function preg_match;
use function rtrim;
use function sprintf;
use function strlen;
use function strtolower;
use function substr;
use function trim;

/**
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.9
 *
 * @throws Exception\InvalidArgumentException
 */
class CacheControl implements HeaderInterface
{
    /** @var string */
    protected $value;

    /**
     * Array of Cache-Control directives
     *
     * @var array
     */
    protected $directives = [];

    /**
     * Creates a CacheControl object from a headerLine
     *
     * @param string $headerLine
     * @throws Exception\InvalidArgumentException
     * @return static
     */
    public static function fromString($headerLine)
    {
        [$name, $value] = GenericHeader::splitHeaderLine($headerLine);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'cache-control') {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid header line for Cache-Control string: "%s"',
                $name
            ));
        }

        HeaderValue::assertValid($value);
        $directives = static::parseValue($value);

        // @todo implementation details
        $header = new static();
        /**
         * @var string $key
         * @var bool|string $value
         */
        foreach ($directives as $key => $value) {
            $header->addDirective($key, $value);
        }

        return $header;
    }

    /**
     * Required from HeaderDescription interface
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'Cache-Control';
    }

    /**
     * Checks if the internal directives array is empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->directives);
    }

    /**
     * Add a directive
     * For directives like 'max-age=60', $value = '60'
     * For directives like 'private', use the default $value = true
     *
     * @param string $key
     * @param string|bool $value
     * @return $this
     */
    public function addDirective($key, $value = true)
    {
        HeaderValue::assertValid($key);
        if (! is_bool($value)) {
            HeaderValue::assertValid($value);
        }
        $this->directives[$key] = $value;
        return $this;
    }

    /**
     * Check the internal directives array for a directive
     *
     * @param string $key
     * @return bool
     */
    public function hasDirective($key)
    {
        return array_key_exists($key, $this->directives);
    }

    /**
     * Fetch the value of a directive from the internal directive array
     *
     * @param string $key
     * @return bool|null
     */
    public function getDirective($key)
    {
        return array_key_exists($key, $this->directives) ? (bool) $this->directives[$key] : null;
    }

    /**
     * Remove a directive
     *
     * @param string $key
     * @return $this
     */
    public function removeDirective($key)
    {
        unset($this->directives[$key]);
        return $this;
    }

    /**
     * Assembles the directives into a comma-delimited string
     *
     * @return string
     */
    public function getFieldValue()
    {
        $parts = [];
        ksort($this->directives);
        /**
         * @var string $key
         * @var string|bool $value
         */
        foreach ($this->directives as $key => $value) {
            if (true === $value) {
                $parts[] = $key;
            } else {
                if (preg_match('#[^a-zA-Z0-9._-]#', (string) $value)) {
                    $value = '"' . (string) $value . '"';
                }
                $parts[] = $key . '=' . (string) $value;
            }
        }
        return implode(', ', $parts);
    }

    /**
     * Returns a string representation of the HTTP Cache-Control header
     *
     * @return string
     */
    public function toString()
    {
        return 'Cache-Control: ' . $this->getFieldValue();
    }

    /**
     * Internal function for parsing the value part of a
     * HTTP Cache-Control header
     *
     * @param string $value
     * @throws Exception\InvalidArgumentException
     * @return array
     */
    protected static function parseValue($value)
    {
        $value = trim($value);

        $directives = [];

        // handle empty string early so we don't need a separate start state
        if ($value === '') {
            return $directives;
        }

        $lastMatch = '';

        // phpcs:disable Generic.PHP.DiscourageGoto.Found

        while (true) {
            // Match a directive
            $matchResult = static::match(['[a-zA-Z][a-zA-Z_-]*'], $value, $lastMatch);
            if ($matchResult === 0) {
                $directive = $lastMatch;
            } else {
                throw new Exception\InvalidArgumentException('expected DIRECTIVE');
            }

            // Handle the directive's value
            $matchResult = static::match(['="[^"]*"', '=[^",\s;]*'], $value, $lastMatch);
            if ($matchResult === 0) {
                $directives[$directive] = substr($lastMatch, 2, -1);
            } elseif ($matchResult === 1) {
                $directives[$directive] = rtrim(substr($lastMatch, 1));
            } else {
                $directives[$directive] = true;
            }

            // Check for separator or end
            $matchResult = static::match(['\s*,\s*', '$'], $value, $lastMatch);
            if ($matchResult === 0) {
                // More directives to parse
                continue;
            } elseif ($matchResult === 1) {
                // End of parsing
                return $directives;
            } else {
                throw new Exception\InvalidArgumentException('expected SEPARATOR or END');
            }
        }

        // phpcs:enable
    }

    /**
     * Internal function used by parseValue to match tokens
     *
     * @param array $tokens
     * @param string $string
     * @param string $lastMatch
     * @return array-key
     */
    protected static function match($tokens, &$string, &$lastMatch)
    {
        // Ensure we have a string
        $value = $string;

        /**
         * @var string $token
         */
        foreach ($tokens as $i => $token) {
            if (preg_match('/^' . $token . '/', $value, $matches)) {
                $lastMatch = $matches[0];
                $string    = substr($value, strlen($matches[0]));
                return $i;
            }
        }
        return -1;
    }
}
