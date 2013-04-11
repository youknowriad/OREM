<?php

namespace Rizeway\OREM\Mapping\Relation;

use Rizeway\OREM\Mapping\Field\MappingField;

class MappingRelation extends MappingField implements MappingRelationInterface
{
    /**
     * @var string
     */
    protected $entityName;

    public function __construct($entityName, $fieldname, $remotename = null)
    {
        $this->entityName = $entityName;

        parent::__construct($fieldname, $remotename);
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return $this->entityName;
    }
}
