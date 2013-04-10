<?php

namespace test\unit\Rizeway\OREM\Mapping\Field;

use atoum;

class MappingField extends atoum\test
{
    public function testAll()
    {
        $this
            ->if($object = new \mock\Rizeway\OREM\Mapping\Field\MappingField('field', 'remote'))
            ->then
                ->object($object)->isInstanceOf('Rizeway\\OREM\\Mapping\\Field\\MappingField')
                ->string($object->getFieldName('field'))
                ->string($object->getRemoteName('remote'))
            ->if($object = new \mock\Rizeway\OREM\Mapping\Field\MappingField('field'))
            ->then
                ->object($object)->isInstanceOf('Rizeway\\OREM\\Mapping\\Field\\MappingField')
                ->string($object->getFieldName('field'))
                ->string($object->getRemoteName('field'))
        ;
    }
}
