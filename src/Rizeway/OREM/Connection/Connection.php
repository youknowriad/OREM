<?php

namespace Rizeway\OREM\Connection;

class Connection implements ConnectionInterface
{
    protected $client;

    /**
     * @param $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param string $method
     * @param string $resource
     * @return array|bool|float|int|string
     */
    public function query($method, $resource)
    {
        $request = $this->client->createRequest($method, $resource, array(
            'Content-Type' => 'application/json'
        ));

        return $request->send()->json();
    }
}
