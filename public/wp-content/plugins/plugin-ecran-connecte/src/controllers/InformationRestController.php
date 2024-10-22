<?php

namespace Controllers;

use Models\Information;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * @class InformationRestController
 * @brief Classe pour gérer le contrôleur REST des informations.
 *
 * Cette classe permet de gérer les requêtes REST concernant les
 * informations dans l'application, notamment la création,
 * la récupération, la mise à jour et la suppression d'informations.
 */
class InformationRestController extends WP_REST_Controller
{
    /**
     * Constructeur de la classe.
     *
     * Initialise le namespace et la base REST pour les informations.
     * Ces valeurs sont utilisées pour définir les points de terminaison
     * lors de la création d'API REST dans WordPress.
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function __construct() {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'information';
    }

    /**
     * Enregistre les routes REST pour la gestion des informations.
     *
     * Cette méthode définit les points de terminaison pour créer, lire,
     * mettre à jour et supprimer des éléments d'information via l'API REST.
     * Elle utilise la classe WP_REST_Server pour définir les méthodes HTTP
     * disponibles et leurs permissions respectives. Les arguments
     * pour chaque point de terminaison sont également spécifiés.
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2024-10-16
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
                    'args' => $this->get_collection_params(),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array($this, 'create_item'),
                    'permission_callback' => array($this, 'create_item_permissions_check'),
                    'args' => array(
                        'title' => array(
                            'type' => 'string',
                            'required' => true,
                            'description' => __('Information title'),
                        ),
                        'content' => array(
                            'type' => 'string',
                            'required' => true,
                            'description' => __('Information content'),
                        ),
                        'expiration-date' => array(
                            'type' => 'string',
                            'required' => true,
                            'description' => __('Information expiration date'),
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
                        'description' => __('Unique identifier for the information'),
                        'type' => 'integer',
                    ),
                ),
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_item'),
                    'permission_callback' => array($this, 'get_item_permissions_check'),
                    'args' => array(),
                ),
                array(
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => array($this, 'update_item'),
                    'permission_callback' => array($this, 'update_item_permissions_check'),
                    'args' => array(
                        'title' => array(
                            'type' => 'string',
                            'description' => __('Information title'),
                        ),
                        'content' => array(
                            'type' => 'string',
                            'description' => __('Information content'),
                        ),
                        'expiration-date' => array(
                            'type' => 'string',
                            'description' => __('Information expiration date'),
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
     * Récupère une liste d'éléments d'information.
     *
     * Cette méthode traite la requête pour récupérer les éléments d'information
     * avec un support pour la pagination via les paramètres `offset` et `limit`.
     * Elle crée une instance du gestionnaire d'informations et renvoie
     * une réponse REST avec la liste des informations.
     *
     * @param WP_REST_Request $request L'objet de la requête REST contenant
     *                                  les paramètres pour la récupération des éléments.
     *
     * @return WP_REST_Response Une réponse REST contenant la liste des informations.
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function get_items($request) {
        // Get an instance of the information manager
        $information = new Information();

        // Try to grab offset and limit from parameters
        $offset = $request->get_param('offset');
        $limit = $request->get_param('limit');

        return new WP_REST_Response($information->getList($offset, $limit), 200);
    }

    /**
     * Crée un nouvel élément d'information.
     *
     * Cette méthode traite la requête pour créer un nouvel élément d'information
     * en utilisant les données fournies dans la requête. Elle initialise les
     * propriétés de l'élément d'information, tente de l'insérer dans la base
     * de données, et renvoie une réponse REST contenant l'ID de l'élément créé
     * ou un message d'erreur en cas d'échec.
     *
     * @param WP_REST_Request $request L'objet de la requête REST contenant
     *                                  les données pour créer l'élément.
     *
     * @return WP_REST_Response Une réponse REST contenant l'ID de l'élément créé
     *                          ou un message d'erreur.
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function create_item($request) {
        // Get an instance of the information manager
        $information = new Information();

        // Set information data
        $information->setTitle($request->get_param('title'));
        $information->setAuthor(wp_get_current_user()->ID);
        $information->setCreationDate(date('Y-m-d'));
        $information->setExpirationDate($request->get_param('expiration-date'));
        $information->setAdminId(null);
        $information->setContent($request->get_param('content'));
        $information->setType('text');

        // Try to insert the information
        if (($insert_id = $information->insert()))
            return new WP_REST_Response(array('id' => $insert_id), 200);

        return new WP_REST_Response(array('message' => 'Could not insert the information'), 400);
    }

    /**
     * Récupère un élément d'information spécifique.
     *
     * Cette méthode traite la requête pour récupérer un élément d'information
     * en fonction de son identifiant. Elle interroge la base de données pour
     * obtenir les détails de l'élément demandé et renvoie une réponse REST
     * contenant les informations de l'élément ou un message d'erreur si
     * l'élément n'est pas trouvé.
     *
     * @param WP_REST_Request $request L'objet de la requête REST contenant
     *                                  l'ID de l'élément à récupérer.
     *
     * @return WP_REST_Response Une réponse REST contenant l'élément d'information
     *                          demandé ou un message d'erreur si non trouvé.
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function get_item($request) {
        // Get an instance of the information manager
        $information = new Information();

        // Grab the information from the database
        $requested_info = $information->get($request->get_param('id'));
        if (!$requested_info)
            return new WP_REST_Response(array('message' => 'Information not found'), 404);

        return new WP_REST_Response($requested_info, 200);
    }

    /**
     * Met à jour un élément d'information spécifique.
     *
     * Cette méthode traite la requête pour mettre à jour un élément d'information
     * en fonction de son identifiant. Elle récupère les données existantes,
     * vérifie les paramètres fournis dans la requête, et effectue la mise à jour
     * dans la base de données. En cas de succès, elle renvoie une réponse REST
     * avec un statut 200. Si l'élément n'est pas trouvé ou si la mise à jour échoue,
     * elle renvoie un message d'erreur approprié.
     *
     * @param WP_REST_Request $request L'objet de la requête REST contenant
     *                                  les données à mettre à jour et l'ID de l'élément.
     *
     * @return WP_REST_Response Une réponse REST indiquant le succès ou l'échec
     *                          de la mise à jour de l'élément d'information.
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function update_item($request) {
        // Get an instance of the information manager
        $information = new Information();

        // Grab the information from the database
        $requested_info = $information->get($request->get_param('id'));
        if (!$requested_info)
            return new WP_REST_Response(array('message' => 'Information not found'), 404);

        // Update the information data
        if (is_string($request->get_json_params()['title']))
            $requested_info->setTitle($request->get_json_params()['title']);

        if (is_string($request->get_json_params()['content']))
            $requested_info->setContent($request->get_json_params()['content']);

        if (is_string($request->get_json_params()['expiration-date']))
            $requested_info->setExpirationDate($request->get_json_params()['expiration-date']);

        // Try to update the information
        if ($requested_info->update() > 0)
            return new WP_REST_Response(null, 200);

        return new WP_REST_Response(array('message' => 'Could not update the information'), 400);
    }

    /**
     * Supprime un élément d'information spécifique.
     *
     * Cette méthode traite la requête pour supprimer un élément d'information
     * identifié par son ID. Elle tente de récupérer l'élément de la base de données
     * et, si celui-ci est trouvé, elle procède à sa suppression. En cas de succès,
     * elle renvoie une réponse REST avec un statut 200. Si l'élément n'est pas trouvé
     * ou si la suppression échoue, elle renvoie un message d'erreur approprié.
     *
     * @param WP_REST_Request $request L'objet de la requête REST contenant
     *                                  l'ID de l'élément à supprimer.
     *
     * @return WP_REST_Response Une réponse REST indiquant le succès ou l'échec
     *                          de la suppression de l'élément d'information.
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function delete_item($request) {
        // Get an instance of the information manager
        $information = new Information();

        // Grab the information from the database
        $requested_info = $information->get($request->get_param('id'));
        if ($requested_info && $requested_info->delete())
            return new WP_REST_Response(null, 200);

        return new WP_REST_Response(array('message' => 'Could not delete the information'), 400);
    }

    /**
     * Vérifie les permissions pour récupérer les éléments d'information.
     *
     * Cette méthode détermine si l'utilisateur actuel a les droits nécessaires
     * pour accéder à la liste des éléments d'information. Seuls les utilisateurs
     * ayant le rôle d'administrateur sont autorisés à effectuer cette action.
     * La méthode renvoie true si l'utilisateur a les permissions requises, sinon false.
     *
     * @param WP_REST_Request $request L'objet de la requête REST.
     *
     * @return bool Vrai si l'utilisateur a les permissions nécessaires, faux sinon.
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function get_items_permissions_check($request) {
        $current_user = wp_get_current_user();
        return in_array("administrator", $current_user->roles);
    }

    /**
     * Vérifie les permissions pour créer un nouvel élément d'information.
     *
     * Cette méthode détermine si l'utilisateur actuel a les droits nécessaires
     * pour créer un nouvel élément d'information. La vérification est effectuée
     * en utilisant la méthode `get_items_permissions_check`, qui permet uniquement
     * aux utilisateurs ayant le rôle d'administrateur d'effectuer cette action.
     *
     * @param WP_REST_Request $request L'objet de la requête REST.
     *
     * @return bool Vrai si l'utilisateur a les permissions nécessaires, faux sinon.
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function create_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie les permissions pour récupérer un élément d'information.
     *
     * Cette méthode détermine si l'utilisateur actuel a les droits nécessaires
     * pour récupérer un élément d'information spécifique. La vérification est effectuée
     * en utilisant la méthode `get_items_permissions_check`, qui permet uniquement
     * aux utilisateurs ayant le rôle d'administrateur d'effectuer cette action.
     *
     * @param WP_REST_Request $request L'objet de la requête REST.
     *
     * @return bool Vrai si l'utilisateur a les permissions nécessaires, faux sinon.
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function get_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie les permissions pour mettre à jour un élément d'information.
     *
     * Cette méthode détermine si l'utilisateur actuel a les droits nécessaires
     * pour mettre à jour un élément d'information spécifique. La vérification est effectuée
     * en utilisant la méthode `get_items_permissions_check`, qui permet uniquement
     * aux utilisateurs ayant le rôle d'administrateur d'effectuer cette action.
     *
     * @param WP_REST_Request $request L'objet de la requête REST.
     *
     * @return bool Vrai si l'utilisateur a les permissions nécessaires, faux sinon.
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function update_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Vérifie les permissions pour supprimer un élément d'information.
     *
     * Cette méthode détermine si l'utilisateur actuel a les droits nécessaires
     * pour supprimer un élément d'information spécifique. La vérification est effectuée
     * en utilisant la méthode `get_items_permissions_check`, qui permet uniquement
     * aux utilisateurs ayant le rôle d'administrateur d'effectuer cette action.
     *
     * @param WP_REST_Request $request L'objet de la requête REST.
     *
     * @return bool Vrai si l'utilisateur a les permissions nécessaires, faux sinon.
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function delete_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Récupère les paramètres de collection pour la requête REST.
     *
     * Cette méthode définit les paramètres de requête acceptés pour récupérer
     * une collection d'éléments d'information, notamment la limite du nombre
     * d'éléments à récupérer et l'offset à partir duquel commencer la récupération.
     *
     * @return array Un tableau associatif des paramètres de requête, incluant
     *               'limit' et 'offset', avec des descriptions et des valeurs par défaut.
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function get_collection_params() {
        $query_params = [];

        $query_params['limit'] = array(
            'description' => __('Maximum number of information to fetch'),
            'type' => 'integer',
            'default' => 25,
        );

        $query_params['offset'] = array(
            'description' => __('Offset of the information to fetch'),
            'type' => 'integer',
            'default' => 0,
        );

        return apply_filters('rest_user_collection_params', $query_params);
    }
}
