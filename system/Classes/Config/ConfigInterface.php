<?php namespace CodeIgniter\Config;

interface ConfigInterface {

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
    public function item($name);

    //--------------------------------------------------------------------

    /**
     * Identical to item() but ensures that it has a trailing forward
     * slash attached.
     *
     * @param      $name
     *
     * @return mixed
     */
    public function slashItem($name);

    //--------------------------------------------------------------------

    /**
     * Returns an array of all config items within a file.
     *
     * @param $name
     *
     * @return mixed
     */
    public function file($name);

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
    public function setItem($name, $value=null);

    //--------------------------------------------------------------------

}