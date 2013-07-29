<?php

namespace Rizeway\OREM\Store;

use Rizeway\OREM\Entity\EntityHelper;

class Store
{
    /**
     * @var \Rizeway\OREM\Mapping\MappingEntity[]
     */
    protected $mappings;

    /**
     * @var object[][]
     */
    protected $entities = array();

    /**
     * @param \Rizeway\OREM\Mapping\MappingEntity[] $mappings
     */
    public function __construct($mappings)
    {
        $this->mappings = $mappings;
    }

    /**
     * @param object $entity
     */
    public function addEntity($entity)
    {
        $mapping = $this->getMappingForObject($entity);
        $helper = new EntityHelper($mapping);

        $this->entities[$mapping->getName()][$helper->getPrimaryKey($entity)] = $entity;
    }

    public function removeEntity($entity)
    {
        $mapping = $this->getMappingForObject($entity);
        $helper = new EntityHelper($mapping);
        $primaryKey = $helper->getPrimaryKey($entity);

        if ($this->hasEntity($mapping->getName(), $primaryKey)) {
            unset($this->entities[$mapping->getName()][$primaryKey]);
        }
    }

    /**
     * @param  string     $name
     * @param  string     $primaryKey
     * @return object
     * @throws \Exception
     */
    public function getEntity($name, $primaryKey)
    {
        if (!isset($this->entities[$name]) || !isset($this->entities[$name][$primaryKey])) {
            throw new \Exception(sprintf('The entity "%s" of PK "%s" is not in the store', $name, $primaryKey));
        }

        return $this->entities[$name][$primaryKey];
    }

    /**
     * @param  string $name
     * @param  string $primaryKey
     * @return bool
     */
    public function hasEntity($name, $primaryKey)
    {
        return isset($this->entities[$name]) && isset($this->entities[$name][$primaryKey]);
    }

    /**
     * @param $object
     * @return \Rizeway\OREM\Mapping\MappingEntity[]
     * @throws \Exception
     */
    public function getMappingForObject($object)
    {
        foreach ($this->mappings as $mapping) {
            if (is_a($object, $mapping->getClassname())) {
                return $mapping;
            }
        }

        throw new \Exception('No Mapping Entity found for class : '. get_class($object));
    }
}
