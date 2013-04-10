<?php

namespace Rizeway\OREM\Mapping\Field;

use Rizeway\OREM\Mapping\MappingInterface;

class MappingField implements MappingInterface
{
    /**
     * @var string
     */
    protected $fieldname;

    /**
     * @var string
     */
    protected $remotename;

    public function __construct($fieldname, $remotename = null)
    {
        $this->fieldname = $fieldname;
        $this->remotename = $remotename;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldname;
    }

    /**
     * @return string
     */
    public function getRemoteName()
    {
        return is_null($this->remotename) ? $this->fieldname : $this->remotename;
    }
}
