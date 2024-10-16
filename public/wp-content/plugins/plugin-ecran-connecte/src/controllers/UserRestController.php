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
     * Constructeur de la classe.
     *
     * Initialise le namespace et la base REST pour les opérations liées à l'utilisateur.
     * Ce constructeur est utilisé pour configurer les propriétés essentielles de la classe
     * lors de sa création.
     *
     * @return void
     */
    public function __construct() {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'user';
    }

    /**
     * Enregistre les routes REST pour les opérations sur les alertes.
     *
     * Cette méthode utilise la classe `WP_REST_Server` pour définir les routes REST
     * qui permettent de créer, lire, mettre à jour et supprimer des alertes.
     * Les routes sont accessibles via le namespace spécifié dans la classe et
     * incluent des vérifications de permission pour chaque opération.
     *
     * Routes enregistrées :
     * - GET /{namespace}/{rest_base} : Récupère une liste d'alertes.
     * - POST /{namespace}/{rest_base} : Crée une nouvelle alerte.
     * - GET /{namespace}/{rest_base}/{id} : Récupère une alerte spécifique par ID.
     * - PUT/PATCH /{namespace}/{rest_base}/{id} : Met à jour une alerte spécifique par ID.
     * - DELETE /{namespace}/{rest_base}/{id} : Supprime une alerte spécifique par ID.
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
     * Récupère une liste d'alertes.
     *
     * Cette méthode est utilisée pour traiter les requêtes GET vers la route
     * REST qui retourne toutes les alertes disponibles. Elle instancie le
     * gestionnaire de codes ADE et utilise sa méthode pour récupérer la liste
     * des alertes.
     *
     * @param WP_REST_Request $request L'objet de requête contenant les paramètres
     *                                et informations de la requête.
     *
     * @return WP_REST_Response La réponse REST contenant la liste des alertes
     *                          ainsi qu'un code de statut HTTP 200.
     */
    public function get_items($request) {
        // Get an instance of the ADE code manager
        $alert = new Alert();

        return new WP_REST_Response($alert->getList(), 200);
    }

    /**
     * Crée une nouvelle alerte.
     *
     * Cette méthode traite les requêtes POST pour créer une nouvelle alerte.
     * Elle récupère les données de la requête, crée une instance de l'alerte
     * et définit ses propriétés, y compris l'auteur, le contenu, la date de
     * création et la date d'expiration. Les codes ADE sont également associés
     * à l'alerte. Si les codes sont valides et que l'alerte est insérée avec
     * succès, l'ID de la nouvelle alerte est retourné. En cas d'erreur, un
     * message d'erreur est renvoyé.
     *
     * @param WP_REST_Request $request L'objet de requête contenant les paramètres
     *                                et informations nécessaires pour créer l'alerte.
     *
     * @return WP_REST_Response La réponse REST contenant l'ID de l'alerte créée
     *                          si l'insertion réussit, ou un message d'erreur
     *                          avec le code de statut 400 en cas d'échec.
     */
    public function create_item($request) {
        // Get an instance of the alert manager
        $alert = new Alert();

        // Set alert data
        $alert->setAuthor(wp_get_current_user()->ID);
        $alert->setContent($request->get_param('content'));
        $alert->setCreationDate(date('Y-m-d'));
        $alert->setExpirationDate($request->get_param('expiration-date'));

        // Set ADE codes to the alert
        $ade_codes = $this->find_ade_codes($alert, $request->get_json_params()['codes']);

        if (is_null($ade_codes))
            return new WP_REST_Response(array('message' => 'An invalid code was specified'), 400);

        $alert->setCodes($ade_codes);

        // Try to insert the ADE code
        if (($insert_id = $alert->insert()))
            return new WP_REST_Response(array('id' => $insert_id), 200);

        return new WP_REST_Response(array('message' => 'Could not insert the alert'), 400);
    }

    /**
     * Récupère une alerte spécifique à partir de la base de données.
     *
     * Cette méthode traite les requêtes GET pour obtenir une alerte
     * basée sur son ID fourni dans la requête. Elle crée une instance
     * du gestionnaire d'alerte et utilise son ID pour récupérer les
     * informations de l'alerte dans la base de données. Si l'alerte
     * est trouvée, les données de l'alerte sont renvoyées ; sinon,
     * un message d'erreur est retourné avec un code de statut 404.
     *
     * @param WP_REST_Request $request L'objet de requête contenant l'ID
     *                                de l'alerte à récupérer.
     *
     * @return WP_REST_Response La réponse REST contenant les données de l'alerte
     *                          si trouvée, ou un message d'erreur avec le
     *                          code de statut 404 si l'alerte n'existe pas.
     */
    public function get_item($request) {
        // Get an instance of the alert manager
        $alert = new Alert();

        // Grab the information from the database
        $requested_alert = $alert->get($request->get_param('id'));
        if (!$requested_alert)
            return new WP_REST_Response(array('message' => 'Alert not found'), 404);

        return new WP_REST_Response($requested_alert, 200);
    }

    /**
     * Met à jour une alerte existante dans la base de données.
     *
     * Cette méthode traite les requêtes PUT pour modifier les détails
     * d'une alerte spécifiée par son ID. Elle crée une instance du gestionnaire
     * d'alerte et récupère l'alerte à partir de la base de données. Si l'alerte
     * existe, elle met à jour ses propriétés (contenu, date d'expiration, et codes
     * associés) selon les données fournies dans la requête. Si les modifications
     * sont réussies, elle renvoie une réponse avec le code de statut 200 ;
     * sinon, elle renvoie un message d'erreur avec le code de statut 400.
     *
     * @param WP_REST_Request $request L'objet de requête contenant l'ID de l'alerte
     *                                à mettre à jour ainsi que les nouvelles données.
     *
     * @return WP_REST_Response La réponse REST indiquant le succès ou l'échec de
     *                          la mise à jour, avec un code de statut approprié.
     */
    public function update_item($request) {
        // Get an instance of the alert manager
        $alert = new Alert();

        // Grab the information from the database
        $requested_alert = $alert->get($request->get_param('id'));
        if (is_null($requested_alert->getId()))
            return new WP_REST_Response(array('message' => 'Alert not found'), 404);

        // Update the alert data
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

        // Try to update the information
        if ($requested_alert->update() > 0)
            return new WP_REST_Response(null, 200);

        return new WP_REST_Response(array('message' => 'Could not update the alert'), 400);
    }

    /**
     * Supprime une alerte existante de la base de données.
     *
     * Cette méthode traite les requêtes DELETE pour supprimer une alerte
     * spécifiée par son ID. Elle crée une instance du gestionnaire d'alerte
     * et tente de récupérer l'alerte à partir de la base de données. Si l'alerte
     * existe et est supprimée avec succès, elle renvoie une réponse avec le code
     * de statut 200. Sinon, elle renvoie un message d'erreur indiquant que la
     * suppression a échoué, avec un code de statut 400.
     *
     * @param WP_REST_Request $request L'objet de requête contenant l'ID de l'alerte
     *                                à supprimer.
     *
     * @return WP_REST_Response La réponse REST indiquant le succès ou l'échec de
     *                          la suppression, avec un code de statut approprié.
     */
    public function delete_item($request) {
        // Get an instance of the alert manager
        $alert = new Alert();

        // Grab the information from the database
        $requested_alert = $alert->get($request->get_param('id'));
        if ($requested_alert && $requested_alert->delete())
            return new WP_REST_Response(null, 200);

        return new WP_REST_Response(array('message' => 'Could not delete the alert'), 400);
    }

    /**
     * Vérifie les permissions pour accéder aux éléments de l'API.
     *
     * Cette méthode vérifie si l'utilisateur actuel a les droits nécessaires
     * pour récupérer des éléments via l'API REST. Elle renvoie true si l'utilisateur
     * a le rôle d'administrateur, sinon elle renvoie false.
     *
     * @param WP_REST_Request $request L'objet de requête contenant les informations
     *                                 sur la requête de l'API REST.
     *
     * @return bool True si l'utilisateur a les permissions requises, sinon false.
     */
    public function get_items_permissions_check($request) {
        $current_user = wp_get_current_user();
        return in_array("administrator", $current_user->roles);
    }

    /**
     * Vérifie les permissions pour créer un nouvel élément via l'API.
     *
     * Cette méthode utilise la vérification des permissions existantes pour
     * déterminer si l'utilisateur actuel a les droits nécessaires pour
     * créer un nouvel élément dans l'API REST. Elle renvoie true si l'utilisateur
     * a le rôle d'administrateur, sinon elle renvoie false.
     *
     * @param WP_REST_Request $request L'objet de requête contenant les informations
     *                                 sur la requête de l'API REST.
     *
     * @return bool True si l'utilisateur a les permissions requises, sinon false.
     */
    public function create_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie les permissions pour récupérer un élément spécifique via l'API.
     *
     * Cette méthode utilise la vérification des permissions existantes pour
     * déterminer si l'utilisateur actuel a les droits nécessaires pour
     * accéder aux détails d'un élément dans l'API REST. Elle renvoie true si l'utilisateur
     * a le rôle d'administrateur, sinon elle renvoie false.
     *
     * @param WP_REST_Request $request L'objet de requête contenant les informations
     *                                 sur la requête de l'API REST.
     *
     * @return bool True si l'utilisateur a les permissions requises, sinon false.
     */
    public function get_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie les permissions pour mettre à jour un élément spécifique via l'API.
     *
     * Cette méthode déléguée appelle la méthode de vérification des permissions
     * pour les éléments afin de déterminer si l'utilisateur actuel a les droits
     * nécessaires pour mettre à jour un élément dans l'API REST. Elle renvoie true
     * si l'utilisateur a le rôle d'administrateur, sinon elle renvoie false.
     *
     * @param WP_REST_Request $request L'objet de requête contenant les informations
     *                                 sur la requête de l'API REST.
     *
     * @return bool True si l'utilisateur a les permissions requises pour mettre à jour
     *              l'élément, sinon false.
     */
    public function update_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie les permissions pour supprimer un élément spécifique via l'API.
     *
     * Cette méthode déléguée appelle la méthode de vérification des permissions
     * pour les éléments afin de déterminer si l'utilisateur actuel a les droits
     * nécessaires pour supprimer un élément dans l'API REST. Elle renvoie true
     * si l'utilisateur a le rôle d'administrateur, sinon elle renvoie false.
     *
     * @param WP_REST_Request $request L'objet de requête contenant les informations
     *                                 sur la requête de l'API REST.
     *
     * @return bool True si l'utilisateur a les permissions requises pour supprimer
     *              l'élément, sinon false.
     */
    public function delete_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Trouve et valide les codes ADE pour une alerte donnée.
     *
     * Cette méthode prend un tableau de codes et détermine s'ils sont valides
     * en interrogeant le gestionnaire de codes ADE. Si le code 'all' est fourni,
     * cela indique que l'alerte doit être applicable à tout le monde.
     * Les codes invalides ou inexistants entraînent un retour null, tandis que
     * les codes valides sont ajoutés à un tableau qui sera retourné.
     *
     * @param Alert $alert L'objet d'alerte pour lequel les codes ADE sont
     *                     validés et qui est mis à jour en conséquence.
     * @param array $codes Un tableau de codes ADE à valider.
     *
     * @return CodeAde[]|null Un tableau d'objets CodeAde correspondant aux
     *                         codes valides, ou null si un code invalide
     *                         est rencontré.
     */
    private function find_ade_codes($alert, $codes) {
        // Find the ADE codes
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
