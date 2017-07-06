<?php

    $GLOBALS['APPLICATION_START_TIME'] = microtime(true);

    /**
     * Debug Mode Settings
     */
    if (isset($_SERVER['REMOTE_ADDR']) && (substr($_SERVER['REMOTE_ADDR'], 0, 10) == '192.168.0.'))
    {
        define('DEBUG_MODE',        true);
        ini_set('display_errors',   true);
        error_reporting(E_ALL | E_STRICT);
    }
    else
    {
        define('DEBUG_MODE',        true);
        ini_set('display_errors',   true);
        error_reporting(E_ALL | E_STRICT);
    }

    /**
     * Path constants
     */
    define('BASE_PATH',        str_replace('\\', '/', dirname(dirname(__FILE__))).'/');
    define('ROOT_PATH',        BASE_PATH.'html/');
    define('CONFIG_PATH',      BASE_PATH.'config/');
    define('LIB_PATH',         BASE_PATH.'php2/');

    define('ROOT_URL',            'http://php2-v3.ekalosha.dev.solartxit.com');
    define('ROOT_SSL_URL',        'https://php2-v3.ekalosha.dev.solartxit.com');
    define('STATIC_ROOT_URL',     'http://php2-v3.static.ekalosha.dev.solartxit.com');
    define('STATIC_ROOT_SSL_URL', 'https://php2-v3.static.ekalosha.dev.solartxit.com');
