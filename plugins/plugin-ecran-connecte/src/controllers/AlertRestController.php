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
 * Cette classe gère les requêtes REST pour les alertes.
 * Elle permet de créer, lire, mettre à jour et supprimer des alertes.
 */
class AlertRestController extends WP_REST_Controller
{
    /**
     * Constructeur pour le contrôleur REST
     *
     * Initialise le namespace et la base REST pour les alertes.
     */
    public function __construct() {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'alert';
    }

    /**
     * Enregistre les routes pour les objets du contrôleur.
     *
     * Cette méthode définit les routes pour obtenir, créer, mettre à jour et supprimer des alertes.
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
                            'description' => __('Alert content'), // Contenu de l'alerte
                        ),
                        'expiration-date' => array(
                            'type' => 'string',
                            'required' => true,
                            'description' => __('Alert expiration date'), // Date d'expiration de l'alerte
                        ),
                        'codes' => array(
                            'type'        => 'array',
                            'required'    => true,
                            'items'       => array( 'type' => 'string' ),
                            'description' => __('ADE codes'), // Codes ADE associés à l'alerte
                        ),
                    ),
                ),
                'schema' => array($this, 'get_public_item_schema'), // Schéma de l'élément public
            )
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>[\d]+)',
            array(
                'args' => array(
                    'id' => array(
                        'description' => __('Unique identifier for the alert'), // Identifiant unique pour l'alerte
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
                            'description' => __('Alert content'), // Contenu de l'alerte
                        ),
                        'expiration-date' => array(
                            'type' => 'string',
                            'description' => __('Alert expiration date'), // Date d'expiration de l'alerte
                        ),
                        'codes' => array(
                            'type'        => 'array',
                            'items'       => array( 'type' => 'string' ),
                            'description' => __('ADE codes'), // Codes ADE associés à l'alerte
                        ),
                    ),
                ),
                array(
                    'methods' => WP_REST_Server::DELETABLE,
                    'callback' => array($this, 'delete_item'),
                    'permission_callback' => array($this, 'delete_item_permissions_check'),
                    'args' => array() // Pas d'arguments requis pour la suppression
                ),
                'schema' => array($this, 'get_public_item_schema'), // Schéma de l'élément public
            )
        );
    }

    /**
     * Get a collection of items
     *
     * Cette méthode récupère la liste des alertes.
     *
     * @param WP_REST_Request $request Données complètes sur la requête.
     * @return WP_Error|WP_REST_Response
     */
    public function get_items($request) {
        // Obtenir une instance du gestionnaire d'alertes
        $alert = new Alert();

        return new WP_REST_Response($alert->getList(), 200); // Retourne la liste des alertes
    }

    /**
     * Creates a single alert.
     *
     * Cette méthode crée une nouvelle alerte avec les paramètres fournis dans la requête.
     *
     * @param WP_REST_Request $request Données complètes sur la requête.
     * @return WP_REST_Response|WP_Error Réponse sur succès, ou objet WP_Error en cas d'échec.
     */
    public function create_item($request) {
        // Obtenir une instance du gestionnaire d'alertes
        $alert = new Alert();

        // Définir les données de l'alerte
        $alert->setAuthor(wp_get_current_user()->ID); // Définit l'auteur de l'alerte
        $alert->setContent($request->get_param('content')); // Définit le contenu de l'alerte
        $alert->setCreationDate(date('Y-m-d')); // Définit la date de création de l'alerte
        $alert->setExpirationDate($request->get_param('expiration-date')); // Définit la date d'expiration de l'alerte

        // Définir les codes ADE pour l'alerte
        $ade_codes = $this->find_ade_codes($alert, $request->get_json_params()['codes']); // Trouve les codes ADE

        if (is_null($ade_codes))
            return new WP_REST_Response(array('message' => 'An invalid code was specified'), 400); // Retourne une erreur si les codes sont invalides

        $alert->setCodes($ade_codes); // Définit les codes ADE pour l'alerte

        // Essaye d'insérer l'alerte
        if (($insert_id = $alert->insert())) {
            // Envoyer la notification push
            $oneSignalPush = new OneSignalPush();

            if ($alert->isForEveryone()) {
                $oneSignalPush->sendNotification(null, $alert->getContent()); // Notification pour tout le monde
            } else {
                $oneSignalPush->sendNotification($ade_codes, $alert->getContent()); // Notification ciblée
            }

            // Retourne l'ID de l'alerte insérée
            return new WP_REST_Response(array('id' => $insert_id), 200);
        }

        return new WP_REST_Response(array('message' => 'Could not insert the alert'), 400); // Retourne une erreur si l'insertion échoue
    }

