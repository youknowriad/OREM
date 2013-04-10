<?php

namespace Rizeway\OREM\Mapping\Field;

use Rizeway\OREM\Mapping\MappingInterface;

interface MappingFieldInterface extends MappingInterface
{
    /**
     * @param mixed $value
     * @return mixed
     */
    public function serializeField($value);

    /**
     * @param mixed $value
     * @return mixed
     */
    public function unserializeField($value);
}
