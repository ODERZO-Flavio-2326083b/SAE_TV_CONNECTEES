<?php

namespace Controllers;

use Models\CodeAde;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Classe CodeAdeRestController
 *
 * Gère les routes REST pour les codes ADE.
 */
class CodeAdeRestController extends WP_REST_Controller
{
    /**
     * Constructeur du contrôleur REST.
     * Initialise le namespace et la base des routes.
     */
    public function __construct() {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'ade';
    }

    /**
     * Enregistre les routes pour les objets du contrôleur.
     *
     * Cette méthode définit les différentes routes REST pour récupérer,
     * créer, mettre à jour et supprimer des codes ADE.
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
                    'args' => array(),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array($this, 'create_item'),
                    'permission_callback' => array($this, 'create_item_permissions_check'),
                    'args' => array(
                        'title' => array(
                            'type' => 'string',
                            'required' => true,
                            'description' => __('ADE code title'),
                        ),
                        'code' => array(
                            'type' => 'number',
                            'required' => true,
                            'description' => __('ADE code'),
                        ),
                        'type' => array(
                            'type' => 'string',
                            'required' => true,
                            'enum' => array('year', 'group', 'halfGroup'),
                            'description' => __('ADE code type'),
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
                        'description' => __('Unique identifier for the ADE code'),
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
                            'description' => __('ADE code title'),
                        ),
                        'code' => array(
                            'type' => 'number',
                            'description' => __('ADE code'),
                        ),
                        'type' => array(
                            'type' => 'string',
                            'enum' => array('year', 'group', 'halfGroup'),
                            'description' => __('ADE code type'),
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
     * Récupère une collection de codes ADE.
     *
     * @param WP_REST_Request $request Données complètes concernant la requête.
     * @return WP_Error|WP_REST_Response Réponse contenant la liste des codes ADE.
     *
     * @example
     * Exemple de requête pour récupérer tous les codes ADE :
     * GET /amu-ecran-connectee/v1/ade
     */
    public function get_items($request) {
        // Obtient une instance du gestionnaire de codes ADE
        $ade_code = new CodeAde();

        return new WP_REST_Response($ade_code->getList(), 200);
    }

    /**
     * Crée un code ADE unique.
     *
     * @param WP_REST_Request $request Données complètes concernant la requête.
     * @return WP_REST_Response|WP_Error Réponse avec l'identifiant du code ADE créé ou une erreur.
     *
     * @example
     * Exemple de requête pour créer un code ADE :
     * POST /amu-ecran-connectee/v1/ade
     * {
     *     "title": "Titre de l'ADE",
     *     "code": 123,
     *     "type": "year"
     * }
     */
    public function create_item($request) {
        // Obtient une instance du gestionnaire de codes ADE
        $ade_code = new CodeAde();

        // Définit les données du code ADE
        $ade_code->setTitle($request->get_param('title'));
        $ade_code->setCode($request->get_param('code'));
        $ade_code->setType($request->get_param('type'));

        // Essaye d'insérer le code ADE
        if (($insert_id = $ade_code->insert()))
            return new WP_REST_Response(array('id' => $insert_id), 200);

        return new WP_REST_Response(array('message' => 'Could not insert the ADE code'), 400);
    }

    /**
     * Récupère un code ADE unique.
     *
     * @param WP_REST_Request $request Données complètes concernant la requête.
     * @return WP_REST_Response|WP_Error Réponse contenant le code ADE demandé ou une erreur.
     *
     * @example
     * Exemple de requête pour récupérer un code ADE :
     * GET /amu-ecran-connectee/v1/ade/1
     */
    public function get_item($request) {
        // Obtient une instance du gestionnaire de codes ADE
        $ade_code = new CodeAde();

        // Récupère les informations depuis la base de données
        $requested_ade_code = $ade_code->get($request->get_param('id'));
        if (!$requested_ade_code)
            return new WP_REST_Response(array('message' => 'ADE code not found'), 404);

        return new WP_REST_Response($requested_ade_code, 200);
    }

