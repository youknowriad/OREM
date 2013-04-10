<?php

namespace test\unit\Rizeway\OREM;

use Rizeway\OREM\Exception\ExceptionNotFound;
use Rizeway\OREM\Manager as TestedClass;
use Rizeway\OREM\Mapping\Field\MappingFieldString;
use Rizeway\OREM\Mapping\MappingEntity;
use atoum;

class MyEntity {
    public $test;
}

class MyEntityGetter {
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

class Manager extends atoum\test
{
    public function test__construct()
    {
        $this
            ->if($connection = new \mock\Rizeway\OREM\Connection\ConnectionInterface())
            ->and($object = new TestedClass($connection, array()))
            ->then
                ->object($object)->isInstanceOf('Rizeway\\OREM\\Manager')
        ;
    }

    public function testGetRepository()
    {
        $this
            ->if($connection = new \mock\Rizeway\OREM\Connection\ConnectionInterface())
            ->and($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\MyEntity', array('test' => $mappingField), 'test'))
            ->and($object = new TestedClass($connection, array('entity' => $mapping)))
            ->then
                ->object($object->getRepository('entity'))
                    ->isInstanceOf('Rizeway\\OREM\\Repository\Repository')
                ->exception(function() use ($object) { $object->getRepository('test'); })
                    ->hasMessage('Unknown Entity : test')
        ;
    }

    public function testFindAll()
    {
        $this
            ->if($connection = new \mock\Rizeway\OREM\Connection\ConnectionInterface())
            ->and($connection->getMockController()->query = array(
                array('test' => 'value')
            ))
            ->and($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\MyEntity', array('test' => $mappingField), 'test'))
            ->and($object = new TestedClass($connection, array('entity' => $mapping)))
            ->then
                ->array($result = $object->findAll('entity'))->hasSize(1)
                ->object($entity = current($result))->isInstanceOf('\test\unit\Rizeway\OREM\MyEntity')
                ->string($entity->test)->isEqualTo('value')
                ->exception(function() use ($object) { $object->findAll('toto'); })
                    ->hasMessage('Unknown Entity : toto')
        ;
    }

    public function testPersist()
    {
        $this
            ->if($connection = new \mock\Rizeway\OREM\Connection\ConnectionInterface())
             ->and($connection->getMockController()->query = array('test' => 'tata'))
            ->and($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\MyEntity', array('test' => $mappingField), 'test'))
            ->and($object = new TestedClass($connection, array('entity' => $mapping)))
            ->and($entity = new MyEntity())
            ->and($entity->test = 'toto')
            ->and($object->persist($entity))
            ->then
                ->mock($connection)
                    ->call('query')
                    ->withArguments('POST', 'entity', array('test' => 'toto'))
                ->string($entity->test)->isEqualTo('tata')
        ;
    }

    public function testUpdate()
    {
        $this
            ->if($connection = new \mock\Rizeway\OREM\Connection\ConnectionInterface())
            ->and($connection->getMockController()->query = array('test' => 'tata'))
            ->and($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\MyEntity', array('test' => $mappingField), 'test'))
            ->and($object = new TestedClass($connection, array('entity' => $mapping)))
            ->and($entity = new MyEntity())
            ->and($entity->test = 'toto')
            ->and($object->update($entity))
            ->then
                ->mock($connection)
                    ->call('query')
                    ->withArguments('PUT', 'entity/toto', array('test' => 'toto'))
                ->string($entity->test)->isEqualTo('tata')
        ;
    }

    public function testFind()
    {
        $this
            ->if($connection = new \mock\Rizeway\OREM\Connection\ConnectionInterface())
            ->and($connection->getMockController()->query = array('test' => 'id'))
            ->and($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\MyEntity', array('test' => $mappingField), 'test'))
            ->and($object = new TestedClass($connection, array('entity' => $mapping)))
            ->and($entity = $object->find('entity', 'id'))
            ->then
                ->mock($connection)
                    ->call('query')
                    ->withArguments('GET', 'entity/id', array())
                ->object($entity)->isInstanceOf('\test\unit\Rizeway\OREM\MyEntity')
                ->string($entity->test)->isEqualTo('id')
            ->if($connection->getMockController()->query->throw = new ExceptionNotFound('test', 404))
            ->then
                ->variable($object->find('entity', 'id'))->isNull()
        ;
    }

    public function testRemove()
    {
        $this
            ->if($connection = new \mock\Rizeway\OREM\Connection\ConnectionInterface())
            ->and($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\MyEntity', array('test' => $mappingField), 'test'))
            ->and($object = new TestedClass($connection, array('entity' => $mapping)))
            ->and($entity = new MyEntity())
            ->and($entity->test = 'toto')
            ->and($object->remove($entity))
            ->then
                ->mock($connection)
                    ->call('query')
                    ->withArguments('DELETE', 'entity/toto', array())
                ->exception(function() use ($object) { $object->remove(new \mock\test()); })
                    ->hasMessage('No Mapping Entity found for class : mock\test')

            ->if($mappingField = new MappingFieldString('tested_value'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\MyEntityGetter', array('tested_value' => $mappingField), 'tested_value'))
            ->and($object = new TestedClass($connection, array('entity' => $mapping)))
            ->and($entity = new MyEntityGetter())
            ->and($entity->setTestedValue('toto'))
            ->and($object->remove($entity))
            ->then
                ->mock($connection)
                    ->call('query')
                    ->withArguments('DELETE', 'entity/toto', array())
            ;
    }
}
