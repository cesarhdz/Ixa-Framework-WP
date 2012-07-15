<?php if(!defined('IXA_PARENT_PATH')) exit();
/*
 * This file contains the 
 */

$ixa_config = array(
    'nav_menus' => array(
        'main' => 'Menú Principal',
        'featured' => 'Menú Featured',
        'footer' => 'Menú del Footer',
    ),
    
    'sidebars' => array(
        'colophon' => array(
            'name'          => __('Colophon'),
            'id'            => 'colophon',
        ),
    ),
    
    'post_types' => array(
        'portafolio' => array(
            'labels' => array(
                'name'               => 'Portafolio',
                'singular_name'      => 'Trabajo',
                'add_new'            => 'Agegar Trabajo al Portafolio',
                'add_new_item'       => 'Agregar Nuevo Trabajo al Portafolio',
                'edit_item'          => 'Editar Trabajo',
                'new_item'           => 'Nuevo Trabajo',
                'all_items'          => 'Todos los Trabajos',
                'view_item'          => 'Ver Trabajo',
                'search_items'       => 'Buscar Trabajo en el Portafolio',
                'not_found'          => 'No se encontró ningún trabajo',
                'not_found_in_trash' => 'No hay trabajos en la Papelera',
                'parent_item_colon'  => ',',
                'menu_name'          => 'Portafolio',
            ),
            
            'supports'      => array('title', 'editor', 'thumbnail', 'page-attributes', 'excerpt', /* 'custom-fields', 'post-formats' */),
            'rewrite'       => array('slug' => 'portafolio', 'with_front' => false),
            'can_export'    => true,
            
            /*
             * Agregamos soporte para catgorias y etiquetas
             */
            'taxonomies' => array('category', 'post_tag'),
            
            /*
             * Para que tenga archivo
             */
            'has_archive' => 'portafolio',
        ),
    ),
    
    'admin_boxes' => array(
    ),
    
    //La ruta es de los estilos que vienen con less
    'meta' => array (
        array ('css'    => get_bloginfo('stylesheet_directory') . '/styles/cesarhdz.css'),
        array ('js'     => get_bloginfo('stylesheet_directory') . '/assets/respond/respond.min.js'),
        array ('js'     => get_bloginfo('stylesheet_directory') . '/assets/yepnope/yepnope.js'),
        array ('js'     => get_bloginfo('stylesheet_directory') . '/assets/modernizr/modernizr.js'),
    )
);
