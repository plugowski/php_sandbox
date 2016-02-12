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
        echo file_get_contents(Config::$tempDir . '/code.php');
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
            $code = '<?php' . PHP_EOL . $code;
        }

        $evaluator = new Evaluator();
        echo $evaluator->evaluate($code);
    }
});

$f3->run();