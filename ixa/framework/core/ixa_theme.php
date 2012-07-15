<?php if(!defined('IXA_PARENT_PATH')) exit;
/*
 * Core Themplate Class
 *
 * @TODO Add Cache System, per pages or modules basis !important 
 * @TODO Add custom post types support !in beta
 * @TODO Add images sizes dynamically
 * @TODO Improve excerpt and meta title
 * @TODO Create dynamically variables for theme in admin panel
 * @TODO Add template for comments and pingback
 */

/*
 * Core Class
 */
class Ixa_Theme {
    
    var $prefix = 'ixa_';
    var $log;
    
    function __construct()
    {
        //Load config files
        $this->_load_default_config();
        
        //Initialize the view
        $this->initialize();
    }
    
    /*
     * Initialize
     * Apply filters, actions and hooks depending on the config options
     * @param Array Config
     * 
     */
    function initialize()
    {
        /*
         * Child theme config
         */
        $file = IXA_CHILD_PATH . '/ixa/config.php';
        
        if(file_exists($file))
        {
            include($file);

            $this->_log("Child theme Config file loaded", $file, 2);
            
            /*
             * Merge config options
             */
            if(is_array($ixa_config) AND count($ixa_config))
            {
                $this->config = array_merge($this->config, $ixa_config);
            }
            
            /*
             * Unset variable
             */
            unset($ixa_config);
        }
        
        
        
        
        //@TODO Move register functions to methods to helpers folder
        //Register Nav Menues
        if(is_array($this->config['nav_menus']) AND count($this->config['nav_menus']))
        {
            register_nav_menus($this->config['nav_menus']);
        }
        
        //Allow show home for menues
        if($this->config['menu_show_home'] === true)
            add_filter( 'wp_page_menu_args', array(&$this, '_menu_show_home' ));
        
        //Set excerpt length
        add_filter( 'excerpt_length', array(&$this, '_excerpt_length') );
        
        //Remove gallery inline styles
        if($this->config['remove_gallery_css'])
            add_filter( 'gallery_style', array(&$this, '_remove_gallery_css' ));
        
        //Register sidebars, following wordpress standars for sidebars
        if(is_array($this->config['sidebars']) AND count($this->config['sidebars']))
        {
            $this->_register_sidebars();
        }
        
        //Register for custom post types
        if(is_array($this->config['post_types']) AND count($this->config['post_types']))
        {
            $this->_prepare_post_types();
        }
        
        /*
         * Areas are an easy way to make editable some areas like footer, etc
         * Are better than Theme Options, because we have a full editor and can add
         * images from library
         */    
        $this->_prepare_areas();
        
        if(count($this->_custom_post_types))
        {
            //@TODO Add Admin Boxes automatically, see: http://wp.tutsplus.com/series/reusable-custom-meta-boxes/
            add_action('init', array(&$this, '_register_post_types'));
        }
        
        
        //Remove texturizer to use code and prittifier 
        //@TODO Include Prettyffier
        if($this->config['remove_wptexturize'] === true)
        {
            remove_filter('the_content', 'wptexturize');
            remove_filter('the_excerpt', 'wptexturize');
        }
        
        if($this->config['pages_excerpt'] == true)
        {
            add_post_type_support( 'page', 'excerpt' );
        }

        
        //Get conditional statements
//        $this->statements = themeplate_statements();
        
        //From twenty eleven theme
        // Add support for a variety of post formats
	add_theme_support( 'post-formats', array( 'aside', 'link', 'gallery', 'status', 'quote', 'image' ) );

	// This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
	add_theme_support('post-thumbnails');
        
        /*
         * @TODO Add more theme featues
         */
//        	// We'll be using post thumbnails for custom header images on posts and pages.
//	// We want them to be the size of the header image that we just defined
//	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
//	set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );
//
//	// Add Twenty Eleven's custom image sizes
//	add_image_size( 'large-feature', HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true ); // Used for large feature (header) images
//	add_image_size( 'small-feature', 500, 300 ); // Used for featured posts if a large-feature doesn't exist
//
//	// Turn on random header image rotation by default.
//	add_theme_support( 'custom-header', array( 'random-default' => true ) );
//
//	// Add a way for the custom header to be styled in the admin panel that controls
//	// custom headers. See twentyeleven_admin_header_style(), below.
//	add_custom_image_header( 'twentyeleven_header_style', 'twentyeleven_admin_header_style', 'twentyeleven_admin_header_image' );
        
        //Include admin options
        //@TODO Make admin options customizables
//        include_once(THEMEPLATE_ROOT.'/themeplate/core/themplate_options.php');
    }
    
