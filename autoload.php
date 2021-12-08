<?php

/**
 * Class Autoloader
 * Autoloads classes as they are initialized
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            $file = __DIR__ . '/src/' . str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
            if (file_exists($file)) {
                require $file;
                return true;
            }
            return false;
        });
    }
}
Autoloader::register();
