<?php 
/*
 * The Menu Walker
 */
/**
 * Create a nav menu with very basic markup.
 *
 * @author Thomas Scholz http://toscho.de
 * @version 1.0
 */
class The_Menu_Walker extends Walker_Nav_Menu
{
	/**
	 * Start the element output.
	 *
	 * @param  string $output Passed by reference. Used to append additional content.
	 * @param  object $item   Menu item data object.
	 * @param  int $depth     Depth of menu item. May be used for padding.
	 * @param  array $args    Additional strings.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth, $args )
	{
                /*
                     * Adds post_name as class 
                     *  1. The title as class, to support portability, uses title, because post_title is not consitent
                     *  2. If has children (not currently supported)
                     *  3. Current regardless the level
                     */
                $classes = array();
                $classes[] = sanitize_html_class(sanitize_title($item->title));
                $classes[] = ($args->has_childen) ? 'parent' : '';
                $classes[] = ($item->current OR $item->current_item_ancestor OR $item->current_item_parent)
                           ? 'current' : '';
                
                $output     .= '<li class="' . trim(implode(' ', $classes)). '">';
                
		$attributes  = '';
                
                /*
                     * Adds description if available, wrapped in small tags, because its an alternate way of calling the item
                     */
                $description = ($item->description != '') ? "<p class=\"description\"><small>{$item->description}</small></p>"
                                                           : '';
		! empty ( $item->attr_title )
			// Avoid redundant titles
			and $item->attr_title !== $item->title
			and $attributes .= ' title="' . esc_attr( $item->attr_title ) .'"';

		! empty ( $item->url )
			and $attributes .= ' href="' . esc_attr( $item->url ) .'"';

		$attributes  = trim( $attributes );
		$title       = apply_filters( 'the_title', $item->title, $item->ID );
		$item_output = "{$args->before}<a {$attributes}>{$args->link_before}{$title}</a>{$description}"
						. "$args->link_after$args->after";

		// Since $output is called by reference we don't need to return anything.
		$output .= apply_filters(
			'walker_nav_menu_start_el'
			,   $item_output
			,   $item
			,   $depth
			,   $args
		);
	}
        
        /*
         * This function allow us to get the has children key in the args arras
         */
        public function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output )
        {
            $id_field = $this->db_fields['id'];
            if ( is_object( $args[0] ) ) {
                $args[0]->has_children = ! empty( $children_elements[$element->$id_field] );
            }
            return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
        }

	/**
	 * @see Walker::start_lvl()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @return void
	 */
	public function start_lvl( &$output )
	{
		$output .= '<ul class="sub-menu">';
	}

	/**
	 * @see Walker::end_lvl()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @return void
	 */
	public function end_lvl( &$output )
	{
		$output .= '</ul>';
	}

	/**
	 * @see Walker::end_el()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @return void
	 */
	function end_el( &$output )
	{
		$output .= '</li>';
	}
}