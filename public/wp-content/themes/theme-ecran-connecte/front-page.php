<?php get_header(); ?>
    <!-- MAIN -->
    <main>
        <?php $current_user = wp_get_current_user();
        if(in_array('television', $current_user->roles)) : ?>
        <div class="row">
            <div class="container order-md-2 text-center text-md-left pr-md-5">
			<!-- <div class="container col-md-7 order-md-2 text-center text-md-left pr-md-5"> -->
       <?php elseif(in_array('tablette', $current_user->roles)): ?>
            <div class="tablet-container">
        <?php else: ?>
            <div class="container">
        <?php endif; ?>
        <?php if(is_plugin_active('plugin-ecran-connecte/amu-ecran-connecte.php')) :
            echo schedule_render_callback();
        endif; ?>
        </div>
        <?php $current_user = wp_get_current_user();
        if(in_array('television', $current_user->roles)) :
            get_sidebar();
        endif;
        if(in_array('television', $current_user->roles)) : ?>
            </div>
        </div>
        <?php else: ?>
            </div>
        <?php endif; ?>
    </main>
<?php get_footer(); ?>
