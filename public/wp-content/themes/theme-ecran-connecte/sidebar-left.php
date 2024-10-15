<?php

if (is_user_logged_in()) {
    if (get_theme_mod('sidebar_left_display', 'hide') == 'show') {
	    echo '<div class="sidebar" id="sidebar-left">';
	    if (! function_exists('dynamic_sidebar') || ! dynamic_sidebar('Colonne Gauche')) {
		    if (get_theme_mod('sidebar_right_display', 'show') == 'hide') {
			    the_widget('WidgetInformation');
		    }
	    }
        echo '</div>';
    }
}