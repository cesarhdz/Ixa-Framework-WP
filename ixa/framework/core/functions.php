<?php
/*
 * Core functions for Themeplate
 */

/*
 * Themeplate title
 * Based on wp_title
 */
function themeplate_title()
{
    global $wpdb, $wp_locale;

    $m = get_query_var('m');
    $year = get_query_var('year');
    $monthnum = get_query_var('monthnum');
    $day = get_query_var('day');
    $search = get_query_var('s');
    $title = '';

    // Seem wordpress forgot home page
    if(is_front_page())
    {
        if(is_home())
        {
            $title = get_bloginfo('blogname'); //@TODO Make Variable
        }
        elseif(is_page())
        {
            $title = single_post_title('', false);
        }
    }

    // If there is a post
    if ( is_single() || ( is_home() && !is_front_page() ) || ( is_page() && !is_front_page() ) ) {
            $title = single_post_title( '', false );
    }

    // If there's a category or tag
    if ( is_category() || is_tag() ) {
            $title = single_term_title( '', false );
    }

    // If there's a taxonomy
    if ( is_tax() ) {
            $term = get_queried_object();
            $tax = get_taxonomy( $term->taxonomy );
            $title = single_term_title( $tax->labels->name . $t_sep, false );
    }

    // If there's an author
    if ( is_author() ) {
            $author = get_queried_object();
            $title = $author->display_name;
    }

    // If there's a post type archive
    if ( is_post_type_archive() )
            $title = post_type_archive_title( '', false );

    // If there's a month
    if ( is_archive() && !empty($m) ) {
            $my_year = substr($m, 0, 4);
            $my_month = $wp_locale->get_month(substr($m, 4, 2));
            $my_day = intval(substr($m, 6, 2));
            $title = $my_year . ( $my_month ? $t_sep . $my_month : '' ) . ( $my_day ? $t_sep . $my_day : '' );
    }

    // If there's a year
    if ( is_archive() && !empty($year) ) {
            $title = $year;
            if ( !empty($monthnum) )
                    $title .= $t_sep . $wp_locale->get_month($monthnum);
            if ( !empty($day) )
                    $title .= $t_sep . zeroise($day, 2);
    }

    // If it's a search
    if ( is_search() ) {
            /* translators: 1: separator, 2: search phrase */
            $title = sprintf(__('Search Results %1$s %2$s'), $t_sep, strip_tags($search));
    }

    // If it's a 404 page
    if ( is_404() ) {
            $title = __('Page not found');
    }
    
    return (object) array('title' => ucfirst($title));
}

/*
 * Return the real excerpt as it is stored in the database
 */
function themeplate_real_excerpt()
{
    global $post;
    
    return $post->post_excerpt;
}

/*
 * Meta Description
 */
function themeplate_meta_description()
{
    global $post;

    //if single post then add excerpt as meta description
    if (is_single())
        return strip_tags(themeplate_real_excerpt());
}

/*
 * HTMl Functions
 */
/*
 * Themeplate Doctype
 * @return html5 doctype
 */
function themeplate_doctype()
{
    include_once(THEMEPLATE_ROOT . 'themeplate/includes/doctype_html5.php');
}

/*
 * Wrap in tags
 * Themepalte Wrap
 * @param string String to be wrapped
 * @param string Tag that will be the wrapper
 * @param boolean display if no is string is given
 * @return echo the HTML string if passes the function
 */
function themeplate_wrap($string, $tag = 'p', $display_empty = false)
{
    if(is_string($string))
    {
        $string = trim($string);
        
        //Avoid displaying empty tags
        if($string == '' AND $display_empty === false)
            return false;
            
        //We set default tag if no one is returned
        $tag = _themeplate_get_tag($tag);

        //We wrap it
        echo $tag[0].$string.$tag[1];
            
    }
}

/*
 * Get tag
 * @TODO improve so the function supports id's
 * @param string tag Tag
 * @return array
 *      @arg string index 0: open tag
 *      @arg string index 1 close tag
 */
function _themeplate_get_tag($tag)
{
    if(is_string($tag) AND $tag != '')
    {
        if(strpos($tag, '.'))
        {
            $base = explode('.', $tag);
            
            
            $open_tag = $base[0] . ' class="' . $base[1] . '"';
            $close_tag = $base[0];
        }
        else
        {
            $open_tag = $tag;
            $close_tag = $tag;
        }
        
        return array("<{$open_tag}>", "</{$close_tag}>");
    }
    
    //If the tag isn´t a string we default to paragraph tag
    return array("<p>", "</p>");
}