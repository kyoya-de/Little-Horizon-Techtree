<?php

/**
 * Workaround for debugger usage.
 * Call this script: php index.php <MVC-Path> <cookies>
 *
 * MVC-Path .. Path for MVC pattern in format /<controller>/<action>/<paramName1>/<paramValue1/...
 * cookies  .. Cookies in format cookieName1=cookieValue1&cookieName2=cookieValue2
 */
if (PHP_SAPI == 'cli') {
    $_SERVER['REQUEST_URI'] = $_SERVER['argv'][1];
    if (isset($_SERVER['argv'][2])) {
        foreach (explode('&', $_SERVER['argv'][2]) as $cookie) {
            list($key, $value) = explode('=', $cookie);
            $_COOKIE[$key] = $value;
        }
    }
}

// Define path to application directory
defined('APPLICATION_PATH') || define(
    'APPLICATION_PATH',
    realpath(dirname(__FILE__) . '/../application')
);

// Define application environment
defined('APPLICATION_ENV') || define(
    'APPLICATION_ENV',
    (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production')
);

// Ensure library/ is on include_path
set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath(APPLICATION_PATH . '/../library'),
            get_include_path(),
        )
    )
);

if (!class_exists('Debug') && APPLICATION_ENV == 'development') {
    include 'Debug.php';
}

/** Zend_Application */
require_once 'Zend/Application.php';
require_once 'Zend/Config/Ini.php';

$config = new Zend_Config_Ini(
    APPLICATION_PATH . '/configs/application.ini',
    APPLICATION_ENV,
    array('allowModifications' => true)
);
if (file_exists(APPLICATION_PATH . '/configs/application.local.ini')) {
    $localConfig = new Zend_Config_Ini(
        APPLICATION_PATH . '/configs/application.local.ini',
        APPLICATION_ENV
    );
    $config->merge($localConfig);
}
$application = new Zend_Application(
    APPLICATION_ENV,
    $config
);
$application->bootstrap()
            ->run();
