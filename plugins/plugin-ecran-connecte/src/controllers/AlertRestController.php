<?php

namespace Controllers;

include __DIR__ . '/../utils/OneSignalPush.php';

use Models\Alert;
use Models\CodeAde;
use Utils\OneSignalPush;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Classe AlertRestController
 *
 * Gère les routes REST pour les alertes.
 */
class AlertRestController extends WP_REST_Controller
{
    /**
     * Constructeur du contrôleur REST.
     * Initialise le namespace et la base des routes.
     */
    public function __construct() {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'alert';
    }

    /**
     * Enregistre les routes pour les objets du contrôleur.
     * Cette méthode permet d'associer les différentes méthodes HTTP
     * (GET, POST, etc.) aux fonctions du contrôleur.
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
                            'type'        => 'array',
                            'required'    => true,
                            'items'       => array( 'type' => 'string' ),
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
                        'description' => __('Identifiant unique de l\'alerte'),
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
                            'type'        => 'array',
                            'items'       => array( 'type' => 'string' ),
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
     * @param WP_REST_Request $request Les données complètes concernant la requête.
     * @return WP_Error|WP_REST_Response La réponse REST ou une erreur WP en cas de problème.
     */
    public function get_items($request) {
        // Obtenir une instance du gestionnaire d'alertes.
        $alert = new Alert();

        return new WP_REST_Response($alert->getList(), 200);
    }

    /**
     * Crée une alerte.
     *
     * @param WP_REST_Request $request Les détails complets concernant la requête.
     * @return WP_REST_Response|WP_Error La réponse en cas de succès ou une erreur WP en cas d'échec.
     */
    public function create_item($request) {
        // Obtenir une instance du gestionnaire d'alertes.
        $alert = new Alert();

        // Définir les données de l'alerte.
        $alert->setAuthor(wp_get_current_user()->ID);
        $alert->setContent($request->get_param('content'));
        $alert->setCreationDate(date('Y-m-d'));
        $alert->setExpirationDate($request->get_param('expiration-date'));

        // Définir les codes ADE associés à l'alerte.
        $ade_codes = $this->find_ade_codes($alert, $request->get_json_params()['codes']);

        if (is_null($ade_codes))
            return new WP_REST_Response(array('message' => 'Un code invalide a été spécifié'), 400);

        $alert->setCodes($ade_codes);

        // Tentative d'insertion du code ADE
        if (($insert_id = $alert->insert())) {
            // Envoi de la notification push
            $oneSignalPush = new OneSignalPush();

            if ($alert->isForEveryone()) {
                $oneSignalPush->sendNotification(null, $alert->getContent());
            } else {
                $oneSignalPush->sendNotification($ade_codes, $alert->getContent());
            }

            // Retourne l'ID de l'alerte insérée.
            return new WP_REST_Response(array('id' => $insert_id), 200);
        }

        return new WP_REST_Response(array('message' => 'Impossible d\'insérer l\'alerte'), 400);
    }

    /**
     * Récupère une seule alerte.
     *
     * @param WP_REST_Request $request Les détails complets concernant la requête.
     * @return WP_REST_Response|WP_Error La réponse REST ou une erreur WP en cas de problème.
     */
    public function get_item($request) {
        // Obtenir une instance du gestionnaire d'alertes.
        $alert = new Alert();

        // Récupérer les informations depuis la base de données.
        $requested_alert = $alert->get($request->get_param('id'));
        if (!$requested_alert)
            return new WP_REST_Response(array('message' => 'Alerte non trouvée'), 404);

        return new WP_REST_Response($requested_alert, 200);
    }

    /**
     * Met à jour une seule alerte.
     *
     * @param WP_REST_Request $request Les détails complets concernant la requête.
     * @return WP_REST_Response|WP_Error La réponse en cas de succès ou une erreur WP en cas d'échec.
     */
    public function update_item($request) {
        // Obtenir une instance du gestionnaire d'alertes.
        $alert = new Alert();

        // Récupérer les informations depuis la base de données.
        $requested_alert = $alert->get($request->get_param('id'));
        if (is_null($requested_alert->getId()))
            return new WP_REST_Response(array('message' => 'Alerte non trouvée'), 404);

        // Mise à jour des données de l'alerte.
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

        // Tentative de mise à jour.
        if ($requested_alert->update() > 0)
            return new WP_REST_Response(null, 200);

        return new WP_REST_Response(array('message' => 'Impossible de mettre à jour l\'alerte'), 400);
    }

    /**
     * Supprime une seule alerte.
     *
     * @param WP_REST_Request $request Les détails complets concernant la requête.
     * @return WP_REST_Response|WP_Error La réponse en cas de succès ou une erreur WP en cas d'échec.
     */
    public function delete_item($request) {
        // Obtenir une instance du gestionnaire d'alertes.
        $alert = new Alert();

        // Récupérer les informations depuis la base de données.
        $requested_alert = $alert->get($request->get_param('id'));
        if ($requested_alert && $requested_alert->delete())
            return new WP_REST_Response(null, 200);

        return new WP_REST_Response(array('message' => 'Impossible de supprimer l\'alerte'), 400);
    }

    /**
     * Vérifie si une requête donnée a accès à la liste des éléments.
     *
     * @param WP_REST_Request $request Les données complètes concernant la requête.
     * @return WP_Error|bool Retourne vrai si l'utilisateur a les droits, sinon une erreur WP.
     */
    public function get_items_permissions_check($request) {
        $current_user = wp_get_current_user();
        return in_array("administrator", $current_user->roles);
    }

    /**
     * Vérifie si une requête a accès pour créer une alerte.
     *
     * @param WP_REST_Request $request Les détails complets concernant la requête.
     * @return true|WP_Error Retourne vrai si l'utilisateur a les droits, sinon une erreur WP.
     */
    public function create_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une requête a accès à la lecture d'une alerte.
     *
     * @param WP_REST_Request $request Les détails complets concernant la requête.
     * @return true|WP_Error Retourne vrai si l'utilisateur a les droits, sinon une erreur WP.
     */
    public function get_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une requête a accès pour mettre à jour une alerte.
     *
     * @param WP_REST_Request $request Les détails complets concernant la requête.
     * @return true|WP_Error Retourne vrai si l'utilisateur a les droits, sinon une erreur WP.
     */
    public function update_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une requête a accès pour supprimer une alerte.
     *
     * @param WP_REST_Request $request Les détails complets concernant la requête.
     * @return true|WP_Error Retourne vrai si l'utilisateur a les droits, sinon une erreur WP.
     */
    public function delete_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Trouve les codes ADE et vérifie leur validité dans un tableau de chaînes.
     *
     * @param Alert $alert Alerte pour laquelle les codes ADE doivent être trouvés.
     * @param array $codes Tableau contenant les codes ADE sous forme de chaînes.
     * @return array|null Tableau de codes ADE instanciés ou null si une erreur est survenue.
     */
    private function find_ade_codes($alert, $codes) {
        // Trouver les codes ADE.
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
