<?php if(get_theme_mod('sidebar_right_display', 'show') == 'show') : ?>
    <!-- SIDEBAR -->
    <aside id="sidebar" class="col-md-4 order-md-3 text-center sidebar" role="complementary">
        <?php if (! function_exists('dynamic_sidebar') || ! dynamic_sidebar('Colonne Droite')) :
            the_widget('WidgetInformation');
        endif; ?>
    </aside>
<?php endif; ?>

<?php if(get_theme_mod('sidebar_left_display', 'hide') == 'show') : ?>
    <!-- SIDEBAR -->
    <aside id="sidebar" class="col-md-4 order-md-1 text-center sidebar" role="complementary">
        <?php if (! function_exists('dynamic_sidebar') || ! dynamic_sidebar('Colonne Gauche')) :
            the_widget('WidgetInformation');
        endif; ?>
    </aside>
<?php endif; ?>
