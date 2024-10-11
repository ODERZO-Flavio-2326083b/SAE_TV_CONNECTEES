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
 * Classe ProfileRestController
 *
 * Cette classe gère les opérations REST pour le profil de l'utilisateur.
 * Elle permet de récupérer les informations sur l'utilisateur actuellement connecté.
 */
class ProfileRestController extends WP_REST_Controller
{
    /**
     * Constructeur du contrôleur REST
     *
     * Initialise l'espace de noms et la base REST.
     */
    public function __construct() {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'profile';
    }

    /**
     * Enregistre les routes pour les objets du contrôleur.
     *
     * Cette méthode définit les routes disponibles pour l'API REST.
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
     * Récupère l'utilisateur actuellement connecté.
     *
     * @param WP_REST_Request $request Détails complets sur la requête.
     * @return WP_REST_Response|WP_Error Objet de réponse en cas de succès, ou objet WP_Error en cas d'échec.
     *
     * Exemple d'utilisation :
     * GET /amu-ecran-connectee/v1/profile
     */
    public function get_item($request) {
        // Obtient une instance du gestionnaire d'utilisateur
        $user = new User();
        $current_user = wp_get_current_user();

        // Récupère les informations de l'utilisateur dans la base de données
        $requested_user = $user->get($current_user->ID);
        if (!$requested_user) {
            return new WP_REST_Response(array('message' => 'User not found'), 404);
        }

        // Prépare les données utilisateur à retourner
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
     * Vérifie si une requête donnée a accès à lire les informations.
     *
     * @param WP_REST_Request $request Détails complets sur la requête.
     * @return true|WP_Error Vrai si la requête a accès pour lire l'item, sinon un objet WP_Error.
     */
    public function get_item_permissions_check($request) {
        $current_user = wp_get_current_user();
        return !is_null($current_user);
    }
}
