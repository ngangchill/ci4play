<?php namespace CodeIgniter;

use CodeIgniter\Interfaces\ConfigInterface;

class Config implements ConfigInterface {

    /**
     * Acts as a cache for loaded config files.
     * @var array
     */
    protected $cache = [];

    //--------------------------------------------------------------------

    /**
     * Grabs the value of a single item.
     * The name is intended to use the format
     *
     *      {filename}.{item_name}
     *
     * where {filename} is the name of the file
     * within the config directory, and {item_name}
     * is the name of value within that file.
     *
     * @param $name
     *
     * @return mixed
     */
    public function item($name)
    {
        $file = substr($name, 0, strpos($name, '.') );
        $item = substr($name, strlen($file) + 1);

        if (empty($file))
        {
            $file = 'config';
        }

        if (empty($this->cache[$file]))
        {
            $this->loadFile($file);
        }

        return array_key_exists($item, $this->cache[$file]) ? $this->cache[$file][$item] : null;
    }

    //--------------------------------------------------------------------

    /**
     * Identical to item() but ensures that it has a trailing forward
     * slash attached.
     *
     * @param      $name
     *
     * @return mixed
     */
    public function slashItem($name)
    {
        $item = $this->item($name);

        return rtrim($item, '/ ') .'/';
    }

    //--------------------------------------------------------------------

    /**
     * Returns an array of all config items within a file.
     *
     * @param $name
     *
     * @return mixed
     */
    public function file($name)
    {
        if (! array_key_exists($name, $this->cache))
        {
            $this->loadFile($name);
        }

        return $this->cache[$name];
    }

    //--------------------------------------------------------------------

    /**
     * Sets a single item's value for use during run-time only
     * Not responsible for saving it to file.
     *
     * @param      $name
     * @param null $value
     *
     * @return self
     */
    public function setItem($name, $value=null)
    {
        $file = substr($name, 0, strpos($name, '.') );
        $item = substr($name, strlen($file) + 1);

        // We need to go ahead and load the file
        // so we don't trick ourselves later into
        // believing the file has already been loaded.
        if (empty($this->cache[$file]) )
        {
            $this->loadFile($file);
        }

        $this->cache[$file][$item] = $value;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Loads the actual config file and caches it in $this->cache.
     *
     * @param $name
     */
    protected function loadFile($name)
    {
        $path = APPPATH .'/Config/'. $name .'.php';

        if (! file_exists($path))
        {
            throw new \RuntimeException("Cannot locate the config file: {$name}.");
        }

        include $path;

        if (empty($config))
        {
            throw new \RuntimeException("You have an invalid or empty config array in {$name}");
        }

        $this->cache[$name] = $config;
        unset($config);
    }

    //--------------------------------------------------------------------

}