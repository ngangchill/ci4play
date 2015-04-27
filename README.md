# CodeIgniter 4 Playground

**This is not an official project** This is simply a playground for me to experiment with some ideas that may or may not be used within CodeIgniter 4.

## Current Challenges

### Routing
Working on the routing currently. Exploring ways to make it so that you can easiliy route to any namespaced controller (working) even if brought in via a Composer package. Also needs to support traditional CodeIgniter controllers and "modules" (or packages with the additional benefit of routing to classes within).

Am thinking that we can require all classes to be namespaced. This could mean that it's namespaced in App\Controllers, or a package brought down through GitHub, or another folder they've given a namespace to. This gets rid of the need to route to any strange modules, etc. The challenge then is to provide ways to load helpers, configs and language files from namespaced packages. But I think this can be handled by reading in the compser.json file ourself to get access to namespace locations.

### Minimal Core
Need to explore ways that certain portions of the application can be optionally loaded. For example, when doing a CLI-only service, you don't need features like caching or sessions, so you shouldn't require the overhead of loading them. Could handle this via "middleware" but that doesn't feel like CodeIgniter, though it might prove to be the easiest method to handle it.

Am thinking this could be handled through different "app boot files". Basically, the index.php file would have a  little detection that determines if it is cli or a web request, and then fires off different bootstrap files from there. Could also be custom bootstrap files if someone really needs to customize differently. 

**Is this even worth it?** 

## Backward Compatibility
While the framework is being considered a total rewrite, I believe it would be possible to provide a compatibility layer through the Controller object. In the new framework, the CIController class is not needed, so normal controllers will not need to extend anything in the system. This means we can provide methods within a CIController to map between CI3-style calls to CI4-style calls. 

For example, we could map `$this->load->view` in the controller to a new `View` class and `load` method. If the parameters are close to the same it should work relatively well. Otherwise, compability classes might need to provide an extra layer between to take care of the mapping for us. It looks like we might have code that runs enough faster than v3 that they might still end up with a faster application from it. Will be interesting to see if that’s the case.  

This would likely be a fairly time-consuming process, but would probably be worth it to help transfer people over to using v4 faster, since they wouldn’t have to rewrite their application or wait until they had a new application to work on with it (good for long-running projects they must maintain).

## Current State and Overview
While this codebase is a huge departure from traditional CodeIgniter, the goals are the same as what is currently liked about CodeIgniter 2 and 3, namely somewhat minimalistic, easy to learn and use, and fast. Where possible, I'm striving to keep the "feel" of CodeIgniter while modernizing the codebase and making it possible (and encouraging) the ability to play nice with other modern frameworks where possible.

### Composer and Services
[Composer][1] forms the heart of the framework, providing the autoloading of all classes. Both the `system` and `application` folders live in their own namespaces, making extending and overriding core framework features a breeze.

There is a `CI` object that acts as a [dependency injection container][2] for common classes. It provides a `getInstance()` method that returns the CI singleton.  Classes can be defined in `application/Config/services.php` along with an alias they are referred as. Those classes can then be accessed through the CI object as a property. 

For example, the 'CodeIgniter\Log' class is aliased as `logger` in the config file. You can access that from within any class in the application like so: 

	$ci = \CodeIgniter\CI::getInstance();
	$ci->logger->logEmergency('...');
 
This always returns the same instance of the logger. You can always get a new instance of the object by calling `$ci->make('logger')`.

When you want to override or extend one of the core classes, all you have to do is to change the class name in the `services` config file to match yours. 

	$config['services'] = [
	    'logger'    => '\App\Logger`
	];
 
None of these classes are ever instantiated until they are needed for maximum performance and memory usage. 

Any dependencies required in constructors of instantiated classes will first attempt to resolve through the DI object. If that doesn’t work, it will try to create a new instance of that class. 

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



[1]:	http://getcomposer.org
[2]:	https://github.com/lonnieezell/ci4play/blob/master/user_guide_src/ci_container.md