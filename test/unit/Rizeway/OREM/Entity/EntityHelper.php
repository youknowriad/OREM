<?php

namespace test\unit\Rizeway\OREM\Entity;

use Rizeway\OREM\Entity\EntityHelper as TestedClass;
use Rizeway\OREM\Mapping\Field\MappingFieldString;
use Rizeway\OREM\Mapping\MappingEntity;
use atoum;

class MyEntity {
    private $test;
    public $toto;

    public function __construct($primary = null)
    {
        $this->test = $primary;
    }

    public function getTest()
    {
        return $this->test;
    }
}

class EntityHelper extends atoum\test
{
    public function testAll()
    {
        $this
            ->if($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity('entity', '\test\unit\Rizeway\OREM\Entity\MyEntity', 'test', array('test' => $mappingField), array()))
            ->and($object = new TestedClass($mapping))
            ->and($entity = new MyEntity())
            ->and($entity->toto = 'test')
            ->and($object->setPropertyValue($entity, 'test', 'value'))
            ->then
                ->string($object->getAccessiblePropertyValue($entity, 'toto'))->isEqualTo('test')
                ->string($object->getPropertyValue($entity, 'toto'))->isEqualTo('test')
                ->string($object->getPropertyValue($entity, 'test'))->isEqualTo('value')
                ->string($object->getPrimaryKey($entity))->isEqualTo('value')
        ;
    }

    public function testGetPrimaryKey()
    {
        $this
            ->if($mappingField = new MappingFieldString('test'))
            ->and($mapping = new MappingEntity(
                'entity',
                '\test\unit\Rizeway\OREM\Entity\MyEntity',
                'test',
                array('test' => $mappingField),
                array()
            ))
            ->and($object = new TestedClass($mapping))
            ->and($entity = new \test\unit\Rizeway\OREM\Entity\MyEntity())
            ->then
                ->variable($object->getPrimaryKey($entity))->isNull()
            ->if($entity = new \test\unit\Rizeway\OREM\Entity\MyEntity($primaryKey = uniqid()))
            ->then()
                ->string($object->getPrimaryKey($entity))->isEqualTo($primaryKey)
            ->if($mapping = new MappingEntity(
                'entity',
                '\test\unit\Rizeway\OREM\Entity\MyEntity',
                null,
                array('test' => $mappingField),
                array()
            ))
            ->and($object = new TestedClass($mapping))
            ->and($entity = new \test\unit\Rizeway\OREM\Entity\MyEntity())
            ->then()
                ->string($object->getPrimaryKey($entity))->isEqualTo(spl_object_hash($entity))
        ;
    }
}
