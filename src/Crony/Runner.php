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
class Runner {

    /**
     * @var string The directory containing all Crony tasks.
     */
    private $crony_job_root;

    /**
     * @var string The namespace that these jobs exist in.
     */
    private $crony_job_namespace;

    /**
     * Create a new instance of the Crony Runner for a given task namespace.
     * 
     * @param string $namespace The PSR-0 autoloaded namespace containing all Crony tasks.
     * @param string $application_root The root of the application, where we'll expect to find the namespaces' files.
     * @return void
     */
    public function __construct( $namespace, $application_root ) {
        $namespace_directory_path = $this->namespace_to_directory( $namespace );

        // Now, let's make sure the directory exists...
        if ( !is_dir( $application_root . $namespace_directory_path ) ) {
            throw new \Crony\Exception\UndefinedJobPath( 'Specified task path ' . $application_root . 
                $namespace_directory_path . ' does not exist.' );
        }

        $this->crony_job_root = $application_root . $namespace_directory_path;
        $this->crony_job_namespace = $namespace;
    }

    /**
     * Run all Crony-registered tasks by registering them with Jobby. For now, we are trusting
     * that our users can properly format their jobs :)
     */
    public function run() {
            $job_list = $this->find_all_jobs();
            $jobby = new \Jobby\Jobby();

            $this->register_tasks( $jobby, $job_list );
            return $jobby->run();
    }

    /**
     * Return a list of all available (defined) jobs.
     * 
     * @return array
     */
    public function get_job_list() {
        return $this->find_all_jobs();
    }

    /**
     * Register all of our tasks with our job runner.
     *
     * @param \Jobby\Jobby $runner The Jobby instance to bind these tasks to.
     * @param array $task_list A list of tasks to register.
     */
    private function register_tasks( \Jobby\Jobby $runner, $task_list ) {
        foreach( $task_list as $task ) {
            $task_class = $this->crony_job_namespace . '\\' . $task;

            // Per the interface...
            $task_config = call_user_func( $task_class . '::config' );

            // If there's no command registered in the configuration, we'll bind an anonymous function to
            // run our specified task.
            if ( !isset( $task_config['command'] ) ) {
                $task_config['command'] = function() { call_user_func( $task_class . '::run' ); };
            }
            
            $runner->add( $task, $task_config );
        }
    }

    /**
     * Scan the task directory and get a list of available jobs.
     * 
     * @return array
     */
    private function find_all_jobs() {
        $jobs = array();
        foreach( glob( $this->crony_job_root . '/*.php') as $job_file ) {
            $job_class = basename( $job_file, '.php' );

            // Before we include this job in the job list, we need to make sure it implements \Crony\TaskInterface
            $implementations = class_implements( $this->crony_job_namespace . '\\' . $job_class );
            if ( in_array( 'Crony\TaskInterface', $implementations ) ) {
                array_push( $jobs, $job_class );
            }
        }

        return $jobs;
    }

    /**
     * Convert a namespace string to a directory path.
     * 
     * @param string $namespace
     * @param string The directory-path-ified namespace.
     */
    private function namespace_to_directory( $namespace ) {
        return str_replace( '\\', '/', $namespace );
    }
}