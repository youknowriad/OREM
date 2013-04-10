<?php

namespace Rizeway\OREM\Serializer;

class Serializer
{
    /**
     * @var \Rizeway\OREM\Mapping\MappingEntity[]
     */
    protected $mappings;

    /**
     * @param \Rizeway\OREM\Mapping\MappingEntity[] $mappings
     */
    public function __construct($mappings)
    {
        $this->mappings = $mappings;
    }

    /**
     * @param array $serial
     * @param string $name
     * @return object
     * @throws \Exception
     */
    public function unserializeEntity(array $serial, $name)
    {
        $mapping = $this->getMappingForEntity($name);
        $classname = $mapping->getClassname();
        $object = unserialize(sprintf('O:%d:"%s":0:{}', strlen($classname), $classname));
        $this->updateEntity($object, $serial, $name);

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
        $serial = array();
        $refelct = new \ReflectionClass($object);
        foreach ($mapping->getMappings() as $fieldMapping) {
            $property = $refelct->getProperty($fieldMapping->getFieldName());
            $accessible = $property->isPublic();
            $property->setAccessible(true);
            $serial[$fieldMapping->getRemoteName()] = $fieldMapping->serializeField($property->getValue($object));
            $property->setAccessible($accessible);
        }

        return $serial;
    }

    /**
     * @param object $object
     * @param array  $serial
     * @param string $name
     * @throws \Exception
     */
    public function updateEntity($object, $serial, $name)
    {
        $mapping = $this->getMappingForEntity($name);
        $refelct = new \ReflectionClass($object);
        foreach ($mapping->getMappings() as $fieldMapping) {
            if (isset($serial[$fieldMapping->getRemoteName()])) {
                $property = $refelct->getProperty($fieldMapping->getFieldName());
                $accessible = $property->isPublic();
                $property->setAccessible(true);
                $property->setValue($object, $fieldMapping->unserializeField($serial[$fieldMapping->getRemoteName()]));
                $property->setAccessible($accessible);
            }
        }
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
