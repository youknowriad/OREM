<?php

namespace Rizeway\OREM\Connection;

use Guzzle\Http\ClientInterface;

interface ConnectionInterface
{
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_POST = 'POST';

    /**
     * @return ClientInterface
     */
    public function getClient();

    /**
     * @param ClientInterface $client
     */
    public function setClient(ClientInterface $client);

    /**
     * @param string $method
     * @param string $resource
     * @param array  $content
     * @param array  $urlParameters
     * @return array|bool|float|int|string
     */
    public function query($method, $resource, $content = null, array $urlParameters = array());
}
