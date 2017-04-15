<?php

namespace Rizeway\OREM\Entity;

use Rizeway\OREM\Manager;

class Entity
{
    /**
     * @var string
     */
    protected $__oremName;


    /**
     * @var \Rizeway\OREM\Manager
     */
    protected $__oremManager;

    /**
     * @param string $name
     * @return Entity
     */
    public function __setOremName($name)
    {
        $this->__oremName = $name;

        return $this;
    }

    /**
     * @param \Rizeway\OREM\Manager $manager
     * @return Entity
     */
    public function __setOremManager(Manager $manager)
    {
        $this->__oremManager = $manager;

        return $this;
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if(preg_match('/get([A-Z][a-zA-Z0-9_]*)/', $method, $matches)) {
            $relation = strtolower($matches[1]);
            $primaryKey = $this->__oremManager->getMappingForObject($this)->getPrimaryKey();

            $this->$relation = $this->__oremManager->getRepository($this->__oremName)->findRelation(
                $this->$primaryKey,
                $relation,
                count($args) ? $args[0] : array()
            );

            return $this->$relation;
        }
    }

    public function refresh() {
        $this->__oremManager->find(
            $this->__oremName,
            $this->__oremManager->getMappingForObject($this)->getPrimaryKey()
        );

        return $this;
    }
}
