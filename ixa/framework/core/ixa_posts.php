<?php
/**
 *Ixa Posts
 */

class Ixa_Posts {
    
    static $query_defaults;
    static $templates_default;
    

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
        
        self::$template_defaults = array(
            'loop' => array(
                'header' => 'default_loop_header',
                'header_empty' => 'default_loop_header-empty',
                'footer' => 'default_loop_footer',
                'footer_empty' => 'default_loop_footer-empty',
                'posts' => 'default_loop_posts',
                'posts_empty' => 'default_loop_posts-empty',
             ),
            'single' => array(
            ),
        );
    }
    
    /*
     * Loop
     * @param array query Son las variables para filtar los posts
     *   @arg booloean|array Query
     *   @arg array  Templates
     *   @arg array  Vars
     */
    static function loop($templates = array(), $query = array(), $vars = array())
    {
        /*
         * Merge all the templates we want to show
         */
        $templates = (is_array($templates))
                   ? array_merge(self::$templates_default['loop'], $params['templates'])
                   : self::$templates_default;
        
        
        /*
         * Grab Query object, if we have customm query, cerate new instance f
         * WP_Query, else reference to main $qp_query, this way both syntax are
         * compatible
         */
        if(is_array($query) AND count($query))
        {
            // Execute query, mergin default settings
            $Q =&  new WP_Query( array_merge(self::$query_defaults, $query));
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
        $out = '';
        
        // Save have posts variable to make available in footer
        $have_posts = $Q->have_posts();
        
        
        /*
         * Load the header of the loop
         */
        $header = ($have_posts) ? $templates['header'] : $templates['header_empty'];
        $out .= ($header) ? $IXA->module($header, $vars) : '';
        
        /*
         * Load the content of the loop
         */
        if($Q->have_posts())
        {
           $pos = 1;
           
           while ($Q->have_posts())
           {
               $Q->the_post();
               
               $vars['_pos'] = $pos;
               
               $out .= $IXA->module($templates['posts'], $vars);
           }
        }
        else
        {
            $out .= $IXA->module($templates['no_posts']);
        }
        
        /*
         * Load the footer
         */
        $footer = ($have_posts) ? $templates['footer'] : $templates['footer_empty'];
        $out .= ($footer) ?  $IXA->module($footer, $vars) : '';
        
        
        // Reset query if we had custom query
        if(is_array($query) AND count($query))
            wp_reset_query();
        
        // Return
        $out;

    } 
}

new Ixa_Posts();