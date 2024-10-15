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
     * Constructeur de la classe pour l'API REST "Profile".
     *
     * Cette méthode initialise les propriétés nécessaires pour définir
     * le namespace et le point de terminaison de base de l'API REST qui
     * permet de gérer les profils utilisateurs.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function __construct() {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'profile';
    }

    /**
     * Enregistre les routes pour l'API REST "Profile".
     *
     * Cette méthode utilise `register_rest_route()` pour définir les routes
     * disponibles dans l'API REST sous le namespace spécifié. Elle enregistre
     * une route accessible en méthode "GET" pour récupérer des informations
     * de profil d'utilisateur, avec une vérification des permissions et un
     * schéma public.
     *
     * @return void Cette méthode n'a pas de valeur de retour.
     *
     * @version 1.0
     * @date 2024-10-15
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
     * Récupère les informations de l'utilisateur courant via l'API REST.
     *
     * Cette méthode récupère les informations du profil de l'utilisateur actuellement
     * connecté en interrogeant la base de données. Elle renvoie les données de l'utilisateur,
     * telles que l'ID, le login, l'email, le rôle et les codes associés. Si l'utilisateur
     * n'est pas trouvé, une réponse HTTP 404 avec un message d'erreur est retournée.
     *
     * @param WP_REST_Request $request Requête envoyée à l'API REST.
     *
     * @return WP_REST_Response Réponse REST contenant les données utilisateur ou un message d'erreur.
     *
     * @example
     * // Appel de la méthode via une requête REST :
     * $response = $this->get_item($request);
     *
     * @version 1.0
     * @date 2024-10-15
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
     * Vérifie les permissions de l'utilisateur pour récupérer ses informations via l'API REST.
     *
     * Cette méthode s'assure que l'utilisateur est actuellement connecté avant de permettre
     * l'accès à ses données. Si l'utilisateur est connecté, l'accès est accordé, sinon,
     * la requête est refusée.
     *
     * @param WP_REST_Request $request Requête envoyée à l'API REST.
     *
     * @return bool Renvoie true si l'utilisateur est connecté, false sinon.
     *
     * @example
     * // Vérification des permissions avant d'accéder à l'API :
     * $has_permission = $this->get_item_permissions_check($request);
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function get_item_permissions_check($request) {
        $current_user = wp_get_current_user();
        return !is_null($current_user);
    }
}
