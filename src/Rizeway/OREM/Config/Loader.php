<?php

namespace Rizeway\OREM\Config;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class Loader
{
    /**
     * @var string
     */
    protected $directory;

    /**
     * @param $directory
     */
    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    /**
     * @return array
     */
    public function load()
    {
        $config = array();
        $finder = new Finder();
        $iterator = $finder->files('*.orem.yml')->depth(0)->in($this->directory);
        foreach ($iterator as $file) {
            $config[substr($file->getFilename(), 0, -9)] = Yaml::parse($file->getRealpath());
        }

        return $config;
    }
}
