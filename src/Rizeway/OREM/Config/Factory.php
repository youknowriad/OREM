<?php

namespace Rizeway\OREM\Config;

use GuzzleHttp\Client;
use Rizeway\OREM\Connection\Connection;
use Rizeway\OREM\Manager;

class Factory
{
    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $url;

    /**
     * @param string $directory
     * @param string $url
     */
    public function __construct($directory, $url)
    {
        $this->directory = $directory;
        $this->url       = $url;
    }

    /**
     * @return \Rizeway\OREM\Connection\ConnectionInterface
     */
    public function getConnection()
    {
        return new Connection(new Client([
            'base_uri' => $this->url
        ]));
    }

    /**
     * @return \Rizeway\OREM\Mapping\MappingEntity[]
     */
    public function getEntityMappings()
    {
        $loader = new Loader($this->directory);
        $parser = new Parser();

        return $parser->parse($loader->load());
    }

    public function getManager()
    {
        return new Manager($this->getConnection(), $this->getEntityMappings());
    }
}
