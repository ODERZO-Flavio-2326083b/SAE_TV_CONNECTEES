<?php

namespace Controllers;

use Models\Information;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Classe InformationRestController
 *
 * Cette classe gère les opérations REST pour les informations.
 * Elle permet de créer, lire, mettre à jour et supprimer des informations
 * à travers des requêtes REST.
 */
class InformationRestController extends WP_REST_Controller
{
    /**
     * Constructeur du contrôleur REST
     *
     * Initialise l'espace de noms et la base REST.
     */
    public function __construct() {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'information';
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
                    'callback' => array($this, 'get_items'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                    'args' => $this->get_collection_params(),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array($this, 'create_item'),
                    'permission_callback' => array($this, 'create_item_permissions_check'),
                    'args' => array(
                        'title' => array(
                            'type' => 'string',
                            'required' => true,
                            'description' => __('Titre de l\'information'),
                        ),
                        'content' => array(
                            'type' => 'string',
                            'required' => true,
                            'description' => __('Contenu de l\'information'),
                        ),
                        'expiration-date' => array(
                            'type' => 'string',
                            'required' => true,
                            'description' => __('Date d\'expiration de l\'information'),
                        ),
                    ),
                ),
                'schema' => array($this, 'get_public_item_schema'),
            )
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>[\d]+)',
            array(
                'args' => array(
                    'id' => array(
                        'description' => __('Identifiant unique pour l\'information'),
                        'type' => 'integer',
                    ),
                ),
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_item'),
                    'permission_callback' => array($this, 'get_item_permissions_check'),
                    'args' => null,
                ),
                array(
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => array($this, 'update_item'),
                    'permission_callback' => array($this, 'update_item_permissions_check'),
                    'args' => array(
                        'title' => array(
                            'type' => 'string',
                            'description' => __('Titre de l\'information'),
                        ),
                        'content' => array(
                            'type' => 'string',
                            'description' => __('Contenu de l\'information'),
                        ),
                        'expiration-date' => array(
                            'type' => 'string',
                            'description' => __('Date d\'expiration de l\'information'),
                        ),
                    ),
                ),
                array(
                    'methods' => WP_REST_Server::DELETABLE,
                    'callback' => array($this, 'delete_item'),
                    'permission_callback' => array($this, 'delete_item_permissions_check'),
                    'args' => array()
                ),
                'schema' => array($this, 'get_public_item_schema'),
            )
        );
    }

    /**
     * Récupère une collection d'items
     *
     * @param WP_REST_Request $request Données complètes sur la requête.
     * @return WP_Error|WP_REST_Response
     *
     * Exemple d'utilisation :
     * GET /amu-ecran-connectee/v1/information?offset=0&limit=10
     */
    public function get_items($request) {
        // Obtient une instance du gestionnaire d'information
        $information = new Information();

        // Essaie d'obtenir les paramètres d'offset et de limite
        $offset = $request->get_param('offset');
        $limit = $request->get_param('limit');

        return new WP_REST_Response($information->getList($offset, $limit), 200);
    }

    /**
     * Crée une seule information.
     *
     * @param WP_REST_Request $request Détails complets sur la requête.
     * @return WP_REST_Response|WP_Error Objet de réponse en cas de succès, ou objet WP_Error en cas d'échec.
     *
     * Exemple d'utilisation :
     * POST /amu-ecran-connectee/v1/information
     * {
     *   "title": "Titre de l'information",
     *   "content": "Contenu de l'information",
     *   "expiration-date": "2024-12-31"
     * }
     */
    public function create_item($request) {
        // Obtient une instance du gestionnaire d'information
        $information = new Information();

        // Définit les données de l'information
        $information->setTitle($request->get_param('title'));
        $information->setAuthor(wp_get_current_user()->ID);
        $information->setCreationDate(date('Y-m-d'));
        $information->setExpirationDate($request->get_param('expiration-date'));
        $information->setAdminId(null);
        $information->setContent($request->get_param('content'));
        $information->setType('text');

        // Essaie d'insérer l'information
        if (($insert_id = $information->insert())) {
            return new WP_REST_Response(array('id' => $insert_id), 200);
        }

        return new WP_REST_Response(array('message' => 'Impossible d\'insérer l\'information'), 400);
    }

    /**
     * Récupère une seule information.
     *
     * @param WP_REST_Request $request Détails complets sur la requête.
     * @return WP_REST_Response|WP_Error Objet de réponse en cas de succès, ou objet WP_Error en cas d'échec.
     *
     * Exemple d'utilisation :
     * GET /amu-ecran-connectee/v1/information/1
     */
    public function get_item($request) {
        // Obtient une instance du gestionnaire d'information
        $information = new Information();

        // Récupère l'information dans la base de données
        $requested_info = $information->get($request->get_param('id'));
        if (!$requested_info) {
            return new WP_REST_Response(array('message' => 'Information non trouvée'), 404);
        }

        return new WP_REST_Response($requested_info, 200);
    }

    /**
     * Met à jour une seule information.
     *
     * @param WP_REST_Request $request Détails complets sur la requête.
     * @return WP_REST_Response|WP_Error Objet de réponse en cas de succès, ou objet WP_Error en cas d'échec.
     *
     * Exemple d'utilisation :
     * PUT /amu-ecran-connectee/v1/information/1
     * {
     *   "title": "Nouveau titre",
     *   "content": "Nouveau contenu",
     *   "expiration-date": "2025-01-01"
     * }
     */
    public function update_item($request) {
        // Obtient une instance du gestionnaire d'information
        $information = new Information();

        // Récupère l'information dans la base de données
        $requested_info = $information->get($request->get_param('id'));
        if (!$requested_info) {
            return new WP_REST_Response(array('message' => 'Information non trouvée'), 404);
        }

        // Met à jour les données de l'information
        if (is_string($request->get_json_params()['title'])) {
            $requested_info->setTitle($request->get_json_params()['title']);
        }

        if (is_string($request->get_json_params()['content'])) {
            $requested_info->setContent($request->get_json_params()['content']);
        }

        if (is_string($request->get_json_params()['expiration-date'])) {
            $requested_info->setExpirationDate($request->get_json_params()['expiration-date']);
        }

        // Essaie de mettre à jour l'information
        if ($requested_info->update() > 0) {
            return new WP_REST_Response(null, 200);
        }

        return new WP_REST_Response(array('message' => 'Impossible de mettre à jour l\'information'), 400);
    }

    /**
     * Supprime une seule information.
     *
     * @param WP_REST_Request $request Détails complets sur la requête.
     * @return WP_REST_Response|WP_Error Objet de réponse en cas de succès, ou objet WP_Error en cas d'échec.
     *
     * Exemple d'utilisation :
     * DELETE /amu-ecran-connectee/v1/information/1
     */
    public function delete_item($request) {
        // Obtient une instance du gestionnaire d'information
        $information = new Information();

        // Récupère l'information dans la base de données
        $requested_info = $information->get($request->get_param('id'));
        if ($requested_info && $requested_info->delete()) {
            return new WP_REST_Response(null, 200);
        }

        return new WP_REST_Response(array('message' => 'Impossible de supprimer l\'information'), 400);
    }

    /**
     * Vérifie si une requête donnée a accès à la récupération des items
     *
     * @param WP_REST_Request $request Données complètes sur la requête.
     * @return WP_Error|bool
     */
    public function get_items_permissions_check($request) {
        $current_user = wp_get_current_user();
        return in_array("administrator", $current_user->roles);
    }

    /**
     * Vérifie si une requête donnée a accès à la création d'une information.
     *
     * @param WP_REST_Request $request Détails complets sur la requête.
     * @return true|WP_Error Vrai si la requête a accès pour créer des items, sinon un objet WP_Error.
     */
    public function create_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une requête donnée a accès à la lecture d'une information.
     *
     * @param WP_REST_Request $request Détails complets sur la requête.
     * @return true|WP_Error Vrai si la requête a accès à lire l'item, sinon un objet WP_Error.
     */
    public function get_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une requête donnée a accès à mettre à jour une seule information.
     *
     * @param WP_REST_Request $request Détails complets sur la requête.
     * @return true|WP_Error Vrai si la requête a accès à mettre à jour l'item, sinon un objet WP_Error.
     */
    public function update_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une requête donnée a accès à supprimer une information.
     *
     * @param WP_REST_Request $request Détails complets sur la requête.
     * @return true|WP_Error Vrai si la requête a accès à supprimer l'item, sinon un objet WP_Error.
     */
    public function delete_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Récupère les paramètres de requête pour les collections.
     *
     * @return array Paramètres de collection.
     */
    public function get_collection_params() {
        $query_params = [];

        $query_params['limit'] = array(
            'description' => __('Nombre maximum d\'informations à récupérer'),
            'type' => 'integer',
            'default' => 25,
        );

        $query_params['offset'] = array(
            'description' => __('Offset des informations à récupérer'),
            'type' => 'integer',
            'default' => 0,
        );

        return apply_filters('rest_user_collection_params', $query_params);
    }
}
