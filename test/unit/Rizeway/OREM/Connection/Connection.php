<?php

namespace test\unit\Rizeway\OREM\Connection;

use Rizeway\OREM\Connection\Connection as TestedClass;
use atoum;

class Connection extends atoum\test
{
    public function testAll()
    {
        $this
            ->if($client = new \mock\GuzzleHttp\Client())
            ->and($client->getMockController()->request = function() {
                $body = new \mock\body();
                $response = new \mock\response();
                $response->getMockController()->getBody = $body;
                $body->getMockController()->getContents = '["ok"]';

                return $response;
            })
            ->and($object = new TestedClass($client))
            ->then
                ->object($object)->isInstanceOf('Rizeway\\OREM\\Connection\\Connection')
                ->object($object->getClient())->isEqualTo($client)
            ->if($body = array('body' => 'value'))
            ->and($result = $object->query('GET', 'resource', $body))
                ->mock($client)
                    ->call('request')
                        ->withArguments('GET', 'resource', ['headers' => ['Content-Type' => 'application/json'], 'body' => json_encode($body)])
                        ->once()
                ->array($result)->isEqualTo(["ok"])
        ;
    }

    public function testWithParameters()
    {
        $this
            ->if($client = new \mock\GuzzleHttp\Client())
            ->and($client->getMockController()->request = function() {
                $body = new \mock\body();
                $response = new \mock\response();
                $response->getMockController()->getBody = $body;
                $body->getMockController()->getContents = '["ok"]';

                return $response
                    ;
            })
            ->and($object = new TestedClass($client))
            ->and($result = $object->query('GET', 'resource', null, array('param' => 'a', 'param2' => 'b')))
                ->mock($client)
                    ->call('request')
                        ->withArguments('GET', 'resource?param=a&param2=b', ['headers' => ['Content-Type' => 'application/json'], 'body' => ''])
                        ->once()
                ->array($result)->isEqualTo(['ok'])
        ;
    }


    public function testSetClient()
    {
        $this
            ->if($client = new \mock\GuzzleHttp\Client())
            ->if($client2 = new \mock\GuzzleHttp\Client())
            ->and($object = new TestedClass($client))
            ->and($object->setClient($client2))
            ->then
                ->object($object->getClient())
                    ->isIdenticalTo($client2)
                    ->isNotIdenticalTo($client)
        ;
    }
}
