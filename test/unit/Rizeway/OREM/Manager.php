<?php

namespace test\unit\Rizeway\OREM;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Rizeway\OREM\Mapping\Relation\MappingRelationHasMany;
use Rizeway\OREM\Mapping\Relation\MappingRelationHasOne;
use Rizeway\OREM\Entity\Entity;
use Rizeway\OREM\Manager as TestedClass;
use Rizeway\OREM\Mapping\Field\MappingFieldString;
use Rizeway\OREM\Mapping\MappingEntity;
use atoum;

class MyEntity extends Entity {
    public $test;
    public $relation;
}

class MyEntityGetter extends Entity {
    private $tested_value;

    public function getTestedValue()
    {
        return $this->tested_value;
    }

    public function setTestedValue($tested_value)
    {
        $this->tested_value = $tested_value;
    }
}

class MyEntityRelated extends Entity {
    public $test;
}

class Manager extends atoum\test
{
    public function test__construct()
    {
        $this
            ->if($connection = new \mock\Rizeway\OREM\Connection\ConnectionInterface())
            ->and($object = new TestedClass($connection, array()))
            ->then
                ->object($object)->isInstanceOf('Rizeway\\OREM\\Manager')
                ->object($object->getConnection())->isIdenticalTo($connection)
        ;
    }

