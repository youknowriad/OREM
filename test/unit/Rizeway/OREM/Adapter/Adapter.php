<?php

namespace test\unit\Rizeway\OREM\Adapter;

use atoum;
use Rizeway\OREM\Exception\ExceptionNotFound;
use Rizeway\OREM\Mapping\Relation\MappingRelationHasMany;
use Rizeway\OREM\Mapping\Relation\MappingRelationHasOne;
use Rizeway\OREM\Mapping\Field\MappingFieldString;
use Rizeway\OREM\Mapping\MappingEntity;
use Rizeway\OREM\Adapter\Adapter as TestedClass;

class Adapter extends atoum
{
    public function testClass()
    {
        $this->testedClass->isSubclassOf('\\Rizeway\\OREM\\Adapter\\AdapterInterface');
    }

    public function test__construct()
    {
        $this
            ->if($name = uniqid())
            ->and($classname = uniqid())
            ->and($primaryKey = uniqid())
            ->and($fields = array())
            ->and($mapping = new MappingEntity($name, $classname, $primaryKey, $fields))
            ->then
                ->object(new testedClass($mapping))
        ;
    }

    public function testFindQuery()
    {
        $this
            ->if($connection = new \mock\Rizeway\OREM\Connection\ConnectionInterface())
            ->and($connection->getMockController()->query = $response = array(
                array('test' => 'value')
            ))
            ->and($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\MyEntity', 'test', array('test' => $mappingField), array()))
            ->and($object = new TestedClass($mapping))
            ->then
                ->array($entity = $object->findQuery($connection))->isIdenticalTo($response)
                ->mock($connection)
                    ->call('query')
                    ->withArguments('GET', 'entity', array())
                    ->once()
        ;
    }

    public function testFind()
    {
        $this
            ->if($connection = new \mock\Rizeway\OREM\Connection\ConnectionInterface())
            ->and($connection->getMockController()->query = $response = array('test' => 'id'))
            ->and($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\MyEntity', 'test', array('test' => $mappingField), array()))
            ->and($object = new TestedClass($mapping))
            ->then
                ->array($entity = $object->find($connection, 'id'))->isIdenticalTo($response)
                ->mock($connection)
                    ->call('query')
                    ->withArguments('GET', 'entity/id', array())
                    ->once()

            ->if($connection->getMockController()->resetCalls())
            ->and($connection->getMockController()->query = $response = array('test' => 'id', 'relation' => array('test' => 'id')))
            ->and($mappingField = new MappingFieldString('test'))
            ->and($mappingRelation = new MappingRelationHasOne('related', 'relation'))
            ->and($mapping = new MappingEntity(
                'entity',
                '\test\unit\Rizeway\OREM\MyEntity',
                'test',
                array($mappingField),
                array(),
                array($mappingRelation)
            ))
            ->and($object = new TestedClass($mapping))
            ->then
                ->array($object->find($connection, 'id'))->isIdenticalTo($response)
                ->mock($connection)
                    ->call('query')->withArguments('GET', 'entity/id', array())->once()

            ->if($connection->getMockController()->query->throw = $exception = new ExceptionNotFound('test', 404))
            ->then
                ->exception(function() use ($object, $connection) { $object->find($connection, 'id'); })->isIdenticalTo($exception)
            ->if($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\MyEntity', null, array('test' => $mappingField), array()))
            ->and($object = new TestedClass($mapping))
            ->then
                ->exception(function() use($object, $connection) { $object->find($connection, 'id'); })
                    ->hasMessage('A field must be defined as primary key')
        ;
    }

    public function testFindRelation()
    {
        $this
            ->if($connection = new \mock\Rizeway\OREM\Connection\ConnectionInterface())
            ->and($connection->getMockController()->query = array('test' => 'id'))
            ->and($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity(
                'entity',
                '\test\unit\Rizeway\OREM\MyEntity',
                'test',
                array('test' => $mappingField),
                array(),
                array()
            ))
            ->and($object = new TestedClass($mapping))
            ->and($mapping = new MappingEntity(
                'entity',
                '\test\unit\Rizeway\OREM\MyEntity',
                'test',
                array($mappingField),
                array(),
                array(
                    new MappingRelationHasOne('useless', 'uselessRelation', null, true),
                    $relation = new MappingRelationHasOne('related', 'relation', null, true)
                )
            ))
            ->and($object = new TestedClass($mapping))
            ->then
                ->array($object->findRelation($connection, $relation, 'id'))->hasSize(1)
                ->mock($connection)
                    ->call('query')->withArguments('GET', 'entity/id/relation', array())->once()

            ->if($connection->getMockController()->resetCalls())
            ->and($connection->getMockController()->query = array(array('test' => 'id')))
            ->and($mapping = new MappingEntity(
                'entity',
                '\test\unit\Rizeway\OREM\MyEntity',
                'test',
                array($mappingField),
                array(
                    new MappingRelationHasMany('useless', 'uselessRelation', null, true),
                    $relation = new MappingRelationHasMany('related', 'relation', null, true)
                ),
                array()
            ))
            ->and($object = new TestedClass($mapping))
            ->then
                ->array($result = $object->findRelation($connection, $relation, 'id'))->hasSize(1)
                ->array($result[0])->isEqualTo(array('test' => 'id'))
                ->mock($connection)
                    ->call('query')->withArguments('GET', 'entity/id/relation', array())->once()
            ->if($mapping = new MappingEntity(
                'entity',
                '\test\unit\Rizeway\OREM\MyEntity',
                null,
                array($mappingField),
                array(
                    new MappingRelationHasMany('useless', 'uselessRelation', null, true),
                    $relation = new MappingRelationHasMany('related', 'relation', null, true)
                ),
                array()
            ))
            ->and($object = new TestedClass($mapping))
            ->then
                ->exception(function() use($object, $connection, $relation) { $object->findRelation($connection, $relation, 'id'); })
                    ->hasMessage('A field must be defined as primary key')
        ;
    }
}
