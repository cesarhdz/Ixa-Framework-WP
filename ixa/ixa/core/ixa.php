<?php
/*
 * Name: Ixa
 * Description: Core Class, the loader
 * Encoding : UTF-8
 *
 * 
 *
 * @package WordPress
 * @subpackage Ixa
 * @since 0.1
 * @author Céar Hernández
 */

/*
 * Define framework constants for version and prefix
 *  the prefix is useful to avoid name collisions
 */
define('IXA_VERSION', '1.0');
define('IXA_PREFIX', 'ixa_');


/*
 * Define constants for parent and child paths
 */
$_themes_root = get_theme_root();
define('IXA_PARENT_PATH',   $_themes_root . '/ixa');
define('IXA_CHILD_PATH',    $_themes_root . '/' . get_template());



/*
 * Load The Ixa theme CLass
 */
require IXA_PARENT_PATH . '/ixa/core/ixa_theme.php';

/*
 * Load The Functions
 * @TODO Improve functions or merge with helepers
 */
//    require IXA_PARENT_PATH . 'themeplate/core/the_functions.php';
    
/*
* Load Develpment functions
* But only in debug time
*/
if('WP_DEBUG' == true)
{
    /*
     * Display almost all the errors
     */
    error_reporting(E_ALL & ~E_NOTICE);

    require IXA_PARENT_PATH . '/ixa/core/dev_tools.php';
}
    
/*
 * The Themeplate Starts...
 */
    
/*
 * Function to get the instance of Ixa Template
 */
function &get_ixa_theme()
// return the handle to the standard date validation object.
{
    static $instance;
    
    if (! is_object($instance)) {
        $instance = new Ixa_Theme();
    }
    
    return $instance;
}

/*
 * then initialize the object and we are done
 */
$theme =& get_ixa_theme();


//End .../core/ixa.php