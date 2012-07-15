<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//This array will be merge with user settings
$ixa_defaults = array(
    
    /*
     * Sidebars
     * Array
     */
    'sidebars' => '',
    
    /*
     * Nav Menus
     * Array
     */
    'nav_menus' => '',
    
    /*
     * Menu Show Home
     * Boolean
     */
    'menu_show_home' => true,
    
    /*
//     * Excerpt Length
     * Int
     */
    'excerpt_length' => 40,
    
    /*
     * Remove Gallery CSS
     * Boolean
     */
    'remove_gallery_css' => true,
    
    /*
     * Remove WP TExturize
     * Boolean
     */
    'remove_wptexturize' => true,
    
    /*
     * Options
     * Array of options that will be display in theme options admin panel
     * See doc
     */
    'options' => array(
        /*
         * Header Options
         */
        'header' => array(
            
          /*
             * Default Logo
             */
            'logo' => array()
        ),
        
        /*
         * Footer Options
         */
        'footer' => array(
         
          /*
             * Legals
             */    
           'legals' => array(),     
                
         ),
        
       /*
         * MIscelaneous options
         */
        'misc' => array(
          /*
             * 404 Headeing
             */
            '404_heading' => array(),
            
          /*
             * 404 Message
             */
            '404_msg' => array(),
        )
    ),
    
    /*
     * Show Log
     * Cahnge for production to false
     */
    'show_log' => false,
    
    /*
     * Pages Excerpt
     * Allows pages to have excerpt
     */
    'pages_excerpt' => true,
    
    /*
     * Main CSS
     */
    'main_css' => get_bloginfo('stylesheet_url'),
    
    /*
     * Sidebar Deafults
     * Array
     */
    'sidebar_defaults' => array(
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget'  => '</li>',
        'before_title'  => '<div class="header"><h3 class="title">',
        'after_title'   => '</h3></div>',
    ),
    
    /*
     * Post Types Defaults
     */
    'post_types_defaults' => array(
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => false,
        'capability_type'    => 'post',
        'hierarchical'       => false,
        'supports'           => array('title', 'editor', 'thumbnail', 'page-attributes', 'excerpt'),
        'has_archive'        => true,
    ),
    
    /*
     * The areas Post types are useful to load content editable in WordPress
     */
    'areas_post_type' => array(
        'labels' => array(
            'name'               => 'Areas',
            'singular'           => 'Area',
            'menu_name'          => 'Areas',
        ),

        'public' => false,
        'hierarchical' => false,
        'query_var' => false,
        'show_ui' => true,
        'capability_type' => 'page',
        'menu_position' => 60,
        'supports' => array('editor', 'author', 'revisions', 'slug', 'title'),
        'has_archive' => false,
    ),
    
    /*
     * Default Areas, will be createn in theme initialization
     */
    'default_areas' => array(
        
    ),
    
    /*
     * Meta script to be loades
     */
    'meta' => array(
        
    )
);