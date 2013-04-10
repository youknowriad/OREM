<?php

namespace Rizeway\OREM;

use Rizeway\OREM\Exception\ExceptionNotFound;
use Rizeway\OREM\Repository\Repository;
use Rizeway\OREM\Connection\ConnectionInterface;
use Rizeway\OREM\Serializer\Serializer;

class Manager
{
    /**
     * @var \Rizeway\OREM\Connection\ConnectionInterface
     */
    protected $connection;

    /**
     * @var array|Mapping\MappingEntity[]
     */
    protected $mappings;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @param ConnectionInterface $connection
     * @param \Rizeway\OREM\Mapping\MappingEntity[] $mappings
     */
    public function __construct(ConnectionInterface $connection, array $mappings)
    {
        $this->connection = $connection;
        $this->mappings   = $mappings;
        $this->serializer = new Serializer($mappings);
    }

    /**
     * @param string $entityName
     * @return Repository
     * @throws \Exception
     */
    public function getRepository($entityName)
    {
        $this->getMappingForEntity($entityName);

        return new Repository($this, $entityName);
    }

    /**
     * @param $object
     */
    public function persist($object)
    {
        $mapping = $this->getMappingForObject($object);
        $result = $this->connection->query(
            ConnectionInterface::METHOD_POST,
            $mapping->getResourceUrl(),
            $this->serializer->serializeEntity($object, $mapping->getName())
        );
        $this->serializer->updateEntity($object, $result, $mapping->getName());
    }

    /**
     * @param $object
     */
    public function update($object)
    {
        $mapping = $this->getMappingForObject($object);
        $result = $this->connection->query(
            ConnectionInterface::METHOD_PUT,
            $mapping->getResourceUrl().'/'.$this->getPropertyValue($object, $mapping->getPrimaryKey()),
            $this->serializer->serializeEntity($object, $mapping->getName())
        );

        $this->serializer->updateEntity($object, $result, $mapping->getName());
    }

    /**
     * @param string $entityName
     * @return object[]
     * @throws \Exception
     */
    public function findAll($entityName)
    {
        $mapping = $this->getMappingForEntity($entityName);
        $results = $this->connection->query(ConnectionInterface::METHOD_GET, $mapping->getResourceUrl());
        $entities = array();
        foreach ($results as $result) {
            $entities[] = $this->serializer->unserializeEntity($result, $mapping->getName());
        }

        return $entities;
    }

    /**
     * @param string $entityName
     * @param mixed $primaryKeyValue
     * @return object|null
     * @throws \Exception
     */
    public function find($entityName, $primaryKeyValue)
    {
        $mapping = $this->getMappingForEntity($entityName);

        try {
            $result = $this->connection->query(ConnectionInterface::METHOD_GET, $mapping->getResourceUrl().'/'.$primaryKeyValue);

            return $this->serializer->unserializeEntity($result, $mapping->getName());
        } catch(ExceptionNotFound $e) {
            return null;
        }
    }

    /**
     * @param $entityName
     * @return Mapping\MappingEntity
     * @throws \Exception
     */
    protected function getMappingForEntity($entityName)
    {
        if (!isset($this->mappings[$entityName])) {
            throw new \Exception('Unknown Entity : ' . $entityName);
        }

        return $this->mappings[$entityName];
    }

    /**
     * @param object $object
     * @throws \Exception
     */
    public function remove($object)
    {
        $mapping = $this->getMappingForObject($object);
        $this->connection->query(ConnectionInterface::METHOD_DELETE, $mapping->getResourceUrl().'/'.
            $this->getPropertyValue($object, $mapping->getPrimaryKey()));
    }

    /**
     * @param $object
     * @return Mapping\MappingEntity
     * @throws \Exception
     */
    protected function getMappingForObject($object)
    {
        foreach ($this->mappings as $mapping) {
            if (is_a($object, $mapping->getClassname())) {
                return $mapping;
            }
        }

        throw new \Exception('No Mapping Entity found for class : '. get_class($object));
    }

    protected function getPropertyValue($object, $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        if ($reflection->isPublic()) {
            return $object->$property;
        } else {
            $method = 'get'.ucfirst($this->camelize($property));

            return $object->$method();
        }
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
