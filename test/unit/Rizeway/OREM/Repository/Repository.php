<?php

namespace test\unit\Rizeway\OREM\Repository;

use Rizeway\OREM\Repository\Repository as TestedClass;
use atoum;

class Repository extends atoum\test
{
    public function testAll()
    {
        $this
            ->if($this->mockGenerator->shuntParentClassCalls())
            ->if($manager = new \mock\Rizeway\OREM\Manager(new \mock\Rizeway\OREM\Connection\ConnectionInterface(), array()))
            ->and($object = new TestedClass($manager, 'test'))
            ->and($object->find('id'))
            ->then
                ->mock($manager)
                    ->call('find')->withArguments('test', 'id')->once()
            ->if($object->findQuery(array('toto' => 'tata')))
            ->then
                ->mock($manager)
                    ->call('findQuery')->withArguments('test', array('toto' => 'tata'))->once()
            ->if($object->findAll())
            ->then
                ->mock($manager)
                    ->call('findQuery')->withArguments('test', array())->once()
            ->if($object->findRelation('id', 'relation'))
            ->then
                ->mock($manager)
                    ->call('findRelation')->withArguments('test', 'id', 'relation')->once()
        ;
    }
}
