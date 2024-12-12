<?php

namespace Utils;

/**
 * Fonction pour remplacer la fonction dépréciée get_page_by_title(), qui renvoie le lien d'une
 * page wordpress depuis son nom.
 *
 * @param $page_title string Titre de la page à chercher
 *
 * @return array|\WP_Post|null Lien de la ou les pages trouvées, null si aucune.
 */
function get_page_by_title_custom($page_title) {
	$args = array(
		'post_type'   => 'page',
		'title'       => $page_title,
		'post_status' => 'publish',
		'posts_per_page' => 1
	);

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) {
		$query->the_post();
		return get_post();
	}

	return null;
}

/**
 * Renvoie un booléen qui correspond à si l'utilisateur actuel possède au moins un des roles
 *
 * @param $roles_array
 *
 * @return bool
 */
function does_user_has_role($roles_array): bool {
	$current_user = wp_get_current_user();
	foreach ($roles_array as $role) {
		if (in_array( $role, $current_user->roles)) {
			return true;
		}
	}
	return false;
}