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
        if (!isset($this->mappings[$entityName])) {
            throw new \Exception('Unknown Entity : '.$entityName);
        }

        return new Repository($this, $entityName);
    }

    /**
     * @param $object
     */
    public function persist($object)
    {
        $mapping = $this->getMappingForObject($object);
        $this->connection->query(
            ConnectionInterface::METHOD_POST,
            $mapping->getResourceUrl(),
            $this->serializer->serializeEntity($object, $mapping->getName())
        );
    }

    /**
     * @param $object
     */
    public function update($object)
    {
        $mapping = $this->getMappingForObject($object);
        $method = 'get'.ucfirst($mapping->getPrimaryKey());
        $result = $this->connection->query(
            ConnectionInterface::METHOD_PUT,
            $mapping->getResourceUrl().'/'.$object->$method(),
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
        if (!isset($this->mappings[$entityName])) {
            throw new \Exception('Unknown Entity : '.$entityName);
        }

        $results = $this->connection->query(ConnectionInterface::METHOD_GET, $this->mappings[$entityName]->getResourceUrl());
        $entities = array();
        foreach ($results as $result) {
            $entities[] = $this->serializer->unserializeEntity($result, $this->mappings[$entityName]->getName());
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
        if (!isset($this->mappings[$entityName])) {
            throw new \Exception('Unknown Entity : '.$entityName);
        }

        try {
            $result = $this->connection->query(ConnectionInterface::METHOD_GET, $this->mappings[$entityName]->getResourceUrl().'/'.$primaryKeyValue);

            return $this->serializer->unserializeEntity($result, $this->mappings[$entityName]->getName());
        } catch(ExceptionNotFound $e) {
            return null;
        }
    }

    /**
     * @param object $object
     * @throws \Exception
     */
    public function remove($object)
    {
        $mapping = $this->getMappingForObject($object);
        $method = 'get'.ucfirst($mapping->getPrimaryKey());
        $primaryKeyValue = $object->$method();

        $this->connection->query(ConnectionInterface::METHOD_DELETE, $mapping->getResourceUrl().'/'.$primaryKeyValue);
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

    /**
     * @param $string
     * @return mixed
     */
    public static function camelize($string)
    {
        return preg_replace_callback('/(^|_|\.)+(.)/', function ($match) { return ('.' === $match[1] ? '_' : '').strtoupper($match[2]); }, $string);
    }
}
