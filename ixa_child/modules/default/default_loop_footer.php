<?php
    /*
     * Get wp-query to know if we need to load pagination
     */
     global $wp_query;
?>
<div class="loop-footer">
    <?php if (  $wp_query->max_num_pages > 1 ) : ?>
    <nav class="pagination">
            <ul>
                <li class="prev">
                    <?php next_posts_link(__('&larr; Older posts', 'twentyten')); ?>
                </li>
                <li class="next">
                    <?php previous_posts_link(__('Newer posts &rarr;', 'twentyten')); ?>
                </li>
            </ul>
        </nav>
    <?php endif; //Pagination?>
</div>