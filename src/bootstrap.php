<?php
use PhpSandbox\Evaluator\Config;

// start count memory usage
$ev__memory_bootstrap_start = [
    'usage' => memory_get_usage(),
    'peak' => memory_get_peak_usage()
];

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/Evaluator/Config.php';

$config = new Config(__DIR__ . '/config.php');

if (file_exists($config->read('vendors_dir') . '/autoload.php')) {
    require $config->read('vendors_dir') . '/autoload.php';
}

// Kint debug tool configuration
Kint::$expanded = true;
Kint::$display_called_from = false;
Kint_Renderer_Rich::$theme  = 'solarized-dark.css';

// to avoid count total usage with bootstrap, there I reset that values
$ev__memory = [
    'usage' => memory_get_usage(),
    'peak' => memory_get_peak_usage()
];

array_walk($ev__memory, function(&$value, $key) use ($ev__memory_bootstrap_start) {
    $value -= $ev__memory_bootstrap_start[$key];
});