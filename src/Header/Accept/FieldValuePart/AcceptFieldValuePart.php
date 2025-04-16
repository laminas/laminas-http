<?php

namespace Laminas\Http\Header\Accept\FieldValuePart;

/**
 * Field Value Part
 *
 * @see        http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
 */
class AcceptFieldValuePart extends AbstractFieldValuePart
{
    public function getSubtype()
    {
        return (string) $this->getInternalValues()->subtype;
    }

    public function getSubtypeRaw()
    {
        return (string) $this->getInternalValues()->subtypeRaw;
    }

    public function getFormat()
    {
        return (string) $this->getInternalValues()->format;
    }
}
