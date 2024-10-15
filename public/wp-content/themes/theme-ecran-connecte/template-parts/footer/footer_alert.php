<?php

$current_user = wp_get_current_user();

echo '
	</div>
	<!-- FOOTER -->
	<footer>';

if (is_user_logged_in() && !in_array("technicien", $current_user->roles)) {
	if (! function_exists('dynamic_sidebar') || ! dynamic_sidebar('Footer gauche')) {
		if (get_theme_mod('ecran_connecte_footer_alert', 'show') == 'show') {
			the_widget( 'WidgetAlert' );
		}
	}

	echo '
	</footer>
	<footer class="footer_wp">';
		wp_footer();
	echo '</footer>';
}