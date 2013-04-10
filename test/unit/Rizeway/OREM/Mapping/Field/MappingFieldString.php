<?php

namespace test\unit\Rizeway\OREM\Mapping\Field;

use Rizeway\OREM\Mapping\Field\MappingFieldString as TestedClass;
use atoum;

class MappingFieldString extends atoum\test
{
    public function testAll()
    {
        $this
            ->if($object = new TestedClass('field', 'remote'))
            ->then
                ->object($object)->isInstanceOf('Rizeway\\OREM\\Mapping\\Field\\MappingFieldString')
                ->string($object->serializeField('2'))->isEqualTo('2')
                ->string($object->unserializeField(2))->isEqualTo('2')
            ;
    }
}
