<?php

namespace Rizeway\OREM\Connection;

interface ConnectionInterface
{
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_POST = 'POST';

    /**
     * @param string $method
     * @param string $resource
     * @param array  $content
     * @return array|bool|float|int|string
     */
    public function query($method, $resource, $content = null);
}
