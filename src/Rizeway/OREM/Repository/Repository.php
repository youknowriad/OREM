<?php

namespace Rizeway\OREM\Repository;


use Rizeway\OREM\Connection\ConnectionInterface;
use Rizeway\OREM\Mapping\MappingEntity;

class Repository
{
    /**
     * @var \Rizeway\OREM\Connection\ConnectionInterface
     */
    protected $connection;

    /**
     * @var \Rizeway\OREM\Mapping\MappingEntity
     */
    protected $mapping;

    /**
     * @param ConnectionInterface $connection
     * @param MappingEntity $mapping
     */
    public function __construct(ConnectionInterface $connection, MappingEntity $mapping)
    {
        $this->connection = $connection;
        $this->mapping    = $mapping;
    }

    /**
     * @return object[]
     */
    public function findAll()
    {
        $results = $this->connection->query(ConnectionInterface::METHOD_GET, $this->mapping->getResourceUrl());
        $entities = array();
        foreach ($results as $result) {
            $entities[] = $this->hydrate($result);
        }

        return $entities;
    }

    /**
     * @param $primaryKey
     * @return null|object
     */
    public function find($primaryKey)
    {
        try {
            $result = $this->connection->query(ConnectionInterface::METHOD_GET, $this->mapping->getResourceUrl().'/'.$primaryKey);

            return $this->hydrate($result);
        } catch(\Exception $e) {
            return null;
        }
    }

    /**
     * @param $object
     */
    public function remove($object)
    {
        $method = 'get'.$this->camelize($this->mapping->getPrimaryKey());
        $this->connection->query(ConnectionInterface::METHOD_DELETE, $this->mapping->getResourceUrl().'/'.$object->$method);
    }

    /**
     * @param $result
     * @return object
     */
    protected function hydrate($result)
    {
        $classname = $this->mapping->getClassname();
        $object = unserialize(sprintf('O:%d:"%s":0:{}', strlen($classname), $classname));
        $refelct = new \ReflectionClass($object);
        foreach ($this->mapping->getMappings() as $fieldMapping) {
            if (isset($result[$fieldMapping->getRemoteName()])) {
                $property = $refelct->getProperty($fieldMapping->getFieldName());
                $property->setAccessible(true);
                $property->setValue($object, $result[$fieldMapping->getRemoteName()]);
            }
        }

        return $object;
    }

    /**
     * @param $string
     * @return mixed
     */
    public static function camelize($string)
    {
        return preg_replace_callback('/(^|_|\.)+(.)/', function ($match) { return ('.' === $match[1] ? '_' : '').strtoupper($match[2]); }, $string);
    }
}
