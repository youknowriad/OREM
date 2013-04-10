<?php

namespace Rizeway\OREM\Connection;

class Connection implements ConnectionInterface
{
    /**
     * @var \Guzzle\Service\Client
     */
    protected $client;

    /**
     * @param $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @return \Guzzle\Service\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param string $method
     * @param string $resource
     * @param array  $content
     * @return array|bool|float|int|string
     */
    public function query($method, $resource, $content = null)
    {
        $request = $this->client->createRequest($method, $resource, array(
            'Content-Type' => 'application/json'
        ), is_null($content) ? null : json_encode($content));

        return $request->send()->json();
    }
}
