<?php

/*
 *---------------------------------------------------------------
* APPLICATION ENVIRONMENT
*---------------------------------------------------------------
*
* You can load different configurations depending on your
* current environment. Setting the environment also influences
* things like logging and error reporting.
*
* This can be set to anything, but default usage is:
*
*     development
*     testing
*     production
*
* NOTE: If you change these, also change the error_reporting() code below
*
*/
define('ENVIRONMENT', 'testing');
/*
 *---------------------------------------------------------------
* ERROR REPORTING
*---------------------------------------------------------------
*
* Different environments will require different levels of error reporting.
* By default development will show errors but testing and live will hide them.
*/

if (defined('ENVIRONMENT'))
{
	switch (ENVIRONMENT)
	{
		case 'development':
			error_reporting(E_ALL);
			break;

		case 'testing':
		case 'production':
			error_reporting(0);
			break;

		default:
			exit('The application environment is not set correctly.');
	}
}

/*
 *---------------------------------------------------------------
* SYSTEM FOLDER NAME
*---------------------------------------------------------------
*
* This variable must contain the name of your "system" folder.
* Include the path if the folder is not in the same  directory
* as this file.
*
*/
$system_path = 'system';

/*
 *---------------------------------------------------------------
* APPLICATION FOLDER NAME
*---------------------------------------------------------------
*
* If you want this front controller to use a different "application"
* folder then the default one you can set its name here. The folder
* can also be renamed or relocated anywhere on your server.  If
* you do, use a full server path. For more info please see the user guide:
* http://codeigniter.com/user_guide/general/managing_apps.html
*
* NO TRAILING SLASH!
*
*/
$application_folder = 'application';

/*
 * --------------------------------------------------------------------
* DEFAULT CONTROLLER
* --------------------------------------------------------------------
*
* Normally you will set your default controller in the routes.php file.
* You can, however, force a custom routing by hard-coding a
* specific controller class/function here.  For most applications, you
* WILL NOT set your routing here, but it's an option for those
* special instances where you might want to override the standard
* routing in a specific front controller that shares a common CI installation.
*
* IMPORTANT:  If you set the routing here, NO OTHER controller will be
* callable. In essence, this preference limits your application to ONE
* specific controller.  Leave the function name blank if you need
* to call functions dynamically via the URI.
*
* Un-comment the $routing array below to use this feature
*
*/
// The directory name, relative to the "controllers" folder.  Leave blank
// if your controller is not in a sub-folder within the "controllers" folder
// $routing['directory'] = '';

// The controller class file name.  Example:  Mycontroller
// $routing['controller'] = '';

// The controller function you wish to be called.
// $routing['function']	= '';


/*
 * -------------------------------------------------------------------
*  CUSTOM CONFIG VALUES
* -------------------------------------------------------------------
*
* The $assign_to_config array below will be passed dynamically to the
* config class when initialized. This allows you to set custom config
* items or override any default config values found in the config.php file.
* This can be handy as it permits you to share one application between
* multiple front controller files, with each file containing different
* config values.
*
* Un-comment the $assign_to_config array below to use this feature
*
*/
// $assign_to_config['name_of_config_item'] = 'value of config item';



// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
*  Resolve the system path for increased reliability
* ---------------------------------------------------------------
*/

// Set the current directory correctly for CLI requests
// if (defined('STDIN'))
// {
// 	chdir(dirname(__FILE__));
// }

if (realpath($system_path) !== FALSE)
{
	$system_path = realpath($system_path).'/';
}

// ensure there's a trailing slash
$system_path = rtrim($system_path, '/').'/';

// Is the system path correct?
if ( ! is_dir($system_path))
{
	exit("Your system folder path does not appear to be set correctly. Please open the following file and correct this: ".pathinfo(__FILE__, PATHINFO_BASENAME));
}

/*
 * -------------------------------------------------------------------
*  Now that we know the path, set the main path constants
* -------------------------------------------------------------------
*/
// The name of THIS file
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

// The PHP file extension
// this global constant is deprecated.
define('EXT', '.php');

// Path to the system folder
define('BASEPATH', str_replace("\\", "/", $system_path));

// Path to the front controller (this file)
define('FCPATH', str_replace(SELF, '', __FILE__));

// Name of the "system folder"
define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));


// The path to the "application" folder
if (is_dir($application_folder))
{
	define('APPPATH', $application_folder.'/');
}
else
{
	if ( ! is_dir(BASEPATH.$application_folder.'/'))
	{
		exit("Your application folder path does not appear to be set correctly. Please open the following file and correct this: ".SELF);
	}

	define('APPPATH', BASEPATH.$application_folder.'/');
}

