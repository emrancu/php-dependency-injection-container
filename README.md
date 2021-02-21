# Let's Create PHP Dependency Injection Container and Try to Learn How Laravel Initiate Controller and Method with Dependency and Parameter

 
We are building application with [Laravel](https://laravel.com/) but many of us do not know how [Laravel](https://laravel.com/) Initiate Controller and Method with Dependency and Parameters.

We declare route like:

```php
Route::get('hello', 'TestController@index')
```
 

AND Our Controller Like: 

```php
 
 namespace App\Http\Controllers\Test;
  
 
 class TestController extends Controller
 {
      private $requests;
 
      public function __construct( Request $request)
         {
            $this->requests = $request;
         }
 
 
     /**
      * Show the application dashboard.
      */
     public function index(TestModel $testModel)
     {
       return $testModel->get();
     }
 
 
 }

```


OR Route Like:

```php
Route::get('hello', 'TestController')
```
 
 AND Controller like:
```php
 
 namespace App\Http\Controllers\Test;
  
 
 class TestController extends Controller
 {
      private $requests;
 
      public function __construct( Request $request)
         {
            $this->requests = $request;
         }
 
 
     /**
      * Show the application dashboard.
      */
     public function __invoke(TestModel $testModel)
     {
       return $testModel->get();
     }
 
 
 }

```

 **Now Question:**
    
 **1.1 How Laravel detect construct's Dependency and inject ?**
 
 **1.2 And How Laravel initiate class with construct's Dependencies and call the 'index' Method with  It's Dependaencies and Parameters?**
 
**2.1 We do not pass method name but How Laravel Detect __invoke method as default method?**

Let's build an dependency injection container to understand previous Questions.


## Why we should know ?

1. As [Laravel](https://laravel.com/) Developer , We need to know How Laravel Work.
2. It will help us to build a new Framework in our own approach.
3. We can use this container for our large  project or Module.
4. Finally , we will start learning [PHP Reflection](https://www.php.net/manual/en/book.reflection.php) (The Awesome)


## Let's Start

**Our Steps**
1. Create a Class
2. Create a method (make) for initiating a class and injecting constructor's  dependencies and parameters
3. create a method (call) for extracting class & method and injecting dependencies and parameters of method

##1. Let's Create a Class 
 
```php
class DependencyInjectionContainer
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
     *   get Singleton instance of the class
     *
     * @return static
     */
    public static function instance()
    {

    }

    /**
     * @param $callable -- string class and method name with separator @
     * @param  array  $parameters
     */
    public function call($callable, $parameters = [])
    {

    }


    /**
     * separate class and method name
     * @param $callback
     */
    private function resolveCallback($callback)
    {

    }

    /**
     * instantiate class with dependency and return class instance
     * @param $class - class name
     * @param $parameters (optional) -- parameters as array . If constructor need any parameter
     */
    public function make($class, $parameters = [])
    {
    }

}
```

##Create a method (make) for initiating a class and injecting constructor's  dependencies and parameters

We are going to use PHP [PHP Reflection](https://www.php.net/manual/en/book.reflection.php). In this section we use PHP [ReflactionClass](https://www.php.net/manual/en/class.reflectionclass.php) object.There are so many methods exist in  PHP [ReflactionClass](https://www.php.net/manual/en/class.reflectionclass.php).
In this section we need to make a class instance.
We know that if we make instance of any class , we must pass/inject all dependencies & parameters.

With PHP [ReflactionClass](https://www.php.net/manual/en/class.reflectionclass.php) we need to detect all dependencies.

```php
public function make($class, $parameters = [])
    {
        $classReflection = new ReflectionClass($class);
        $constructorParams = $classReflection->getConstructor()->getParameters();
   }
```

In this above code  we collect all parameters for constructor to   ```$constructorParams``` variable. 

Then we need to loop with  ```$constructorParams``` and detect parameter and dependency.  

```php
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
                
                echo "It is a class and we need to instantiate the class and pass to constructor"

            } else {

                echo "This is a normal parameter and We need to find parameter value from $parameters . If not found value then need to check is this parameter optional or not. If not optional then through error"

            }

        } 
    }
```
Point:
1. if Parameter is a class, we need to instantiate the class and pass to constructor.
2. if Parameter is not a class, We need to find parameter value from $parameters . If not found value then need to check is this parameter optional or not. If not optional then through error.

Lets Finalize the make method:

```php
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
                
                // make instance of this class :
                $paramInstance = $constructorParam->getClass()->newInstance()

                // push to $dependencies array
                array_push($dependencies, $paramInstance);

            } else {

                $name = $constructorParam->getName(); // get the name of param

                // check this param value exist in $parameters
                if (array_key_exists($name, $parameters)) { // if exist
                    
                    // push  value to $dependencies sequencially
                    array_push($dependencies, $parameters[$name]);

                } else { // if not exist
                
                    if (!$constructorParam->isOptional()) { // check if not optional
                        throw new Exception("Can not resolve parameters");
                    }

                }

            }

        }
        // finally pass dependancy and param to class instance 
        return $classReflection->newInstance(...$dependencies);
    }
```
I explain everything in code . 

OK.... we have completed a Dependency Injection container for class

 ***Let's Create a Singleton Instance***

```php
  public static function instance()
    {

        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }
```


***Basic Usage***

 ```php
 $container =  DependencyInjectionContainer::instance();
```

Define a class
```php
class MyClass
{
    private $dependency;

    public function __construct(AnotherClass $dependency)
    {
        $this->dependency = $dependency;
    }
}
```

Instead of using `new MyClass`, use the Container's `make()` method:

```php
$instance = $container->make(MyClass::class);
```

The container will automatically instantiate the dependencies, so this is functionally equivalent to:

```php
$instance = new MyClass(new AnotherClass());
```
 

***Practical Example***

Here's a more practical example based on the [PHP-DI docs](http://php-di.org/doc/getting-started.html) - separating the mailer functionality from the user registration:

```php
class Mailer
{
    public function mail($recipient, $content)
    {
        // Send an email to the recipient
        // ...
    }
}
```

```php
class UserManager
{
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function register($email, $password)
    {
        // Create the user account
       
    }
}
```

```php
 

$container = DependencyInjectionContainer::instance();

$userManager = $container->make(UserManager::class);
$userManager->register('dave@davejamesmiller.com', 'MySuperSecurePassword!');
```

##3. create a method (call) for extracting class & method and injecting dependencies and parameters of method 
In this section we are going to understand how Laravel parse controller and method and initiate with dependency/parameters.

Steps 
1. Separate controller and method 
2. Detect method Dependency / parameters and call method
3. Instantiate class with our ```make()``` function.


**1. Separate controller and method**

Our class have a property  ```$methodSeparator``` as a separator for separating class name  and method name.

```php
 private function resolveCallback($callable)
    {
        
        //separate class and method
        $segments = explode($this->methodSeparator, $callable);
    
        // set class name with namespace
        $this->callbackClass = $this->namespace.$segments[0]; 

        // set method name . if method name not provided then default method __invoke
        $this->callbackMethod = isset($segments[1]) ? $segments[1] : '__invoke'; 

    }
```

**2. Detect method Dependency / parameters and call method**

```php
 public function call($callable, $parameters = [])
    {
        
        // set class name with namespace and method name
        $this->resolveCallback($callable);

        // initiate ReflectionMethod with class and method 
        $methodReflection = new ReflectionMethod($this->callbackClass, $this->callbackMethod);

        // get all dependencies/parameters
        $methodParams = $methodReflection->getParameters();

        $dependencies = [];

        // loop with dependencies/parameters
        foreach ($methodParams as $param) {

            $type = $param->getType(); // check type

            if ($type && $type instanceof ReflectionNamedType) { /// if parameter is a class

                $name = $param->getClass()->newInstance(); // create insrance 
                array_push($dependencies, $name); // push  to $dependencies array

            } else {  /// Normal parameter  

                $name = $param->getName();

                if (array_key_exists($name, $parameters)) { // check exist in $parameters

                    array_push($dependencies, $parameters[$name]); // push  to $dependencies array

                } else { // if not exist

                    if (!$param->isOptional()) { // check if not optional
                        throw new Exception("Can not resolve parameters");
                    }
                }

            }

        }
    
        // make class instance
        $initClass = $this->make($this->callbackClass, $parameters);

        // call method with $dependencies/parameters
       return $methodReflection->invoke($initClass, ...$dependencies); 
    }
```

Now you have a question how laravel collect parameter for calling a function.

When we declare a route with dynamic parameter like:

```php
Route::get('hello/{name}', 'TestController@index')
```
 
Laravel Routing system collect all parameter and pass to call function.

```php
$parameters = ["name" => "AL EMRAN" ];
$container->call("TestController@index", $parameters)

```

***Usage***
```php
$container->call('TestController@index');
$container->call('TestController@show', ['id' => 4]);

$container->call('TestController');
$container->call('TestController', ['id' => 4]);

```

OR

**example**

```php

class TestController 
{
     protected $company;
     public function __construct(Company $company)
        {
            $this->company =  $company ;
        }

    /**
     * @param  Request  $request 
     */
    public function index(Request $request){

    $company =  $company->get();

     return view('admin.company', compact('company'));        

    }

}
 
```
We can use :
```php
   $instance = DependencyInjectionContainer::instance();
   $instance->namespace = "App\\Http\Controllers\\Admin\\"; // change namespace

   $class = $instance->make(CompanyController::class); // make class instance

   $instance->call(["CompanyController", 'index']); // call method

   $instance->call([$class, 'index']); // or--call method
```


#install package
```php
composer require emrancu/dependency-injection-container
```

## Declaimer 
 I am not an expert in PHP, try to learn. 
 
 Laravel Dependency Injection Container is so vast and also its routing system. I just create this mini-class only for understanding.

This is my first article, so please comment your valuable opinion or mail me at "emrancu1@gmail.com"

Inspired from [Here](https://gist.github.com/davejamesmiller/bd857d9b0ac895df7604dd2e63b23afe)


[AL EMRAN](http://alemran.me)

Founder , [Babshaye.com](https://babshaye.com) (A business solution for offline and online) Follow on [Facebook](https://www.facebook.com/babshaye)


 
