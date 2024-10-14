<?php

namespace Controllers;

use Models\CodeAde;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class CodeAdeRestController extends WP_REST_Controller
{
    /**
     * Constructeur du contrôleur REST
     * Initialise l'espace de noms et la base REST.
     */
    public function __construct() {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'ade';
    }

    /**
     * Enregistre les routes pour les objets du contrôleur.
     *
     * Cette méthode définit les routes de l'API REST,
     * y compris les méthodes pour obtenir, créer, mettre à jour
     * et supprimer des codes ADE.
     *
     * Exemple d'utilisation :
     * GET /amu-ecran-connectee/v1/ade
     * POST /amu-ecran-connectee/v1/ade
     * GET /amu-ecran-connectee/v1/ade/{id}
     * PUT /amu-ecran-connectee/v1/ade/{id}
     * DELETE /amu-ecran-connectee/v1/ade/{id}
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
     * Récupère une collection d'éléments.
     *
     * @param WP_REST_Request $request Informations complètes sur la requête.
     * @return WP_Error|WP_REST_Response
     *
     * Exemple d'utilisation :
     * GET /amu-ecran-connectee/v1/ade
     */
    public function get_items($request) {
        // Obtenir une instance du gestionnaire de code ADE
        $ade_code = new CodeAde();

        return new WP_REST_Response($ade_code->getList(), 200);
    }

    /**
     * Crée un code ADE unique.
     *
     * @param WP_REST_Request $request Informations complètes sur la requête.
     * @return WP_REST_Response|WP_Error Objet de réponse en cas de succès, ou objet WP_Error en cas d'échec.
     *
     * Exemple d'utilisation :
     * POST /amu-ecran-connectee/v1/ade avec le corps suivant :
     * {
     *   "title": "Titre du code",
     *   "code": 12345,
     *   "type": "year"
     * }
     */
    public function create_item($request) {
        // Obtenir une instance du gestionnaire de code ADE
        $ade_code = new CodeAde();

        // Définir les données du code ADE
        $ade_code->setTitle($request->get_param('title'));
        $ade_code->setCode($request->get_param('code'));
        $ade_code->setType($request->get_param('type'));

        // Essayer d'insérer le code ADE
        if (($insert_id = $ade_code->insert()))
            return new WP_REST_Response(array('id' => $insert_id), 200);

        return new WP_REST_Response(array('message' => 'Could not insert the ADE code'), 400);
    }

    /**
     * Récupère un code ADE unique.
     *
     * @param WP_REST_Request $request Informations complètes sur la requête.
     * @return WP_REST_Response|WP_Error Objet de réponse en cas de succès, ou objet WP_Error en cas d'échec.
     *
     * Exemple d'utilisation :
     * GET /amu-ecran-connectee/v1/ade/{id}
     */
    public function get_item($request) {
        // Obtenir une instance du gestionnaire de code ADE
        $ade_code = new CodeAde();

        // Récupérer les informations de la base de données
        $requested_ade_code = $ade_code->get($request->get_param('id'));
        if (!$requested_ade_code)
            return new WP_REST_Response(array('message' => 'ADE code not found'), 404);

        return new WP_REST_Response($requested_ade_code, 200);
    }

    /**
     * Met à jour un code ADE unique.
     *
     * @param WP_REST_Request $request Informations complètes sur la requête.
     * @return WP_REST_Response|WP_Error Objet de réponse en cas de succès, ou objet WP_Error en cas d'échec.
     *
     * Exemple d'utilisation :
     * PUT /amu-ecran-connectee/v1/ade/{id} avec le corps suivant :
     * {
     *   "title": "Nouveau titre",
     *   "code": 54321,
     *   "type": "group"
     * }
     */
    public function update_item($request) {
        // Obtenir une instance du gestionnaire de code ADE
        $ade_code = new CodeAde();

        // Récupérer les informations de la base de données
        $requested_ade_code = $ade_code->get($request->get_param('id'));
        if (!$requested_ade_code)
            return new WP_REST_Response(array('message' => 'ADE code not found'), 404);

        // Mettre à jour les données
        if (is_string($request->get_json_params()['title']))
            $requested_ade_code->setTitle($request->get_json_params()['title']);

        if (is_numeric($request->get_json_params()['code']))
            $requested_ade_code->setCode($request->get_json_params()['code']);

        if (is_string($request->get_json_params()['type']))
            $requested_ade_code->setType($request->get_json_params()['type']);

        // Essayer de mettre à jour les informations
        if ($requested_ade_code->update() > 0)
            return new WP_REST_Response(null, 200);

        return new WP_REST_Response(array('message' => 'Could not update the ADE code'), 400);
    }

    /**
     * Supprime un code ADE unique.
     *
     * @param WP_REST_Request $request Informations complètes sur la requête.
     * @return WP_REST_Response|WP_Error Objet de réponse en cas de succès, ou objet WP_Error en cas d'échec.
     *
     * Exemple d'utilisation :
     * DELETE /amu-ecran-connectee/v1/ade/{id}
     */
    public function delete_item($request) {
        // Obtenir une instance du gestionnaire de code ADE
        $codeAde = new CodeAde();

        // Récupérer les informations de la base de données
        $requested_ade_code = $codeAde->get($request->get_param('id'));
        if ($requested_ade_code && $requested_ade_code->delete())
            return new WP_REST_Response(null, 200);

        return new WP_REST_Response(array('message' => 'Could not delete the ADE code'), 400);
    }

    /**
     * Vérifie si une requête donnée a accès pour obtenir des éléments.
     *
     * @param WP_REST_Request $request Informations complètes sur la requête.
     * @return WP_Error|bool
     *
     * Vérifie si l'utilisateur actuel a le rôle d'administrateur.
     */
    public function get_items_permissions_check($request) {
        $current_user = wp_get_current_user();
        return in_array("administrator", $current_user->roles);
    }

    /**
     * Vérifie si une requête donnée a accès pour créer une information.
     *
     * @param WP_REST_Request $request Informations complètes sur la requête.
     * @return true|WP_Error Vrai si la requête a accès pour créer des éléments, sinon objet WP_Error.
     */
    public function create_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une requête donnée a accès pour lire une information.
     *
     * @param WP_REST_Request $request Informations complètes sur la requête.
     * @return true|WP_Error Vrai si la requête a accès en lecture pour l'élément, sinon objet WP_Error.
     */
    public function get_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une requête donnée a accès pour mettre à jour une information unique.
     *
     * @param WP_REST_Request $request Informations complètes sur la requête.
     * @return true|WP_Error Vrai si la requête a accès pour mettre à jour l'élément, sinon objet WP_Error.
     */
    public function update_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une requête donnée a accès pour supprimer une information.
     *
     * @param WP_REST_Request $request Informations complètes sur la requête.
     * @return true|WP_Error Vrai si la requête a accès pour supprimer l'élément, sinon objet WP_Error.
     */
    public function delete_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }
}
