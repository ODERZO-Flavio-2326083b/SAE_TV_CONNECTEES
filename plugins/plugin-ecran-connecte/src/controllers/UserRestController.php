<?php

namespace Controllers;

use Models\Alert;
use Models\CodeAde;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class UserRestController extends WP_REST_Controller
{
    /**
     * Constructeur du contrôleur REST.
     */
    public function __construct() {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'user';
    }

    /**
     * Enregistre les routes pour les objets du contrôleur.
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this, 'get_items'],
                    'permission_callback' => [$this, 'get_items_permissions_check'],
                    'args' => [],
                ],
                [
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => [$this, 'create_item'],
                    'permission_callback' => [$this, 'create_item_permissions_check'],
                    'args' => [
                        'content' => [
                            'type' => 'string',
                            'required' => true,
                            'description' => __('Alert content'),
                        ],
                        'expiration-date' => [
                            'type' => 'string',
                            'required' => true,
                            'description' => __('Alert expiration date'),
                        ],
                        'codes' => [
                            'type' => 'array',
                            'required' => true,
                            'items' => ['type' => 'string'],
                            'description' => __('ADE codes'),
                        ],
                    ],
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>[\d]+)',
            [
                'args' => [
                    'id' => [
                        'description' => __('Unique identifier for the alert'),
                        'type' => 'integer',
                    ],
                ],
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this, 'get_item'],
                    'permission_callback' => [$this, 'get_item_permissions_check'],
                    'args' => null,
                ],
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this, 'update_item'],
                    'permission_callback' => [$this, 'update_item_permissions_check'],
                    'args' => [
                        'content' => [
                            'type' => 'string',
                            'description' => __('Alert content'),
                        ],
                        'expiration-date' => [
                            'type' => 'string',
                            'description' => __('Alert expiration date'),
                        ],
                        'codes' => [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                            'description' => __('ADE codes'),
                        ],
                    ],
                ],
                [
                    'methods' => WP_REST_Server::DELETABLE,
                    'callback' => [$this, 'delete_item'],
                    'permission_callback' => [$this, 'delete_item_permissions_check'],
                    'args' => [],
                ],
                'schema' => [$this, 'get_public_item_schema'],
            ]
        );
    }

    /**
     * Récupère une collection d'alertes.
     *
     * @param WP_REST_Request $request Données complètes concernant la requête.
     * @return WP_Error|WP_REST_Response
     */
    public function get_items($request) {
        // Obtenir une instance du gestionnaire d'alertes
        $alert = new Alert();

        return new WP_REST_Response($alert->getList(), 200);
    }

    /**
     * Crée une seule alerte.
     *
     * @param WP_REST_Request $request Détails complets concernant la requête.
     * @return WP_REST_Response|WP_Error Objet de réponse en cas de succès, ou objet WP_Error en cas d'échec.
     */
    public function create_item($request) {
        // Obtenir une instance du gestionnaire d'alertes
        $alert = new Alert();

        // Définir les données de l'alerte
        $alert->setAuthor(wp_get_current_user()->ID);
        $alert->setContent($request->get_param('content'));
        $alert->setCreationDate(date('Y-m-d'));
        $alert->setExpirationDate($request->get_param('expiration-date'));

        // Définir les codes ADE pour l'alerte
        $ade_codes = $this->find_ade_codes($alert, $request->get_json_params()['codes']);

        if (is_null($ade_codes)) {
            return new WP_REST_Response(['message' => 'An invalid code was specified'], 400);
        }

        $alert->setCodes($ade_codes);

        // Essayer d'insérer l'alerte
        if (($insert_id = $alert->insert())) {
            return new WP_REST_Response(['id' => $insert_id], 200);
        }

        return new WP_REST_Response(['message' => 'Could not insert the alert'], 400);
    }

    /**
     * Récupère une seule alerte.
     *
     * @param WP_REST_Request $request Détails complets concernant la requête.
     * @return WP_REST_Response|WP_Error Objet de réponse en cas de succès, ou objet WP_Error en cas d'échec.
     */
    public function get_item($request) {
        // Obtenir une instance du gestionnaire d'alertes
        $alert = new Alert();

        // Récupérer les informations depuis la base de données
        $requested_alert = $alert->get($request->get_param('id'));
        if (!$requested_alert) {
            return new WP_REST_Response(['message' => 'Alert not found'], 404);
        }

        return new WP_REST_Response($requested_alert, 200);
    }

    /**
     * Met à jour une seule alerte.
     *
     * @param WP_REST_Request $request Détails complets concernant la requête.
     * @return WP_REST_Response|WP_Error Objet de réponse en cas de succès, ou objet WP_Error en cas d'échec.
     */
    public function update_item($request) {
        // Obtenir une instance du gestionnaire d'alertes
        $alert = new Alert();

        // Récupérer les informations depuis la base de données
        $requested_alert = $alert->get($request->get_param('id'));
        if (is_null($requested_alert->getId())) {
            return new WP_REST_Response(['message' => 'Alert not found'], 404);
        }

        // Mettre à jour les données de l'alerte
        if (is_string($request->get_json_params()['content'])) {
            $requested_alert->setContent($request->get_json_params()['content']);
        }

        if (is_string($request->get_json_params()['expiration-date'])) {
            $requested_alert->setExpirationDate($request->get_json_params()['expiration-date']);
        }

        if (is_array($request->get_json_params()['codes'])) {
            $ade_codes = $this->find_ade_codes($requested_alert, $request->get_json_params()['codes']);

            if (is_null($ade_codes)) {
                return new WP_REST_Response(['message' => 'An invalid code was specified'], 400);
            }

            $requested_alert->setCodes($ade_codes);
        }

        // Essayer de mettre à jour les informations
        if ($requested_alert->update() > 0) {
            return new WP_REST_Response(null, 200);
        }

        return new WP_REST_Response(['message' => 'Could not update the alert'], 400);
    }

    /**
     * Supprime une seule alerte.
     *
     * @param WP_REST_Request $request Détails complets concernant la requête.
     * @return WP_REST_Response|WP_Error Objet de réponse en cas de succès, ou objet WP_Error en cas d'échec.
     */
    public function delete_item($request) {
        // Obtenir une instance du gestionnaire d'alertes
        $alert = new Alert();

        // Récupérer les informations depuis la base de données
        $requested_alert = $alert->get($request->get_param('id'));
        if ($requested_alert && $requested_alert->delete()) {
            return new WP_REST_Response(null, 200);
        }

        return new WP_REST_Response(['message' => 'Could not delete the alert'], 400);
    }

    /**
     * Vérifie si une requête donnée a accès pour récupérer des éléments.
     *
     * @param WP_REST_Request $request Données complètes concernant la requête.
     * @return WP_Error|bool
     */
    public function get_items_permissions_check($request) {
        $current_user = wp_get_current_user();
        return in_array("administrator", $current_user->roles);
    }

    /**
     * Vérifie si une requête donnée a accès pour créer une alerte.
     *
     * @param WP_REST_Request $request Détails complets concernant la requête.
     * @return true|WP_Error Vrai si la requête a accès pour créer des éléments, objet WP_Error sinon.
     */
    public function create_item_permissions_check($request) {
        return current_user_can('edit_posts');
    }

    /**
     * Vérifie si une requête donnée a accès pour récupérer une alerte spécifique.
     *
     * @param WP_REST_Request $request Détails complets concernant la requête.
     * @return true|WP_Error Vrai si la requête a accès pour récupérer l'alerte, objet WP_Error sinon.
     */
    public function get_item_permissions_check($request) {
        return current_user_can('edit_posts');
    }

    /**
     * Vérifie si une requête donnée a accès pour mettre à jour une alerte.
     *
     * @param WP_REST_Request $request Détails complets concernant la requête.
     * @return true|WP_Error Vrai si la requête a accès pour mettre à jour l'alerte, objet WP_Error sinon.
     */
    public function update_item_permissions_check($request) {
        return current_user_can('edit_posts');
    }

    /**
     * Vérifie si une requête donnée a accès pour supprimer une alerte.
     *
     * @param WP_REST_Request $request Détails complets concernant la requête.
     * @return true|WP_Error Vrai si la requête a accès pour supprimer l'alerte, objet WP_Error sinon.
     */
    public function delete_item_permissions_check($request) {
        return current_user_can('edit_posts');
    }

    /**
     * Récupère le schéma public pour les alertes.
     *
     * @return array Le schéma public de l'alerte.
     */
    public function get_public_item_schema() {
        return [
            '$schema' => 'http://json-schema.org/draft-07/schema#',
            'title' => 'alert',
            'type' => 'object',
            'properties' => [
                'id' => [
                    'type' => 'integer',
                    'description' => __('ID of the alert'),
                ],
                'author' => [
                    'type' => 'integer',
                    'description' => __('Author ID of the alert'),
                ],
                'content' => [
                    'type' => 'string',
                    'description' => __('Content of the alert'),
                ],
                'creation_date' => [
                    'type' => 'string',
                    'description' => __('Creation date of the alert'),
                ],
                'expiration_date' => [
                    'type' => 'string',
                    'description' => __('Expiration date of the alert'),
                ],
                'codes' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                    'description' => __('List of ADE codes associated with the alert'),
                ],
            ],
            'required' => ['content', 'creation_date', 'expiration_date'],
        ];
    }
}
