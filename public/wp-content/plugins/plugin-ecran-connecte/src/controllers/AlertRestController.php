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
     * Initialise l'API REST pour la gestion des alertes.
     *
     * Ce constructeur définit le namespace et le chemin de base pour les routes REST liées aux alertes.
     * Ces informations sont utilisées pour enregistrer les routes de l'API dans WordPress.
     *
     * @return void
     *
     * @example
     * // Initialiser l'API REST pour les alertes :
     * $alertController = new AlertController();
     *
     * @version 1.0
     * @date 16-09-2024
     */
    public function __construct() {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'alert';
    }

    /**
     * Enregistre les routes REST pour la gestion des alertes.
     *
     * Cette méthode définit les différentes routes pour les opérations CRUD (Créer, Lire, Mettre à jour,
     * Supprimer) sur les alertes dans l'API REST. Elle enregistre les routes pour obtenir la liste des alertes,
     * créer une nouvelle alerte, récupérer une alerte spécifique par son identifiant, mettre à jour une alerte
     * existante et supprimer une alerte. Pour chaque route, les méthodes, les callbacks, les contrôles de
     * permission et les arguments requis sont spécifiés.
     *
     * @return void
     *
     * @example
     * // Enregistrer les routes pour l'API REST des alertes :
     * $this->register_routes();
     *
     * @version 1.0
     * @date 16-09-2024
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
     * Récupère la liste des alertes.
     *
     * Cette méthode renvoie une réponse de l'API REST contenant une liste de toutes les alertes
     * enregistrées dans le système. Elle utilise le gestionnaire d'alertes pour récupérer les
     * informations nécessaires. Si la récupération des alertes est réussie, elle retourne un
     * code HTTP 200 avec les données des alertes.
     *
     * @param WP_REST_Request $request Les paramètres de la requête REST.
     * @return WP_REST_Response Une réponse de l'API REST contenant la liste des alertes.
     *
     * @example
     * // Récupérer la liste des alertes :
     * $response = $this->get_items($request);
     *
     * @version 1.0
     * @date 16-09-2024
     */
    public function get_items($request) {
        // Obtenir une instance du gestionnaire de codes ADE
        $alert = new Alert();

        return new WP_REST_Response($alert->getList(), 200);
    }

    /**
     * Crée une nouvelle alerte.
     *
     * Cette méthode permet à un utilisateur d'ajouter une nouvelle alerte au système. Elle récupère
     * les données fournies dans la requête, définit les propriétés de l'alerte, y compris l'auteur,
     * le contenu, la date de création et la date d'expiration. Les codes ADE associés à l'alerte
     * sont également définis. En cas d'erreur lors de la création de l'alerte, un message d'erreur
     * approprié est renvoyé. Si l'insertion est réussie, une notification push est envoyée et
     * l'ID de l'alerte créée est retourné.
     *
     * @param WP_REST_Request $request Les paramètres de la requête REST contenant les détails de l'alerte.
     * @return WP_REST_Response Une réponse de l'API REST contenant l'ID de l'alerte créée ou un message d'erreur.
     *
     * @example
     * // Créer une nouvelle alerte :
     * $response = $this->create_item($request);
     *
     * @version 1.0
     * @date 16-09-2024
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
     * Récupère une alerte spécifique par son identifiant.
     *
     * Cette méthode permet de récupérer les détails d'une alerte existante en fonction de son identifiant
     * fourni dans la requête. Si l'alerte est trouvée dans la base de données, ses informations sont renvoyées.
     * Sinon, un message d'erreur est retourné indiquant que l'alerte n'a pas été trouvée.
     *
     * @param WP_REST_Request $request Les paramètres de la requête REST contenant l'identifiant de l'alerte à récupérer.
     * @return WP_REST_Response Une réponse de l'API REST contenant les détails de l'alerte ou un message d'erreur.
     *
     * @example
     * // Récupérer les informations d'une alerte spécifique :
     * $response = $this->get_item($request);
     *
     * @version 1.0
     * @date 16-09-2024
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
     * Met à jour une alerte existante avec les nouvelles données fournies.
     *
     * Cette méthode récupère les détails d'une alerte spécifique à l'aide de son identifiant,
     * puis met à jour son contenu, sa date d'expiration et ses codes ADE si de nouvelles informations
     * sont fournies dans la requête. Si l'alerte n'est pas trouvée ou si les données sont invalides,
     * un message d'erreur approprié est retourné.
     *
     * @param WP_REST_Request $request Les paramètres de la requête REST contenant l'identifiant de l'alerte à mettre à jour
     *                                 ainsi que les nouvelles données (content, expiration-date, codes).
     * @return WP_REST_Response Une réponse de l'API REST indiquant le succès ou l'échec de la mise à jour de l'alerte.
     *
     * @example
     * // Mettre à jour une alerte existante avec de nouvelles informations :
     * $response = $this->update_item($request);
     *
     * @version 1.0
     * @date 16-09-2024
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
     * Supprime une alerte existante identifiée par son identifiant.
     *
     * Cette méthode récupère une alerte spécifique à partir de la base de données
     * à l'aide de son identifiant. Si l'alerte est trouvée et supprimée avec succès,
     * une réponse indiquant le succès est retournée. Si l'alerte n'est pas trouvée
     * ou si la suppression échoue, un message d'erreur approprié est retourné.
     *
     * @param WP_REST_Request $request Les paramètres de la requête REST contenant l'identifiant de l'alerte à supprimer.
     * @return WP_REST_Response Une réponse de l'API REST indiquant le succès ou l'échec de la suppression de l'alerte.
     *
     * @example
     * // Supprimer une alerte existante :
     * $response = $this->delete_item($request);
     *
     * @version 1.0
     * @date 16-09-2024
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

*
    /**
     * Vérifie les permissions pour accéder à la liste des alertes.
     *
     * Cette méthode vérifie si l'utilisateur actuel a les permissions nécessaires
     * pour accéder à la liste des alertes. Seuls les utilisateurs ayant le rôle
     * d'administrateur sont autorisés à effectuer cette action.
     *
     * @param WP_REST_Request $request Les paramètres de la requête REST.
     * @return bool True si l'utilisateur a les permissions nécessaires, sinon false.
     *
     * @example
     * // Vérifier les permissions avant de récupérer les alertes :
     * if ($this->get_items_permissions_check($request)) {
     *     $alerts = $this->get_items($request);
     * }
     *
     * @version 1.0
     * @date 16-09-2024
     */
    public function get_items_permissions_check($request) {
        $current_user = wp_get_current_user();
        return in_array("administrator", $current_user->roles);
    }

    /**
     * Vérifie les permissions pour créer une nouvelle alerte.
     *
     * Cette méthode appelle la méthode de vérification des permissions pour les
     * éléments afin de déterminer si l'utilisateur a les droits nécessaires
     * pour créer une nouvelle alerte. Seuls les utilisateurs ayant le rôle
     * d'administrateur sont autorisés à effectuer cette action.
     *
     * @param WP_REST_Request $request Les paramètres de la requête REST.
     * @return bool True si l'utilisateur a les permissions nécessaires, sinon false.
     *
     * @example
     * // Vérifier les permissions avant de créer une nouvelle alerte :
     * if ($this->create_item_permissions_check($request)) {
     *     $this->create_item($request);
     * }
     *
     * @version 1.0
     * @date 16-09-2024
     */
    public function create_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie les permissions pour récupérer une alerte spécifique.
     *
     * Cette méthode appelle la méthode de vérification des permissions pour les
     * éléments afin de déterminer si l'utilisateur a les droits nécessaires
     * pour accéder aux détails d'une alerte. Seuls les utilisateurs ayant le rôle
     * d'administrateur sont autorisés à effectuer cette action.
     *
     * @param WP_REST_Request $request Les paramètres de la requête REST.
     * @return bool True si l'utilisateur a les permissions nécessaires, sinon false.
     *
     * @example
     * // Vérifier les permissions avant de récupérer les détails d'une alerte :
     * if ($this->get_item_permissions_check($request)) {
     *     $alert = $this->get_item($request);
     * }
     *
     * @version 1.0
     * @date 16-09-2024
     */
    public function get_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie les permissions pour mettre à jour une alerte spécifique.
     *
     * Cette méthode appelle la méthode de vérification des permissions pour les
     * éléments afin de déterminer si l'utilisateur a les droits nécessaires
     * pour mettre à jour les informations d'une alerte. Seuls les utilisateurs
     * ayant le rôle d'administrateur sont autorisés à effectuer cette action.
     *
     * @param WP_REST_Request $request Les paramètres de la requête REST.
     * @return bool True si l'utilisateur a les permissions nécessaires, sinon false.
     *
     * @example
     * // Vérifier les permissions avant de mettre à jour une alerte :
     * if ($this->update_item_permissions_check($request)) {
     *     $this->update_item($request);
     * }
     *
     * @version 1.0
     * @date 16-09-2024
     */
    public function update_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie les permissions pour supprimer une alerte spécifique.
     *
     * Cette méthode appelle la méthode de vérification des permissions pour les
     * éléments afin de déterminer si l'utilisateur a les droits nécessaires
     * pour supprimer une alerte. Seuls les utilisateurs ayant le rôle d'administrateur
     * sont autorisés à effectuer cette action.
     *
     * @param WP_REST_Request $request Les paramètres de la requête REST.
     * @return bool True si l'utilisateur a les permissions nécessaires, sinon false.
     *
     * @example
     * // Vérifier les permissions avant de supprimer une alerte :
     * if ($this->delete_item_permissions_check($request)) {
     *     $this->delete_item($request);
     * }
     *
     * @version 1.0
     * @date 16-09-2024
     */
    public function delete_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Trouve les codes ADE associés à l'alerte.
     *
     * Cette méthode vérifie la validité des codes ADE fournis et met à jour
     * l'alerte pour indiquer si elle s'applique à tout le monde. Elle retourne
     * un tableau de codes ADE valides ou null si un code invalide est trouvé.
     *
     * @param Alert $alert L'objet alerte à mettre à jour.
     * @param array $codes Un tableau contenant les codes ADE à vérifier.
     * @return array|null Un tableau de codes ADE valides ou null si un code est invalide.
     *
     * @example
     * // Trouver et définir les codes ADE pour une alerte :
     * $validCodes = $this->find_ade_codes($alert, $inputCodes);
     * if ($validCodes !== null) {
     *     // Codes valides trouvés, procéder avec l'alerte
     * } else {
     *     // Gérer les codes invalides
     * }
     *
     * @version 1.0
     * @date 2024-09-16
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
