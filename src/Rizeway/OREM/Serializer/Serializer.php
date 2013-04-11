<?php

namespace Rizeway\OREM\Serializer;

use Rizeway\OREM\Entity\EntityHelper;
use Rizeway\OREM\Store\Store;

class Serializer
{
    /**
     * @var \Rizeway\OREM\Mapping\MappingEntity[]
     */
    protected $mappings;

    /**
     * @var \Rizeway\OREM\Store\Store
     */
    protected $store;

    /**
     * @param \Rizeway\OREM\Mapping\MappingEntity[] $mappings
     * @param Store $store
     */
    public function __construct($mappings, Store $store)
    {
        $this->mappings = $mappings;
        $this->store    = $store;
    }

    /**
     * @param array $serial
     * @param string $name
     * @return object
     */
    public function unserializeEntity(array $serial, $name)
    {
        $mapping = $this->getMappingForEntity($name);
        $primaryKey = $serial[$mapping->getRemotePrimaryKey()];
        if ($this->store->hasEntity($name, $primaryKey)) {
            $object = $this->store->getEntity($name, $primaryKey);
            $this->updateEntity($object, $serial, $name);
        } else {
            $object = $this->constructEntity($name);
            $this->updateEntity($object, $serial, $name);
            $this->store->addEntity($object);
        }

        return $object;
    }

    /**
     * @param object $object
     * @param string $name
     * @return array
     * @throws \Exception
     */
    public function serializeEntity($object, $name)
    {
        $mapping = $this->getMappingForEntity($name);
        $helper = new EntityHelper($mapping);
        $serial = array();

        foreach ($mapping->getFieldMappings() as $fieldMapping) {
            $serial[$fieldMapping->getRemoteName()] = $helper->getPropertyValue($object, $fieldMapping->getFieldName());
        }

        foreach ($mapping->getHasManyMappings() as $relationMapping) {
            $propertyValue = $helper->getPropertyValue($object, $relationMapping->getFieldName());
            $serial[$relationMapping->getRemoteName()] = null;
            if (!is_null($propertyValue)) {
                $serial[$relationMapping->getRemoteName()] =  array();
                foreach ($propertyValue as $subEntity) {
                    $serial[$relationMapping->getRemoteName()][] = $this->serializeEntity($subEntity,
                        $relationMapping->getEntityName());
                }
            }
        }

        foreach ($mapping->getHasOneMappings() as $relationMapping) {
            $propertyValue = $helper->getPropertyValue($object, $relationMapping->getFieldName());
            $serial[$relationMapping->getRemoteName()] = is_null($propertyValue) ? null : $this->serializeEntity($propertyValue,
                $relationMapping->getEntityName());
        }

        return $serial;
    }

    /**
     * @param $object
     * @param $serial
     * @param $name
     */
    public function updateEntity($object, $serial, $name)
    {
        $mapping = $this->getMappingForEntity($name);
        $helper = new EntityHelper($mapping);

        foreach ($mapping->getFieldMappings() as $fieldMapping) {
            if (isset($serial[$fieldMapping->getRemoteName()])) {
                $helper->setPropertyValue($object, $fieldMapping->getFieldName(),
                    $fieldMapping->unserializeField($serial[$fieldMapping->getRemoteName()]));
            }
        }

        foreach ($mapping->getHasManyMappings() as $relationMapping) {
            if (isset($serial[$relationMapping->getRemoteName()])) {
                $subEntities = array();
                foreach ($serial[$relationMapping->getRemoteName()] as $subEntitySerial)
                {
                    $subEntities[] = $this->unserializeEntity($subEntitySerial, $relationMapping->getEntityName());
                }
                $helper->setPropertyValue($object, $relationMapping->getFieldName(), $subEntities);
            }
        }

        foreach ($mapping->getHasOneMappings() as $relationMapping) {
            if (isset($serial[$relationMapping->getRemoteName()])) {
                $helper->setPropertyValue($object, $relationMapping->getFieldName(),
                    $this->unserializeEntity($serial[$relationMapping->getRemoteName()], $relationMapping->getEntityName()));
            }
        }
    }

    /**
     * @param string $name
     * @return object
     */
    protected function constructEntity($name)
    {
        $mapping = $this->getMappingForEntity($name);
        $classname = $mapping->getClassname();

        return unserialize(sprintf('O:%d:"%s":0:{}', strlen($classname), $classname));
    }

    /**
     * @param $entityName
     * @return \Rizeway\OREM\Mapping\MappingEntity
     * @throws \Exception
     */
    protected function getMappingForEntity($entityName)
    {
        if (!isset($this->mappings[$entityName])) {
            throw new \Exception('Unknown Entity : ' . $entityName);
        }

        return $this->mappings[$entityName];
    }
}
