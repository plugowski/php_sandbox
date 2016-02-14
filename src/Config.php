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
        'apache_child_terminate',
        'apache_setenv',
        'curl_exec',
        'curl_multi_exec',
        'define_syslog_variables',
        'escapeshellarg',
        'escapeshellcmd',
        'eval',
        'exec',
//        'fopen',
        'fp',
        'fput',
        'ftp_connect',
        'ftp_exec',
        'ftp_get',
        'ftp_login',
        'ftp_nb_fput',
        'ftp_put',
        'ftp_raw',
        'ftp_rawlist',
        'highlight_file',
        'ini_alter',
        'ini_get_all',
        'ini_restore',
//        'ini_set',
        'inject_code',
        'mysql_pconnect',
        'openlog',
        'parse_ini_file',
        'passthru',
        'phpAds_XmlRpc',
        'phpAds_remoteInfo',
        'phpAds_xmlrpcDecode',
        'phpAds_xmlrpcEncode',
        'php_uname',
        'popen',
        'posix_getpwuid',
        'posix_kill',
        'posix_mkfifo',
        'posix_setpgid',
        'posix_setsid',
        'posix_setuid',
        'posix_uname',
        'proc_close',
        'proc_get_status',
        'proc_nice',
        'proc_open',
        'proc_terminate',
        'set_time_limit',
        'shell_exec',
        'show_source',
        'syslog',
        'system',
        'xmlrpc_entity_decode'
    ];
    /**
     * @var array
     */
    public static $other_directives = [
        'allow_url_fopen' => 'Off',
        'allow_url_include' => 'Off',
        'max_execution_time' => '5'
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

    /**
     * @var string
     */
    public static $bootstrapFile = '/../webroot/bootstrap.php';

    /**
     * @return string
     */
    public static function getBootstrapPath()
    {
        return realpath(__DIR__ . self::$bootstrapFile);
    }
}