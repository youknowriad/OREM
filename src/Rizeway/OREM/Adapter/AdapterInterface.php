<?php
namespace Rizeway\OREM\Adapter;

use Rizeway\OREM\Mapping\Relation\MappingRelationInterface;

interface AdapterInterface {
    function findQuery(array $urlParameters = array());
    function find($primaryKeyValue);
    function findRelation(MappingRelationInterface $relation, $primaryKeyValue);
    function persist($object);
    function update($object);
    function remove($object);
}
