# Crony

`Crony` is a PHP-based cron job framework. Add the master task to your crontab, and `Crony` will take care of all of the work necessary to ensure that your offline jobs run how you want them, when you want them.

## Installation

1. Install [`composer`](<http://getcomposer.org>).
2. Add `Jobby` to `composer.json`.

    `"pcrumm/crony": "dev-master"`

3. Run `composer install`
4. Add the following to your crontab (see below for an example `jobs.php`:

    `* * * * * cd /path/to/project && php jobs.php 1>> /dev/null 2>&1`

## Usage

### jobs.php

`jobs.php` loads and runs Crony tasks, and should be referrenced from your crontab. It should include a call to the `Crony::init()` method, which takes a single argument: the namespace that your tasks reside under. This namespace should be [PSR-0 autoloaded by composer](https://getcomposer.org/doc/01-basic-usage.md#autoloading), and by convention should exist in the `src` directory in your project's root. In the example below, all jobs are sub-directories of the `src/PhilCrumm/ExampleTasks` directory and thus exist in the `\PhilCrumm\ExampleTasks` namespace.

```php
<?php
/**
 * This is an example jobs.php file, which will initialize Crony and provide
 * the configuration information necessary to run your tasks.
 */
require( __DIR__ . '/vendor/autoload.php' );

$crony = \Crony\Crony::init( '\PhilCrumm\ExampleTasks', __DIR__ . '/src' )->run();
?>
```

### Example Task

Generally, Crony tasks will exist in the specified namespace, and will consist of a single PHP file (whose name matches its class) that implements the `\Crony\TaskInterface` interface. It will consist of at least two functions: `config()`, which returns a Jobby-formatted configuration array, and `run()`, which will be ran when it's time to run the cron task.

#### `src/PhilCrumm/ExampleTasks/SayHello.php`
```php
<?php
namespace PhilCrumm\ExampleTasks;

class SayHello implements \Crony\TaskInterface {
    /**
     * Crony supports all of Jobby's configuration settings.
     * @see https://github.com/hellogerard/jobby
     * 
     * At minimum, we'll need enabled (a boolean), which determines whether
     * the job can run, and schedule, a cron-formatted string for when to run a job.
     */
    public static function config() {
        return array(
            'enabled'   => true,
            // Run every five minutes
            'schedule'  => '*/5 * * * *',
        );
    }

    /**
     * This function will be ran every time the above schedule is met.
     * We're just printing a string to the standard output (which will be
     * discarded per the crontab line earlier in the readme), but generally
     * you'd do something a little heftier.
     */
    public static function run() {
        print 'Hello, world!';
        return true;
    }
}
?>
```

## Features
Crony is a wrapper around [Jobby](https://github.com/hellogerard/jobby), and provides all of the features included in Jobby.

Particularly, Crony suggests a convention (rather than configuration) for cron jobs.

## Credits

Crony is a tool built-upon Gerard Sychay's wonderful [Jobby](https://github.com/hellogerard/jobby).