    /*
     * Load defaults config
     */
    function _load_default_config()
    {
        /*
         * Default config files are mandatory
         */
        $file = IXA_PARENT_PATH .'/framework/config/default.php';
        if(file_exists($file))
        {
            include($file);

            $this->_log("Default Config file loaded", $file, 2);
        }
        else
        {
            $this->_log("Default Config file not found", array('file' => $file), 1);
            exit;
        }
        
        /*
         * Save and Unset variable
         */
        $this->config = $ixa_defaults;
        unset($ixa_config);
    }
    
    /*
     * Add head and meta info
     */
    function open($vars = array())
    {
        $vars = (is_array($vars))
                ? array_merge($this->config['meta'], $vars)
                : $this->config['meta'];
        
        /*
         * Set meta vars
         */
        $this->_global_cache_vars['meta'] = $vars;
//        
//        return $this->module('meta', array('meta' => $vars) );
    }
    
    /*
     * Funcion para mostrar el final
     */
    function close($scripts = array())
    {
        //@TODO Add scripts to the footer
        
        wp_footer();
        echo "</body></html>";
    }
    
    /********************  Core Loaders */
    
    /*
     * Block
     * Display a block of content, can include anthor block or modules
     * @param string Name of the block
     * @param Array Elements that contain the block and attributes:
     *      Starting with underscore: Are config options of the block
     *      Strating with 'b:' Are nested block that will be loaded, this function is recursive
     *      Starting with 'm:' Are modules that also will be loades 
     * @return HTML string The whole block
     */
    function block($block, $args = array())
    {
        /*
         * If we have an array we extact the variables and config
         */
       $args = $this->_extract_arguments($args);
        
       /*
         * This->block is not currently supported  
        * If we have predifiend a block, we can use the arguments 
         */
//        if(is_array($this->blocks[$block]))
//        {
//            $args = array_merge($this->blocks[$block], $args);
//        }
        
       
       /*
         * @TODO Check Cache for blocks
         */
      
       /*
        * Default attributes for blocks
        */
       $defaults = array(
           'tag' => 'section',
           'class' => $block,
           'id' => $args['config']['id'],
       );
       
       /*
        * Get open and closing tags, by default section is the tag
        */
       $block_tag = $this->_parse_tags($args['config']['block'], $defaults);
       
//       $tag = ($args['config']['tag']) ? $args['config']['tag'] : 'section';
//       $block_attr = array(
//           'id' => $args['config']['id'], 
//           
//           //We preppend the name as a class of the block
//           'class' => ($args['config']['class'])
//                ? $block . ' ' . $args['config']['class']
//                : $block
//      );
       
       //Register the block tags
       $tags = array(
           'block' => $this->_get_tags($block_tag['tag'], $block_tag['attr']),
       );
       
       //Then we look for container, it only accepts classes an id's
       if($args['config']['container'])
       {
           $defaults = array(
               'class' => 'container_' . $block .' container',
               'tag'    => 'div',
           );
           
           $container = $this->_parse_tags($args['config']['container'], $defaults);
           $tags['container'] = $this->_get_tags($container['tag'], $container['attr']);
       }
       
       /*
         * Build the block
         */
        return $this->_build_block($block, $args['vars'], $tags, $container);
    }
    
