<?php
require(__DIR__ . '/../vendor/autoload.php');

use PhpRouter\Exception\RouteNotFoundException;
use PhpRouter\Route;
use PhpRouter\RouteCollection;
use PhpRouter\Router;
use PhpRouter\RouteRequest;
use PhpSandbox\Evaluator\Config;
use PhpSandbox\Evaluator\Evaluator;
use PhpSandbox\Library\LibraryRepository;
use PhpSandbox\Library\LibraryService;
use PhpSandbox\Snippet\SnippetException;
use PhpSandbox\Snippet\SnippetRepository;
use PhpSandbox\Snippet\SnippetService;

// load config file
$config = new Config(__DIR__ . '/../config/config.php');
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

        $evaluator = new Evaluator($config, $version);
        $result = $evaluator->evaluate($code);

        $benchmark = [
            'memory' => sprintf('%.3f', $evaluator->getMemory() / 1024.0 / 1024.0),
            'memory_peak' => sprintf('%.3f', $evaluator->getMemoryPeak() / 1024.0 / 1024.0),
            'time' => sprintf('%.3f', $evaluator->getTime() * 1000)
        ];

        echo json_encode(compact('result', 'benchmark'));
    }
}));

$snippetService = new SnippetService(new SnippetRepository($config->read('snippets_dir')));

/**
 * Validate and save new snippet
 */
$routing->attach(new Route('POST /save_snippet.json [ajax]', function() use ($snippetService){

    if (empty($_POST['name']) || empty($_POST['code'])) {
        echo json_encode(['status' => 'error', 'message' => 'Name and content can\'t be empty.']);
        return;
    }

    try {
        $snippetService->save($_POST['name'], $_POST['code']);
        echo json_encode(['status' => 'success']);
    } catch (SnippetException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}));

/**
 * Get snippets list
 */
$routing->attach(new Route('GET  /get_snippets_list.json [ajax]', function() use ($snippetService) {
    echo json_encode($snippetService->getList());
}));

/**
 * Load snippet contents
 */
$routing->attach(new Route('GET  /get_snippet/@filename', ['filename' => '[/\w]+.php'], function($params) use ($snippetService) {
    echo json_encode($snippetService->load($params['filename']));
}));

/**
 * Delete specified snippets
 */
$routing->attach(new Route('DELETE  /delete_snippet/@filename', ['filename' => '[/\w]+.php'], function($params) use ($snippetService) {
    $snippetService->delete($params['filename']);
}));

$libraryService = new LibraryService(new LibraryRepository($config->read('vendors_dir'), $config->read('tmp_dir')));

/**
 * Get libraries list
 */
$routing->attach(new Route('GET  /get_libraries_list.json', function() use ($libraryService) {
    echo json_encode(['composer' => $libraryService->getList()]);
}));

/**
 * Remove library
 */
$routing->attach(new Route('DELETE  /delete_library/@filename [ajax]', ['filename' => '[/\w]+'], function($param) use ($config, $libraryService) {
    ini_set('memory_limit', $config->read('memory_limit'));
    $libraryService->removePackage($param['filename']);
}));

/**
 * Add new library
 */
$routing->attach(new Route('POST  /add_library.json [ajax]', function() use ($config, $libraryService) {
    ini_set('memory_limit', $config->read('memory_limit'));

    if (empty($_POST['name']) || false === $libraryService->validatePackage($_POST['name'])) {
        echo json_encode(['status' => 'error', 'message' => 'Wrong package name or package doesn\'t exist.']);
        return;
    }

    try {
        $libraryService->addPackage($_POST['name']);
        echo json_encode(['status' => 'success']);
    } catch (SnippetException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}));

/**
 * Get available php versions
 */
$routing->attach(new Route('GET  /get_php_versions.json [ajax]', function() use ($config) {
    $phpPaths = $config->read('fast_cgi_hosts');
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
