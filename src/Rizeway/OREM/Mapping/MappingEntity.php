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
     * @var MappingInterface[]
     */
    protected $mappings;

    /**
     * @var string
     */
    protected $primaryKey;

    /**
     * @param string $name
     * @param string $classname
     * @param MappingInterface[] $mappings
     * @param string $primaryKey
     */
    public function __construct($name, $classname, $mappings, $primaryKey)
    {
        $this->name       = $name;
        $this->classname  = $classname;
        $this->mappings   = $mappings;
        $this->primaryKey = $primaryKey;
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
     * @return MappingInterface[]
     */
    public function getMappings()
    {
        return $this->mappings;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }
}
