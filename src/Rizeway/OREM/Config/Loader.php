<?php

namespace Rizeway\OREM\Config;

use Symfony\Component\Finder\Adapter\PhpAdapter;
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
            $config[basename($file->getFilename(), '.orem.yml')] = Yaml::parse($file->getPathname());
        }

        return $config;
    }
}
