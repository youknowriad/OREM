<?php

namespace Rizeway\OREM\Config;

use Rizeway\OREM\Mapping\MappingEntity;
use Rizeway\OREM\Mapping\MappingField;

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
            $fieldmappings = array();
            $primaryKey = null;
            foreach ($entityConfiguration['fields'] as $fieldname => $fieldConfiguration) {
                $primaryKey = isset($fieldConfiguration['primary_key']) && $fieldConfiguration['primary_key'] ? $name : $primaryKey;
                $fieldmappings[] = new MappingField($fieldname, isset($fieldConfiguration['remote']) ? $fieldConfiguration['remote'] : null);
            }

            if (!class_exists($class)) {
                throw new \Exception('Invalid classname for Entity '.$name);
            }

            if (is_null($primaryKey)) {
                throw new \Exception('A field must be defined as primary key '.$primaryKey);
            }

            $mappings[$name] = new MappingEntity($name, $class, $fieldmappings, $primaryKey);
        }

        return $mappings;
    }
}
