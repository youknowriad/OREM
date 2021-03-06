<?php

namespace Rizeway\OREM\Mapping\Field;

class MappingFieldBoolean extends MappingField implements MappingFieldInterface
{
    /**
     * @param mixed $value
     * @return mixed
     */
    public function serializeField($value)
    {
        return $value;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function unserializeField($value)
    {
        return (bool) $value;
    }
}
