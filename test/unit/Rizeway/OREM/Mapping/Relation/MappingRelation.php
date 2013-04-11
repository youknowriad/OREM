<?php

namespace test\unit\Rizeway\OREM\Mapping\Relation;

use atoum;

class MappingRelation extends atoum\test
{
    public function testAll()
    {
        $this
            ->if($object = new \mock\Rizeway\OREM\Mapping\Relation\MappingRelation('entity', 'field', 'remote'))
            ->then
                ->object($object)->isInstanceOf('Rizeway\\OREM\\Mapping\\Relation\\MappingRelation')
                ->string($object->getEntityName('field'))
        ;
    }
}
