<?php

namespace Rizeway\OREM\Config;

use Guzzle\Common\Event;
use Rizeway\OREM\Connection\Connection;
use Rizeway\OREM\Exception\ExceptionNotFound;
use Rizeway\OREM\Manager;
use Guzzle\Service\Client;

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
        $client = new Client($this->url);
        $client->getEventDispatcher()->addListener('request.error', function(Event $event) {
            if ($event['response']->getStatusCode() == 404) {
                $event->stopPropagation();
                throw new ExceptionNotFound($event['response']->getMessage(), $event['response']->getStatusCode());
            }
        });
        return new Connection($client);
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
