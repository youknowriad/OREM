<?php
namespace Rizeway\OREM\Adapter;

use Rizeway\OREM\Mapping\MappingEntity;
use Rizeway\OREM\Connection\ConnectionInterface;
use Rizeway\OREM\Mapping\Relation\MappingRelationInterface;
use Rizeway\OREM\Mapping\Relation\MappingRelationHasOne;
use Rizeway\OREM\Mapping\Relation\MappingRelationHasMany;

class Adapter implements AdapterInterface {
    protected $mapping;

    function __construct(MappingEntity $mapping) {
        $this->mapping = $mapping;
    }

    public function findQuery(ConnectionInterface $connection, array $urlParameters = array())
    {
        return $connection->query(
            ConnectionInterface::METHOD_GET,
            $this->mapping->getResourceUrl(),
            null,
            $urlParameters
        );
    }

    public function find(ConnectionInterface $connection, $primaryKeyValue)
    {
        if (is_null($this->mapping->getPrimaryKey())) {
            throw new \Exception('A field must be defined as primary key');
        }

        return $connection->query(
            ConnectionInterface::METHOD_GET,
            $this->mapping->getResourceUrl().'/'.$primaryKeyValue
        );
    }

    public function findRelation(ConnectionInterface $connection, MappingRelationInterface $relation, $primaryKeyValue, array $urlParameters = array())
    {
        if (is_null($this->mapping->getPrimaryKey())) {
            throw new \Exception('A field must be defined as primary key');
        }

        return $connection->query(
            ConnectionInterface::METHOD_GET,
            $this->mapping->getResourceUrl().'/'.$primaryKeyValue.'/'.$relation->getRemoteName(),
            null,
            $urlParameters
        );
    }
}
