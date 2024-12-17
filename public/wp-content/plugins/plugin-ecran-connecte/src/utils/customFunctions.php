<?php

namespace Utils;

/**
 * Fonction customisée qui recherche une page à partir de son nom, pour remplacer
 * la fonction dépréciée get_page_by_title().
 *
 * @param string $page_title Nom de la page à chercher
 *
 * @return WP_Post|array|null La page trouvée, ou l'array de pages trouvées. null si aucune page trouvée.
 */
function get_page_by_title_custom( string $page_title): WP_Post|array|null {
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
 * @param array $roles_array Liste de roles à vérifier
 *
 * @return bool
 */
function does_user_has_role( array $roles_array): bool {
	$current_user = wp_get_current_user();
	foreach ($roles_array as $role) {
		if (in_array( $role, $current_user->roles)) {
			return true;
		}
	}
	return false;
}