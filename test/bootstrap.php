<?php
$vendor = dirname(__DIR__) . '/vendor';
if(false === is_dir($vendor)) {
    $vendor = dirname(__DIR__) . '/../..';
}

require_once $vendor . '/atoum/atoum/scripts/runner.php';
require_once $vendor . '/autoload.php';
