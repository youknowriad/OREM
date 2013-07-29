<?php
namespace Rizeway\OREM\Adapter;

use Rizeway\OREM\Mapping\Relation\MappingRelationInterface;

interface AdapterInterface
{
    public function findQuery(array $urlParameters = array());
    public function find($primaryKeyValue);
    public function findRelation(MappingRelationInterface $relation, $primaryKeyValue);
    public function persist($object);
    public function update($object);
    public function remove($object);
}
