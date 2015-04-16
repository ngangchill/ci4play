<?php namespace CodeIgniter\Router;

use CodeIgniter\CI;

/**
 * Class RouteParser
 *
 * This parser
 *
 * @package CodeIgniter\Router
 */
class RouteParser {

    protected $ci;

    //--------------------------------------------------------------------

    public function __construct()
    {
        $this->ci = CI::getInstance();
    }

    //--------------------------------------------------------------------

    /**
     * @param       $uri
     * @param array $routes
     */
    public function route($uri, array $routes = [])
    {
        var_dump($uri);
        die(var_dump($routes));
    }

    //--------------------------------------------------------------------


}