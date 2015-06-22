<?php
/**
 * @package Crony
 * @author Phil Crumm <pkcrumm@gmail.com>
 * @license MIT
 */
namespace Crony;

/**
 * The format that we'll expect all of our Tasks to follow.
 */
interface TaskInterface {
    /**
     * Cron job configuration. Accepts all options supported by the Jobby library.
     * @see https://github.com/hellogerard/jobby/blob/master/README.md
     * 
     * @return array
     */
    public static function config();

    /**
     * When called, should run the specified task. Returns true on success
     * or false on failure.
     * 
     * @return bool
     */
    public static function run();
}