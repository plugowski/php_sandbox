<?php

return [

    /**
     * cli command for php, if you are using other version put there path to your php
     */
    'php_command' => 'php',

    /**
     * destinations for different php versions, uncomment or modify paths that match to your environment
     */
    'php_commands' => [
        '5.6' => '/opt/local/bin/php56',
        '5.4' => '/opt/local/bin/php54',
        '5.5' => '/opt/local/bin/php55',
        '7.0' => '/opt/local/bin/php70'
    ],

    /**
     * Dir where temporary file code.php will be created and executed
     */
    'tmp_dir' => __DIR__ . '/../tmp/',

    /**
     * Dir where all snippets will be stored
     */
    'snippets_dir' => __DIR__ . '/../tmp/snippets/',

    /**
     * Dir where all libraries/vendors should be stored
     */
    'vendors_dir' => __DIR__ . '/../tmp/vendor/',

    /**
     * Pre-execute scripts
     */
    'bootstrap_file' => realpath(__DIR__ . '/bootstrap.php'),

    /**
     * Which benchmarks should be collect on end of script
     */
    'benchmarks' => [
        'memory' => '(memory_get_usage() - $ev__memory[\'usage\'])',
        'memory_peak' => '(memory_get_peak_usage() - $ev__memory[\'peak\'])'
    ],

    /**
     * Definition of directives use when script will be executed
     */
    'directives' => [
        // 'allow_url_fopen' => 'Off',
        // 'allow_url_include' => 'Off',
        'max_execution_time' => '5'
    ],

    /**
     * List of danger functions in php, please uncomment one you want to block
     */
    'disable_functions' => [
//        'apache_child_terminate',
//        'apache_setenv',
//        'curl_exec',
//        'curl_multi_exec',
//        'define_syslog_variables',
//        'escapeshellarg',
//        'escapeshellcmd',
//        'eval',
//        'exec',
//        'fopen',
//        'fp',
//        'fput',
//        'ftp_connect',
//        'ftp_exec',
//        'ftp_get',
//        'ftp_login',
//        'ftp_nb_fput',
//        'ftp_put',
//        'ftp_raw',
//        'ftp_rawlist',
//        'highlight_file',
//        'ini_alter',
//        'ini_get_all',
//        'ini_restore',
//        'ini_set',
//        'inject_code',
//        'mysql_pconnect',
//        'openlog',
//        'parse_ini_file',
//        'passthru',
//        'phpAds_XmlRpc',
//        'phpAds_remoteInfo',
//        'phpAds_xmlrpcDecode',
//        'phpAds_xmlrpcEncode',
//        'php_uname',
//        'popen',
//        'posix_getpwuid',
//        'posix_kill',
//        'posix_mkfifo',
//        'posix_setpgid',
//        'posix_setsid',
//        'posix_setuid',
//        'posix_uname',
//        'proc_close',
//        'proc_get_status',
//        'proc_nice',
//        'proc_open',
//        'proc_terminate',
//        'set_time_limit',
//        'shell_exec',
//        'show_source',
//        'syslog',
//        'system',
//        'xmlrpc_entity_decode'
    ]
];