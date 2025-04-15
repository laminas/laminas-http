<?php

namespace Laminas\Http\Header\Accept\FieldValuePart;

/**
 * Field Value Part
 *
 * @see        http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
 */
class LanguageFieldValuePart extends AbstractFieldValuePart
{
    public function getLanguage(): string
    {
        return (string) $this->getInternalValues()->typeString;
    }

    public function getPrimaryTag(): string
    {
        return (string) $this->getInternalValues()->type;
    }

    public function getSubTag(): string
    {
        return (string) $this->getInternalValues()->subtype;
    }
}
