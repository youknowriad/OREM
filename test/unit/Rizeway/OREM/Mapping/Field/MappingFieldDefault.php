<?php

namespace test\unit\Rizeway\OREM\Mapping\Field;

use Rizeway\OREM\Mapping\Field\MappingFieldDefault as TestedClass;
use atoum;

class MappingFieldDefault extends atoum\test
{
    public function testAll()
    {
        $this
            ->if($object = new TestedClass('field', 'remote'))
            ->then
                ->object($object)->isInstanceOf('Rizeway\\OREM\\Mapping\\Field\\MappingFieldDefault')
                ->array($object->serializeField(array('toto')))->isEqualTo(array('toto'))
                ->array($object->unserializeField(array('toto')))->isEqualTo(array('toto'))
                ->string($object->unserializeField('toto'))->isEqualTo('toto')
                ->string($object->unserializeField('toto'))->isEqualTo('toto')
        ;
    }
}
