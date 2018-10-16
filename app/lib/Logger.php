<?php

class Logger extends Singleton
{
    const LOG_FILE_PATH = 'logs/log.txt';

    protected static $_instance;

    /**
     * Log file resource
     *
     * @var SplFileObject
     */
    private static $_file;

    protected function __construct()
    {
        try {
            $file_info = new SplFileInfo(static::LOG_FILE_PATH);

            /* throw exception if log file found but cant write */
            if ($file_info->isFile() && !$file_info->isWritable()) {
                throw new Exception;
            }

            /* open or create log file for further writing */
            static::$_file = $file_info->openFile('a');
        } catch (Exception $e) {
            app()->msg("Can't instantiate " . static::class);
            app()->terminate();
        }
    }

    /**
     * Writes application error to log file
     *
     * @param string $msg error message
     */
    public static function error($msg)
    {
        static::write(sprintf(
                  "[ERR] %s\n%s\n[URL] %s\n[PARAMS] %s\n\n",
                  (new DateTime)->format("d-m-Y H:i:s"),
                  $msg,
                  Request::getUrl(),
                  var_export(Request::getParams(), true)
              ));
    }


    /**
     * Writes application info to log file
     *
     * @param string $msg info message
     * @return void
     */
    public static function info($msg)
    {
        static::write(sprintf(
                "[INFO] %s [Message] %s\n\n",
                (new DateTime)->format("d-m-Y H:i:s"),
                $msg
              ));
    }

    /**
     * @param string $msg String to write file
     */
    private static function write($msg)
    {
        (static::inst())::$_file->fwrite($msg);
    }

    /**
     * Closes active log file resources
     */
    public static function close()
    {
        if (static::$_file) {
            static::$_file = null;
        }
    }
}