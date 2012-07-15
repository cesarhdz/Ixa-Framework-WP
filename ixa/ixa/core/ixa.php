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
 * @author César Hernández
 */

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


//Termina .../core/ixa.php ?>