    /**
     * Met à jour un code ADE unique.
     *
     * @param WP_REST_Request $request Données complètes concernant la requête.
     * @return WP_REST_Response|WP_Error Réponse indiquant le succès ou l'échec de la mise à jour.
     *
     * @example
     * Exemple de requête pour mettre à jour un code ADE :
     * PUT /amu-ecran-connectee/v1/ade/1
     * {
     *     "title": "Nouveau titre",
     *     "code": 456,
     *     "type": "group"
     * }
     */
    public function update_item($request) {
        // Obtient une instance du gestionnaire de codes ADE
        $ade_code = new CodeAde();

        // Récupère les informations depuis la base de données
        $requested_ade_code = $ade_code->get($request->get_param('id'));
        if (!$requested_ade_code)
            return new WP_REST_Response(array('message' => 'ADE code not found'), 404);

        // Met à jour les informations
        if (is_string($request->get_json_params()['title']))
            $requested_ade_code->setTitle($request->get_json_params()['title']);

        if (is_string($request->get_json_params()['code']))
            $requested_ade_code->setCode($request->get_json_params()['code']);

        if (is_string($request->get_json_params()['type']))
            $requested_ade_code->setType($request->get_json_params()['type']);

        // Essaye de mettre à jour les informations
        if ($requested_ade_code->update() > 0)
            return new WP_REST_Response(null, 200);

        return new WP_REST_Response(array('message' => 'Could not update the ADE code'), 400);
    }

    /**
     * Supprime un code ADE unique.
     *
     * @param WP_REST_Request $request Données complètes concernant la requête.
     * @return WP_REST_Response|WP_Error Réponse indiquant le succès ou l'échec de la suppression.
     *
     * @example
     * Exemple de requête pour supprimer un code ADE :
     * DELETE /amu-ecran-connectee/v1/ade/1
     */
    public function delete_item($request) {
        // Obtient une instance du gestionnaire de codes ADE
        $codeAde = new CodeAde();

        // Récupère les informations depuis la base de données
        $requested_ade_code = $codeAde->get($request->get_param('id'));
        if ($requested_ade_code && $requested_ade_code->delete()) {
            return new WP_REST_Response(null, 204);
        }

        return new WP_REST_Response(array('message' => 'Could not delete the ADE code'), 400);
    }

    /**
     * Vérifie si une requête donnée a l'accès pour récupérer des éléments.
     *
     * @param WP_REST_Request $request Données complètes concernant la requête.
     * @return WP_Error|bool Retourne true si l'accès est autorisé, WP_Error sinon.
     *
     * @example
     * Cette méthode est utilisée pour vérifier si l'utilisateur courant a
     * les droits d'accès pour récupérer des codes ADE.
     */
    public function get_items_permissions_check($request) {
        $current_user = wp_get_current_user();
        return in_array("administrator", $current_user->roles);
    }

    /**
     * Vérifie si une requête donnée a l'accès pour créer un élément.
     *
     * @param WP_REST_Request $request Données complètes concernant la requête.
     * @return true|WP_Error Retourne true si l'accès est autorisé, WP_Error sinon.
     *
     * @example
     * Cette méthode vérifie si l'utilisateur a les droits nécessaires
     * pour créer un code ADE.
     */
    public function create_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une requête donnée a l'accès pour lire un élément.
     *
     * @param WP_REST_Request $request Données complètes concernant la requête.
     * @return true|WP_Error Retourne true si l'accès est autorisé, WP_Error sinon.
     *
     * @example
     * Cette méthode est utilisée pour vérifier les droits d'accès
     * lors de la lecture d'un code ADE.
     */
    public function get_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une requête donnée a l'accès pour mettre à jour un élément.
     *
     * @param WP_REST_Request $request Données complètes concernant la requête.
     * @return true|WP_Error Retourne true si l'accès est autorisé, WP_Error sinon.
     *
     * @example
     * Cette méthode vérifie si l'utilisateur a les droits nécessaires
     * pour mettre à jour un code ADE.
     */
    public function update_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une requête donnée a l'accès pour supprimer un élément.
     *
     * @param WP_REST_Request $request Données complètes concernant la requête.
     * @return true|WP_Error Retourne true si l'accès est autorisé, WP_Error sinon.
     *
     * @example
     * Cette méthode est utilisée pour vérifier les droits d'accès
     * lors de la suppression d'un code ADE.
     */
    public function delete_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }
}
