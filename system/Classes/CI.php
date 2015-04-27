<?php namespace CodeIgniter;

/**
 * Class CI
 *
 * This class acts as both a singleton and a registry for the
 * "services" available. Services are specified through the
 * application/config/services.php config file. Any service
 * listed there can be accessed by its alias through $CI->{service alias}
 *
 * Example:
 *      A service with alias 'logger' could be access like:
 *
 *      $ci = CodeIgniter\CI::getInstance();
 *      $ci->logger->log();
 *
 * New services can always be added later with the register(), save_instance(),
 * and __get() methods.
 *
 * @package CodeIgniter
 * @author Lonnie Ezell (lonnie@newmythmedia.com)
 */
class CI {

    /**
     * Stores the map of provider
     * name and class names.
     *
     * @var
     */
    protected $providers = [];

    /**
     * Holds all instantiated singleton
     * objects.
     *
     * @var array
     */
    protected $instances = [];

    /**
     * An instance of this class for static usage.
     *
     * @var
     */
    protected static $instance;

    //--------------------------------------------------------------------

    /**
     * The constructor is kept private to ensure that
     * this class can only be used as a singleton DI container.
     */
    private function __construct( array $config=[] )
    {
        $this->providers = $config;
    }

    //--------------------------------------------------------------------

    /**
     * Returns the singleton instance of this class.
     *
     * Provides a simple way to access the class elsewhere in the
     * application. Care should be used, though, as this could simply
     * defer the dependency onto this container and still keep your
     * class dependant on this CI class.
     *
     * A preferred way would be to request the CI class in your
     * classes constructor.
     *
     * Example:
     *      public function __construct(CI $ci)
     *      {
     *          $this->ci = $ci;
     *      }
     */
    public static function getInstance( array $config=[] )
    {
        if (empty(static::$instance))
        {
            static::$instance = new CI( $config );
        }

        return static::$instance;
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Service Providers
    //--------------------------------------------------------------------

    /**
     * Registers a specific class name with the corresponding alias
     * for the dependency inversion.
     *
     * @param $name
     * @param $class
     *
     * @return $this
     */
    public function register($alias, $class)
    {
        $alias = strtolower($alias);

        if (array_key_exists($alias, $this->instances))
        {
            throw new \RuntimeException('You cannot register a provider when an instance of that class has already been created.');
        }

        $this->providers[$alias] = $class;

        return $this;
    }
    
    //--------------------------------------------------------------------

    /**
     * Unregisters a single service provider. If an instance of that
     * provider has already been created, it will be destroyed.
     *
     * @param $alias
     *
     * @return $this
     */
    public function unregister($alias)
    {
        $alias = strtolower($alias);

        if (array_key_exists($alias, $this->instances))
        {
            unset($this->instances[$alias]);
        }

        unset($this->providers[$alias]);

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Registers an instantiated class as a Service Provider.
     *
     * @param $alias
     * @param $class
     *
     * @return $this
     */
    public function saveInstance($alias, &$class)
    {
        $alias = strtolower($alias);

        if (array_key_exists($alias, $this->instances))
        {
            unset($this->instances[$alias]);
        }

        $this->instances[$alias] = $class;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Creates a new instance of the service provider with $alias, and
     * return it. Does not check for an existing instance but always
     * returns a new one.
     *
     * @param $alias
     * @param bool $use_singletons
     *
     * @return null
     */
    public function make($alias, $use_singletons=false)
    {
        $alias = strtolower($alias);

        if (! array_key_exists($alias, $this->providers))
        {
            throw new \InvalidArgumentException('No Service is registered for alias: '. $alias);
        }

        // The provider could be either a string (namespaced class)
        // or a Closure that returns an instance of the desired class.
        $service = $this->providers[$alias];

        if (is_string($service))
        {
            if (! class_exists($service, true))
            {
                throw new \RuntimeException('Unable to locate the Service Provider: '. $this->providers[$alias] );
            }

            return $this->inject($service, $use_singletons);
        }

        else if (is_callable($service))
        {
            return $service( $this );
        }

        return null;
    }

    //--------------------------------------------------------------------

    /**
     * Allows you to create a new instance of an object as a singleton,
     * passing in arguments.
     *
     * @param $alias
     * @return mixed
     */
    public function single($alias)
    {
        $alias = strtolower($alias);

        // Return the existing object if it exists.
        if (! empty($this->instances[$alias]) && is_object($this->instances[$alias]))
        {
            return $this->instances[$alias];
        }

        // Die if we don't know what class to use.
        if (empty($this->providers[$alias]))
        {
            throw new \InvalidArgumentException('Unable to find class with alias: '. $alias);
        }

        $instance = $this->make($alias, true);

        $this->instances[$alias] =& $instance;

        return $this->instances[$alias];
    }

    //--------------------------------------------------------------------



    //--------------------------------------------------------------------
    // Magic
    //--------------------------------------------------------------------

    /**
     * Attempts to locate a service provider that exists with an alias
     * matching $name and return it.
     *
     * Example:
     *      $app->load   will search for the provider with the alias 'load'
     *                   and create an instance, if it doesn't exist
     *
     * @param $alias
     *
     * @return null
     */
    public function __get($alias)
    {
        return $this->single($alias);
    }
    
    //--------------------------------------------------------------------

    /**
     * Determines the classes needed, creates instances (or uses existing
     * instances, if exists) to pass in constructor and returns
     * a new instance of the desired service.
     *
     * @param $service
     *
     * @return null|object
     */
    protected function inject($service, $single=false)
    {
        $mirror = new \ReflectionClass($service);
        $constructor = $mirror->getConstructor();

        $params = null;

        if (empty($constructor))
        {
            return new $service();
        }

        $params = $this->getParams($constructor, $single);

        // No params means we simply create a new
        // instance of the class and return it...
        if (is_null($params))
        {
            return new $service();
        }

        // Still here - then return an instance
        // with those params as arguments
        return $mirror->newInstanceArgs($params);
    }

    //--------------------------------------------------------------------

    /**
     * Given a reflection method, will get or create an array of objects
     * ready to be inserted into the class' constructor.
     *
     * If $single is true, will return a singleton version of dependencies
     * else will create a new class.
     *
     * @param \ReflectionMethod $mirror
     * @param bool $single
     *
     * @return array
     */
    protected function getParams(\ReflectionMethod $mirror, $single=false)
    {
        $params = [];

        foreach ($mirror->getParameters() as $param)
        {
            $alias = strtolower($param->name);

            // Is this a mapped alias?
            if (! empty($this->providers[$alias]))
            {
                $params[] = $single ? $this->single($alias) : $this->make($alias);
                continue;
            }

            // Is this a normal class we can give them?
            $class = $param->getClass()->name;

            if (class_exists($class))
            {
                $params[] = new $class();
            }

            $params[] = null;
        }

        return $params;
    }
}