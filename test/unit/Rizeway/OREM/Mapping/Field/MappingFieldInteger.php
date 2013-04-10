<?php

namespace test\unit\Rizeway\OREM\Mapping\Field;

use Rizeway\OREM\Mapping\Field\MappingFieldInteger as TestedClass;
use atoum;

class MappingFieldInteger extends atoum\test
{
    public function testAll()
    {
        $this
            ->if($object = new TestedClass('field', 'remote'))
            ->then
                ->object($object)->isInstanceOf('Rizeway\\OREM\\Mapping\\Field\\MappingFieldInteger')
                ->integer($object->serializeField(2))->isEqualTo(2)
                ->integer($object->unserializeField('2'))->isEqualTo(2)
            ;
    }
}
