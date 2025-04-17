<?php

namespace Laminas\Http\Header;

use function explode;
use function implode;
use function str_contains;

class GenericMultiHeader extends GenericHeader implements MultipleHeaderInterface
{
    /**
     * @param string $headerLine
     * @return static|static[]
     */
    public static function fromString($headerLine)
    {
        [$fieldName, $fieldValue] = GenericHeader::splitHeaderLine($headerLine);

        if (str_contains($fieldValue, ',')) {
            $headers = [];
            foreach (explode(',', $fieldValue) as $multiValue) {
                $headers[] = new static($fieldName, $multiValue);
            }
            return $headers;
        } else {
            return new static($fieldName, $fieldValue);
        }
    }

    /**
     * @return string
     */
    public function toStringMultipleHeaders(array $headers)
    {
        $name   = $this->getFieldName();
        $values = [$this->getFieldValue()];
        /** @var GenericHeader $header */
        foreach ($headers as $header) {
            if (! $header instanceof static) {
                throw new Exception\InvalidArgumentException(
                    'This method toStringMultipleHeaders was expecting an array of headers of the same type'
                );
            }
            $values[] = $header->getFieldValue();
        }
        return $name . ': ' . implode(',', $values) . "\r\n";
    }
}
