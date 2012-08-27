<?php
/**
 *Ixa Modules
 * @TODO Make a cleaner integration with Ixa Theme
 */

class Ixa_Modules {
    
    static $query_defaults;
    static $templates_default;
    
    // Output Mode defines whether the output will be echoe or return as string
    static $output_mode;

    public function __construct()
    {
        // @TODO Allow custom configuration
        self::$query_defaults = array(
            /*
             * Post per page
             */
            'post_per_page' => 12,
            /*
             * Order by menu order
             */
            'orderby' => 'menu_order',
            /*
             * Ascendente para que los primeros sean los primeros
             */
            'order' => 'ASC',
        );
        
        self::$templates_default = array(
            'loop' => array(
                'header' => 'default_loop_header',
                'footer' => 'default_loop_footer',
                'no_posts' => 'default_loop_no_posts',
                'posts' => 'default_loop_posts',
             ),
            'post' => array(
                'header' => 'default_post_header',
                'content' => 'default_post_content',
                'footer' => 'default_post_footer'
            ),
        );
    }
    
    static function set_outputmode($mode)
    {
        self::$output_mode = ($mode == 'echo')
                           ? 'echo' : 'string';
    }


    /*
     * Loop
     * @param array query Son las variables para filtar los posts
     *   @arg booloean|array Query
     *   @arg array  Templates
     *   @arg array  Vars
     */
    static function loop($params = array())
    {
        /*
         * Merge all the templates we want to show
         */
        $templates = (is_array($params))
                   ? array_merge(self::$templates_default['loop'], $params)
                   : self::$templates_default;
        
        
        /*
         * Grab Query object, if we have customm query, cerate new instance f
         * WP_Query, else reference to main $qp_query, this way both syntax are
         * compatible
         */
        if(is_array($params['query']) AND count($params['query']))
        {
            // Execute query, mergin default settings
            $Q =&  new WP_Query( array_merge(self::$query_defaults, $params['query']));
        }
        else
        {
            global $wp_query;
            $Q =& $wp_query;
        }
        
        
        /*
         * New instance of Ixa Theme to load files
         */
        $IXA =& get_ixa_theme();
        $out = "\n";
        
        // Save have posts variable to make available in footer
        $have_posts = $Q->have_posts();
        
        
        /*
         * Load the header of the loop
         */
        $templates['header'] = (! $have_posts and isset($templates['header_empty'])) 
                             ? $templates['header_empty'] 
                             : $templates['footer'];
        $out .= ($templates['header']) ? $IXA->module($templates['header'], $params['vars']) . "\n" : "\n";
        
        /*
         * Load the content of the loop
         */
        if($Q->have_posts())
        {
           $pos = 1;
           
           while ($Q->have_posts())
           {
               $Q->the_post();
               
               $out .= $IXA->module($templates['posts'] . '-' . $pos, $params['vars']) . "\n";
               
               $pos ++;
           }
        }
        else
        {
            $out .= $IXA->module($templates['no_posts']);
        }
        
        // Reset query if we had custom query
        if(is_array($params['query']) AND count($params['query']))
            wp_reset_query();
        
        /*
         * Load the footer
         */
        $templates['footer'] = (! $have_posts and isset($templates['footer_empty'])) 
                             ? $templates['footer_empty'] 
                             : $templates['footer'];
        $out .= ($templates['footer']) ?  $IXA->module($templates['footer'], $params['vars']) . "\n" : "\n";
        
        // Return
        return self::_set_output($out);
    }
    
    static function post($templates = array(), $vars = array())
    {
              /*
         * Merge all the templates we want to show
         */
        $templates = (is_array($templates))
                   ? array_merge(self::$templates_default['post'], $templates)
                   : self::$templates_default;
        
        $IXA =& get_ixa_theme();
        $out = "\n";
        
        
        if(have_posts())
        {
            // Initialize the post
            the_post();
            
            // Load header, content and footer
            $out .= ($template['header']) ? $IXA->module($templates['header'], $vars) . "\n" : "\n";
            $out .= $IXA->module($templates['content'], $vars) . "\n";
            $out .= ($template['footer']) ? $IXA->module($templates['footer'], $vars) . "\n" : "\n";
        }
        
        return self::_set_output($out);
        
    }
    
    static function _set_output(&$out)
    {
        if(self::$output_mode == 'echo')
        {
            echo $out;
            return true;
        }
        else
            return $out;
    }
}

new Ixa_Modules();