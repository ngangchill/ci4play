<?php namespace CodeIgniter;

use CodeIgniter\Interfaces\LoggerInterface;

class Log implements LoggerInterface {

    /**
     * Path to save log files to.
     *
     * @var string
     */
    protected $log_path;

    /**
     * File permissions
     *
     * @var int
     */
    protected $file_permissions = 0644;

    /**
     * Array of levels that will be logged.
     * The rest will be ignored.
     * Set in config/config.php
     *
     * @var array
     */
    protected $loggable_levels = [];

    /**
     * Format of timestamp for log files
     *
     * @var string
     */
    protected $date_format = 'Y-m-d H:i:s';

    /**
     * Filename extension
     *
     * @var	string
     */
    protected $file_ext;

    //--------------------------------------------------------------------

    public function __construct($ci)
    {
        $config = $ci->config->file('config');

        $this->log_path = ! empty($config['log_path']) ? rtrim($config['log_path'], '/') .'/' : WRITEPATH .'logs/';

        $this->loggable_levels = $config['log_levels'];

        $this->file_ext = ! empty($config['log_file_extension']) ? ltrim($config['log_file_extension'], '.' ) : 'php';

        // Date Format
        $this->date_format = ! empty($config['log_file_extension']) ? ltrim($config['log_file_extension'], '.' ) : $this->date_format;

        $this->file_permissions = ! empty($config['log_file_permissions']) && is_int($config['log_file_permissions'])
            ? $config['log_file_permissions'] : $this->file_permissions;
    }

    //--------------------------------------------------------------------


    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = array())
    {
        $this->log('emergency', $message, $context);
    }

    //--------------------------------------------------------------------

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function alert($message, array $context = array())
    {
        $this->log('alert', $message, $context);
    }

    //--------------------------------------------------------------------

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function critical($message, array $context = array())
    {
        $this->log('critical', $message, $context);
    }

    //--------------------------------------------------------------------

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function error($message, array $context = array())
    {
        $this->log('error', $message, $context);
    }

    //--------------------------------------------------------------------

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function warning($message, array $context = array())
    {
        $this->log('warning', $message, $context);
    }

    //--------------------------------------------------------------------

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function notice($message, array $context = array())
    {
        $this->log('notice', $message, $context);
    }

    //--------------------------------------------------------------------

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function info($message, array $context = array())
    {
        $this->log('info', $message, $context);
    }

    //--------------------------------------------------------------------

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function debug($message, array $context = array())
    {
        $this->log('debug', $message, $context);
    }

    //--------------------------------------------------------------------

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        if (! in_array($level, $this->loggable_levels))
        {
            return false;
        }

        $filepath = $this->log_path .'log-'. date('Y-m-d'). '.'. $this->file_ext;

        $message = '';

        if ( ! file_exists($filepath))
        {
            $newfile = TRUE;

            // Only add protection to php files
            if ($this->file_ext === 'php')
            {
                $message .= "<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>\n\n";
            }
        }

        if ( ! $fp = @fopen($filepath, 'ab'))
        {
            return FALSE;
        }

        // Instantiating DateTime with microseconds appended to initial date is needed for proper support of this format
        if (strpos($this->date_format, 'u') !== FALSE)
        {
            $microtime_full = microtime(TRUE);
            $microtime_short = sprintf("%06d", ($microtime_full - floor($microtime_full)) * 1000000);
            $date = new DateTime(date('Y-m-d H:i:s.'.$microtime_short, $microtime_full));
            $date = $date->format($this->date_format);
        }
        else
        {
            $date = date($this->date_format);
        }

        $message .= $level .' - '. $date .' --> '. $message ."\n";

        flock($fp, LOCK_EX);

        for ($written = 0, $length = strlen($message); $written < $length; $written += $result)
        {
            if (($result = fwrite($fp, substr($message, $written))) === FALSE)
            {
                break;
            }
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        if (isset($newfile) && $newfile === TRUE)
        {
            chmod($filepath, $this->file_permissions);
        }

        return is_int($result);
    }

    //--------------------------------------------------------------------

}