    /*
     * Build Block
     * We have a block to build
     * this function is recursive, so we will loop until we read all the levels defined in the content array
     */
    function _build_block($block, $content, $tags)
    {
        //Initialize out
        $out = "\n<!-- {$block} start -->";
        
        //Open container and block tags
        $out .= $tags['container']['open'] . $tags['block']['open'];
        
        //If the block has only string, we displayit whitout trasformations
        if(is_string($content))
        {
            $out .= $content;
        }
        //If we have an array we'll make a loop in order to display every children
        //they can be another block or modules. 
        elseif(is_array($content))
        {
            foreach($content AS $key => $val)
            {
                /*
                    * Explode to know the name and key
                    * $type = $key[0]
                    * $name = $key[1]
                    */
                $key = explode(':', $key);

                /*
                    *  We have a block if the key starts with b:name
                    */
                if($key[0] == 'b' AND is_array($val))
                {
                    //$key[1] is the name of the block, and then we have the content and arguments
                    $out .= $this->block($key[1], $val);
                }
                
                /*
                 * We are dealing with a module when we have the prefix m:module
                 */
                if($key[0] == 'm')
                {
                    /*
                     * Load the module with attr
                     */
                 $out .= $this->module($key[1], $val);
                    
                }
            }
        }
        
        //Close the block
        $out .= $tags['block']['close'] . $tags['container']['close'];
        
       //Return with comments
        return $out . "<!-- .{$block} end -->\n\n";
    }
    
    
    /*
     * Load a module
     * @param string Name of the module to be loaded
     * @param Array $vars taht will be used in the module
     * @param mixed The way we handle cache (currently has no effects)
     */
    function module($module, $vars = array(), $cache = false)
    {
        $files = array();
        
       /* 
        * This check can be @deprecated in favor of the use of vars in every module
         * We fisrt look for the module key in the _mod_vars_cache
         * if it exists it overwrite the name, and if it is set to false, we return empty
         */
        if(array_key_exists($module, $this->_global_cache_vars))
        {
            /*
            * If it has been set tp false we display nothing
            */
            if($this->_global_cache_vars[$module] === false)
            {
                return '';
            }

           /*
             * Overwrite the module if has been set a variable with the same name
             */
            if(is_string($this->_global_cache_vars[$module]))
            {
                $module = $this->_global_cache_vars[$module];
            }
        }
        
        
        /*
         * Get the module name if exists, is the default modifier, e.g.
         *  sidebar-main will first look for modules/sidebar/sidebar-name, and if the file is not found, 
         *               then we add a var named $sidebar-name equals to 'main', so we can make use of it
         *              in the parent module modules/sidebar/sidebar. 
         *              BTW, ofentimes the name is translated into a class, and seldom is an id, but it depends on the module
         */
        if(strpos($module, '-'))
        {
            $comp = explode('-', $module);
            $name = $comp[1];
            $module = $comp[0];
        }
        else
        {
            $name = false;
        }
        
        
       /*
         * Default root for modules
         */
        $root   = IXA_CHILD_PATH . '/modules/';
       
        /*
         * If we have an underscore means it's a submodule wich is located in the parent module, for instance:
         *  meta_tilte
         * If located in ../modules/meta/meta_title
         * This can make easier to organize, name and find your files and besides it makes more sense.
         * In short: underscore means submodule, so we can have only one
         */
        if(strpos($module, '_'))
        {
            $submod = explode('_', $module);
            
            //The new root is: THEMEPALTE ROOT . modules/{module}/{module}_{submodule}
            $root .= $submod[0] . '/';
        }
        else
        {
            //Append module to 
            $root .= $module . '/';
        }
            
        
      /*
       * Separate vars, between private and public,
       * $those starting with underscore are private and are prefixed with the name of the module
       * The other ones are fully availables in the scope of the module
       * We generate after knowing the core module name so that we can append to the cor e name
       */
        $vars = $this->_extract_arguments($vars, (is_array(@$submod)) ? $submod[0] : $module);
        
       /*
        * @TODO Add text variable in Modules similar to Uepnope
         * @TODO Look for Module cache
         */
        
        
        /*
         *  We can give many names separated by pipes '|', and the loop will find in the order you gave it
         */
        if(strpos($name, '|'))
        {
            $name = explode('|', trim($name, '|'));
            
            foreach($name AS $n)
            {
                $n = sanitize_file_name($n);
                $files[] = "{$module}-{$n}"; 
            }
        }
        /*
         * If no pipes we have only one name
         */
        elseif($name != '')
        {
            $name = sanitize_file_name($name);
            $files[] = "{$module}-{$name}";
            
        }
        
        /*
         * We set the last fallback, that equals to module
         */
        $files[] = $module;
        
        /*
         * Search the module
         */
        foreach($files as $f)
        {
            $file = $root . $f . '.php';
            if(is_readable($file))
            {
                //@TODO Add tags to modules
                
                $this->_log('Module Loaded',  array('module' => $module, 'name' => $name, 'file' => $file), 2);
                return $this->_load_view($file, $vars['vars'], $vars['config']);
            }
            
          /*
             * If the file is not found we save the name in the config vars with the pattern: $module_name => $name
             * this way, the module can use it to show different content.
             */
           $vars['config'][$module.'_name'] = $name;
            
            //We log that the file was not found
            $this->_log("The module was not found", array ('module' => $module, 'name' => $name, 'file' => $file), 1);
        }
        
        /*
         * If we are here, we haven't found any module, so we log it and return false
         */
        return false;
    }
    
