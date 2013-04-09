<?php

namespace Rizeway\OREM;

use Rizeway\OREM\Repository\Repository;
use Rizeway\OREM\Connection\ConnectionInterface;

class Manager
{
    /**
     * @var \Rizeway\OREM\Connection
     */
    protected $connection;

    /**
     * @var array|Mapping\MappingEntity[]
     */
    protected $mappings;

    /**
     * @param ConnectionInterface $connection
     * @param \Rizeway\OREM\Mapping\MappingEntity[] $mappings
     */
    public function __construct(ConnectionInterface $connection, array $mappings)
    {
        $this->connection = $connection;
        $this->mappings   = $mappings;
    }

    public function getRepository($entityName)
    {
        if (!isset($this->mappings[$entityName])) {
            throw new \Exception('Unknown Entity : '.$entityName);
        }

        return new Repository($this->connection, $this->mappings[$entityName]);
    }
}
