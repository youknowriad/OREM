<?php
use \mageekguy\atoum;

define('TESTS_ROOT', __DIR__ . '/test/unit');
define('COVERAGE_TITLE', 'Rizeway OREM');
define('COVERAGE_DIRECTORY', './coverage');

if(false === is_dir(COVERAGE_DIRECTORY)) {
    mkdir(COVERAGE_DIRECTORY, 0777, true);
}

$coverageField = new atoum\report\fields\runner\coverage\html(COVERAGE_TITLE, COVERAGE_DIRECTORY);

$runner->addTestsFromDirectory(TESTS_ROOT);
$runner->setBootstrapFile(TESTS_ROOT . '/../bootstrap.php');
$cliReport = $script->addDefaultReport();
$cliReport->addField($coverageField);
