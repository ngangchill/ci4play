<?php namespace CodeIgniter\Router;

interface RouterInterface {

    /**
     * Attempts to route
     *
     * @param null $uri
     * @return bool
     */
    public function route($uri=null);

    //--------------------------------------------------------------------

    /**
     * Handles the actual parsing of our routes and doing magic voodoo,
     * by trying to match up the URI segments with a controller/method.
     *
     * @param $uri
     * @param $routes
     */
    public function parseRoute($uri, array $routes=[]);

    //--------------------------------------------------------------------


}