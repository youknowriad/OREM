<?php

namespace test\unit\Rizeway\OREM\Connection;

use Rizeway\OREM\Connection\Connection as TestedClass;
use atoum;

class Connection extends atoum\test
{
    public function testAll()
    {
        $this
            ->if($client = new \mock\Client())
            ->and($client->getMockController()->createRequest = function() {
                $request = new \mock\request();
                $response = new \mock\response();
                $response->getMockController()->json = 'ok';
                $request->getMockController()->send = $response;

                return $request;
            })
            ->and($object = new TestedClass($client))
            ->then
                ->object($object)->isInstanceOf('Rizeway\\OREM\\Connection\\Connection')
                ->object($object->getClient())->isEqualTo($client)
            ->if($body = array('body' => 'value'))
            ->and($result = $object->query('GET', 'resource', array()))
                ->mock($client)
                    ->call('createRequest')
                        ->withArguments('GET', 'resource', array('Content-Type' => 'application/json'), json_encode($body))
                ->string($result)->isEqualTo('ok')
        ;
    }
}
