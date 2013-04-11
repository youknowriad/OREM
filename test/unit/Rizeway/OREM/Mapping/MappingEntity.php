<?php

namespace test\unit\Rizeway\OREM\Mapping;

use Rizeway\OREM\Mapping\Field\MappingFieldString;
use Rizeway\OREM\Mapping\MappingEntity as TestedClass;
use atoum;

class MappingEntity extends atoum\test
{
    public function testAll()
    {
        $this
            ->if($object = new TestedClass('name', 'class', 'test', array(), array(), array()))
            ->then
                ->object($object)->isInstanceOf('Rizeway\\OREM\\Mapping\\MappingEntity')
                ->string($object->getPrimaryKey('test'))
                ->string($object->getName())->isEqualTo('name')
                ->string($object->getClassname())->isEqualTo('class')
                ->array($object->getFieldMappings())->isEqualTo(array())
                ->array($object->getHasManyMappings())->isEqualTo(array())
                ->array($object->getHasOneMappings())->isEqualTo(array())
                ->string($object->getResourceUrl())->isEqualTo('name')
                ->exception(function() use ($object) { $object->getRemotePrimaryKey(); })
                    ->hasMessage('No mapping found for primary Key in entity : name')
        ;
    }

    public function testGetRemotePrimaryKey()
    {
        $this
            ->if($object = new TestedClass('name', 'class', 'test', array(new MappingFieldString('test', 'test2')), array()))
            ->then
                ->string($object->getRemotePrimaryKey('test2'))
        ;

    }
}
