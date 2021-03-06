<?php

/**
 * CodeIgniter Version
 *
 * @var	string
 *
 */
    define('CI_VERSION', '4.0-dev');

/*
 * ------------------------------------------------------
 *  Load the framework constants
 * ------------------------------------------------------
 */
//    if (file_exists(APPPATH.'Config/'.ENVIRONMENT.'/constants.php'))
//    {
//        require_once(APPPATH.'Config/'.ENVIRONMENT.'/constants.php');
//    }
//
//    require_once(APPPATH.'Config/constants.php');


/*
 * ------------------------------------------------------
 *  Load the global functions
 * ------------------------------------------------------
 */
    require_once(BASEPATH.'Common.php');

/*
 * ------------------------------------------------------
 *  Define a custom error handler so we can log PHP errors
 * ------------------------------------------------------
 */
//    set_error_handler('_error_handler');
//    set_exception_handler('_exception_handler');
//    register_shutdown_function('_shutdown_handler');


/*
 * ------------------------------------------------------
 *  Load the CI IoC container and get it ready for use.
 * ------------------------------------------------------
 */
    $config = require APPPATH .'Config/services.php';

    if (empty($config) || empty($config['services']) || ! is_array($config['services']))
    {
        throw new \RuntimeException('The Service Providers configuration file does not contain a proper array.');
    }

    $ci = CodeIgniter\CI::getInstance( $config['services'] );

    unset($config);

/*
 * ------------------------------------------------------
 *  Start the timer... tick tock tick tock...
 * ------------------------------------------------------
 */
$BM = $ci->benchmark;
$BM->mark('total_execution_time_start');
$BM->mark('loading_time:_base_classes_start');

/*
 * ------------------------------------------------------
 *  Instantiate the UTF-8 class
 * ------------------------------------------------------
 */
//$UNI = $app->utf8;

/*
 * ------------------------------------------------------
 *  Instantiate the URI class
 * ------------------------------------------------------
 */
//$URI = $app->uri;

/*
 * ------------------------------------------------------
 *  Instantiate the routing class and set the routing
 * ------------------------------------------------------
 */
$RTR = $ci->single('router', $ci->routes);
$RTR->route( $_SERVER['REQUEST_URI'] );

$memory = round(memory_get_usage() / 1024 / 1024, 2). 'MB';
echo "Executed in ". $BM->elapsed_time('total_execution_time_start', null, 7) ." seconds, using {$memory}";


