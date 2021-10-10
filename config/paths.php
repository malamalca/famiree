<?php
/**
 * Use the DS to separate the directories in other defines
 */
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * The full path to the directory which holds "App", WITHOUT a trailing DS.
 */
define('ROOT', dirname(__DIR__));

/**
 * The actual directory name for the "App".
 */
define('APP_DIR', 'src');

/**
 * Path to the application's directory.
 */
define('APP', ROOT . DS . APP_DIR . DS);

/**
 * Path to the config directory.
 */
define('CONFIG', ROOT . DS . 'config' . DS);

/**
 * Path to the logs directory.
 */
define('LOGS', ROOT . DS . 'logs' . DS);

/**
 * File path to the webroot directory.
 */
define('WWW_ROOT', ROOT . DS . 'webroot' . DS);

/**
 * Path to the templates directory.
 */
define('TEMPLATES', ROOT . DS . 'templates' . DS);

/**
 * Path to the temporary files directory.
 */
define('TMP', ROOT . DS . 'tmp' . DS);

/**
 * Path to the photos directory.
 */
define('PHOTOS', ROOT . DS . 'photos' . DS);

/**
 * Path to the resources directory.
 */
define('RESOURCES', ROOT . DS . 'resources' . DS);
