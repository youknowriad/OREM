<?php

namespace Rizeway\OREM\Mapping;

class MappingEntity
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $classname;

    /**
     * @var \Rizeway\OREM\Mapping\Field\MappingFieldInterface[]
     */
    protected $fieldMappings;

    /**
     * @var \Rizeway\OREM\Mapping\Relation\MappingRelationInterface[]
     */
    protected $hasManyMappings;

    /**
     * @var \Rizeway\OREM\Mapping\Relation\MappingRelationInterface[]
     */
    protected $hasOneMappings;

    /**
     * @var string
     */
    protected $primaryKey;

    /**
     * @param string $name
     * @param string $classname
     * @param string $primaryKey
     * @param \Rizeway\OREM\Mapping\Field\MappingFieldInterface[] $fieldMappings
     * @param \Rizeway\OREM\Mapping\Relation\MappingRelationInterface[] $hasManyMappings
     * @param \Rizeway\OREM\Mapping\Relation\MappingRelationInterface[] $hasOneMappings
     */
    public function __construct($name, $classname, $primaryKey, $fieldMappings,
        $hasManyMappings = array(), $hasOneMappings = array())
    {
        $this->name            = $name;
        $this->classname       = $classname;
        $this->primaryKey      = $primaryKey;
        $this->fieldMappings   = $fieldMappings;
        $this->hasManyMappings = $hasManyMappings;
        $this->hasOneMappings  = $hasOneMappings;
    }

    /**
     * @return string
     */
    public function getResourceUrl()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getClassname()
    {
        return $this->classname;
    }

    /**
     * @return \Rizeway\OREM\Mapping\Field\MappingFieldInterface[]
     */
    public function getFieldMappings()
    {
        return $this->fieldMappings;
    }

    /**
     * @return \Rizeway\OREM\Mapping\Relation\MappingRelationInterface[]
     */
    public function getHasManyMappings()
    {
        return $this->hasManyMappings;
    }

    /**
     * @return \Rizeway\OREM\Mapping\Relation\MappingRelationInterface[]
     */
    public function getHasOneMappings()
    {
        return $this->hasOneMappings;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    public function getRemotePrimaryKey()
    {
        foreach ($this->fieldMappings as $mapping) {
            if ($mapping->getFieldName() == $this->getPrimaryKey()) {
                return $mapping->getRemoteName();
            }
        }

        throw new \Exception('No mapping found for primary Key in entity : '.$this->name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
