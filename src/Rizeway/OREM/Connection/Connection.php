<?php

namespace Rizeway\OREM\Connection;

use Guzzle\Http\ClientInterface;

class Connection implements ConnectionInterface
{
    /**
     * @var \Guzzle\Service\Client
     */
    protected $client;

    /**
     * @param $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return \Guzzle\Http\ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param \Guzzle\Http\ClientInterface $client
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param  string                      $method
     * @param  string                      $resource
     * @param  array                       $content
     * @param  array                       $urlParameters
     * @return array|bool|float|int|string
     */
    public function query($method, $resource, $content = null, array $urlParameters = array())
    {
        $request = $this->client->createRequest($method, $resource . $this->getQueryUrl($urlParameters), array(
            'Content-Type' => 'application/json'
        ), is_null($content) ? null : json_encode($content));

        return $request->send()->json();
    }

    /**
     * @param  array  $urlParameters
     * @return string
     */
    protected function getQueryUrl(array $urlParameters = array())
    {
        if (!count($urlParameters)) {
            return '';
        }

        $parts = array();
        foreach ($urlParameters as $key => $value) {
            $parts[] = $key.'='.urlencode($value);
        }

        return '?'.implode('&', $parts);
    }
}
