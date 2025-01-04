<?php

namespace utils;

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