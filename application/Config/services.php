<?php

/**
 * This file contains a map of namespaced classes and their aliases.
 * These are used by the CI class to provide instances of classes.
 *
 * The 'alias' (key) is how you will reference the class instance through CI.
 * The class name (value) is the fully namespaced class name to use.
 * If you want to substitute a different class in place of the current one,
 * just change the name of the class to the fully namespaced class you
 * want to use.
 *
 * Examples:
 *      $ci = \CodeIgniter\CI::getInstance();
 *      $bm = $ci->benchmark;
 *      $ci->benchmark->mark('some_mark_start');
 */

$config['services'] = [

    // alias            class name
    //--------------------------------------------------------------------

    // The core CodeIgniter files
    'benchmark'         => '\CodeIgniter\Benchmark',
    'config'            => '\CodeIgniter\Config',
    'logger'            => '\CodeIgniter\Log',
    'router'            => '\CodeIgniter\Router\Router',
    'routes'            => '\CodeIgniter\Router\RouteCollection',

    // Your custom files can be added here.
];