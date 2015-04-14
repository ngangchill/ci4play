<?php

if (! function_exists('config_item'))
{
    /**
     * Grabs a single config item.
     *
     * @param $name
     *
     * @return mixed
     */
    function config_item($name)
    {
        return \CodeIgniter\CI::getInstance()->config->item($name);
    }
}

//--------------------------------------------------------------------
