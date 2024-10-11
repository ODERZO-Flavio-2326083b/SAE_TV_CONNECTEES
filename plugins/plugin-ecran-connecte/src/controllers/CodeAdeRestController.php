<?php

namespace Controllers;

use Models\CodeAde;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Class CodeAdeRestController
 *
 * Contrôleur REST pour gérer les opérations CRUD des codes ADE.
 *
 * Ce contrôleur gère les routes RESTful pour la création, la récupération,
 * la mise à jour et la suppression des codes ADE dans WordPress.
 *
 * @package Controllers
 */
class CodeAdeRestController extends WP_REST_Controller
{
    /**
     * Namespace pour les routes REST.
     * @var string
     */
    protected $namespace;

    /**
     * Base des routes REST.
     * @var string
     */
    protected $rest_base;

    /**
     * Constructeur pour le contrôleur REST.
     *
     * Initialise le namespace et la base des routes.
     */
    public function __construct() {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'ade';
    }

    /**
     * Enregistre les routes pour les objets du contrôleur.
     *
     * Cette méthode doit être appelée pour ajouter les routes aux API REST
     * et définir les méthodes HTTP correspondantes pour chaque opération.
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
                            'description' => __('Titre du code ADE'),
                        ),
                        'code' => array(
                            'type' => 'number',
                            'required' => true,
                            'description' => __('Code ADE'),
                        ),
                        'type' => array(
                            'type' => 'string',
                            'required' => true,
                            'enum' => array('year', 'group', 'halfGroup'),
                            'description' => __('Type de code ADE'),
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
                        'description' => __('Identifiant unique pour le code ADE'),
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
                            'description' => __('Titre du code ADE'),
                        ),
                        'code' => array(
                            'type' => 'number',
                            'description' => __('Code ADE'),
                        ),
                        'type' => array(
                            'type' => 'string',
                            'enum' => array('year', 'group', 'halfGroup'),
                            'description' => __('Type de code ADE'),
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
     * Obtient une collection d'éléments.
     *
     * @param WP_REST_Request $request Données complètes concernant la requête.
     * @return WP_Error|WP_REST_Response Réponse contenant la liste des codes ADE ou une erreur.
     */
    public function get_items($request) {
        // Récupération de l'instance du gestionnaire de codes ADE
        $ade_code = new CodeAde();

        return new WP_REST_Response($ade_code->getList(), 200);
    }

    /**
     * Crée un code ADE unique.
     *
     * @param WP_REST_Request $request Détails complets concernant la requête.
     * @return WP_REST_Response|WP_Error Objet réponse en cas de succès ou objet WP_Error en cas d'échec.
     */
    public function create_item($request) {
        // Récupération de l'instance du gestionnaire de codes ADE
        $ade_code = new CodeAde();

        // Définition des données du code ADE
        $ade_code->setTitle($request->get_param('title'));
        $ade_code->setCode($request->get_param('code'));
        $ade_code->setType($request->get_param('type'));

        // Tentative d'insertion du code ADE
        if (($insert_id = $ade_code->insert()))
            return new WP_REST_Response(array('id' => $insert_id), 200);

        return new WP_REST_Response(array('message' => 'Impossible d\'insérer le code ADE'), 400);
    }

    /**
     * Récupère un code ADE unique.
     *
     * @param WP_REST_Request $request Détails complets concernant la requête.
     * @return WP_REST_Response|WP_Error Objet réponse en cas de succès ou objet WP_Error en cas d'échec.
     */
    public function get_item($request) {
        // Récupération de l'instance du gestionnaire de codes ADE
        $ade_code = new CodeAde();

        // Récupération des informations depuis la base de données
        $requested_ade_code = $ade_code->get($request->get_param('id'));
        if (!$requested_ade_code)
            return new WP_REST_Response(array('message' => 'Code ADE non trouvé'), 404);

        return new WP_REST_Response($requested_ade_code, 200);
    }

    /**
     * Met à jour un code ADE unique.
     *
     * @param WP_REST_Request $request Détails complets concernant la requête.
     * @return WP_REST_Response|WP_Error Objet réponse en cas de succès ou objet WP_Error en cas d'échec.
     */
    public function update_item($request) {
        // Récupération de l'instance du gestionnaire de codes ADE
        $ade_code = new CodeAde();

        // Récupération des informations depuis la base de données
        $requested_ade_code = $ade_code->get($request->get_param('id'));
        if (!$requested_ade_code)
            return new WP_REST_Response(array('message' => 'Code ADE non trouvé'), 404);

        // Mise à jour des données
        if (is_string($request->get_json_params()['title']))
            $requested_ade_code->setTitle($request->get_json_params()['title']);

        if (is_string($request->get_json_params()['code']))
            $requested_ade_code->setCode($request->get_json_params()['code']);

        if (is_string($request->get_json_params()['type']))
            $requested_ade_code->setType($request->get_json_params()['type']);

        // Tentative de mise à jour des informations
        if ($requested_ade_code->update() > 0)
            return new WP_REST_Response(null, 200);

        return new WP_REST_Response(array('message' => 'Impossible de mettre à jour le code ADE'), 400);
    }

    /**
     * Supprime un code ADE unique.
     *
     * @param WP_REST_Request $request Détails complets concernant la requête.
     * @return WP_REST_Response|WP_Error Objet réponse en cas de succès ou objet WP_Error en cas d'échec.
     */
    public function delete_item($request) {
        // Récupération de l'instance du gestionnaire de codes ADE
        $codeAde = new CodeAde();

        // Récupération des informations depuis la base de données
        $requested_ade_code = $codeAde->get($request->get_param('id'));
        if ($requested_ade_code && $requested_ade_code->delete())
            return new WP_REST_Response(null, 200);

        return new WP_REST_Response(array('message' => 'Impossible de supprimer le code ADE'), 400);
    }

    /**
     * Vérifie si une requête donnée a accès pour obtenir des éléments.
     *
     * @param WP_REST_Request $request Détails complets concernant la requête.
     * @return WP_Error|bool
     */
    public function get_items_permissions_check($request) {
        $current_user = wp_get_current_user();
        return in_array("administrator", $current_user->roles);
    }

    /**
     * Vérifie si une requête donnée a accès pour créer un élément.
     *
     * @param WP_REST_Request $request Détails complets concernant la requête.
     * @return true|WP_Error True si la requête a accès pour créer des éléments, sinon objet WP_Error.
     */
    public function create_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une requête donnée a accès pour lire un élément.
     *
     * @param WP_REST_Request $request Détails complets concernant la requête.
     * @return true|WP_Error True si la requête a accès pour lire l'élément, sinon objet WP_Error.
     */
    public function get_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une requête donnée a accès pour mettre à jour un élément.
     *
     * @param WP_REST_Request $request Détails complets concernant la requête.
     * @return true|WP_Error True si la requête a accès pour mettre à jour l'élément, sinon objet WP_Error.
     */
    public function update_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une requête donnée a accès pour supprimer un élément.
     *
     * @param WP_REST_Request $request Détails complets concernant la requête.
     * @return true|WP_Error True si la requête a accès pour supprimer l'élément, sinon objet WP_Error.
     */
    public function delete_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }
}
