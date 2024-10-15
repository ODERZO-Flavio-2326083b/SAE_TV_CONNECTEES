<?php

echo '
</div>
<!-- FOOTER -->
<footer>
	<div id="footer_left">';

if (is_user_logged_in()) {
    if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer gauche')) {
	    the_widget('WidgetWeather');
    }
}

echo '
</div>
<div id="footer_right">';

if (is_user_logged_in()) {
    if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer droite')) {
	    if (get_theme_mod('ecran_connecte_footer_alert', 'show') == 'show') {
		    the_widget('WidgetAlert');
	    }
    }
}

echo '
	</div>
</footer>
<footer class="footer_wp">';
	wp_footer();
echo '</footer>';