    function part($part, $vars = array(), $cache = false)
    {
       /*
         * Default root for modules
         */
        $file   = IXA_CHILD_PATH . '/parts/' . $part . '.php';
       
      /*
       * Separate vars, between private and public,
       * $those starting with underscore are private and are prefixed with the name of the module
       * The other ones are fully availables in the scope of the module
       * We generate after knowing the core module name so that we can append to the cor e name
       */
        $vars = $this->_extract_arguments($vars);
        
        /*
         * Search the part
         */
        if(is_readable($file))
        {
            $this->_log('Part Loaded',  array('part' => $part, 'filename' => $file), 2);
            return $this->_load_view($file, $vars['vars'], $vars['config']);
        }

        //We log that the file was not found
        $this->_log("The module was not found", array ('module' => $module, 'name' => $name, 'file' => $file), 1);
        
        /*
         * If we are here, we haven't found any module, so we log it and return false
         */
        return false;
    }
    
    /*
     * Build Page
     * This is the main function that build the whole page based on the template file
     */
    function build_page($template, $vars = array())
    {
        //Set the object level to allow nestes files
        $this->_ob_level  = ob_get_level();
        
        /*
         * Add more vars in the open function
         */
        $this->open();
        
        /*
         * Default root for templates
         */
        $file   = IXA_CHILD_PATH . '/templates/' . $template . '.tmpl.php';
       
      /*
       * Separate vars, between private and public,
       * $those starting with underscore are private and are prefixed with the name of the module
       * The other ones are fully availables in the scope of the module
       * We generate after knowing the core module name so that we can append to the cor e name
       */
        $vars = $this->_extract_arguments($vars);
        
        
        /*
         * Search the part
         */
        if(is_readable($file))
        {
            $this->_log('Part Loaded',  array('template' => $template, 'filename' => $file), 2);
            
            /*
             * Mostramos todo el contenido
             */
            echo $this->_load_view($file, $vars['vars'], $vars['config']);
            
            $this->close();
            
            return;
        }

        //We log that the file was not found
        $this->_log("The template was not found", array ('template' => $template, 'file' => $file), 1);
        
        /*
         * If we are here, we haven't found any template, so we log it and return false
         */
        return false;
    }
    
    /*
     * Area
     * Core function that will load an area instead of setting mutltiple options, we build areas
     * @param String $area that will be included, we use the slug as identifier
     * @return Object, containing post data
     */
    function area($area)
    {
        // @TODO  Preload default areas, to make it faster
        
       /*
         * Star a new query
         */
        $args = array(
    
            /*
                * Post Type
                */
            'post_type' => $this->prefix.'areas',
            
            /*
                * Sólo necesitamos un post
                */
            'numberposts' => 1,
            
            /*
                * Y la buscamos por el nombre 
                */
            'name' => $area,

        );
        
        /*
         * Utilizamos la funcion get_posts, en lugar del query
         */
        $my_area = get_posts($args);
        
        /*
         * Buscamos el contenido si es que hay posts
         */
        if($my_area)
        {
            return $my_area[0];
        }
        else
        {
            return '';
        }
    }
 
