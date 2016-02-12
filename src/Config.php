<?php
namespace PhpSandbox;

/**
 * Class Config
 */
class Config
{
    /**
     * @var array
     */
    public static $disable_functions = [
        'exec', 'passthru', 'shell_exec', 'system', 'proc_open', 'popen', 'curl_exec', 'curl_multi_exec',
        'parse_ini_file', 'show_source'
    ];
    /**
     * @var array
     */
    public static $other_directives = [
        'allow_url_fopen' => 'Off',
        'allow_url_include' => 'Off',
    ];
    /**
     * @var int
     */
    public static $error_reporting = E_ALL;
    /**
     * @var string
     */
    public static $tempDir = '/tmp/';
    /**
     * @var string
     */
    public static $phpCommand = "php";
}