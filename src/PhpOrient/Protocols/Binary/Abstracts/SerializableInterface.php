<?php

namespace PhpOrient\Protocols\Binary\Abstracts;

interface SerializableInterface
{
    /**
     * Return a representation of the class that can be serialized as an
     * OrientDB record.
     *
     * @return mixed
     */
    public function recordSerialize();
}
