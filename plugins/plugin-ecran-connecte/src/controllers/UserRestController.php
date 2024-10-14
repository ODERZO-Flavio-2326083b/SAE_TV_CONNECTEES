<?php

namespace Controllers;

use Models\Alert;
use Models\CodeAde;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Classe UserRestController
 *
 * Ce contrôleur gère les requêtes REST liées aux alertes.
 * Il permet de créer, lire, mettre à jour et supprimer des alertes via l'API REST de WordPress.
 */
class UserRestController extends WP_REST_Controller
{
    /**
     * Constructeur du contrôleur REST.
     * Initialise le namespace et le rest_base pour les routes.
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
                        'content' => array(
                            'type' => 'string',
                            'required' => true,
                            'description' => __('Contenu de l\'alerte'),
                        ),
                        'expiration-date' => array(
                            'type' => 'string',
                            'required' => true,
                            'description' => __('Date d\'expiration de l\'alerte'),
                        ),
                        'codes' => array(
                            'type' => 'array',
                            'required' => true,
                            'items' => array('type' => 'string'),
                            'description' => __('Codes ADE'),
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
                        'description' => __('Identifiant unique pour l\'alerte'),
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
                        'content' => array(
                            'type' => 'string',
                            'description' => __('Contenu de l\'alerte'),
                        ),
                        'expiration-date' => array(
                            'type' => 'string',
                            'description' => __('Date d\'expiration de l\'alerte'),
                        ),
                        'codes' => array(
                            'type' => 'array',
                            'items' => array('type' => 'string'),
                            'description' => __('Codes ADE'),
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
     * Récupère une collection d'alertes.
     *
     * @param WP_REST_Request $request Données complètes sur la requête.
     * @return WP_Error|WP_REST_Response Réponse contenant la liste des alertes.
     */
    public function get_items($request) {
        // Récupérer une instance du gestionnaire d'alertes
        $alert = new Alert();

        return new WP_REST_Response($alert->getList(), 200);
    }

    /**
     * Crée une alerte unique.
     *
     * @param WP_REST_Request $request Détails complets sur la requête.
     * @return WP_REST_Response|WP_Error Réponse contenant l'ID de l'alerte créée ou un objet WP_Error en cas d'échec.
     */
    public function create_item($request) {
        // Récupérer une instance du gestionnaire d'alertes
        $alert = new Alert();

        // Définir les données de l'alerte
        $alert->setAuthor(wp_get_current_user()->ID);
        $alert->setContent($request->get_param('content'));
        $alert->setCreationDate(date('Y-m-d'));
        $alert->setExpirationDate($request->get_param('expiration-date'));

        // Définir les codes ADE pour l'alerte
        $ade_codes = $this->find_ade_codes($alert, $request->get_json_params()['codes']);

        if (is_null($ade_codes))
            return new WP_REST_Response(array('message' => 'Un code invalide a été spécifié'), 400);

        $alert->setCodes($ade_codes);

        // Essayer d'insérer le code ADE
        if (($insert_id = $alert->insert()))
            return new WP_REST_Response(array('id' => $insert_id), 200);

        return new WP_REST_Response(array('message' => 'Impossible d\'insérer l\'alerte'), 400);
    }

    /**
     * Récupère une alerte unique.
     *
     * @param WP_REST_Request $request Détails complets sur la requête.
     * @return WP_REST_Response|WP_Error Réponse contenant les détails de l'alerte ou un objet WP_Error en cas d'échec.
     */
    public function get_item($request) {
        // Récupérer une instance du gestionnaire d'alertes
        $alert = new Alert();

        // Récupérer les informations depuis la base de données
        $requested_alert = $alert->get($request->get_param('id'));
        if (!$requested_alert)
            return new WP_REST_Response(array('message' => 'Alerte non trouvée'), 404);

        return new WP_REST_Response($requested_alert, 200);
    }

