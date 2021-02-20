<?php

namespace AlEmran\PHPDependencyInjection;


use AlEmran\PHPDependencyInjection\Base\Container;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;

class DependencyInjectionContainer implements Container
{

    /**
     * The container's  instance.
     *
     * @var static
     */
    protected static $instance;


    /**
     * the class name with namespace
     *
     * @var string
     */
    protected $callbackClass;

    /**
     * the method name of provided class
     *
     * @var string
     */
    protected $callbackMethod;

    /**
     * method separator of a class. when pass class and method as string
     */
    protected $methodSeparator = '@';

    /**
     * namespace  for  class. when pass class and method as string
     *
     * @var string
     */
    protected $namespace = "App\\controller\\";


    /**
     *  Get the globally available instance
     *
     * @return static
     */
    public static function instance()
    {

        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * @param $callable
     * @param  array  $parameters
     * @return object
     * @throws ReflectionException
     * @throws Exception
     */
    public function call($callable, $parameters = [])
    {

        $this->resolveCallback($callable);

        $methodReflection = new ReflectionMethod($this->callbackClass, $this->callbackMethod);
        $methodParams = $methodReflection->getParameters();

        $dependencies = [];

        foreach ($methodParams as $param) {

            $type = $param->getType();

            if ($type && $type instanceof ReflectionNamedType) {

                $name = $param->getClass()->newInstance();
                array_push($dependencies, $name);

            } else {

                $name = $param->getName();

                if (array_key_exists($name, $parameters)) {

                    array_push($dependencies, $parameters[$name]);

                } else {

                    if (!$param->isOptional()) {
                        throw new Exception("Can not resolve parameters");
                    }
                }

            }

        }

        $initClass = $this->make($this->callbackClass, $parameters);

       return $methodReflection->invoke($initClass, ...$dependencies);
    }


    /**
     * @param $callable
     */
    private function resolveCallback($callable)
    {

        $segments = explode($this->methodSeparator, $callable);

        $this->callbackClass = $this->namespace.$segments[0];
        $this->callbackMethod = isset($segments[1]) ? $segments[1] : '__invoke';

    }

    /**
     * @param $class
     * @param $parameters
     * @return object
     * @throws ReflectionException
     * @throws Exception
     * @throws ReflectionException
     */
    public function make($class, $parameters = [])
    {

        $classReflection = new ReflectionClass($class);
        $constructorParams = $classReflection->getConstructor()->getParameters();
        $dependencies = [];

        /*
         * loop with constructor parameters or dependency
         */
        foreach ($constructorParams as $constructorParam) {

            $type = $constructorParam->getType();

            if ($type && $type instanceof ReflectionNamedType) {

                array_push($dependencies, $constructorParam->getClass()->newInstance());

            } else {

                $name = $constructorParam->getName();
                if (array_key_exists($name, $parameters)) {

                    array_push($dependencies, $parameters[$name]);

                } else {

                    if (!$constructorParam->isOptional()) {
                        throw new Exception("Can not resolve parameters");
                    }

                }

            }

        }

        return $classReflection->newInstance(...$dependencies);
    }

}