<?php
require(__DIR__ . '/../vendor/autoload.php');

use PhpRouter\Exception\RouteNotFoundException;
use PhpRouter\Route;
use PhpRouter\RouteCollection;
use PhpRouter\Router;
use PhpRouter\RouteRequest;
use PhpSandbox\Evaluator\Config;
use PhpSandbox\Evaluator\Evaluator;
use PhpSandbox\Evaluator\Snippet;
use PhpSandbox\Evaluator\SnippetException;

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
$routing->attach(new Route('POST /execute/@phpversion.json [ajax]', ['phpversion' => 'null|[\d\.]+'], function($params) use ($config) {

    if (isset($_POST['code'])) {

        $code = $_POST['code'];
        if (!preg_match('/^<\?php.*/', $code)) {
            $code = '<?php ' . $code;
        }

        $version = !empty($params['phpversion']) ? $params['phpversion'] : null;

        $evaluator = new Evaluator($config);
        $evaluator->setPHP($version);
        $result = $evaluator->evaluate($code);

        $benchmark = [
            'memory' => sprintf('%.3f', ($evaluator->getMemory()) / 1024.0 / 1024.0),
            'memory_peak' => sprintf('%.3f', ($evaluator->getMemoryPeak()) / 1024.0 / 1024.0),
            'time' => sprintf('%.3f', (($evaluator->getTime()) * 1000))
        ];

        echo json_encode(compact('result', 'benchmark'));
    }
}));

/**
 * Validate and save new snippet
 */
$routing->attach(new Route('POST /save_snippet.json [ajax]', function() use ($config){

    if (!empty($_POST['name']) && !empty($_POST['code'])) {
        try {
            (new Snippet($config))->save($_POST['name'], $_POST['code']);
            echo json_encode(['status' => 'success']);
        } catch (SnippetException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}));

/**
 * Get snippets list
 */
$routing->attach(new Route('GET  /get_snippets_list.json', function() use ($config) {
    $snippets = (new Snippet($config))->getList();
    echo json_encode($snippets);
}));

$routing->attach(new Route('GET  /get_snippet/@filename', ['filename' => '[/\w]+.php'], '\PhpSandbox\Evaluator\Snippet->load', [$config]));
$routing->attach(new Route('DELETE  /delete_snippet/@filename', ['filename' => '[/\w]+.php'], '\PhpSandbox\Evaluator\Snippet->delete', [$config]));

/**
 * Get snippets list
 */
$routing->attach(new Route('GET  /get_php_versions.json [ajax]', function() use ($config) {
    $phpPaths = $config->read('php_commands');
    $versions = empty($phpPaths) ? [] : array_keys($phpPaths);
    echo json_encode(compact('versions'));
}));

try {
    $request = new RouteRequest();
    (new Router($request, $routing))->run();
} catch (RouteNotFoundException $e) {

    header("HTTP/1.0 404 Not Found");
    if (true === $request->isAjax()) {
        echo json_encode(['error' => 'Not Found']);
    } else {
        echo "<h1>404. Not Found.</h1>";
    }
}