// ------------------------------------------------------------------------

/**
 * System Initialization File
 *
 * Loads the base classes and executes the request.
 *
 * @package		CodeIgniter
 * @subpackage	codeigniter
 * @category	Front-controller
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/
*/

/**
 * CodeIgniter Version
 *
 * @var string
 *
*/
define('CI_VERSION', '2.1.2');

/**
 * CodeIgniter Branch (Core = TRUE, Reactor = FALSE)
 *
 * @var boolean
 *
*/
define('CI_CORE', FALSE);

/*
 * ------------------------------------------------------
*  Load the global functions
* ------------------------------------------------------
*/
require(BASEPATH.'core/Common.php');

/*
 * ------------------------------------------------------
*  Load the framework constants
* ------------------------------------------------------
*/
if (defined('ENVIRONMENT') AND file_exists(APPPATH.'config/'.ENVIRONMENT.'/constants.php'))
{
	require(APPPATH.'config/'.ENVIRONMENT.'/constants.php');
}
else
{
	require(APPPATH.'config/constants.php');
}

/*
 * ------------------------------------------------------
*  Define a custom error handler so we can log PHP errors
* ------------------------------------------------------
*/
set_error_handler('_exception_handler');

if ( ! is_php('5.3'))
{
	@set_magic_quotes_runtime(0); // Kill magic quotes
}

/*
 * ------------------------------------------------------
*  Set the subclass_prefix
* ------------------------------------------------------
*
* Normally the "subclass_prefix" is set in the config file.
* The subclass prefix allows CI to know if a core class is
* being extended via a library in the local application
* "libraries" folder. Since CI allows config items to be
* overriden via data set in the main index. php file,
* before proceeding we need to know if a subclass_prefix
* override exists.  If so, we will set this value now,
* before any classes are loaded
* Note: Since the config file data is cached it doesn't
* hurt to load it here.
*/
if (isset($assign_to_config['subclass_prefix']) AND $assign_to_config['subclass_prefix'] != '')
{
	get_config(array('subclass_prefix' => $assign_to_config['subclass_prefix']));
}

/*
 * ------------------------------------------------------
*  Set a liberal script execution time limit
* ------------------------------------------------------
*/
if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0)
{
	@set_time_limit(300);
}

/*
 * ------------------------------------------------------
*  Start the timer... tick tock tick tock...
* ------------------------------------------------------
*/
$BM =& load_class('Benchmark', 'core');

/*
 * ------------------------------------------------------
*  Instantiate the hooks class
* ------------------------------------------------------
*/
$EXT =& load_class('Hooks', 'core');

/*
 * ------------------------------------------------------
*  Is there a "pre_system" hook?
* ------------------------------------------------------
*/
$EXT->_call_hook('pre_system');

/*
 * ------------------------------------------------------
*  Instantiate the config class
* ------------------------------------------------------
*/
$CFG =& load_class('Config', 'core');

/*
 * ------------------------------------------------------
*  Instantiate the UTF-8 class
* ------------------------------------------------------
*
* Note: Order here is rather important as the UTF-8
* class needs to be used very early on, but it cannot
* properly determine if UTf-8 can be supported until
* after the Config class is instantiated.
*
*/

$UNI =& load_class('Utf8', 'core');

/*
 * ------------------------------------------------------
*  Instantiate the URI class
* ------------------------------------------------------
*/
$URI =& load_class('URI', 'core');

/*
 * ------------------------------------------------------
*  Instantiate the routing class and set the routing
* ------------------------------------------------------
*/
$RTR =& load_class('Router', 'core');

/*
 * ------------------------------------------------------
*  Instantiate the output class
* ------------------------------------------------------
*/
$OUT =& load_class('Output', 'core');


/*
 * -----------------------------------------------------
* Load the security class for xss and csrf support
* -----------------------------------------------------
*/
$SEC =& load_class('Security', 'core');

/*
 * ------------------------------------------------------
*  Load the Input class and sanitize globals
* ------------------------------------------------------
*/
$IN	=& load_class('Input', 'core');

/*
 * ------------------------------------------------------
*  Load the Language class
* ------------------------------------------------------
*/
$LANG =& load_class('Lang', 'core');

/*
 * ------------------------------------------------------
*  Load the app controller and local controller
* ------------------------------------------------------
*
*/
// Load the base controller class
require BASEPATH.'core/Controller.php';

function &get_instance()
{
	return CI_Controller::get_instance();
}

new CI_Controller;

require_once './goutte.phar';
