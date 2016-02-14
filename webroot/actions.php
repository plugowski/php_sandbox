<?php
require(__DIR__ . '/../vendor/autoload.php');

use PhpSandbox\Evaluator\Config;
use PhpSandbox\Evaluator\Evaluator;

$f3 = \Base::instance();

// load config file
$config = new Config(__DIR__ . '/../src/config.php');

/**
 * get last executed script from tmp file
 */
$f3->route('GET /get_last [ajax]', function() use ($config) {

    if (file_exists($config->read('tmp_dir') . '/code.php')) {
        echo (new Evaluator($config))->getLastCode();
    }
});

/**
 * execute code from post
 */
$f3->route('POST /execute', function($f3) use ($config) {

    /** @var \Base $f3 */
    if ($f3->exists('POST.code')) {

        $code = $f3->get('POST.code');
        if (!preg_match('/^<\?php.*/', $code)) {
            $code = '<?php ' . $code;
        }

        $evaluator = new Evaluator($config);
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