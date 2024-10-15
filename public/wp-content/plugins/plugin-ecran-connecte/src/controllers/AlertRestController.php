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

class AlertRestController extends WP_REST_Controller
{
    /**
     * Constructeur du contrôleur REST
     */
    public function __construct() {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'alert';
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
                            'description' => __('Alert content'),
                        ),
                        'expiration-date' => array(
                            'type' => 'string',
                            'required' => true,
                            'description' => __('Alert expiration date'),
                        ),
                        'codes' => array(
                            'type'        => 'array',
                            'required'    => true,
                            'items'       => array( 'type' => 'string' ),
                            'description' => __('ADE codes'),
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
                        'description' => __('Unique identifier for the alert'),
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
                            'description' => __('Alert content'),
                        ),
                        'expiration-date' => array(
                            'type' => 'string',
                            'description' => __('Alert expiration date'),
                        ),
                        'codes' => array(
                            'type'        => 'array',
                            'items'       => array( 'type' => 'string' ),
                            'description' => __('ADE codes'),
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
     * @param WP_REST_Request $request Données complètes sur la demande.
     * @return WP_Error|WP_REST_Response
     */
    public function get_items($request) {
        // Obtenir une instance du gestionnaire de codes ADE
        $alert = new Alert();

        return new WP_REST_Response($alert->getList(), 200);
    }

    /**
     * Crée une alerte unique.
     *
     * @param WP_REST_Request $request Détails complets sur la demande.
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

        if (is_null($ade_codes))
            return new WP_REST_Response(array('message' => 'An invalid code was specified'), 400);

        $alert->setCodes($ade_codes);

        // Essayer d'insérer le code ADE
        if (($insert_id = $alert->insert())) {
            // Envoyer la notification push
            $oneSignalPush = new OneSignalPush();

            if ($alert->isForEveryone()) {
                $oneSignalPush->sendNotification(null, $alert->getContent());
            } else {
                $oneSignalPush->sendNotification($ade_codes, $alert->getContent());
            }

            // Retourner l'ID de l'alerte insérée
            return new WP_REST_Response(array('id' => $insert_id), 200);
        }

        return new WP_REST_Response(array('message' => 'Could not insert the alert'), 400);
    }

    /**
     * Récupère une alerte unique.
     *
     * @param WP_REST_Request $request Détails complets sur la demande.
     * @return WP_REST_Response|WP_Error Objet de réponse en cas de succès, ou objet WP_Error en cas d'échec.
     */
    public function get_item($request) {
        // Obtenir une instance du gestionnaire d'alertes
        $alert = new Alert();

        // Récupérer les informations de la base de données
        $requested_alert = $alert->get($request->get_param('id'));
        if (!$requested_alert)
            return new WP_REST_Response(array('message' => 'Alert not found'), 404);

        return new WP_REST_Response($requested_alert, 200);
    }

    /**
     * Met à jour une alerte unique.
     *
     * @param WP_REST_Request $request Détails complets sur la demande.
     * @return WP_REST_Response|WP_Error Objet de réponse en cas de succès, ou objet WP_Error en cas d'échec.
     */
    public function update_item($request) {
        // Obtenir une instance du gestionnaire d'alertes
        $alert = new Alert();

        // Récupérer les informations de la base de données
        $requested_alert = $alert->get($request->get_param('id'));
        if (is_null($requested_alert->getId()))
            return new WP_REST_Response(array('message' => 'Alert not found'), 404);

        // Mettre à jour les données de l'alerte
        if (is_string($request->get_json_params()['content']))
            $requested_alert->setContent($request->get_json_params()['content']);

        if (is_string($request->get_json_params()['expiration-date']))
            $requested_alert->setExpirationDate($request->get_json_params()['expiration-date']);

        if (is_array($request->get_json_params()['codes'])) {
            $ade_codes = $this->find_ade_codes($requested_alert, $request->get_json_params()['codes']);

            if (is_null($ade_codes))
                return new WP_REST_Response(array('message' => 'An invalid code was specified'), 400);

            $requested_alert->setCodes($ade_codes);
        }

        // Essayer de mettre à jour les informations
        if ($requested_alert->update() > 0)
            return new WP_REST_Response(null, 200);

        return new WP_REST_Response(array('message' => 'Could not update the alert'), 400);
    }

    /**
     * Supprime une alerte unique.
     *
     * @param WP_REST_Request $request Détails complets sur la demande.
     * @return WP_REST_Response|WP_Error Objet de réponse en cas de succès, ou objet WP_Error en cas d'échec.
     */
    public function delete_item($request) {
        // Obtenir une instance du gestionnaire d'alertes
        $alert = new Alert();

        // Récupérer les informations de la base de données
        $requested_alert = $alert->get($request->get_param('id'));
        if ($requested_alert && $requested_alert->delete())
            return new WP_REST_Response(null, 200);

        return new WP_REST_Response(array('message' => 'Could not delete the alert'), 400);
    }

    /**
     * Vérifie si une demande donnée a accès pour récupérer des éléments
     *
     * @param WP_REST_Request $request Données complètes sur la demande.
     * @return WP_Error|bool
     */
    public function get_items_permissions_check($request) {
        $current_user = wp_get_current_user();
        return in_array("administrator", $current_user->roles);
    }

    /**
     * Vérifie si une demande donnée a accès pour créer une information.
     *
     * @param WP_REST_Request $request Détails complets sur la demande.
     * @return true|WP_Error Vrai si la demande a accès pour créer des éléments, objet WP_Error sinon.
     */
    public function create_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une demande donnée a accès pour lire une information.
     *
     * @param WP_REST_Request $request Détails complètes sur la demande.
     * @return true|WP_Error Vrai si la demande a accès en lecture pour l'élément, sinon objet WP_Error.
     */
    public function get_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une demande donnée a accès pour mettre à jour une seule information.
     *
     * @param WP_REST_Request $request Détails complètes sur la demande.
     * @return true|WP_Error Vrai si la demande a accès pour mettre à jour l'élément, objet WP_Error sinon.
     */
    public function update_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie si une demande donnée a accès pour supprimer une information.
     *
     * @param WP_REST_Request $request Détails complètes sur la demande.
     * @return true|WP_Error Vrai si la demande a accès pour supprimer l'élément, objet WP_Error sinon.
     */
    public function delete_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Trouve les codes ADE et teste leur validité dans un tableau de chaînes
     *
     * @param Alert $alert Alerte pour laquelle trouver les codes ADE
     * @param array $codes Tableau de chaînes contenant les codes ADE
     * @return array|null Le tableau de codes ADE instanciés, ou null si une erreur s'est produite
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
