<?php namespace CodeIgniter;

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
    private function __construct() {}

    //--------------------------------------------------------------------

    /**
     * Returns the singleton instance of this class.
     */
    public static function getInstance()
    {
        if (empty(static::$instance))
        {
            static::$instance = new CI();
            static::$instance->loadProviders();
        }

        return static::$instance;
    }

    //--------------------------------------------------------------------


    /**
     * Responsible for registering all service providers with
     * the Dependency Injection container.
     */
    public function loadProviders()
    {
        // Load our provider map
        if (! file_exists(APPPATH .'Config/providers.php'))
        {
            show_error('The Service Providers configuration file cannot be found.');
        }

        include APPPATH .'Config/providers.php';

        if (empty($config))
        {
            show_error('The Service Providers configuration file does not contain a proper array.');
        }

        $this->providers = $config['providers'];
        unset($config);
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
    public function save_instance($alias, &$class)
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

    public static function singleton($alias)
    {
        return static::$instance->{$alias};
    }

    //--------------------------------------------------------------------



    /**
     * Creates a new instance of the service provider with $alias, and
     * return it. Does not check for an existing instance but always
     * returns a new one.
     *
     * @param $alias
     * @return null
     */
    public function make($alias)
    {
        if (! array_key_exists($alias, $this->providers))
        {
            return null;
        }

        if (! class_exists($this->providers[$alias], true))
        {
            throw new \RuntimeException('Unable to locate the Service Provider: '. $this->providers[$alias] );
        }

        $class = new $this->providers[$alias]( $this );

        return $class;
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
        $alias = strtolower($alias);

        // Does an instance already exist?
        if (! array_key_exists($alias, $this->instances))
        {
            $this->instances[$alias] = $this->make($alias);
        }

        return $this->instances[$alias];
    }
    
    //--------------------------------------------------------------------

}