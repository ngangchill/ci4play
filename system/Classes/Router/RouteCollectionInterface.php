<?php namespace CodeIgniter\Router;

interface RouteCollectionInterface {

    /**
     * Returns an array of our routes.
     *
     * @return mixed
     */
    public function routes();

    //--------------------------------------------------------------------

    public function any($from, $to, $options = array());

    //--------------------------------------------------------------------

    /**
     * Sets the default constraint to be used in the system. Typically
     * for use with the 'resources' method.
     *
     * @param $constraint
     */
    public function setDefaultConstraint($constraint);

    //--------------------------------------------------------------------

    /**
     * Registers a new constraint to be used internally. Useful for creating
     * very specific regex patterns, or simply to allow your routes to be
     * a tad more readable.
     *
     * Example:
     *      $route->registerConstraint('hero', '(^.*)');
     *
     *      $route->any('home/{hero}', 'heroes/journey');
     *
     *      // Route then looks like:
     *      $route['home/(^.*)'] = 'heroes/journey';
     *
     * @param      $name
     * @param      $pattern
     * @param bool $overwrite
     */
    public function registerConstraint($name, $pattern, $overwrite = false);

    //--------------------------------------------------------------------

    /**
     * Returns the value of a named route. Useful for getting named
     * routes for use while building with site_url() or in templates
     * where you don't need to instantiate the route class.
     *
     * Example:
     *      $route->any('news', 'posts/index', ['as' => 'blog']);
     *
     *      // Returns http://mysite.com/news
     *      site_url( Route::named('blog') );
     *
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public static function named($name);

    //--------------------------------------------------------------------

    /**
     * Group a series of routes under a single URL segment. This is handy
     * for grouping items into an admin area, like:
     *
     * Example:
     *     $route->group('admin', function() {
     *            $route->resources('users');
     *     });
     *
     * @param  string   $name     The name to group/prefix the routes with.
     * @param  \Closure $callback An anonymous function that allows you route inside of this group.
     * @return void
     */
    public function group($name, \Closure $callback);

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // HTTP verb-based routing
    //--------------------------------------------------------------------
    // Routing works here because, as the routes config file is read in,
    // the various HTTP verb-based routes will only be added to the in-memory
    // routes if it is a call that should respond to that verb.
    //
    // The options array is typically used to pass in an 'as' or var, but may
    // be expanded in the future. See the docblock for 'any' method above for
    // current list of globally available options.
    //

    /**
     * Specifies a single route to match for multiple HTTP Verbs.
     *
     * Example:
     *  $route->match( ['get', 'post'], 'users/(:num)', 'users/$1);
     *
     * @param array $verbs
     * @param       $from
     * @param       $to
     * @param array $options
     */
    public function match($verbs = [], $from, $to, $options = []);

    //--------------------------------------------------------------------

    /**
     * Specifies a route that is only available to GET requests.
     *
     * @param       $from
     * @param       $to
     * @param array $options
     */
    public function get($from, $to, $options = []);

    //--------------------------------------------------------------------

    /**
     * Specifies a route that is only available to POST requests.
     *
     * @param       $from
     * @param       $to
     * @param array $options
     */
    public function post($from, $to, $options = []);

    //--------------------------------------------------------------------

    /**
     * Specifies a route that is only available to PUT requests.
     *
     * @param       $from
     * @param       $to
     * @param array $options
     */
    public function put($from, $to, $options = []);

    //--------------------------------------------------------------------

    /**
     * Specifies a route that is only available to DELETE requests.
     *
     * @param       $from
     * @param       $to
     * @param array $options
     */
    public function delete($from, $to, $options = []);

    //--------------------------------------------------------------------

    /**
     * Specifies a route that is only available to HEAD requests.
     *
     * @param       $from
     * @param       $to
     * @param array $options
     */
    public function head($from, $to, $options = []);

    //--------------------------------------------------------------------

    /**
     * Specifies a route that is only available to PATCH requests.
     *
     * @param       $from
     * @param       $to
     * @param array $options
     */
    public function patch($from, $to, $options = []);

    //--------------------------------------------------------------------

    /**
     * Specifies a route that is only available to OPTIONS requests.
     *
     * @param       $from
     * @param       $to
     * @param array $options
     */
    public function options($from, $to, $options = []);

    //--------------------------------------------------------------------

    /**
     * Creates a collections of HTTP-verb based routes for a controller.
     *
     * Possible Options:
     *      'controller'    - Customize the name of the controller used in the 'to' route
     *      'module'        - Prepend a module name to the generate 'to' routes
     *      'constraint'    - The regex used by the Router. Defaults to '(:any)'
     *
     * Example:
     *      $route->resources('photos');
     *
     *      // Generates the following routes:
     *      HTTP Verb | Path        | Action        | Used for...
     *      ----------+-------------+---------------+-----------------
     *      GET         /photos             index           display a list of photos
     *      GET         /photos/{id}        show            display a specific photo
     *      POST        /photos             create          create a new photo
     *      PUT         /photos/{id}        update          update an existing photo
     *      DELETE      /photos/{id}/delete delete          delete an existing photo
     *
     * @param  string $name    The name of the controller to route to.
     * @param  array  $options An list of possible ways to customize the routing.
     */
    public function resources($name, $options = []);

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Specialized Methods
    //--------------------------------------------------------------------

    /**
     * Limits the routes to a specified ENVIRONMENT or they won't run.
     *
     * @param $env
     * @param callable $callback
     *
     * @return bool|null
     */
    public function environment($env, \Closure $callback);

    //--------------------------------------------------------------------

    /**
     * Allows you to easily block access to any number of routes by setting
     * that route to an empty path ('').
     *
     * Example:
     *     Route::block('posts', 'photos/(:num)');
     *
     *     // Same as...
     *     $route['posts']          = '';
     *     $route['photos/(:num)']  = '';
     */
    public function block();

    //--------------------------------------------------------------------


}