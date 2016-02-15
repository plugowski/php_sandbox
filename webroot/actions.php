<?php
require(__DIR__ . '/../vendor/autoload.php');

use PhpRouter\Route;
use PhpRouter\RouteCollection;
use PhpRouter\Router;
use PhpRouter\RouteRequest;
use PhpSandbox\Evaluator\Config;
use PhpSandbox\Evaluator\Evaluator;

// load config file
$config = new Config(__DIR__ . '/../src/config.php');
$routing = new RouteCollection();

/**
 * get last executed script from tmp file
 */
$routing->attach(new Route('GET /get_last [ajax]', function() use ($config) {

    if (file_exists($config->read('tmp_dir') . '/code.php')) {
        echo (new Evaluator($config))->getLastCode();
    }
}));

/**
 * execute code from post
 */
$routing->attach(new Route('POST /execute [ajax]', function() use ($config) {

    /** @var \Base $f3 */
    if (isset($_POST['code'])) {

        $code = $_POST['code'];
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
}));

(new Router(new RouteRequest(), $routing))->run();