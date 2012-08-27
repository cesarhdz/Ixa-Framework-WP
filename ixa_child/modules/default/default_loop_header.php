<?php
    /*
     * Grab index title because ther is not a built-in function
     * @TODO Load index title automatically in Ixa_module
     */
    $title = index_title();
?>

<header class="loop-header">
    <hgroup>
        <h2><?php echo $title->type ?></h2>
        <h1><?php echo $title->title ?></h1>
    </hgroup>
</header>