<?php
namespace Rizeway\OREM\Adapter;

use Rizeway\OREM\Connection\ConnectionInterface;
use Rizeway\OREM\Mapping\Relation\MappingRelationInterface;

interface AdapterInterface {
    function findQuery(ConnectionInterface $connection, array $urlParameters = array());
    function find(ConnectionInterface $connection, $primaryKeyValue);
    function findRelation(ConnectionInterface $connection, MappingRelationInterface $relation, $primaryKeyValue, array $urlParameters = array());
}
