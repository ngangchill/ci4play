<?php namespace CodeIgniter\Router;

use CodeIgniter\CI;
use CodeIgniter\Interfaces\RouterInterface;

/**
 * Class Router
 *
 * This class handles sending off the routes
 * through the defined parser class. If the route
 * hasn't been found there, and $use_magic_routing = true
 * then it will attempt to route it in the traditional
 * CodeIgniter style of URI segments matching to controller
 * and method.
 *
 * @package CodeIgniter\Router
 */
class Router implements RouterInterface {

    /**
     * Link to CI object.
     *
     * @var
     */
    protected $ci;

    /**
     * Link to the current RouteCollection.
     *
     * @var null
     */
    protected $collection = null;

    /**
     * Is prepended to class names when we're calling them.
     *
     * @var string
     */
    protected $namespace = '\App\Controllers\\';

    //--------------------------------------------------------------------

    public function __construct()
    {
        $this->ci = CI::getInstance();

        $this->collection =& $this->ci->routes;
    }

    //--------------------------------------------------------------------

    /**
     * Attempts to route
     *
     * @param null $uri
     * @return bool
     */
    public function route($uri=null)
    {
        // Read in our routes file so that all routes are published
        // in the RouteCollection class.
        if (! file_exists(APPPATH .'Config/routes.php'))
        {
            throw new \RuntimeException('The routes config file does not exist.');
        }

        require APPPATH .'Config/routes.php';

        $uri = trim($uri, '/ ');

        // First, try to find it with the specified parser.
        if ($route = $this->parseRoute($uri, $this->collection->routes() ) )
        {
            return $this->dispatch($route);
        }

        // Use Magic Routing?
        if (! $this->collection->use_magic_routing)
        {
            return false;
        }


    }
    
    //--------------------------------------------------------------------

    /**
     * Handles the actual parsing of our routes by scanning the array of
     * routes and attempting to locate the desired route. Sends back
     * the route as an array so it can be handled in multiple possible
     * ways.
     *
     * @param $uri
     * @param $routes
     */
    public function parseRoute($uri, array $routes=[])
    {
        if (! count($routes))
        {
            return false;
        }

        // Do we have an exact match?
        if (array_key_exists($uri, $routes))
        {
            return [ $uri, $routes[$uri], [] ];
        }

        // Look for one matching with regular expressions.
        foreach ($routes as $key => $val)
        {
            // Does the regex match?
            if (preg_match('#^'. $key .'$#', $uri, $matches))
            {
                // Remove the original string from the matches array.
                array_shift($matches);

                return [$uri, $val, $matches];
            }
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * Determines how to fire off the controller and launches it.
     *
     * $route = [
     *      0 => $uri,
     *      1 => $to,       // Callable or string
     *      2 => $params    // array of empty
     * ]
     *
     * @param $route
     * @return bool|mixed
     */
    public function dispatch($route)
    {
        if (is_callable($route[1]))
        {
            return call_user_func_array($route[1], $route[2]);
        }

        // Is it a controller@method combo?
        if (strpos($route[1], '@') !== false)
        {
            list($controller, $method) = explode('@', $route[1]);

            if (strpos($controller, '\\') === false)
            {
                $controller = $this->namespace . $controller;
            }

            $class = new $controller();

            return call_user_func_array([$class, $method], $route[2]);
        }

    }

    //--------------------------------------------------------------------


}