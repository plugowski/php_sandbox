<?php
require(__DIR__ . '/../vendor/autoload.php');

use PhpSandbox\Config;
use PhpSandbox\Evaluator;

$f3 = \Base::instance();

/**
 * get last executed script from tmp file
 */
$f3->route('GET /get_last [ajax]', function() {

    if (file_exists(Config::$tempDir . '/code.php')) {
        echo (new Evaluator())->getLastCode();
    }
});

/**
 * execute code from post
 */
$f3->route('POST /execute', function($f3) {

    /** @var \Base $f3 */
    if ($f3->exists('POST.code')) {

        $code = $f3->get('POST.code');
        if (!preg_match('/^<\?php.*/', $code)) {
            $code = '<?php ' . $code;
        }

        $evaluator = new Evaluator();
        $result = $evaluator->evaluate($code);

        $benchmark = [
            'memory' => sprintf('%.3f', ($evaluator->getMemory()) / 1024.0 / 1024.0),
            'memory_peak' => sprintf('%.3f', ($evaluator->getMemoryPeak()) / 1024.0 / 1024.0),
            'time' => sprintf('%.3f', (($evaluator->getTime()) * 1000))
        ];

        echo json_encode(compact('result', 'benchmark'));
    }
});

$f3->run();