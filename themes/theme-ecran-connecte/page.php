<?php get_header(); ?>
    <!-- MAIN -->
    <main>
        <div class="container">
            <section class="content-area content-thin">
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    <article class="article-full">
                        <header>
                            <!-- <h2><?php the_title(); ?></h2> -->
                        </header>
                        <?php the_content(); ?>
                    </article>
                <?php endwhile; else : ?>
                    <article>
                        <p>Sorry, no page was found!</p>
                    </article>
                <?php endif; ?>
            </section>
            <?php //get_sidebar(); ?>
        </div>
    </main>
<?php get_footer(); ?>