<?php

namespace Rizeway\OREM\Config;

use Rizeway\OREM\Mapping\MappingEntity;

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
                $primaryKey = isset($fieldConfiguration['primary_key']) && $fieldConfiguration['primary_key'] ? $fieldname : $primaryKey;
                $type = isset($fieldConfiguration['type']) ? $fieldConfiguration['type'] : 'string';
                $classname = '\Rizeway\OREM\Mapping\Field\MappingField'.ucfirst($type);
                if (!class_exists($classname)) {
                    throw new \Exception('Invalid field type '.$type);
                }
                $fieldmappings[] = new $classname($fieldname, isset($fieldConfiguration['remote']) ? $fieldConfiguration['remote'] : null);
            }

            if (!class_exists($class)) {
                throw new \Exception('Invalid classname for Entity '.$name);
            }

            if (is_null($primaryKey)) {
                throw new \Exception('A field must be defined as primary key');
            }

            $mappings[$name] = new MappingEntity($name, $class, $fieldmappings, $primaryKey);
        }

        return $mappings;
    }
}
