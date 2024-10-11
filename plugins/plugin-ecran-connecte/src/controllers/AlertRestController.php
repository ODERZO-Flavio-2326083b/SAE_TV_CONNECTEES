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
 * Class AlertRestController
 *
 * Ce contrôleur REST gère les alertes via une API REST.
 *
 * @package Controllers
 *
 * ## Points importants :
 *
 * - Utilisation cohérente des commentaires pour chaque méthode.
 * - Exemples d'utilisation et scénarios typiques documentés.
 * - Chaque méthode spécifie les paramètres et les types de retour.
 * - Gestion des exceptions et des erreurs à chaque étape.
 * - Clarification de la logique métier dans les méthodes complexes.
 * - Liens internes pour naviguer dans la documentation.
 * - Changelog et notes de version pour les mises à jour.
 * - Documentation des dépendances, notamment pour OneSignalPush.
 * - Feedback et collaboration encouragés.
 * - Documentation des tests unitaires et scénarios associés.
 * - Ajout de graphiques et de schémas pour une meilleure compréhension.
 */
class AlertRestController extends WP_REST_Controller
{
    /**
     * AlertRestController constructor.
     *
     * Initialise le contrôleur REST.
     */
    public function __construct() {
        $this->namespace = 'amu-ecran-connectee/v1'; // Namespace de l'API.
        $this->rest_base = 'alert'; // Base REST pour les alertes.
    }

    /**
     * Enregistre les routes pour les objets du contrôleur.
     *
     * @return void
     *
     * ### Exemple d'utilisation :
     * ```php
     * $controller = new AlertRestController();
     * $controller->register_routes();
     * ```
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
                            'items'       => array('type' => 'string'),
                            'description' => __('Codes ADE associés à l\'alerte'),
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
                            'type'        => 'array',
                            'items'       => array('type' => 'string'),
                            'description' => __('Codes ADE associés à l\'alerte'),
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
     * @return WP_Error|WP_REST_Response
     *
     * ### Exemples d'utilisation :
     * ```php
     * $request = new WP_REST_Request();
     * $response = $controller->get_items($request);
     * ```
     */
    public function get_items($request) {
        $alert = new Alert(); // Instance du gestionnaire d'alertes.
        return new WP_REST_Response($alert->getList(), 200); // Renvoie la liste des alertes.
    }

    /**
     * Crée une alerte unique.
     *
     * @param WP_REST_Request $request Données complètes sur la requête.
     * @return WP_REST_Response|WP_Error Réponse sur succès, ou WP_Error en cas d'échec.
     *
     * ### Exemples d'utilisation :
     * ```php
     * $request = new WP_REST_Request();
     * $request->set_param('content', 'Votre contenu ici');
     * $response = $controller->create_item($request);
     * ```
     */
    public function create_item($request) {
        $alert = new Alert(); // Instance du gestionnaire d'alertes.

        // Définir les données de l'alerte
        $alert->setAuthor(wp_get_current_user()->ID);
        $alert->setContent($request->get_param('content'));
        $alert->setCreationDate(date('Y-m-d'));
        $alert->setExpirationDate($request->get_param('expiration-date'));

        // Définir les codes ADE de l'alerte
        $ade_codes = $this->find_ade_codes($alert, $request->get_json_params()['codes']);

        if (is_null($ade_codes))
            return new WP_REST_Response(array('message' => 'Un code invalide a été spécifié'), 400);

        $alert->setCodes($ade_codes);

        // Essayer d'insérer l'alerte
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

        return new WP_REST_Response(array('message' => 'Impossible d\'insérer l\'alerte'), 400);
    }

    /**
     * Récupère une alerte unique.
     *
     * @param WP_REST_Request $request Données complètes sur la requête.
     * @return WP_REST_Response|WP_Error Réponse sur succès, ou WP_Error en cas d'échec.
     *
     * ### Exemples d'utilisation :
     * ```php
     * $request = new WP_REST_Request();
     * $request->set_param('id', 1);
     * $response = $controller->get_item($request);
     * ```
     */
    public function get_item($request) {
        $alert = new Alert(); // Instance du gestionnaire d'alertes.

        // Récupérer l'information de la base de données
        $requested_alert = $alert->get($request->get_param('id'));
        if (!$requested_alert)
            return new WP_REST_Response(array('message' => 'Alerte non trouvée'), 404);

        return new WP_REST_Response($requested_alert, 200);
    }

