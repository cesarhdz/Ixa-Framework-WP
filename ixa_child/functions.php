<?php
/*
 * -----------
 * DO NOT EDIT
 * -----------
 * Below are the functions that make this theme work as Ixa child theme
 */

/*
 * Child and parent path are stored in constants, to properly work
 */
$themes_root = get_theme_root();
define('IXA_PARENT_PATH',   $themes_root . '/ixa');
define('IXA_CHILD_PATH',    $themes_root . '/' . get_template());

/*
 * We only need to load the ixa core file
 */
require_once IXA_PARENT_PATH .'/ixa/core/ixa.php';
    
/*
 * then initialize the object and we are done
 */
$theme =& get_ixa_theme();

/*
 * --------
 * END IXA
 * --------
 */
