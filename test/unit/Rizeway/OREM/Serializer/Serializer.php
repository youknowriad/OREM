<?php

namespace test\unit\Rizeway\OREM\Serializer;

use Rizeway\OREM\Mapping\Field\MappingFieldString;
use Rizeway\OREM\Mapping\MappingEntity;
use Rizeway\OREM\Mapping\Relation\MappingRelationHasMany;
use Rizeway\OREM\Mapping\Relation\MappingRelationHasOne;
use Rizeway\OREM\Serializer\Serializer as TestedClass;
use Rizeway\OREM\Store\Store;
use Rizeway\OREM\Manager;
use Rizeway\OREM\Entity\Entity;
use atoum;

class MyEntity extends Entity {
    public $test;
    public $relation;
}

class MyEntityRelated extends Entity {
    public $test;
    public $prop;
}

class Serializer extends atoum\test
{
    public function testAll()
    {
        $this
            ->if($mappingField = new MappingFieldString('test'))
            ->and($related = new MappingEntity(
                'related',
                '\test\unit\Rizeway\OREM\Serializer\MyEntityRelated',
                'test',
                array(new MappingFieldString('test'))
            ))
            ->and($mappingRelation = new MappingRelationHasOne('related', 'relation', null, true))
            ->and($mapping = new MappingEntity(
                'entity',
                '\test\unit\Rizeway\OREM\Serializer\MyEntity',
                'test',
                array('test' => $mappingField),
                array($mappingRelation),
                array($mappingRelation)
            ))
            ->and($manager = new Manager(new \mock\Rizeway\OREM\Connection\ConnectionInterface(), array('entity' => $mapping)))
            ->and($object = new TestedClass($manager, new Store(array('entity' => $mapping))))
            ->then
                ->object($object)->isInstanceOf('Rizeway\\OREM\\Serializer\\Serializer')
                ->object($entity = $object->unserializeEntity(array('test' => 'toto'), 'entity'))->isInstanceOf('test\unit\Rizeway\OREM\Serializer\MyEntity')
                ->string($entity->test)->isEqualTo('toto')
                ->array($object->serializeEntity($entity, 'entity'))->isEqualTo(array('test' => 'toto'))
            ->if($object->updateEntity($entity, array('test' => 'tata'), 'entity'))
            ->then
                ->string($entity->test)->isEqualTo('tata')
                ->exception(function() use ($object) { $object->unserializeEntity(array(), 'toto'); })
                    ->hasMessage('Unknown Entity : toto')
        ;
    }

    public function testWithHasOneRelations()
    {
        $this
            ->if($mappingField = new MappingFieldString('test'))
            ->and($mappingRelation = new MappingRelationHasOne('entity2', 'relation'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\Serializer\MyEntity', 'test',
                    array($mappingField), array(), array($mappingRelation)))
            ->and($mapping2 = new MappingEntity('entity2', '\test\unit\Rizeway\OREM\Serializer\MyEntityRelated', 'test',
                    array('test' => $mappingField), array()))
            ->and($mappings = array('entity' => $mapping, 'entity2' => $mapping2))
            ->and($manager = new Manager(new \mock\Rizeway\OREM\Connection\ConnectionInterface(), $mappings))
            ->and($object = new TestedClass($manager, new Store($mappings)))
            ->and($serial = array('test' => 'toto', 'relation' => array('test' => 'tata')))
            ->then
                ->object($entity = $object->unserializeEntity($serial, 'entity'))
                ->isInstanceOf('test\unit\Rizeway\OREM\Serializer\MyEntity')
                ->string($entity->relation->test)->isEqualTo('tata')
                ->array($object->serializeEntity($entity, 'entity'))->isEqualTo($serial)
            ->if($serial2 = array('test' => 'toto', 'relation' => array('test' => 'titi')))
                ->and($object->updateEntity($entity, $serial2, 'entity'))
            ->then
                ->string($entity->relation->test)->isEqualTo('titi')
        ;
    }

    public function testWithHasManyRelations()
    {
        $this
            ->if($mappingField = new MappingFieldString('test'))
            ->and($mappingRelation = new MappingRelationHasMany('entity2', 'relation'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\Serializer\MyEntity', 'test',
                array($mappingField), array($mappingRelation), array()))
            ->and($mapping2 = new MappingEntity('entity2', '\test\unit\Rizeway\OREM\Serializer\MyEntityRelated', 'test',
                array('test' => $mappingField), array()))
            ->and($mappings = array('entity' => $mapping, 'entity2' => $mapping2))
            ->and($manager = new Manager(new \mock\Rizeway\OREM\Connection\ConnectionInterface(), $mappings))
            ->and($object = new TestedClass($manager, new Store($mappings)))
            ->and($serial = array('test' => 'toto', 'relation' => array(array('test' => 'tata'))))
            ->then
                ->object($entity = $object->unserializeEntity($serial, 'entity'))
                    ->isInstanceOf('test\unit\Rizeway\OREM\Serializer\MyEntity')
                ->string(current($entity->relation)->test)->isEqualTo('tata')
                ->array($entity->relation)->hasSize(1)
                ->array($object->serializeEntity($entity, 'entity'))->isEqualTo($serial)
        ;
    }

    public function testFullStore()
    {
        $this
            ->if($mappingField = new MappingFieldString('test'))
            ->and($mappingFieldProp = new MappingFieldString('prop'))
            ->and($mappingRelation = new MappingRelationHasOne('entity2', 'relation'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\Serializer\MyEntity', 'test',
                    array($mappingField), array(), array($mappingRelation)))
            ->and($mapping2 = new MappingEntity('entity2', '\test\unit\Rizeway\OREM\Serializer\MyEntityRelated', 'test',
                    array($mappingField, $mappingFieldProp), array()))
            ->and($mappings = array('entity' => $mapping, 'entity2' => $mapping2))
            ->and($store = new Store($mappings))
            ->and($manager = new Manager(new \mock\Rizeway\OREM\Connection\ConnectionInterface(), $mappings))
            ->and($object = new TestedClass($manager, $store))
            ->and($serial = array('test' => 'toto', 'relation' => array('test' => 'tata', 'prop' => 'titi')))
            ->and($entityRelated = new MyEntityRelated())
            ->and($entityRelated->test = 'tata')
            ->and($store->addEntity($entityRelated))
            ->and($entity = $object->unserializeEntity($serial, 'entity'))
            ->then
                ->object($entity->relation)->isIdenticalTo($entityRelated)
                ->string($entityRelated->prop)->isEqualTo('titi')
        ;
    }

    public function testUnserializeEntity()
    {
        $this
            ->if($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity(
                'entity',
                '\test\unit\Rizeway\OREM\Serializer\MyEntity',
                'test',
                array($mappingField)
            ))
            ->and($mappings = array('entity' => $mapping))
            ->and($store = new \mock\Rizeway\OREM\Store\Store($mappings))
            ->and($manager = new Manager(new \mock\Rizeway\OREM\Connection\ConnectionInterface(), $mappings))
            ->and($object = new TestedClass($manager, $store))
            ->then
                ->object($result = $object->unserializeEntity(array('test' => 'value'), 'entity'))->isInstanceOf('\\test\\unit\\Rizeway\\OREM\\Serializer\\MyEntity')
                ->string($result->test)->isEqualTo('value')
        ;
    }
}
