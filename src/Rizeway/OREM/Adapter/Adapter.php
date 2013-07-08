<?php
namespace Rizeway\OREM\Adapter;

use Rizeway\OREM\Connection\Connection;
use Rizeway\OREM\Mapping\MappingEntity;
use Rizeway\OREM\Connection\ConnectionInterface;
use Rizeway\OREM\Mapping\Relation\MappingRelationInterface;
use Rizeway\OREM\Serializer\Serializer;

class Adapter implements AdapterInterface {
    protected $mapping;
    protected $serializer;
    protected $connection;

    /**
     * @param MappingEntity $mapping
     */
    public function setMappingEntity(MappingEntity $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * @param Serializer $serializer
     */
    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param Connection $connection
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param array $urlParameters
     * @return mixed
     */
    public function findQuery(array $urlParameters = array())
    {
        return $this->connection->query(
            ConnectionInterface::METHOD_GET,
            $this->mapping->getResourceUrl(),
            null,
            $urlParameters
        );
    }

    /**
     * @param $primaryKeyValue
     * @return mixed
     * @throws \Exception
     */
    public function find($primaryKeyValue)
    {
        if (is_null($this->mapping->getPrimaryKey())) {
            throw new \Exception('A field must be defined as primary key');
        }

        return $this->connection->query(
            ConnectionInterface::METHOD_GET,
            $this->mapping->getResourceUrl().'/'.$primaryKeyValue
        );
    }

    /**
     * @param MappingRelationInterface $relation
     * @param $primaryKeyValue
     * @return mixed
     * @throws \Exception
     */
    public function findRelation(MappingRelationInterface $relation, $primaryKeyValue)
    {
        if (is_null($this->mapping->getPrimaryKey())) {
            throw new \Exception('A field must be defined as primary key');
        }

        return $this->connection->query(
            ConnectionInterface::METHOD_GET,
            $this->mapping->getResourceUrl().'/'.$primaryKeyValue.'/'.$relation->getRemoteName()
        );
    }

    /**
     * @param $object
     * @throws \Exception
     */
    public function persist($object)
    {
        if (is_null($this->mapping->getPrimaryKey())) {
            throw new \Exception('A field must be defined as primary key');
        }

        return $this->connection->query(
            ConnectionInterface::METHOD_POST,
            $this->mapping->getResourceUrl(),
            $this->serializer->serializeEntity($object, $this->mapping->getName())
        );
    }

    /**
     * @param $object
     * @throws \Exception
     */
    public function update($object)
    {
        if (is_null($this->mapping->getPrimaryKey())) {
            throw new \Exception('A field must be defined as primary key');
        }

        $helper = new EntityHelper($this->mapping);

        return $this->connection->query(
            ConnectionInterface::METHOD_PUT,
            $this->mapping->getResourceUrl().'/'.$helper->getPrimaryKey($object),
            $this->serializer->serializeEntity($object, $this->mapping->getName())
        );
    }

    /**
     * @param $object
     * @return mixed
     */
    public function remove($object)
    {
        $mapping = $this->getMappingForObject($object);
        $helper = new EntityHelper($mapping);
        
        return $this->connection->query(ConnectionInterface::METHOD_DELETE, $mapping->getResourceUrl().'/'.
            $helper->getPrimaryKey($object));
    }
}
