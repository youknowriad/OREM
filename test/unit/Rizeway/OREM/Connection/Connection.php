<?php

namespace test\unit\Rizeway\OREM\Connection;

use Rizeway\OREM\Connection\Connection as TestedClass;
use atoum;

class Connection extends atoum\test
{
    public function testAll()
    {
        $this
            ->if($client = new \mock\Guzzle\Http\Client())
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
            ->and($result = $object->query('GET', 'resource', $body))
                ->mock($client)
                    ->call('createRequest')
                        ->withArguments('GET', 'resource', array('Content-Type' => 'application/json'), json_encode($body))
                        ->once()
                ->string($result)->isEqualTo('ok')
        ;
    }

    public function testWithParameters()
    {
        $this
            ->if($client = new \mock\Guzzle\Http\Client())
            ->and($client->getMockController()->createRequest = function() {
                $request = new \mock\request();
                $response = new \mock\response();
                $response->getMockController()->json = 'ok';
                $request->getMockController()->send = $response;

                return $request;
            })
            ->and($object = new TestedClass($client))
            ->and($result = $object->query('GET', 'resource', null, array('param' => 'a', 'param2' => 'b')))
                ->mock($client)
                    ->call('createRequest')
                        ->withArguments('GET', 'resource?param=a&param2=b', array('Content-Type' => 'application/json'), null)
                        ->once()
                ->string($result)->isEqualTo('ok')
        ;
    }


    public function testSetClient()
    {
        $this
            ->if($client = new \mock\Guzzle\Http\Client())
            ->if($client2 = new \mock\Guzzle\Http\Client())
            ->and($object = new TestedClass($client))
            ->and($object->setClient($client2))
            ->then
                ->object($object->getClient())
                    ->isIdenticalTo($client2)
                    ->isNotIdenticalTo($client)
        ;
    }
}