    /**
     * Retrieves a single alert.
     *
     * Cette méthode récupère une alerte en fonction de son identifiant.
     *
     * @param WP_REST_Request $request Données complètes sur la requête.
     * @return WP_REST_Response|WP_Error Réponse sur succès, ou WP_Error en cas d'échec.
     */
    public function get_item($request) {
        // Obtenir une instance du gestionnaire d'alertes
        $alert = new Alert();

        // Récupérer les informations de la base de données
        $requested_alert = $alert->get($request->get_param('id'));
        if (!$requested_alert)
            return new WP_REST_Response(array('message' => 'Alert not found'), 404); // Retourne une erreur si l'alerte n'est pas trouvée

        return new WP_REST_Response($requested_alert, 200); // Retourne les détails de l'alerte
    }

    /**
     * Updates a single alert.
     *
     * Cette méthode met à jour une alerte existante avec les nouveaux paramètres fournis.
     *
     * @param WP_REST_Request $request Données complètes sur la requête.
     * @return WP_REST_Response|WP_Error Réponse sur succès, ou WP_Error en cas d'échec.
     */
    public function update_item($request) {
        // Obtenir une instance du gestionnaire d'alertes
        $alert = new Alert();

        // Récupérer les informations de la base de données
        $requested_alert = $alert->get($request->get_param('id'));
        if (is_null($requested_alert->getId()))
            return new WP_REST_Response(array('message' => 'Alert not found'), 404); // Retourne une erreur si l'alerte n'est pas trouvée

        // Mise à jour des données de l'alerte
        if ($request->has_param('content')) {
            $alert->setContent($request->get_param('content')); // Définit le nouveau contenu
        }

        if ($request->has_param('expiration-date')) {
            $alert->setExpirationDate($request->get_param('expiration-date')); // Définit la nouvelle date d'expiration
        }

        // Essaye de mettre à jour l'alerte
        if ($alert->update($request->get_param('id'))) {
            return new WP_REST_Response(array('message' => 'Alert updated'), 200); // Retourne un message de succès
        }

        return new WP_REST_Response(array('message' => 'Could not update the alert'), 400); // Retourne une erreur si la mise à jour échoue
    }

    /**
     * Deletes a single alert.
     *
     * Cette méthode supprime une alerte existante en fonction de son identifiant.
     *
     * @param WP_REST_Request $request Données complètes sur la requête.
     * @return WP_REST_Response|WP_Error Réponse sur succès, ou WP_Error en cas d'échec.
     */
    public function delete_item($request) {
        // Obtenir une instance du gestionnaire d'alertes
        $alert = new Alert();

        // Essaye de supprimer l'alerte
        if ($alert->delete($request->get_param('id'))) {
            return new WP_REST_Response(array('message' => 'Alert deleted'), 200); // Retourne un message de succès
        }

        return new WP_REST_Response(array('message' => 'Could not delete the alert'), 400); // Retourne une erreur si la suppression échoue
    }

    /**
     * Vérifie les autorisations pour obtenir les éléments.
     *
     * @return bool
     */
    public function get_items_permissions_check() {
        return true; // Retourne vrai si les autorisations sont accordées
    }

    /**
     * Vérifie les autorisations pour créer un élément.
     *
     * @return bool
     */
    public function create_item_permissions_check() {
        return true; // Retourne vrai si les autorisations sont accordées
    }

    /**
     * Vérifie les autorisations pour obtenir un élément.
     *
     * @return bool
     */
    public function get_item_permissions_check() {
        return true; // Retourne vrai si les autorisations sont accordées
    }

    /**
     * Vérifie les autorisations pour mettre à jour un élément.
     *
     * @return bool
     */
    public function update_item_permissions_check() {
        return true; // Retourne vrai si les autorisations sont accordées
    }

    /**
     * Vérifie les autorisations pour supprimer un élément.
     *
     * @return bool
     */
    public function delete_item_permissions_check() {
        return true; // Retourne vrai si les autorisations sont accordées
    }

    /**
     * Cherche les codes ADE valides pour l'alerte.
     *
     * Cette méthode vérifie les codes ADE spécifiés et les associe à l'alerte.
     *
     * @param Alert $alert Instance de l'alerte.
     * @param array $codes Liste des codes ADE à vérifier.
     * @return array|null Retourne les codes valides ou null si aucun code valide.
     */
    private function find_ade_codes($alert, $codes) {
        // Logique pour trouver les codes ADE valides
        $valid_codes = array();
        foreach ($codes as $code) {
            if (CodeAde::exists($code)) {
                $valid_codes[] = $code; // Ajoute le code valide à la liste
            }
        }

        return !empty($valid_codes) ? $valid_codes : null; // Retourne les codes valides ou null
    }
}
