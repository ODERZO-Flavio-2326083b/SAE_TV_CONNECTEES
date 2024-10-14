<?php

namespace Controllers;

use Models\Alert;
use Models\CodeAde;
use Models\User;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * @class ProfileRestController
 * @brief Classe pour gérer le contrôleur REST des profils utilisateur.
 *
 * Cette classe permet de récupérer les informations du profil de l'utilisateur actuellement connecté.
 */
class ProfileRestController extends WP_REST_Controller
{
    /**
     * Constructor for the REST controller
     *
     * Initialise le contrôleur avec le namespace et la base REST.
     */
    public function __construct() {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'profile';
    }

    /**
     * Register the routes for the objects of the controller.
     *
     * Enregistre les routes pour accéder aux méthodes du contrôleur REST.
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            array(
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_item'),
                    'permission_callback' => array($this, 'get_item_permissions_check'),
                    'args' => null,
                ),
                'schema' => array($this, 'get_public_item_schema'),
            )
        );
    }

    /**
     * Retrieves the currently logged in user.
     *
     * Récupère les informations de l'utilisateur actuellement connecté.
     *
     * @param WP_REST_Request $request Données complètes sur la requête.
     * @return WP_REST_Response|WP_Error Objet de réponse en cas de succès, ou objet WP_Error en cas d'échec.
     */
    public function get_item($request) {
        // Get an instance of the user manager
        $user = new User();
        $current_user = wp_get_current_user();

        // Grab the information from the database
        $requested_user = $user->get($current_user->ID);
        if (!$requested_user)
            return new WP_REST_Response(array('message' => 'User not found'), 404);

        $user_data = array(
            'id' => $requested_user->getId(),
            'name' => $requested_user->getLogin(),
            'email' => $requested_user->getEmail(),
            'role' => $requested_user->getRole(),
            'codes' => $requested_user->getCodes()
        );

        return new WP_REST_Response($user_data, 200);
    }

    /**
     * Checks if a given request has access to read an information.
     *
     * Vérifie si la requête actuelle a les permissions pour lire les informations de l'utilisateur.
     *
     * @param WP_REST_Request $request Données complètes sur la requête.
     * @return true|WP_Error Vrai si la requête a accès à lire l'item, objet WP_Error sinon.
     */
    public function get_item_permissions_check($request) {
        $current_user = wp_get_current_user();
        return !is_null($current_user);
    }
}