    /**
     * Met à jour une alerte unique.
     *
     * @param WP_REST_Request $request Détails complets sur la requête.
     * @return WP_REST_Response|WP_Error Réponse vide en cas de succès ou un objet WP_Error en cas d'échec.
     */
    public function update_item($request) {
        // Récupérer une instance du gestionnaire d'alertes
        $alert = new Alert();

        // Récupérer les informations depuis la base de données
        $requested_alert = $alert->get($request->get_param('id'));
        if (is_null($requested_alert->getId()))
            return new WP_REST_Response(array('message' => 'Alerte non trouvée'), 404);

        // Mettre à jour les données de l'alerte
        if (is_string($request->get_json_params()['content']))
            $requested_alert->setContent($request->get_json_params()['content']);

        if (is_string($request->get_json_params()['expiration-date']))
            $requested_alert->setExpirationDate($request->get_json_params()['expiration-date']);

        if (is_array($request->get_json_params()['codes'])) {
            $ade_codes = $this->find_ade_codes($requested_alert, $request->get_json_params()['codes']);

            if (is_null($ade_codes))
                return new WP_REST_Response(array('message' => 'Un code invalide a été spécifié'), 400);

            $requested_alert->setCodes($ade_codes);
        }

        // Essayer de mettre à jour les informations
        if ($requested_alert->update() > 0)
            return new WP_REST_Response(null, 200);

        return new WP_REST_Response(array('message' => 'Impossible de mettre à jour l\'alerte'), 400);
    }

    /**
     * Supprime une alerte unique.
     *
     * @param WP_REST_Request $request Détails complets sur la requête.
     * @return WP_REST_Response|WP_Error Réponse vide en cas de succès ou un objet WP_Error en cas d'échec.
     */
    public function delete_item($request) {
        // Récupérer une instance du gestionnaire d'alertes
        $alert = new Alert();

        // Récupérer les informations depuis la base de données
        $requested_alert = $alert->get($request->get_param('id'));
        if ($requested_alert && $requested_alert->delete())
            return new WP_REST_Response(null, 200);

        return new WP_REST_Response(array('message' => 'Impossible de supprimer l\'alerte'), 400);
    }

    /**
     * Vérifie si une requête donnée a accès pour obtenir des alertes.
     *
     * @param WP_REST_Request $request Données complètes sur la requête.
     * @return WP_Error|bool True si l'utilisateur a les permissions, sinon un objet WP_Error.
     */
    public function get_items_permissions_check($request) {
        $current_user = wp_get_current_user();
        return in_array("administrator", $current_user->roles);
    }

    /**
     * Vérifie si une requête donnée a accès pour créer une alerte.
     *
     * @param WP_REST_Request $request Détails complets sur la requête.
     * @return true|WP_Error True si la requête a accès pour créer des alertes, WP_Error sinon.
     */
    public function create_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une requête donnée a accès pour lire une alerte.
     *
     * @param WP_REST_Request $request Détails complets sur la requête.
     * @return true|WP_Error True si la requête a accès pour lire l'alerte, sinon WP_Error.
     */
    public function get_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une requête donnée a accès pour mettre à jour une alerte.
     *
     * @param WP_REST_Request $request Détails complets sur la requête.
     * @return true|WP_Error True si la requête a accès pour mettre à jour l'alerte, WP_Error sinon.
     */
    public function update_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une requête donnée a accès pour supprimer une alerte.
     *
     * @param WP_REST_Request $request Détails complets sur la requête.
     * @return true|WP_Error True si la requête a accès pour supprimer l'alerte, WP_Error sinon.
     */
    public function delete_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Trouve les codes ADE et teste leur validité dans un tableau de chaînes.
     *
     * @param Alert $alert Alerte pour laquelle trouver des codes ADE.
     * @param array $codes Tableau de chaînes contenant les codes ADE.
     * @return array|null Tableau des codes ADE instanciés, ou null si une erreur s'est produite.
     */
    private function find_ade_codes($alert, $codes) {
        // Trouver les codes ADE
        $ade_code = new CodeAde();
        $alert->setForEveryone(0);
        $ade_codes = array();

        foreach ($codes as $code) {
            if ($code == 'all') {
                $alert->setForEveryone(1);
            } else if ($code != 0) {
                if (is_null($ade_code->getByCode($code)->getId())) {
                    return null;
                } else {
                    $ade_codes[] = $ade_code->getByCode($code);
                }
            } else {
                return null;
            }
        }

        return $ade_codes;
    }
}
