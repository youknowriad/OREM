<?php

namespace test\unit\Rizeway\OREM\Store;

use Rizeway\OREM\Store\Store as TestedClass;
use Rizeway\OREM\Mapping\Field\MappingFieldString;
use Rizeway\OREM\Mapping\MappingEntity;
use atoum;

class MyEntity {
    public $test;
}

class Store extends atoum\test
{
    public function testAll()
    {
        $this
            ->if($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\Store\MyEntity', 'test', array('test' => $mappingField), array()))
            ->and($entity = new MyEntity())
            ->and($entity->test = 'test')
            ->and($object = new TestedClass(array('entity' => $mapping)))
            ->then
                ->boolean($object->hasEntity('entity', 'test'))->isFalse()
            ->if($object->addEntity($entity))
            ->then
                ->boolean($object->hasEntity('entity', 'test'))->isTrue()
                ->object($object->getEntity('entity', 'test'))->isIdenticalTo($entity)
            ->if($object->removeEntity($entity))
            ->then
                ->boolean($object->hasEntity('entity', 'test'))->isFalse()
                ->exception(function() use ($object) { $object->getEntity('entity', 'test'); })
                    ->hasMessage('The entity "entity" of PK "test" is not in the store')
                ->exception(function() use ($object) { $object->addEntity(new \StdClass()); })
                    ->hasMessage('No Mapping Entity found for class : stdClass')
        ;
    }
}