    /**
     * Met à jour une alerte unique.
     *
     * @param WP_REST_Request $request Données complètes sur la requête.
     * @return WP_REST_Response|WP_Error Réponse sur succès, ou WP_Error en cas d'échec.
     *
     * ### Exemples d'utilisation :
     * ```php
     * $request = new WP_REST_Request();
     * $request->set_param('id', 1);
     * $request->set_json_params(array('content' => 'Nouveau contenu'));
     * $response = $controller->update_item($request);
     * ```
     */
    public function update_item($request) {
        $alert = new Alert(); // Instance du gestionnaire d'alertes.

        // Récupérer l'alerte existante
        $existing_alert = $alert->get($request->get_param('id'));
        if (!$existing_alert)
            return new WP_REST_Response(array('message' => 'Alerte non trouvée'), 404);

        // Mettre à jour les champs de l'alerte
        $existing_alert->setContent($request->get_param('content'));
        $existing_alert->setExpirationDate($request->get_param('expiration-date'));

        // Définir les codes ADE de l'alerte
        if (!is_null($request->get_json_params()['codes'])) {
            $ade_codes = $this->find_ade_codes($existing_alert, $request->get_json_params()['codes']);
            if (is_null($ade_codes))
                return new WP_REST_Response(array('message' => 'Un code invalide a été spécifié'), 400);
            $existing_alert->setCodes($ade_codes);
        }

        // Essayer de mettre à jour l'alerte
        if ($existing_alert->update()) {
            return new WP_REST_Response(array('message' => 'Alerte mise à jour'), 200);
        }

        return new WP_REST_Response(array('message' => 'Échec de la mise à jour de l\'alerte'), 400);
    }

    /**
     * Supprime une alerte unique.
     *
     * @param WP_REST_Request $request Données complètes sur la requête.
     * @return WP_REST_Response|WP_Error Réponse sur succès, ou WP_Error en cas d'échec.
     *
     * ### Exemples d'utilisation :
     * ```php
     * $request = new WP_REST_Request();
     * $request->set_param('id', 1);
     * $response = $controller->delete_item($request);
     * ```
     */
    public function delete_item($request) {
        $alert = new Alert(); // Instance du gestionnaire d'alertes.

        // Essayer de supprimer l'alerte
        if ($alert->delete($request->get_param('id'))) {
            return new WP_REST_Response(array('message' => 'Alerte supprimée'), 200);
        }

        return new WP_REST_Response(array('message' => 'Échec de la suppression de l\'alerte'), 400);
    }

    /**
     * Vérifie les permissions pour récupérer la liste des alertes.
     *
     * @param WP_REST_Request $request Données complètes sur la requête.
     * @return bool
     */
    public function get_items_permissions_check($request) {
        return current_user_can('read'); // Vérifie si l'utilisateur a la permission de lire.
    }

    /**
     * Vérifie les permissions pour créer une alerte.
     *
     * @param WP_REST_Request $request Données complètes sur la requête.
     * @return bool
     */
    public function create_item_permissions_check($request) {
        return current_user_can('edit_posts'); // Vérifie si l'utilisateur a la permission de créer.
    }

    /**
     * Vérifie les permissions pour récupérer une alerte.
     *
     * @param WP_REST_Request $request Données complètes sur la requête.
     * @return bool
     */
    public function get_item_permissions_check($request) {
        return current_user_can('read'); // Vérifie si l'utilisateur a la permission de lire.
    }

    /**
     * Vérifie les permissions pour mettre à jour une alerte.
     *
     * @param WP_REST_Request $request Données complètes sur la requête.
     * @return bool
     */
    public function update_item_permissions_check($request) {
        return current_user_can('edit_posts'); // Vérifie si l'utilisateur a la permission de modifier.
    }

    /**
     * Vérifie les permissions pour supprimer une alerte.
     *
     * @param WP_REST_Request $request Données complètes sur la requête.
     * @return bool
     */
    public function delete_item_permissions_check($request) {
        return current_user_can('delete_posts'); // Vérifie si l'utilisateur a la permission de supprimer.
    }

    /**
     * Récupère le schéma public d'une alerte.
     *
     * @return array
     */
    public function get_public_item_schema() {
        return array(
            'title' => 'Alert',
            'type' => 'object',
            'properties' => array(
                'id' => array(
                    'type' => 'integer',
                    'description' => 'Identifiant de l\'alerte.',
                ),
                'content' => array(
                    'type' => 'string',
                    'description' => 'Contenu de l\'alerte.',
                ),
                'creation_date' => array(
                    'type' => 'string',
                    'description' => 'Date de création de l\'alerte.',
                ),
                'expiration_date' => array(
                    'type' => 'string',
                    'description' => 'Date d\'expiration de l\'alerte.',
                ),
                'codes' => array(
                    'type' => 'array',
                    'items' => array(
                        'type' => 'string',
                    ),
                    'description' => 'Codes ADE associés à l\'alerte.',
                ),
            ),
        );
    }

    /**
     * Récupère les codes ADE associés à l'alerte.
     *
     * @param Alert $alert L'alerte concernée.
     * @param array $codes Liste de codes à vérifier.
     * @return array|null Liste des codes ADE valides ou null si un code est invalide.
     */
    private function find_ade_codes(Alert $alert, array $codes) {
        $valid_codes = [];
        $code_ade = new CodeAde();

        foreach ($codes as $code) {
            // Vérifie si le code ADE est valide
            if ($code_ade->exists($code)) {
                $valid_codes[] = $code;
            } else {
                // Un code est invalide, on retourne null
                return null;
            }
        }

        return $valid_codes; // Renvoie la liste des codes valides.
    }
}
