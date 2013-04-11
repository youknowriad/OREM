<?php

namespace Rizeway\OREM\Mapping\Relation;

use Rizeway\OREM\Mapping\MappingInterface;

interface MappingRelationInterface extends MappingInterface
{
    /**
     * @return string
     */
    public function getEntityName();
}
