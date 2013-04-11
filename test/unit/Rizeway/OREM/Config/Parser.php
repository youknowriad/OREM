<?php

namespace test\unit\Rizeway\OREM\Config;

use Rizeway\OREM\Config\Parser as TestedClass;
use atoum;

class Parser extends atoum\test
{
    public function testBase()
    {
        $this
            ->if($object = new TestedClass())
            ->and($config = array(
                'entity' => array(
                    'class' => '\mock\test',
                    'fields' => array(
                        'field1' => '',
                        'field2' => array(
                            'remote' => 'field2remote',
                            'primaryKey' => true
                        ),
                        'field3' => array(
                            'type' => 'boolean'
                        ),
                        'field4' => array(
                            'type' => 'integer'
                        ),
                        'field5' => array(
                            'type' => 'string'
                        )
                    )
                )
            ))
            ->then
                ->object($object)->isInstanceOf('Rizeway\\OREM\\Config\\Parser')
                ->array($mappings = $object->parse($config))->hasSize(1)
                ->string(key($mappings))->isEqualTo('entity')
                ->object($entityMapping = current($mappings))->isInstanceOf('Rizeway\\OREM\\Mapping\\MappingEntity')
                ->string($entityMapping->getClassname())->isEqualTo('\mock\test')
                ->string($entityMapping->getResourceUrl())->isEqualTo('entity')
                ->string($entityMapping->getName())->isEqualTo('entity')
                ->string($entityMapping->getPrimaryKey())->isEqualTo('field2')
                ->array($fieldMappings = $entityMapping->getFieldMappings())->hasSize(5)

                ->object($fieldMappings[0])->isInstanceOf('Rizeway\\OREM\\Mapping\\Field\\MappingFieldString')
                    ->string($fieldMappings[0]->getFieldName())->isEqualTo('field1')
                    ->string($fieldMappings[0]->getRemoteName())->isEqualTo('field1')
                ->object($fieldMappings[1])->isInstanceOf('Rizeway\\OREM\\Mapping\\Field\\MappingFieldString')
                    ->string($fieldMappings[1]->getFieldName())->isEqualTo('field2')
                    ->string($fieldMappings[1]->getRemoteName())->isEqualTo('field2remote')
                ->object($fieldMappings[2])->isInstanceOf('Rizeway\\OREM\\Mapping\\Field\\MappingFieldBoolean')
                ->object($fieldMappings[3])->isInstanceOf('Rizeway\\OREM\\Mapping\\Field\\MappingFieldInteger')
                ->object($fieldMappings[4])->isInstanceOf('Rizeway\\OREM\\Mapping\\Field\\MappingFieldString')
        ;
    }

    public function testHasMany()
    {
        $this
            ->if($object = new TestedClass())
            ->and($config = array(
                'entity' => array(
                    'class' => '\mock\test',
                    'fields' => array(
                        'field' => array(
                            'primaryKey' => true
                        ),
                    ),
                    'hasMany' => array(
                        'relation' => array(
                            'remote' => 'remote_relation',
                            'targetEntity' => 'entity2'
                        ),
                    )
                ),
                'entity2' => array(
                'class' => '\mock\test2',
                'fields' => array(
                    'field' => array(
                        'primaryKey' => true
                    ),
                ),
            )))
            ->then
                ->array($mappings = $object->parse($config))->hasSize(2)
                ->object($entityMapping = $mappings['entity'])->isInstanceOf('Rizeway\\OREM\\Mapping\\MappingEntity')
                ->array($hasManyMappings = $entityMapping->getHasManyMappings())->hasSize(1)
                ->object($hasManyMappings[0])->isInstanceOf('Rizeway\\OREM\\Mapping\\Relation\\MappingRelationHasMany')
                ->string($hasManyMappings[0]->getFieldName())->isEqualTo('relation')
                ->string($hasManyMappings[0]->getRemoteName())->isEqualTo('remote_relation')
                ->string($hasManyMappings[0]->getEntityName())->isEqualTo('entity2')
        ;
    }

    public function testHasOne()
    {
        $this
            ->if($object = new TestedClass())
            ->and($config = array(
                    'entity' => array(
                        'class' => '\mock\test',
                        'fields' => array(
                            'field' => array(
                                'primaryKey' => true
                            ),
                        ),
                        'hasOne' => array(
                            'relation' => array(
                                'remote' => 'remote_relation',
                                'targetEntity' => 'entity2'
                            ),
                        )
                    ),
                    'entity2' => array(
                        'class' => '\mock\test2',
                        'fields' => array(
                            'field' => array(
                                'primaryKey' => true
                            ),
                        ),
                    )))
            ->then
                ->array($mappings = $object->parse($config))->hasSize(2)
                ->object($entityMapping = $mappings['entity'])->isInstanceOf('Rizeway\\OREM\\Mapping\\MappingEntity')
                ->array($hasManyMappings = $entityMapping->getHasOneMappings())->hasSize(1)
                ->object($hasManyMappings[0])->isInstanceOf('Rizeway\\OREM\\Mapping\\Relation\\MappingRelationHasOne')
                ->string($hasManyMappings[0]->getFieldName())->isEqualTo('relation')
                ->string($hasManyMappings[0]->getRemoteName())->isEqualTo('remote_relation')
                ->string($hasManyMappings[0]->getEntityName())->isEqualTo('entity2')
        ;
    }

    public function testInvalidClassname()
    {
        $this
            ->if($object = new TestedClass())
            ->and($config = array(
                    'entity' => array(
                        'class' => 'test',
                        'fields' => array()
                    )
                ))
            ->then
                ->exception(function() use($object, $config) { $object->parse($config); })
                    ->hasMessage('Invalid classname for Entity entity')
        ;
    }

    public function testInvalidFieldType()
    {
        $this
            ->if($object = new TestedClass())
            ->and($config = array(
                    'entity' => array(
                        'class' => '\mock\test',
                        'fields' => array(
                            'toto' => array('type' => 'mytype')
                        )
                    )
                ))
            ->then
                ->exception(function() use($object, $config) { $object->parse($config); })
                    ->hasMessage('Invalid field type mytype')
        ;
    }

    public function testNoPrimaryKey()
    {
        $this
            ->if($object = new TestedClass())
            ->and($config = array(
                    'entity' => array(
                        'class' => '\mock\test',
                        'fields' => array(
                            'toto' => array('type' => 'string')
                        )
                    )
                ))
            ->then
                ->exception(function() use($object, $config) { $object->parse($config); })
                    ->hasMessage('A field must be defined as primary key')
        ;
    }
}