    /*
     * Hold the vars that has been sent to the modules
     */
    private $_global_cache_vars = array();
    /*
     * Load Files
     * Core Function. Includes all the needed files, e.g. modules, elements, templates. And stores in output array
     * @param string    $_tmpl_filename   Name of the file to be loaded
     * @param array     Variables that will be availabl to the view
     * @param string    $_tmp_file_scope, What key of output will be used to save the view, defaults to content
     */
    private function _load_view($_tmpl_filename, $_public_vars = array(), $_private_vars = array())
    {
      /*
        * Extract and cache variables so we will have all of them availables in everý module
        * and can be overwrriten in order to change the behaviour, (borrowed from Codeigniter)
        *
        * You can either set variables using the dedicated $this->load_vars()
        * function or via the second parameter of this function. We'll merge
        * the two types and cache them so that views that are embedded within
        * other views can have access to these variables.
        */	
        if (is_array($_public_vars))
        {
            $this->_global_cache_vars = array_merge($this->_global_cache_vars, $_public_vars);
        }
        
        /*
         * Extract global and private vars
         */
        extract($this->_global_cache_vars);
        extract($_private_vars);
        
        /*
        * Buffer the output, borrowed from CodeIgniter
        *
        * We buffer the output for two reasons:
        * 1. Speed. You get a significant speed boost.
        * 2. So that the final rendered template can be
        * post-processed by the output class.  Why do we
        * need post processing?  For one thing, in order to
        * show the elapsed page load time.  Unless we
        * can intercept the content right before it's sent to
        * the browser and then stop the timer it won't be accurate.
        */
        ob_start();
        
        //Include the file
        include($_tmpl_filename);

        //Nested Modules or elements
        if (ob_get_level() > $this->_ob_level + 1)
        {
            //We stro to be displayed properly
            ob_end_flush();
        }
        else
        {
            //We save the output
            $out = ob_get_contents();
            @ob_end_clean();
            
            return $out;
        }
    }
    
    
    /*
     * Extract Arguments
     * Rules strating with underscore goes to config
     * The other ones are kept.
     */
    function _extract_arguments($vars)
    {
        $config = array();
        
        if(is_array($vars) AND count($vars))
        {
            foreach($vars AS $key => $val)
            {
                /*
                 * If we find that a variable strart with underscore then its saved in the 
                 * config array and deleted from the original array
                 */
                if($key[0] == '_')
                {
                    $config[$key] = $val;
                    unset($vars[$key]);
                }
            }
        }
        else
        {
            //We return an empty array as vars
            $vars = array();
        }
        
        //return an array with config and vars
        return array(
          'config' => $config,
          'vars' => $vars,
        );
    }
    
    /*
     * Parse Tags, using similar syntax to Zen Coding
     * @param string The string containing tag, clases and id in Zen Coding similar syntax
     * @param string Default
     *  @arg tag
     *  @arg class
     *  @arg id
     * 
     */
    function _parse_tags($string, $default = array())
    {
        
        $default_class = ($default['class'] != '' AND is_string($default['class'])) 
                       ? ' ' . $default['class']
                        : '';
        
       /*
         * We first we the tag, then the classes and finally the id
         */
        //The tag are the first letters
        $has_tag = preg_match('/^[a-z]+/i', $string, $tag);
        
        //The class must start with a period, and the space is optional in order to declare two or more classes
        $has_class = preg_match('/\.[a-z\s]+/i', $string, $class);
        
        //The id
        $has_id = preg_match('/#[a-z+]+/i', $string, $id);
        
        $out = array(
            'tag' => ($has_tag == 1) ? $tag[0] : $default['tag'],
            'attr' => array(
                'class' => ($has_class == 1)
                        ? substr($class[0], 1) . $default_class
                        : $default_class,
            )
        );
        
        /*
         * We first look for the id, and then default id
         */
        if($has_id == 1)
        {
            $out['attr']['id'] = substr($id[0], 1);
        }
        elseif($default['id'] != '' AND is_string($default['id']))
        {
            $out['attr']['id'] = $default['id'];
        }
        
        return $out;
    }
    
