# CodeIgniter 4 Playground

**This is not an official project** This is simply a playground for me to experiment with some ideas that may or may not be used within CodeIgniter 4.

## Current Challenges

### Routing
Working on the routing currently. Exploring ways to make it so that you can easiliy route to any namespaced controller (working) even if brought in via a Composer package. Also needs to support traditional CodeIgniter controllers and "modules" (or packages with the additional benefit of routing to classes within).

### Minimal Core
Need to explore ways that certain portions of the application can be optionally loaded. For example, when doing a CLI-only service, you don't need features like caching or sessions, so you shouldn't require the overhead of loading them. Could handle this via "middleware" but that doesn't feel like CodeIgniter, though it might prove to be the easiest method to handle it.

## Current State and Overview
While this codebase is a huge departure from traditional CodeIgniter, the goals are the same as what is currently liked about CodeIgniter 2 and 3, namely somewhat minimalistic, easy to learn and use, and fast. Where possible, I'm striving to keep the "feel" of CodeIgniter while modernizing the codebase and making it possible (and encouraging) the ability to play nice with other modern frameworks where possible.

### Composer and Services
[Composer](http://getcomposer.org) forms the heart of the framework, providing the autoloading of all classes. Both the `system` and `application` folders live in their own namespaces, making extending and overriding core framework features a breeze.

There is a `CI` object that acts as a singleton, dependency injection container, and registry for common classes. It provides a `getInstance()` method that returns the CI singleton.  Classes can be defined in `application/Config/services.php` along with an alias they are referred as. Those classes can then be accessed through the CI object as a property. 

For example, the 'CodeIgniter\Log' class is aliased as `logger` in the config file. You can access that from within any class in the application like so: 

	$ci = \CodeIgniter\CI::getInstance();
	$ci->logger->logEmergency('...');
	
This always returns the same instance of the logger. You can always get a new instance of the object by calling `$ci->make('logger')`.

When you want to override or extend one of the core classes, all you have to do is to change the class name in the `services` config file to match yours. 

	$config['services'] = [
		'logger'	=> '\App\Logger`
	];
	
None of these classes are ever instantiated until they are needed for maximum performance and memory usage. 

### Config Class
The Config class has been reworked and simplified. You do not need to load config files anymore. Instead, filenames are specified as part of the item name. To get the value of the `migration_type` from the `migrations` config file, you would call `$ci->config->item('migrations.migration_type')`. The first time a file is accessed, it's config settings are loaded and cached in the class for faster access next time. 

### Logging
Logging works similar to how it did previously, but has been expanded to support the full eight levels of PSR3. In addition, it will fire events when logging, allow you to respond to different situations. For example, when the system has become unusable due to an error, that error gets logged, an event is fired and subscribers to that event might shoot off text messages or emails to those responsible for getting it back up and running. 

### Events
Hooks have been replaced with the Events class that works with Closures defined in the `config/events.php` file. Any class can easily call them, and event subscribers can assign priorities so that higher priority subscribers will run first, giving them the chance to cancel the execution of other subscribers if needed due to errors, etc. 

### Routing
The Router is being reworked with the following goals: 

* more modern routing, similar in nature to what you might find in Laravel.
* by default, works with closures and specifying controller/methods to use.
* should also support traditional, "magic routing" as it's being called on the forums, that maps the URI to found controllers/methods. 
* should support some form of modules/addons/packages, etc. that can be routed to, in addition to using composer packages.