<?php

return [

    /**
     * PHP versions with connected fastCGI hosts to evaluate code
     */
    'fast_cgi_hosts' => [
        '5.6' => 'php56',
        '7.0' => 'php70',
        '7.1' => 'php71'
    ],

    /**
     * Temporary directory - for file with evaluated code etc.
     */
    'tmp_dir' => realpath(__DIR__ . '/../tmp') . '/',

    /**
     * Dir where all snippets will be stored
     */
    'snippets_dir' => realpath(__DIR__ . '/../tmp/snippets') . '/',

    /**
     * Dir where all libraries/vendors should be stored
     */
    'vendors_dir' => realpath(__DIR__ . '/../tmp/vendor') . '/',

    /**
     * Pre-execute scripts
     */
    'bootstrap_file' => realpath(__DIR__ . '/../src/bootstrap.php'),

    /**
     * Set memory limit for Sandbox
     */
    'memory_limit' => '1G',

    /**
     * Which benchmarks should be collect on end of script
     */
    'benchmarks' => [
        'memory' => '(memory_get_usage() - $ev__memory[\'usage\'])',
        'memory_peak' => '(memory_get_peak_usage() - $ev__memory[\'peak\'])'
    ]
];