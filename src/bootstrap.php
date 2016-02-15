<?php
// start count memory usage
$ev__memory_bootstrap_start = [
    'usage' => memory_get_usage(),
    'peak' => memory_get_peak_usage()
];

require(__DIR__ . '/../vendor/raveren/kint/Kint.class.php');

// Kint configuration
Kint::$displayCalledFrom = false;
Kint::$expandedByDefault = true;
Kint::$cliDetection = false;
Kint::$theme = 'solarized-dark';


// we do not want calculate memory used by bootstrap
$ev__memory = [
    'usage' => memory_get_usage(),
    'peak' => memory_get_peak_usage()
];

array_walk($ev__memory, function(&$value, $key) use ($ev__memory_bootstrap_start){
    $value = ($value - $ev__memory_bootstrap_start[$key]);
});