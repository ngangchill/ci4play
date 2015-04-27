# The CI Container

The CI Container acts as a very fast Inversion of Control container, also known as a Dependency Injection container. This allows a single instance of an object to be used in multiple areas of your application.

## Basic Configuration
The CI is given it’s initial configuration from `application/config/services.php`. This file contains the basic list of services that it knows how to create, along with an alias for each one. At the beginning, this is simply a list of framework-provided classes that you can use in your application. You can always add your own here as it creates a simple way to swap out the actual classes used throughout your application in one central place.

	$config['services'] = [
			'benchmark' => '\CodeIgniter\Benchmark\Benchmark',
			'routes' => '\CodeIgniter\Router\RouteCollection',
			'router' => '\CodeIgniter\Router\Router'
			. . .
	];


The rules are defined in one of two ways. The first way is to simply provide the namespaced classname as shown above. The second method allows you to manually define how the instance is generated through an anonymous function.

	$config[‘services’] = [
		'benchmark' => function ($ci) {
				return new Benchmark();
		}
	];

Both of these styles can be combined in the services config file.

## Getting A New Class Instance
You can retrieve a new instance of a class at any time by calling `make()`. The only parameter is the alias of the class you want to retrieve. To get a new benchmark class instance, we would do something like: 

	$ci = \CodeIgniter\CI::getInstance();
	$bm = $ci->make('benchmark');

Assuming that the benchmark class does not have any parameters that need to be passed into the constructor, then this is the same as: 

	$bm = new \CodeIgniter\Benchmark\Benchmark();

The only advantage here is that you can use the CI object anywhere in your code and, if you want to use a different benchmarking class, then you only need to change the name in your config file. 

### Constructor Parameters
If, however, your class needs one or more objects passed in the constructor, then the CI class will attempt to inject new instances of those classes into the constructor for you. These objects will first try to match an instance of a class registered to the container. If that class cannot be found in the services list, then it will try to autoload the class for you and pass in a new instance. If that is not possible, then it will throw a new `InvalidArgumentException`.

There is one bit of convention we have to adhere to, though, in order to make the mapping of services work correctly. The parameter name MUST match the alias of any mapped services in order to work correctly. An example will make this clear. 

Looking at our list of services above, we decide we need a new Router. This class requires an instance of the RouteCollection class in it’s constructor. So we must name the parameter `routes` to match the alias in our services config file. Since the RouteCollection class has an interface available you should typehint to that to ensure you get a class you can use.

Altogether it would look something like: 

	class RouteCollection { } 
	
	class Router {
		protected $collection;
	
		// The parameter name must match the alias name. 
		public function __construct(\CodeIgniter\Router\RouteCollection $routes)
		{ 
			 $this->collection = $routes;
		}
	}
	 
	$router = $ci->make('router');

## Getting A Singleton Instance
If you want to ensure that you always get back the same instance of a class you have two ways that you can do this. 

The first is by using the `single()` method. This method is identical to the `make()` method except that it will cache the class if it has to create it, or return an existing instance if it already has been instantiated. Note that all classes required by the constructor will also be singleton instances, if possible. 

	$bm = $ci->single('benchmark');

Alternatively, you can access the class as a parameter of the CI object. This will simply call the `single()` method to grab the object.

	$bm = $ci->benchmark;

 