<?php
/*
 * Includes all the meta information
 */
?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<?php 
    //Stylesheets from styles folder  
    //@VAR Stylesheets
?>

<?php 
//@TODO Use excerpt as Description
//@VAR Description
?>
<meta name="description" content="<?php //@TODO ixa_meta_description() to improve SEO ?>">

<!-- Mobile viewport optimized: h5bp.com/viewport -->
<meta name="viewport" content="width=device-width">

<?php echo $this->part('meta_title'); ?>

<?php //Funcion para cargar los scripts 

    $items = '';
    
    /*
     * Por el momento sólo se aceptan strings, no arrays, por el momento sólo se soportan js y css
     * @TODO Mover a function
     */
    foreach($meta AS $src)
    {
        foreach($src AS $type => $val)
        {
            switch($type)
            {
                case 'css':
                $items .= "<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"{$val}\">\n";
                    break;

                case 'js':
                    $items .= "<script src=\"{$val}\"></script>\n";
                    break;
            }
        }
    }
    
    echo $items;
?>


<!--<link rel="stylesheet" type="text/css" media="all" href="<?php echo $THEME->vars->main_css ?>" />-->

<!-- All JavaScript at the bottom or module, except this Modernizr build. 
    For production create your own custom Modernizr build: www.modernizr.com/download/ -->
<!--<script src="<?php echo get_bloginfo('template_url') ?>/assets/respond/respond.min.js"></script>
<script src="<?php echo get_bloginfo('template_url') ?>/assets/modernizr/modernizr.js"></script>-->

<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

<?php
    /*
     * WP_HEAD must be included to support plugins
     */
    wp_head();
    
    
    /*
     * Agregamos la clase debug, si está activo WP_DEBUG
     */
    $debug = (WP_DEBUG === true) ? 'debug' : '';
    
    /* 
     * Agegamos el slug del post, por si es single, para que haya maor compatibilidad
     * de estilos y no tengamos que estar aplicando ids con ids del post
     */
    if(is_single() || is_page())
    {
        global $post;
        $page_id = $post->post_name;
        
        $categories = get_the_category();
        
       /*
         * Agregamos la categoria como clase
         */
        $bodyclass= '';
        
        if(is_array($categories) AND count($categories))
        {
            foreach($categories AS $cat)
            {
                $bodyclass .= ' ' . $cat->slug;
            }
        }
    }
    
?>
</head>
<body <?php body_class($debug.$bodyclass) ?> id="<?php echo $page_id?>">
    <a id="inicio"></a>