    public function testGetRepository()
    {
        $this
            ->if($connection = new \mock\Rizeway\OREM\Connection\ConnectionInterface())
            ->and($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\MyEntity', 'test', array('test' => $mappingField), array()))
            ->and($object = new TestedClass($connection, array('entity' => $mapping)))
            ->then
                ->object($repository = $object->getRepository('entity'))
                    ->isInstanceOf('Rizeway\\OREM\\Repository\Repository')
                ->object($object->getRepository('entity'))->isIdenticalTo($repository)
                ->exception(function() use ($object) { $object->getRepository('test'); })
                    ->hasMessage('Unknown Entity : test')
        ;
    }

    public function testGetAdapter()
    {
        $this
            ->if($connection = new \mock\Rizeway\OREM\Connection\ConnectionInterface())
            ->and($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity(
                'entity',
                '\test\unit\Rizeway\OREM\MyEntity',
                'test',
                array('test' => $mappingField),
                array(),
                array(),
                'url',
                '\\mock\\Rizeway\\OREM\\Adapter\\Adapter'
            ))
            ->and($object = new TestedClass($connection, array('entity' => $mapping)))
            ->then
                ->object($adapter = $object->getAdapter('entity'))
                    ->isInstanceOf('\\Rizeway\\OREM\\Adapter\\AdapterInterface')
                ->object($object->getAdapter('entity'))->isIdenticalTo($adapter)
                ->exception(function() use ($object) { $object->getAdapter('test'); })
                    ->hasMessage('Unknown Entity : test')
            ->if($mapping = new MappingEntity(
                'entity',
                '\test\unit\Rizeway\OREM\MyEntity',
                'test',
                array('test' => $mappingField),
                array(),
                array(),
                'url',
                '\\mock\\Unknown\\Rizeway\\OREM\\Adapter\\Adapter'
            ))
            ->and($object = new TestedClass($connection, array('entity' => $mapping)))
            ->then
                ->exception(function() use ($object) { $object->getAdapter('entity'); })
                    ->hasMessage('Adapter \\mock\\Unknown\\Rizeway\\OREM\\Adapter\\Adapter of entity entity is not a valid adapter')
        ;
    }

    public function testFindQuery()
    {
        $this
            ->if($connection = new \mock\Rizeway\OREM\Connection\ConnectionInterface())
            ->and($connection->getMockController()->query = array(
                array('test' => 'value')
            ))
            ->and($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\MyEntity', 'test', array('test' => $mappingField), array()))
            ->and($object = new TestedClass($connection, array('entity' => $mapping)))
            ->then
                ->array($result = $object->findQuery('entity'))->hasSize(1)
                ->object($entity = current($result))->isInstanceOf('\test\unit\Rizeway\OREM\MyEntity')
                ->string($entity->test)->isEqualTo('value')
                ->exception(function() use ($object) { $object->findQuery('toto'); })
                    ->hasMessage('Unknown Entity : toto')
        ;
    }

    public function testPersist()
    {
        $this
            ->if($connection = new \mock\Rizeway\OREM\Connection\ConnectionInterface())
             ->and($connection->getMockController()->query = array('test' => 'tata'))
            ->and($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\MyEntity', 'test', array('test' => $mappingField), array()))
            ->and($object = new TestedClass($connection, array('entity' => $mapping)))
            ->and($entity = new MyEntity())
            ->and($entity->test = 'toto')
            ->and($object->persist($entity))
            ->then
                ->mock($connection)
                    ->call('query')
                    ->withArguments('POST', 'entity', array('test' => 'toto'))
                    ->once()
                ->string($entity->test)->isEqualTo('tata')
        ;
    }

    public function testUpdate()
    {
        $this
            ->if($connection = new \mock\Rizeway\OREM\Connection\ConnectionInterface())
            ->and($connection->getMockController()->query = array('test' => 'tata'))
            ->and($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\MyEntity', 'test', array('test' => $mappingField), array()))
            ->and($object = new TestedClass($connection, array('entity' => $mapping)))
            ->and($entity = new MyEntity())
            ->and($entity->test = 'toto')
            ->and($object->update($entity))
            ->then
                ->mock($connection)
                    ->call('query')
                    ->withArguments('PUT', 'entity/toto', array('test' => 'toto'))
                    ->once()
                ->string($entity->test)->isEqualTo('tata')
        ;
    }

    public function testFind()
    {
        $this
            ->if($connection = new \mock\Rizeway\OREM\Connection\ConnectionInterface())
            ->and($connection->getMockController()->query = array('test' => 'id'))
            ->and($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\MyEntity', 'test', array('test' => $mappingField), array()))
            ->and($object = new TestedClass($connection, array('entity' => $mapping)))
            ->and($entity = $object->find('entity', 'id'))
            ->then
                ->mock($connection)
                    ->call('query')
                    ->withArguments('GET', 'entity/id', array())
                    ->once()
                ->object($entity)->isInstanceOf('\test\unit\Rizeway\OREM\MyEntity')
                ->string($entity->test)->isEqualTo('id')
            ->if($connection->getMockController()->query->throw = $exception = new ClientException('test', new Request('get', ''), new Response(404)))
            ->then
                ->variable($object->find('entity', 'id'))->isNull()

            ->if($connection->getMockController()->resetCalls())
            ->and($connection->getMockController()->query = array('test' => 'id', 'relation' => array('test' => 'id')))
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
            ->and($related = new MappingEntity(
                'related',
                '\test\unit\Rizeway\OREM\MyEntityRelated',
                'test',
                array($mappingField)
            ))
            ->and($mappings = array('entity' => $mapping, 'related' => $related))
            ->and($object = new TestedClass($connection, $mappings))
            ->and($object->find('entity', 'id'))
            ->then
                ->mock($connection)
                    ->call('query')->withArguments('GET', 'entity/id', array())->once()
        ;
    }

    public function testFindRelation()
    {
        $this
            ->if($connection = new \mock\Rizeway\OREM\Connection\ConnectionInterface())
            ->and($connection->getMockController()->query = array('test' => 'id'))
            ->and($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\MyEntity', 'test', array('test' => $mappingField), array()))
            ->and($object = new TestedClass($connection, array('entity' => $mapping)))
            ->and($entity = $object->find('entity', 'id'))
            ->then
                ->exception(function() use ($object) { $object->findRelation('entity', 'id', 'related'); })
                    ->isInstanceOf('\\InvalidArgumentException')
                    ->hasMessage('Unknown relation : related')

            ->if($connection->getMockController()->resetCalls())
            ->and($connection->getMockController()->query = array('test' => 'id'))
            ->and($mappingField = new MappingFieldString('test'))
            ->and($mappingRelation = new MappingRelationHasOne('related', 'relation', null, true))
            ->and($mapping = new MappingEntity(
                'entity',
                '\test\unit\Rizeway\OREM\MyEntity',
                'test',
                array($mappingField),
                array(),
                array(new MappingRelationHasOne('useless', 'uselessRelation', null, true), $mappingRelation)
            ))
            ->and($related = new MappingEntity(
                'related',
                '\test\unit\Rizeway\OREM\MyEntityRelated',
                'test',
                array($mappingField)
            ))
            ->and($useless = new MappingEntity(
                'useless',
                '\test\unit\Rizeway\OREM\MyEntityGetter',
                'tested_value',
                array(new MappingFieldString('tested_value'))
            ))
            ->and($mappings = array('entity' => $mapping, 'related' => $related, 'useless' => $useless))
            ->and($object = new TestedClass($connection, $mappings))
            ->then
                ->object($object->findRelation('entity', 'id', 'relation'))->isInstanceOf('\test\unit\Rizeway\OREM\MyEntityRelated')
                ->mock($connection)
                    ->call('query')->withArguments('GET', 'entity/id/relation', array())->once()

            ->if($connection->getMockController()->resetCalls())
            ->and($connection->getMockController()->query = array(array('test' => 'id')))
            ->and($mappingField = new MappingFieldString('test'))
            ->and($mappingRelation = new MappingRelationHasMany('related', 'relation', null, true))
            ->and($mapping = new MappingEntity(
                'entity',
                '\test\unit\Rizeway\OREM\MyEntity',
                'test',
                array($mappingField),
                array(new MappingRelationHasOne('useless', 'uselessRelation', null, true), $mappingRelation),
                array()
            ))
            ->and($related = new MappingEntity(
                'related',
                '\test\unit\Rizeway\OREM\MyEntityRelated',
                'test',
                array($mappingField)
            ))
            ->and($mappings = array('entity' => $mapping, 'related' => $related, 'useless' => $useless))
            ->and($object = new TestedClass($connection, $mappings))
            ->then
                ->array($result = $object->findRelation('entity', 'id', 'relation'))->hasSize(1)
                ->object($result[0])->isInstanceOf('\test\unit\Rizeway\OREM\MyEntityRelated')
                ->mock($connection)
                    ->call('query')->withArguments('GET', 'entity/id/relation', array())->once()
        ;
    }

    public function testRemove()
    {
        $this
            ->if($connection = new \mock\Rizeway\OREM\Connection\ConnectionInterface())
            ->and($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\MyEntity', 'test', array('test' => $mappingField), array()))
            ->and($object = new TestedClass($connection, array('entity' => $mapping)))
            ->and($entity = new MyEntity())
            ->and($entity->test = 'toto')
            ->and($object->remove($entity))
            ->then
                ->mock($connection)
                    ->call('query')
                    ->withArguments('DELETE', 'entity/toto', array())
                    ->once()
                ->exception(function() use ($object) { $object->remove(new \mock\test()); })
                    ->hasMessage('No Mapping Entity found for class : mock\test')
            ->if($connection->getMockController()->resetCalls())
            ->and($mappingField = new MappingFieldString('tested_value'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\MyEntityGetter', 'tested_value', array('tested_value' => $mappingField), array()))
            ->and($object = new TestedClass($connection, array('entity' => $mapping)))
            ->and($entity = new MyEntityGetter())
            ->and($entity->setTestedValue('toto'))
            ->and($object->remove($entity))
            ->then
                ->mock($connection)
                    ->call('query')
                    ->withArguments('DELETE', 'entity/toto', array())
                    ->once()
            ;
    }
}
