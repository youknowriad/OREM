<?php

namespace Rizeway\OREM;

use Rizeway\OREM\Entity\EntityHelper;
use Rizeway\OREM\Exception\ExceptionNotFound;
use Rizeway\OREM\Repository\Repository;
use Rizeway\OREM\Connection\ConnectionInterface;
use Rizeway\OREM\Serializer\Serializer;
use Rizeway\OREM\Store\Store;

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
     * @var Store
     */
    protected $store;

    /**
     * @param ConnectionInterface $connection
     * @param \Rizeway\OREM\Mapping\MappingEntity[] $mappings
     */
    public function __construct(ConnectionInterface $connection, array $mappings)
    {
        $this->connection = $connection;
        $this->mappings   = $mappings;
        $this->store      = new Store($mappings);
        $this->serializer = new Serializer($this, $this->store);
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
        $helper = new EntityHelper($mapping);
        $result = $this->connection->query(
            ConnectionInterface::METHOD_PUT,
            $mapping->getResourceUrl().'/'.$helper->getPrimaryKey($object),
            $this->serializer->serializeEntity($object, $mapping->getName())
        );

        $this->serializer->updateEntity($object, $result, $mapping->getName());
    }

    /**
     * @param string $entityName
     * @param string[] $urlParameters
     * @return object[]
     * @throws \Exception
     */
    public function findQuery($entityName, array $urlParameters = array())
    {
        $mapping = $this->getMappingForEntity($entityName);
        $results = $this->connection->query(ConnectionInterface::METHOD_GET, $mapping->getResourceUrl(), null, $urlParameters);
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
            $entity = $this->serializer->unserializeEntity($result, $mapping->getName());

            return $entity;
        } catch(ExceptionNotFound $e) {
            return null;
        }
    }

    /**
     * @param string $entityName
     * @param mixed $primaryKeyValue
     * @param string $relationName
     * @return object|array
     * @throws \InvalidArgumentException
     */
    public function findRelation($entityName, $primaryKeyValue, $relationName)
    {
        $mapping = $this->getMappingForEntity($entityName);

        foreach($mapping->getHasOneMappings() as $relation) {
            if($relation->getFieldName() !== $relationName || $relation->isLazy() === false) {
                continue;
            }

            $result = $this->connection->query(ConnectionInterface::METHOD_GET, $mapping->getResourceUrl().'/'.$primaryKeyValue.'/'.$relation->getRemoteName());

            if(is_null($result) === false) {
                return $this->serializer->unserializeEntity($result, $relation->getEntityName());
            }
        }

        foreach($mapping->getHasManyMappings() as $relation) {
            if($relation->getFieldName() !== $relationName || $relation->isLazy() === false) {
                continue;
            }

            $results = $this->connection->query(ConnectionInterface::METHOD_GET, $mapping->getResourceUrl().'/'.$primaryKeyValue.'/'.$relation->getRemoteName());
            $entities = array();
            foreach ($results as $result) {
                $entities[] = $this->serializer->unserializeEntity($result, $relation->getEntityName());
            }

            return $entities;
        }

        throw new \InvalidArgumentException('Unknown relation : ' . $relationName);
    }

    /**
     * @param $entityName
     * @return Mapping\MappingEntity
     * @throws \Exception
     */
    public function getMappingForEntity($entityName)
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
        $helper = new EntityHelper($mapping);
        $this->connection->query(ConnectionInterface::METHOD_DELETE, $mapping->getResourceUrl().'/'.
            $helper->getPrimaryKey($object));
        $this->store->removeEntity($object);
    }

    /**
     * @param $object
     * @return Mapping\MappingEntity
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

	/**
	 * @return Connection\ConnectionInterface
	 */
	public function getConnection()
	{
		return $this->connection;
	}
}
