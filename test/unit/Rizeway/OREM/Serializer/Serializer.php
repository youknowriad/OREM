<?php

namespace test\unit\Rizeway\OREM\Serializer;

use Rizeway\OREM\Mapping\Field\MappingFieldString;
use Rizeway\OREM\Mapping\MappingEntity;
use Rizeway\OREM\Serializer\Serializer as TestedClass;
use atoum;

class MyEntity {
    public $test;
}

class Serializer extends atoum\test
{
    public function testAll()
    {
        $this
            ->if($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\Serializer\MyEntity', array('test' => $mappingField), 'test'))
            ->and($object = new TestedClass(array('entity' => $mapping)))
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
}
