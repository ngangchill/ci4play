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

if (! function_exists('log_message'))
{
    /**
     * Logs a message to the log files.
     *
     * @param       $level
     * @param       $message
     * @param array $context
     */
    function log_message($level, $message, array $context=[])
    {
        \CodeIgniter\CI::getInstance()->logger->log($level, $message, $context);
    }
}

//--------------------------------------------------------------------
