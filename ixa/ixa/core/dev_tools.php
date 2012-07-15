<?php
/*
 * Template Name: the_dev_tools
 * Description: Tool only available in development
 * Encoding : UTF-8
 *
 * 
 *
 * @package WordPress
 * @subpackage Themeplate
 * @author César Hernández
 */

/*
 * Preprint
 * Prints the var content wrapped in pre tags
 * @param mixed $var to be printed
 * @param string $title to be displayed, usually the name of the variable
 */
function preprint($var, $title = '')
{
    echo '<div class="debug">';
    
    if(trim($title) != '' AND is_string($title))
    {
        echo '<h3>' . $title . '</h3>';
    }
    
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    
    echo '</div>';
}

/*
 * List Hooked Functions
 * Output the hooked functions
 * @return void
 */
function list_hooked_functions($tag=false){
    global $wp_filter;
    if($tag)
    {
        $hook[$tag] = $wp_filter[$tag];
        if(!is_array($hook[$tag]))
        {
            trigger_error("Nothing found for '$tag' hook", E_USER_WARNING);
            return;
        }
    }
    else
    {
        $hook = $wp_filter;
        ksort($hook);
    }
    echo '<pre>';
    foreach($hook as $tag => $priority)
    {
        echo "<br />&gt;&gt;&gt;&gt;&gt;\t<strong>$tag</strong><br />";
        ksort($priority);
        foreach($priority as $priority => $function)
        {
            echo $priority;
            foreach($function as $name => $properties)
                echo "\t$name<br />";
        }
    }
    echo '</pre>';
    return;
}




/* Ending of .../themepalte/core/the_dev_tools.php */