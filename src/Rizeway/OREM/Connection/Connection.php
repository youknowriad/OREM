<?php

namespace Rizeway\OREM\Connection;


use GuzzleHttp\ClientInterface;

class Connection implements ConnectionInterface
{
    /**
     * @var ClientInterface
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
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param ClientInterface $client
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $method
     * @param string $resource
     * @param array  $content
     * @param array  $urlParameters
     * @return array|bool|float|int|string
     */
    public function query($method, $resource, $content = null, array $urlParameters = array())
    {
        $response = $this->client->request($method, $resource . $this->getQueryUrl($urlParameters), [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => is_null($content) ? '' : json_encode($content)
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param array $urlParameters
     * @return string
     */
    protected function getQueryUrl($urlParameters = array())
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
