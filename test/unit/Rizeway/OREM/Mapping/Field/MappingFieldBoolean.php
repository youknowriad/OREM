<?php

namespace test\unit\Rizeway\OREM\Mapping\Field;

use Rizeway\OREM\Mapping\Field\MappingFieldBoolean as TestedClass;
use atoum;

class MappingFieldBoolean extends atoum\test
{
    public function testAll()
    {
        $this
            ->if($object = new TestedClass('field', 'remote'))
            ->then
                ->object($object)->isInstanceOf('Rizeway\\OREM\\Mapping\\Field\\MappingFieldBoolean')
                ->boolean($object->serializeField(false))->isEqualTo(false)
                ->boolean($object->unserializeField('2'))->isEqualTo(true)
                ->boolean($object->unserializeField(0))->isEqualTo(false)
        ;
    }
}
