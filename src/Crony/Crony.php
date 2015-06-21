<?php
/**
 * @package Crony
 * @author Phil Crumm <pkcrumm@gmail.com>
 * @license MIT
 */
namespace Crony;

/**
 * 
 */
class Crony {
    /**
     * Create a new instance of our Crony Runner.
     * 
     * @param string $namespace The PSR-0 autoloaded namespace containing all Crony tasks.
     * @param string $app_root The root of the application, where we'll expect to find the namespaces' files.
     * @return \Crony\Runner A runnable Crony instance.
     */
    public static function init( $namespace, $app_root ) {
        return new \Crony\Runner( $namespace, $app_root );
    }
}