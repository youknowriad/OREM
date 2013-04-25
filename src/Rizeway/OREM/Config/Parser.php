<?php

namespace Rizeway\OREM\Config;

use Rizeway\OREM\Mapping\MappingEntity;
use Rizeway\OREM\Mapping\Relation\MappingRelationHasMany;
use Rizeway\OREM\Mapping\Relation\MappingRelationHasOne;

class Parser
{
    /**
     * @param $entitiesConfiguration
     * @return MappingEntity[]
     * @throws \Exception
     */
    public function parse($entitiesConfiguration)
    {
        $mappings = array();
        foreach ($entitiesConfiguration as $name => $entityConfiguration) {

            $class = $entityConfiguration['class'];
            if (!class_exists($class)) {
                throw new \Exception('Invalid classname for Entity '.$name);
            }

            $fieldmappings = array();
            $primaryKey = null;
            foreach ($entityConfiguration['fields'] as $fieldname => $fieldConfiguration) {
                $primaryKey = isset($fieldConfiguration['primaryKey']) && $fieldConfiguration['primaryKey'] ? $fieldname : $primaryKey;
                $type = isset($fieldConfiguration['type']) ? $fieldConfiguration['type'] : 'default';
                $classname = '\Rizeway\OREM\Mapping\Field\MappingField'.ucfirst($type);
                if (!class_exists($classname)) {
                    throw new \Exception('Invalid field type '.$type);
                }
                $fieldmappings[] = new $classname($fieldname, isset($fieldConfiguration['remote']) ? $fieldConfiguration['remote'] : null);
            }
            if (is_null($primaryKey)) {
                throw new \Exception('A field must be defined as primary key');
            }

            $hasManyMappings = array();
            if (isset($entityConfiguration['hasMany'])) {
                foreach ($entityConfiguration['hasMany'] as $fieldname => $relationConfiguration) {
                    $hasManyMappings[] = new MappingRelationHasMany(
                        $relationConfiguration['targetEntity'],
                        $fieldname,
                        isset($relationConfiguration['remote']) ? $relationConfiguration['remote'] : null,
                        isset($relationConfiguration['lazy']) ? $relationConfiguration['lazy'] : false
                    );
                }
            }

            $hasOneMappings = array();
            if (isset($entityConfiguration['hasOne'])) {
                foreach ($entityConfiguration['hasOne'] as $fieldname => $relationConfiguration) {
                    $hasOneMappings[] = new MappingRelationHasOne(
                        $relationConfiguration['targetEntity'],
                        $fieldname,
                        isset($relationConfiguration['remote']) ? $relationConfiguration['remote'] : null,
                        isset($relationConfiguration['lazy']) ? $relationConfiguration['lazy'] : false
                    );
                }
            }

            $url = isset($entityConfiguration['url']) ? $entityConfiguration['url'] : null;

            $mappings[$name] = new MappingEntity($name, $class, $primaryKey, $fieldmappings, $hasManyMappings, $hasOneMappings, $url);
        }

        return $mappings;
    }
}
