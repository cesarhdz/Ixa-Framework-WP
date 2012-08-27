<?php
/*
 * Ixa Images
 * @Dependency TimThumb
 */

class Ixa_images {
    
    // Sizes Registered in Ixa FRamework
    //@TODO Make Ixa Image Sizes configurables via admin
    static $sizes = array(
       '2-cols' => array(
            'w' => 116,
        ),
        
        '3-cols' => array(
            'w' => 168,
        ),
        
        '4-cols' => array(
            'w' => 232
        ),
        
        '6-cols' => array(
            'w' => 362
        ),
        
        '8-cols' => array(
            'w' => 490
        ), 
        
        '12-cols' => array(
            'w' => 744
        )
    );
    
    var $max_width = 744;
    
    /*
     * Default paths
     */
    static $tim_folder, $tim_dir, $upload_dir;
    
    function __construct()
    {
        /*
         * Get upload dir
         */
        $upload_dir = wp_upload_dir();
        $upload_dir = $upload_dir['url'];
        
        /*
         * Set default paths
         */
        self::$tim_folder = '/img';
        
        //@TODO Find a cleaner way to add the ixa img url
        self::$tim_dir = get_theme_root_uri() . '/ixa';
        self::$upload_dir = $upload_dir;
        
        add_filter('get_image_tag', array($this, '_tag_filter'), 10, 6);
        add_filter('attachment_fields_to_edit', array($this, '_sizes_options'), 100, 2);
    }
    
    /*
     * URL Prefix
     * Change default upload dir by tim thumb dir
     */
    static function url_prefix($base_url)
    {
        return preg_replace( 
                '#' . preg_quote(self::$upload_dir) . '#', // WP Upload dir
                self::$tim_dir . self::$tim_folder,         // New Location
                $base_url
                );
    }
    
    
    /*
    * Get image data
    */
    static function get($id_or_url, $size)
    {
        /*
        * Tratamos de identificar si es ID o URL
        */
        $url = (preg_match('/^http/', $id_or_url))
                //Si comienza con http, ya no hay que hacer nada
                ? $id_or_url
                // Si es un número diferente a cero, es un ID
                : ((int) $id_or_url) 
                    ? wp_get_attachment_image_src($id_or_url, 'large')
                    : NULL;


        /*
        * Si tenemos un array sólo necesitamos el primer valor
        */
        if(is_array($url))
        {
            $url = $url[0];
            $id = $id_or_url;
        }


        /*
        * Si no hay imagen devolvemos false
        */
        if(!$url) return false;



//        /*
//        * Nos aseguramos que sean arrays, para no generar errores
//        */
//        $defaults = (is_array($defaults)) ? $defaults : array();
//        $attr = (is_array($attr)) ? $attr : array();
//
//        /*
//        * Unimos deafutls con personalizados
//        */
//        $attr = array_merge($defaults, $attr);
//
        /*
         * CHose and filter the size
         */
        //@TODO Create default size
        $attr = (is_array($size)) ? $size : self::$sizes[$size];
        
        /*
        * Filtramos los parámetor y los cambiamos de formato
        * a una cadena $_GET
        */
        $allowed_attr = array('w', 'h', 'q', 'a', 'zc');
        $params = array();
        


        if(is_array($attr) AND count($attr))
        {
            foreach($attr AS $k => $v)
            {
                if(in_array($k, $allowed_attr))
                    $params[] = "{$k}={$v}";
            }
        }

        /*
        * Generamos el parametro get
        */
        $params = (is_array($params) AND count($params))
                    ? '?' . implode('&', $params)
                    : '';

        /*
        * Devolvemos un array de la imagen
        */
        $img = array(
            /*
            * Agregamos el nombre del directorio donde esta el script Clear TimThumb
            */
            'url' => self::url_prefix($url) . $params,
            /*
            * También devolvemos el alt
            */
            'alt' => trim(strip_tags( get_post_meta($id, '_wp_attachment_image_alt', true) )),
        );

        return $img;
    }
    
    /*
     * Shorcut to get post thumbnail
     */
    static function post_thumbnail($post_id, $size)
    {
        return self::get(get_post_thumbnail_id($post_id), $size);
    }
    
    
    /* 
    * Img Tag Filter
    * Registro de etiquetas para imágenes para que funcionene con TimThumb
    */
    function _tag_filter($html, $id, $alt, $title, $align, $size )
    {
        /*
         * Removes width and height in order to make images more flexible
         */
        $patterns = array(
                            '/\s+width="\d+"/i',
                            '/\s+height="\d+"/i',
                            '/alt=""/i'  
        );
        $replacements = array('', '', 'alt="' . $title . '"');
                            
        /*
         * None or full will get full size
         */
        
        if($size == 'full' || ! $size)
        {
            //@TODO Check img widht first
            $width = $this->max_width;
        }
        
        
        if(array_key_exists($size, self::$sizes))
        {
            /*
             * Get preset width and compare to max width allowed
             */
            $width = self::$sizes[$size]['w'];
            $width = ($width > $this->max_width) ? $max_width : $width;
        }
        
        if($width)
        {
           /*
            * Guess SRC
            */
            $img = self::get($id, array('w' => $width));
            
            /*
             * Add patterns replacement for URL
             */
            $patterns[] = '#src="(http|https):([A-Za-z0-9_\./\\-\?]*)"#';
            $replacements[] =   "src=\"{$img['url']}\"";
        }

        //Apply replacement
        return preg_replace($patterns, $replacements, $html);
    }
    
   /*
    * Sizes Options
    * Add sizes to the uploader
    * Based on: http://stackoverflow.com/a/5051999/915034
    */
    function _sizes_options($form_fields, $post) {

        if (!array_key_exists('image-size', $form_fields)) return $form_fields;

        $custom_sizes = '';
        
        foreach(self::$sizes as $size => $properties) 
        {
            if ($size == 'post-thumbnail') continue;

            $label = ucwords(str_replace('-', ' ', $size));
            $cssID = "image-size-{$size}-{$post->ID}";

            $meta = wp_get_attachment_metadata($post->ID);
            $enabled = ($meta['width'] > $properties['w']);

            $html = '<input type="radio" ' . disabled($enabled, false, false) . 'name="attachments[' . $post->ID. '][image-size]" id="' . $cssID . '" value="' . $size .'">';
            $html .= '<label for="'. $cssID . '">' . $label . '</label>';
            if ($enabled) $html .= ' <label for="' . $cssID . '" class="help">(' . $properties['w'] .'px width)</label>';
            $custom_sizes .= '<div class="image-size-item">' . $html . '</div>';
        }
        
        $form_fields['image-size']['html'] = $custom_sizes . $form_fields['image-size']['html'];

        return $form_fields;
    }
}

new Ixa_images();


/* Termina Ixa Images.php */