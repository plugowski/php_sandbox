<?php
require '../vendor/autoload.php';

use PhpSandbox\Evaluator;

if (!empty($_POST['code'])) {

    $code = $_POST['code'];
    if (!preg_match('/^<?php/', $code)) {
        $code = '<?php' . PHP_EOL . $code;
    }

    $evaluator = new Evaluator();
    echo $evaluator->evaluate($_POST['code']);
}