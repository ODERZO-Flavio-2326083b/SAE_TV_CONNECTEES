<?php

get_header();

echo '
<main id="content">
    <section>';

if (have_posts()) {
    while (have_posts()) : the_post(); ?>
        <article class="post" id="post-<?php the_ID(); ?>">
            <h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
            <div class="post_content"><?php the_excerpt(); ?></div>
        </article>
    <?php endwhile;
    } else {
        echo '<h2 class="center">Aucun article trouvé. Essayer une autre recherche ?</h2>';
        include(TEMPLATEPATH . '/searchform.php');
}

echo '
    </section>
</main>';
get_footer();

echo '
    </body>
</html>';