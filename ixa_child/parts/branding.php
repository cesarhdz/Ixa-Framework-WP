<?php
// Branding header para todo el sitio
?>


<div class="block branding_container">
    <header class="branding">
        <figure class="logo">
            <img src="#" alt="Here Goes the Logo" title="Here Goes the Logo" />
        </figure>
        <hgroup>
            <h1>
                <a href="<?php echo home_url( '/' ); ?>" rel="home"  title="<?php bloginfo( 'name' ); ?> | Inicio">
                    <?php bloginfo( 'name' ); ?>
                </a>
            </h1>
        </hgroup>
    </header>
</div>
