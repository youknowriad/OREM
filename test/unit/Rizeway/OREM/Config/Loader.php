<?php

namespace test\unit\Rizeway\OREM\Config;

use Rizeway\OREM\Config\Loader as TestedClass;
use Symfony\Component\Yaml\Yaml;
use atoum;

class Loader extends atoum\test
{
    protected $directory;
    protected $file;

    public function beforeTestMethod($method)
    {
        $this->directory = '/tmp/'.uniqid();
        $this->file = $this->directory.DIRECTORY_SEPARATOR.'test.orem.yml';
        mkdir($this->directory);
        file_put_contents($this->file, Yaml::dump(array(
            'class' => 'toto', 'fields' => array('toto', 'tata')
        )));
    }

    public function testAll()
    {
        $this
            ->if($object = new TestedClass($this->directory))
            ->then
                ->object($object)->isInstanceOf('Rizeway\\OREM\\Config\\Loader')
                ->array($object->load())->isEqualTo(array(
                    'test' => array('class' => 'toto', 'fields' => array('toto', 'tata'))
                ))
        ;
    }

    public function afterTestMethod($method)
    {
        unlink($this->file);
        rmdir($this->directory);
    }
}