    function _get_tags($tag, $attr)
    {
        /*
         * We first get the attributes abnd then return open and closing tags
         */
        $attr = $this->_parse_attr($attr);
        
        return array(
            'open' => "<{$tag}{$attr}>",
            'close' => "</{$tag}>",
        );
    }
    
    /*
     * Parse attr
     */
    function _parse_attr($attr)
    {
        $out = '';
        
        foreach($attr AS $key => $val)
        {
            if($val = trim($val))
            {
                $out .= " {$key}=\"{$val}\"";
            }
        }
        
        return $out;
    }
    
    /*
     * HTML Helpers
     * @param Array Attributes that will be parsed
     * @return string html attributes
     */
    function _implode_attr($attr)
    {
        $out = '';
        
        if(is_array($attr))
        {
            foreach($attr as $key => $val)
            {
                $out .= " {$key}=\"{$val}\"";
            }
        }
        
        return $out;
    }
    
    
    /*
     * Theme Varibales, that will be globally available
     */
    public $vars = array();
    /*
     * Add Vars
     * @param array Save an array of vars
     */
    function add_vars($var)
    {
        if(is_array($vars) AND count($vars))
            $this->vars = array_merge($this->vars, $vars);
    }
    
    //////Helpers WP, required
    /*
     * @TODO Customize continue reading
     * @TODO Customize Auto excerpt more
     * @TODO REcent Comments
     */
    function _menu_show_home($args)
    {
        $args['show_home'] = true;
	return $args;
    }
    
    function _excerpt_length($length)
    {
        return $this->config['excerpt_length'];
    }
    
    function _remove_gallery_css($css)
    {
        return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
    }
    
    function _register_sidebars()
    {
        foreach($this->config['sidebars'] as $sb)
        {
            //We merge the defaults in order avoid repeating code
            $params = array_merge($this->config['sidebar_defaults'], $sb);
            
            register_sidebar($params);
        }
    }
    
    private $_custom_post_types = array();
    private $_rewrite_rules = array();
    private $_query_vars;
    
    function _prepare_areas()
    {
       /*
         * Register Areas in the Post Types
         */
       $this->_custom_post_types[$this->prefix . 'areas'] = $this->config['areas_post_type'];
    }
    
    function _prepare_post_types()
    {
        foreach($this->config['post_types'] as $key => $pt)
        {
            //We merge the defaults in order avoid repeating code
            $params = array_merge($this->config['post_types_defaults'], $pt);
            
            if($params['has_archive'] === true AND $params['public'])
            {
                //Instead of rewriting rules manually if we only add the slug to the has archive key
                //we'll have the archive page of the custom post type in the slug we have defined
                $params['has_archive'] = $key;
            }
            
            $this->_custom_post_types[$this->prefix . $key] = $params;
        }
    }
    
    function _register_post_types()
    {
        foreach($this->_custom_post_types AS $key => $args)
        {
            register_post_type($key, $args);
        }
    }
    
//    function _add_query_vars($query_vars)
//    {
//        return $query_vars + $this->_query_vars;
//    }
//    
//    function _add_rewrite_rules($rules)
//    {
//        return $this->_rewrite_rules + $rules;
//    }
    
    
    /*
     * Debug functions
     * @param String Mesaga
     * @param mixed Data that will make the messag more meaningful
     * @threshold int :
     *      0 Error
     *      1 Warning
     *      2 Notice
     */
    function _log($msg, $data = array(), $threshold = 1)
    {
        if(defined('WP_DEBUG'))
        {
            $this->log[$threshold ][] = array(
               'msg' =>  $msg,
               'data' => $data,
            );
        }
        
        return ($threshold > 1) ? true : false;
    }
    
    function show_log($title = 'Ixa theme Debug Info')
    {
        if(defined('WP_DEBUG'))
        {
            preprint($this->log, $title);
        }
    }
}


/* Ending of  .../ixa/framework/core/ixa_theme.php */