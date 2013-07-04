<?php

namespace Rizeway\OREM\Entity;

use Rizeway\OREM\Mapping\MappingEntity;

class EntityHelper
{
    /**
     * @var \Rizeway\OREM\Mapping\MappingEntity
     */
    protected $mapping;

    /**
     * @param MappingEntity $mapping
     */
    public function __construct(MappingEntity $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * @param $object
     * @param $propertyName
     * @return mixed
     */
    public function getAccessiblePropertyValue($object, $propertyName)
    {
        $reflection = new \ReflectionProperty($object, $propertyName);
        if ($reflection->isPublic()) {
            return $object->$propertyName;
        } else {
            $method = 'get'.ucfirst($this->camelize($propertyName));

            return $object->$method();
        }
    }

    /**
     * @param $object
     * @param $propertyName
     * @return mixed
     */
    public function getPropertyValue($object, $propertyName)
    {
        $reflect = new \ReflectionObject($object);
        $property = $reflect->getProperty($propertyName);
        $accessible = $property->isPublic();
        $property->setAccessible(true);
        $value = $property->getValue($object);
        $property->setAccessible($accessible);

        return $value;
    }

    /**
     * @param $object
     * @param $propertyName
     * @param $value
     */
    public function setPropertyValue($object, $propertyName, $value)
    {
        $reflect = new \ReflectionObject($object);
        $property = $reflect->getProperty($propertyName);
        $accessible = $property->isPublic();
        $property->setAccessible(true);
        $property->setValue($object, $value);
        $property->setAccessible($accessible);
    }

    /**
     * @param $object
     * @return mixed
     */
    public function getPrimaryKey($object)
    {
        return $this->getAccessiblePropertyValue($object, $this->mapping->getPrimaryKey());
    }

    /**
     * @param $string
     * @return mixed
     */
    protected function camelize($string)
    {
        return preg_replace_callback('/(^|_|\.)+(.)/', function ($match) { return ('.' === $match[1] ? '_' : '').strtoupper($match[2]); }, $string);
    }
}
