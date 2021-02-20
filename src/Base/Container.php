<?php


namespace AlEmran\PHPDependencyInjection\Base;


interface Container
{

    /**
     *  Get the globally available instance
     *
     * @return static
     */
    public static function instance();

    /**
     * @param $callable
     * @param  array  $parameters
     * @return object
     */
    public function call($callable, $parameters = []);



    /**
     * @param $class
     * @param $parameters
     * @return object
     */
    public function make($class, $parameters = []);

}