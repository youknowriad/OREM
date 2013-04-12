<?php

namespace Rizeway\OREM\Mapping\Relation;

use Rizeway\OREM\Mapping\Field\MappingField;

class MappingRelation extends MappingField implements MappingRelationInterface
{
    /**
     * @var string
     */
    protected $entityName;

    /**
     * @var bool
     */
    protected $lazy;

    public function __construct($entityName, $fieldname, $remotename = null, $lazy = false)
    {
        $this->entityName = $entityName;
        $this->lazy = $lazy;

        parent::__construct($fieldname, $remotename);
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * @return bool
     */
    public function isLazy()
    {
        return $this->lazy;
    }
}
