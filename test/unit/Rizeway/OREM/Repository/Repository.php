<?php

namespace test\unit\Rizeway\OREM\Repository;

use Rizeway\OREM\Repository\Repository as TestedClass;
use atoum;

class Repository extends atoum\test
{
    public function testAll()
    {
        $this
            ->if($manager = new \mock\Rizeway\OREM\Manager(new \mock\Rizeway\OREM\Connection\ConnectionInterface(), array()))
            ->and($manager->getMockController()->find = null)
            ->and($manager->getMockController()->findAll = array())
            ->and($object = new TestedClass($manager, 'test'))
            ->then
                ->object($object)->isInstanceOf('Rizeway\\OREM\\Repository\\Repository')
                ->variable($object->find('id'))->isNull()
                ->mock($manager)
                    ->call('find')
                        ->withArguments('test', 'id')
                ->array($object->findAll())->isEqualTo(array())
                ->mock($manager)
                    ->call('findAll')
                    ->withArguments('test')
        ;
    }
}
