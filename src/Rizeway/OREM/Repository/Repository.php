<?php

namespace Rizeway\OREM\Repository;


use Rizeway\OREM\Manager;

class Repository
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Rizeway\OREM\Manager
     */
    protected $manager;

    /**
     * @param Manager $manager
     * @param string $name
     */
    public function __construct(Manager $manager, $name)
    {
        $this->manager = $manager;
        $this->name = $name;
    }

    /**
     * @return object[]
     */
    public function findAll()
    {
        return $this->manager->findQuery($this->name);
    }

    /**
     * @param $id
     * @return \object|null
     */
    public function find($id)
    {
        return $this->manager->find($this->name, $id);
    }

    /**
     * @param array $urlParameters
     * @return \object[]
     */
    public function findQuery($urlParameters = array())
    {
        return $this->manager->findQuery($this->name, $urlParameters);
    }

    /** @param $id
     * @return object|null
     */
    public function findRelation($id, $relation)
    {
        return $this->manager->findRelation($this->name, $id, $relation);
    }
}
