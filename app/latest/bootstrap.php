<?php defined('SYSPATH') or die('No direct script access.');

//-- Environment setup --------------------------------------------------------

/**
 * Set the default time zone.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('Europe/Amsterdam');

/**
 * Set the default locale.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://kohanaframework.org/guide/using.autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @see  http://php.net/spl_autoload_call
 * @see  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

//-- Configuration and initialization -----------------------------------------

/**
 * Safe settings for Cookies, so that they are only accessed through HTTP, and not by javascript (prevents XSS to a certain extent)
 */
//Cookie::$httponly = TRUE; // Unfortunately, the ACL doesn't like this when in an AJAX call... Real weird, but anyways...!

/**
 * Set the environment string by the domain (defaults to Kohana::DEVELOPMENT).
 */
Kohana::$environment = ($_SERVER['SERVER_NAME'] !== 'localhost' AND $_SERVER['SERVER_NAME'] !== '127.0.0.1') ? Kohana::PRODUCTION : Kohana::DEVELOPMENT;

/**
 * Initialize Kohana based on the environment, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
 Kohana::init(array(
    //dynamically figure base_url out every request. Con is that it takes up more resources.
	'base_url'   => substr($_SERVER["PHP_SELF"], 0, strpos($_SERVER["PHP_SELF"], "index.php")),
	'index_file' => FALSE,    
    'profile'    => Kohana::$environment !== Kohana::PRODUCTION,
    'caching'    => Kohana::$environment === Kohana::PRODUCTION,
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Kohana_Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Kohana_Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
    //'supercache' => MODPATH.'supercache', // SuperCache 
    'event' => MODPATH.'event', // Event library
    'sprig-auth'       => MODPATH.'sprig-auth',       // Sprig modeling
    'acl'       => APPPATH.'acl',       // Sprig modeling
    'sprig-mptt'       => MODPATH.'sprig-mptt',       // Sprig modeling
    'sprig' => MODPATH.'sprig',       // Sprig modeling
	'auth'       => MODPATH.'auth',       // Basic authentication
	'cache'      => MODPATH.'cache',      // Caching with multiple backends
	// PhPQuery
	'phpquery' => MODPATH.'phpquery',
	// 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
	'database'   => MODPATH.'database',   // Database access
	// 'image'      => MODPATH.'image',      // Image manipulation
	// 'orm'        => MODPATH.'orm',        // Object Relationship Mapping
	// 'oauth'      => MODPATH.'oauth',      // OAuth authentication
	// 'pagination' => MODPATH.'pagination', // Paging of results
	// 'unittest'   => MODPATH.'unittest',   // Unit testing
	'userguide'  => MODPATH.'userguide',  // User guide and API documentation
    // Plugins
    'plugin_multilanguage' => APPPATH.'plugins/multilanguage',
    'plugin_jquery' => APPPATH.'plugins/jquery',
    'plugin_clientjavascriptvars' => APPPATH.'plugins/clientjavascriptvars',
    // Pagefillers
    'pagefiller_default' => APPPATH.'pagefillers/default',
    // HTMLPurifier XSS Prevention
    'htmlpurifier' => MODPATH.'htmlpurifier'
));

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
Route::set('default', '(<controller>(/<action>(/<args>)))', array('args' => '.*'))
	->defaults(array(
		'controller' => 'welcome',
		'action'     => 'index',
	));
	
// Enable the Supercache (don't for the moment, it won't help performance-wise)
// $super_cache = Super_Cache::instance();

if ( ! defined('SUPPRESS_REQUEST'))
{
	/**
	 * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
	 * If no source is specified, the URI will be automatically detected.
	 */
    $request = Request::instance();
    try
    {
        Event::instance('wi3.beforeinit')->execute(); // Used by i.e. caching-mechanisms
        /**
        * Now init Wi3. The main Wi3 class in turn will load its subclasses
        */
        Wi3::instance()->init();
        // Fire event that marks the begin of process execution
        Event::instance('wi3.beforeexecution')->execute(); // Used by i.e. caching-mechanisms
        // Attempt to execute the response
        $request->execute();
        if (is_object($request->response))
        {
            $request->response = $request->response->render(); // ->response is a View object most often. By rendering it and then storing it as a string, Request::instance()->response will contain the final string-result, so that the HTML can be changed by methods that are attached to following events. Also, the views will not get rendered over and over when casted to a string.
        }
        // Request::instance()->response will now contain the page-html
        // A row of Events that are now executed in order
        // Add content to response
        Event::instance('wi3.afterexecution.addcontent.css.first')->execute(); // Add css
        Event::instance('wi3.afterexecution.addcontent.css.second')->execute();
        Event::instance('wi3.afterexecution.addcontent.css')->execute();
        Event::instance('wi3.afterexecution.addcontent.javascript.variables')->execute(); // Add javascript
        Event::instance('wi3.afterexecution.addcontent.javascript.first')->execute();
        Event::instance('wi3.afterexecution.addcontent.javascript.second')->execute();
        Event::instance('wi3.afterexecution.addcontent.javascript')->execute();
        Event::instance('wi3.afterexecution.addcontent.html')->execute(); // Add html
        // Change content
        Event::instance('wi3.afterexecution.changecontent.first')->execute(); // Change final html
        Event::instance('wi3.afterexecution.changecontent.second')->execute(); // Change final html
        Event::instance('wi3.afterexecution.changecontent.third')->execute(); // Change final html
        // Internal: Change ID links to 'normal' links
        // Wi3::inst()->linkconverter->execute();
        // Content is ready for output. Last change to process the data (but not change it)
        Event::instance('wi3.afterexecution.processcontent')->execute(); // Used by i.e. caching-mechanisms
        
    }
    catch (ACL_Exception_401 $e)
    {
        // Redirect to the same controller, but the login action
        // Controller should allow access to that function, and deal with the login and authentication
        Wi3::inst()->session->set("previously_requested_url", Wi3::inst()->routing->url);
        $request->redirect(Wi3::inst()->urlof->action("login"));
    }
    catch (Exception_Continue $e)
    {
        // Just continue with execution
        // ...
    }
    catch (Exception $e)
    {
        if (Kohana::$environment === Kohana::DEVELOPMENT)
        {
            // Just re-throw the exception
            throw $e;
        }
     
        // Log the error
        Kohana::$log->add(Kohana::ERROR, Kohana::exception_text($e));
     
        // Create a 404 response
        $request->status = 404;
        $request->response = View::factory('error')
          ->set('title', '404')
          ->set('content', View::factory('errors/404'));
    }

     
	echo $request
		->send_headers()
		->response;
}
