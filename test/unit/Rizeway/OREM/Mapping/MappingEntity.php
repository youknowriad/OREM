<?php

namespace test\unit\Rizeway\OREM\Mapping;

use Rizeway\OREM\Mapping\MappingEntity as TestedClass;
use atoum;

class MappingEntity extends atoum\test
{
    public function testAll()
    {
        $this
            ->if($object = new TestedClass('name', 'class', array(), 'test'))
            ->then
                ->object($object)->isInstanceOf('Rizeway\\OREM\\Mapping\\MappingEntity')
                ->string($object->getPrimaryKey('test'))
                ->string($object->getName())->isEqualTo('name')
                ->string($object->getClassname())->isEqualTo('class')
                ->array($object->getMappings())->isEqualTo(array())
                ->string($object->getResourceUrl())->isEqualTo('name')
        ;
    }
}
