<?php

namespace Rizeway\OREM\Mapping;

interface MappingInterface
{
    /**
     * @return string
     */
    public function getFieldName();

    /**
     * @return string
     */
    public function getRemoteName();
}
