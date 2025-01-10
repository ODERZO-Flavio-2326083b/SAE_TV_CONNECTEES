<?php

namespace controllers\rest;

use models\CodeAde;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Contrôleur REST pour la gestion des codes ADE.
 *
 * Cette classe implémente les fonctionnalités nécessaires pour gérer les
 * opérations CRUD (Créer, Lire, Mettre à jour, Supprimer) sur les codes ADE
 * via l'API REST de WordPress. Elle étend la classe `WP_REST_Controller` pour
 * s'intégrer à l'infrastructure REST de WordPress et suit le namespace
 * 'amu-ecran-connectee/v1' pour organiser les points de terminaison.
 *
 * Principales fonctionnalités :
 * - Enregistrement des routes REST pour les codes ADE.
 * - Gestion des requêtes REST (GET, POST, PUT, DELETE) pour les codes ADE.
 * - Vérification des permissions pour sécuriser l'accès aux ressources.
 *
 * Les routes REST définies permettent :
 * - Récupération de tous les codes ADE.
 * - Création d'un nouveau code ADE.
 * - Lecture, mise à jour et suppression d'un code ADE spécifique par ID.
 *
 * Les contrôleurs communiquent avec le modèle `CodeAde` pour effectuer
 * les opérations sur la base de données.
 *
 * @package controllers\rest
 */
class CodeAdeRestController extends WP_REST_Controller
{
    /**
     * Initialise une nouvelle instance de la classe.
     *
     * Ce constructeur configure le namespace et la base REST pour les
     * opérations de l'API. Il définit le namespace comme 'amu-ecran-connectee/v1'
     * et le chemin de base REST comme 'ade'.
     *
     * @version 1.0
     * @date    2024-09-16
     */
    public function __construct()
    {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'ade';
    }

    /**
     * Enregistre les routes REST pour les opérations sur les codes ADE.
     *
     * Cette méthode configure les routes de l'API REST sous le namespace
     * 'amu-ecran-connectee/v1' et le chemin de base 'ade'. Elle permet de définir
     * les routes nécessaires pour effectuer des opérations CRUD (Créer, Lire,
     * Mettre à jour, Supprimer) sur les codes ADE. Chaque route est associée à un
     * ensemble spécifique de méthodes HTTP, un callback de traitement,
     * une vérification
     * des permissions, et des arguments requis ou optionnels.
     *
     * Routes définies :
     * - GET /amu-ecran-connectee/v1/ade : Récupère tous les codes ADE.
     * - POST /amu-ecran-connectee/v1/ade : Crée un nouveau code ADE.
     * - GET /amu-ecran-connectee/v1/ade/{id} : Récupère un code ADE
     * spécifique par ID.
     * - PUT /amu-ecran-connectee/v1/ade/{id} : Met à jour un code ADE
     * spécifique par ID.
     * - DELETE /amu-ecran-connectee/v1/ade/{id} : Supprime un code ADE
     * spécifique par ID.
     *
     * Les callbacks gèrent les différentes opérations et vérifient les permissions
     * via des méthodes spécifiques comme `getItemsPermissionsCheck` ou
     * `createItemPermissionsCheck`.
     * Les schémas publics pour les données retournées ou attendues sont définis dans
     * `get_public_item_schema`.
     *
     * @return void
     *
     * @version 1.0
     * @date    2024-09-16
     */
    public function registerRoutes()
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            array(
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => array($this, 'getItems'),
                    'permission_callback' => array($this,
                        'getItemsPermissionsCheck'),
                    'args' => array(),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array($this, 'createItem'),
                    'permission_callback' => array($this,
                        'createItemPermissionsCheck'),
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
                    'callback' => array($this, 'getItem'),
                    'permission_callback' => array($this,
                        'getItemPermissionsCheck'),
                    'args' => array(),
                ),
                array(
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => array($this, 'updateItem'),
                    'permission_callback' => array($this,
                        'updateItemPermissionsCheck'),
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
                    'callback' => array($this, 'deleteItem'),
                    'permission_callback' => array($this,
                        'deleteItemPermissionsCheck'),
                    'args' => array()
                ),
                'schema' => array($this, 'get_public_item_schema'),
            )
        );
    }

    /**
     * Récupère tous les codes ADE.
     *
     * Cette méthode traite la requête pour obtenir la liste des codes ADE
     * à partir de l'instance du gestionnaire de codes ADE. Elle renvoie
     * une réponse au format WP_REST_Response contenant la liste des codes
     * ADE avec un code de statut HTTP 200 (OK).
     *
     * @param WP_REST_Request $request La requête de l'API REST.
     *
     * @return WP_REST_Response La réponse contenant la liste des codes ADE.
     *
     * @version 1.0
     * @date    2024-09-16
     */
    public function getItems($request)
    {
        // Obtenir une instance du gestionnaire de code ADE
        $ade_code = new CodeAde();

        return new WP_REST_Response($ade_code->getList(), 200);
    }

    /**
     * Crée un nouveau code ADE.
     *
     * Cette méthode traite la requête pour créer un nouveau code ADE en
     * utilisant les données fournies dans la requête. Elle crée une
     * instance du gestionnaire de code ADE, définit les propriétés du
     * code ADE à partir des paramètres de la requête, puis essaie
     * d'insérer le code dans la base de données. Si l'insertion est
     * réussie, elle retourne l'ID du nouveau code ADE avec un code de
     * statut HTTP 200 (OK). Sinon, elle retourne un message d'erreur
     * avec un code de statut HTTP 400 (Bad Request).
     *
     * @param WP_REST_Request $request La requête de l'API REST contenant
     *                                 les données du code ADE.
     *
     * @return WP_REST_Response La réponse contenant l'ID du code ADE
     *                          créé ou un message d'erreur.
     *
     * @version 1.0
     * @date    2024-09-16
     */
    public function createItem($request)
    {
        // Obtenir une instance du gestionnaire de code ADE
        $ade_code = new CodeAde();

        // Définir les données du code ADE
        $ade_code->setTitle($request->get_param('title'));
        $ade_code->setCode($request->get_param('code'));
        $ade_code->setType($request->get_param('type'));

        // Essayer d'insérer le code ADE
        if (($insert_id = $ade_code->insert())) {
            return new WP_REST_Response(array('id' => $insert_id), 200);
        }

        return new WP_REST_Response(
            array('message' => 'Could not insert the ADE code'), 400
        );
    }

    /**
     * Récupère un code ADE spécifique.
     *
     * Cette méthode traite la requête pour obtenir les détails d'un
     * code ADE en fonction de son identifiant unique fourni dans la
     * requête. Elle crée une instance du gestionnaire de code ADE,
     * puis tente de récupérer les informations correspondantes
     * depuis la base de données. Si le code ADE est trouvé, la méthode
     * retourne les informations avec un code de statut HTTP 200 (OK).
     * Si le code ADE n'est pas trouvé, elle retourne un message
     * d'erreur avec un code de statut HTTP 404 (Not Found).
     *
     * @param WP_REST_Request $request La requête de l'API REST contenant
     *                                 l'identifiant du code ADE à récupérer.
     *
     * @return WP_REST_Response La réponse contenant les détails du code
     *                          ADE ou un message d'erreur si non trouvé.
     *
     * @version 1.0
     * @date    2024-09-16
     */
    public function getItem($request)
    {
        // Obtenir une instance du gestionnaire de code ADE
        $ade_code = new CodeAde();

        // Récupérer les informations de la base de données
        $requested_ade_code = $ade_code->get($request->get_param('id'));
        if (!$requested_ade_code) {
            return new WP_REST_Response(
                array('message' => 'ADE code not found'), 404
            );
        }

        return new WP_REST_Response($requested_ade_code, 200);
    }

    /**
     * Met à jour un code ADE spécifique.
     *
     * Cette méthode traite la requête pour mettre à jour les détails
     * d'un code ADE existant en fonction de son identifiant unique
     * fourni dans la requête. Elle crée une instance du gestionnaire
     * de code ADE et récupère les informations correspondantes depuis
     * la base de données. Si le code ADE est trouvé, les données
     * (titre, code et type) peuvent être mises à jour si elles sont
     * valides. Si la mise à jour est réussie, la méthode retourne un
     * code de statut HTTP 200 (OK). En cas d'échec ou si le code ADE
     * n'est pas trouvé, elle retourne un message d'erreur avec un
     * code de statut approprié.
     *
     * @param WP_REST_Request $request La requête de l'API REST contenant
     *                                 l'identifiant du code ADE à mettre à jour
     *                                 ainsi que les nouvelles valeurs.
     *
     * @return WP_REST_Response Une réponse indiquant le succès de la mise à jour
     *                          ou un message d'erreur si le code ADE n'a pas pu
     *                          être mis à jour.
     *
     * @version 1.0
     * @date    2024-09-16
     */
    public function updateItem($request)
    {
        // Obtenir une instance du gestionnaire de code ADE
        $ade_code = new CodeAde();

        // Récupérer les informations de la base de données
        $requested_ade_code = $ade_code->get($request->get_param('id'));
        if (!$requested_ade_code) {
            return new WP_REST_Response(
                array('message' => 'ADE code not found'), 404
            );
        }

        // Mettre à jour les données
        if (is_string($request->get_json_params()['title'])) {
            $requested_ade_code->setTitle($request->get_json_params()['title']);
        }

        if (is_string($request->get_json_params()['code'])) {
            $requested_ade_code->setCode($request->get_json_params()['code']);
        }

        if (is_string($request->get_json_params()['type'])) {
            $requested_ade_code->setType($request->get_json_params()['type']);
        }

        // Essayer de mettre à jour les informations
        if ($requested_ade_code->update() > 0) {
            return new WP_REST_Response(null, 200);
        }

        return new WP_REST_Response(
            array('message' => 'Could not update the ADE code'), 400
        );
    }

    /**
     * Supprime un code ADE spécifique.
     *
     * Cette méthode traite la requête pour supprimer un code ADE
     * existant identifié par son identifiant unique fourni dans la
     * requête. Elle crée une instance du gestionnaire de code ADE
     * et récupère les informations correspondantes depuis la base de
     * données. Si le code ADE est trouvé et qu'il est supprimé avec
     * succès, la méthode retourne une réponse HTTP avec le code
     * statut 200 (OK). En cas d'échec, elle retourne un message d'erreur
     * avec un code de statut approprié.
     *
     * @param WP_REST_Request $request La requête de l'API REST contenant
     *                                 l'identifiant du code ADE à supprimer.
     *
     * @return WP_REST_Response Une réponse indiquant le succès de la
     *                          suppression ou un message d'erreur si le
     *                          code ADE n'a pas pu être supprimé.
     *
     * @version 1.0
     * @date    2024-09-16
     */
    public function deleteItem($request)
    {
        // Obtenir une instance du gestionnaire de code ADE
        $codeAde = new CodeAde();

        // Récupérer les informations de la base de données
        $requested_ade_code = $codeAde->get($request->get_param('id'));
        if ($requested_ade_code && $requested_ade_code->delete()) {
            return new WP_REST_Response(null, 200);
        }

        return new WP_REST_Response(
            array('message' => 'Could not delete the ADE code'), 400
        );
    }

    /**
     * Vérifie les permissions pour accéder aux éléments.
     *
     * Cette méthode détermine si l'utilisateur actuel a les droits
     * nécessaires pour accéder à la liste des codes ADE. Elle vérifie
     * si l'utilisateur a le rôle d'administrateur. Si l'utilisateur
     * est un administrateur, la méthode retourne true, permettant
     * l'accès à la ressource; sinon, elle retourne false.
     *
     * @param WP_REST_Request $request La requête de l'API REST contenant
     *                                 les informations de la requête actuelle.
     *
     * @return bool Retourne true si l'utilisateur a l'autorisation
     *              d'accéder aux éléments, sinon false.
     *
     * @version 1.0
     * @date    2024-09-16
     */
    public function getItemsPermissionsCheck($request)
    {
        $current_user = wp_get_current_user();
        return in_array("administrator", $current_user->roles);
    }

    /**
     * Vérifie les permissions pour créer un nouvel élément.
     *
     * Cette méthode appelle la méthode de vérification des permissions
     * pour obtenir les autorisations d'accès à la liste des codes ADE.
     * Si l'utilisateur a les droits d'administrateur, il pourra
     * créer un nouvel élément; sinon, l'accès sera refusé.
     *
     * @param WP_REST_Request $request La requête de l'API REST contenant
     *                                 les informations de la requête actuelle.
     *
     * @return bool Retourne true si l'utilisateur a l'autorisation
     *              de créer un nouvel élément, sinon false.
     *
     * @version 1.0
     * @date    2024-09-16
     */
    public function createItemPermissionsCheck($request)
    {
        return $this->getItemsPermissionsCheck($request);
    }

    /**
     * Vérifie les permissions pour récupérer un élément spécifique.
     *
     * Cette méthode appelle la méthode de vérification des permissions
     * pour s'assurer que l'utilisateur a le droit d'accéder aux
     * informations sur un code ADE particulier. Seuls les utilisateurs
     * disposant des droits d'administrateur peuvent récupérer les
     * informations.
     *
     * @param WP_REST_Request $request La requête de l'API REST contenant
     *                                 les informations de la requête actuelle.
     *
     * @return bool Retourne true si l'utilisateur a l'autorisation
     *              de récupérer l'élément, sinon false.
     *
     * @version 1.0
     * @date    2024-09-16
     */
    public function getItemPermissionsCheck($request)
    {
        return $this->getItemsPermissionsCheck($request);
    }

    /**
     * Vérifie les permissions pour mettre à jour un élément spécifique.
     *
     * Cette méthode appelle la méthode de vérification des permissions
     * pour s'assurer que l'utilisateur a le droit de mettre à jour
     * les informations d'un code ADE particulier. Seuls les utilisateurs
     * disposant des droits d'administrateur peuvent procéder à la
     * mise à jour.
     *
     * @param WP_REST_Request $request La requête de l'API REST contenant
     *                                 les informations de la requête actuelle.
     *
     * @return bool Retourne true si l'utilisateur a l'autorisation
     *              de mettre à jour l'élément, sinon false.
     *
     * @version 1.0
     * @date    2024-09-16
     */
    public function updateItemPermissionsCheck($request)
    {
        return $this->getItemsPermissionsCheck($request);
    }

    /**
     * Vérifie les permissions pour supprimer un élément spécifique.
     *
     * Cette méthode appelle la méthode de vérification des permissions
     * pour s'assurer que l'utilisateur a le droit de supprimer
     * un code ADE spécifique. Seuls les utilisateurs disposant des
     * droits d'administrateur peuvent procéder à la suppression.
     *
     * @param WP_REST_Request $request La requête de l'API REST contenant
     *                                 les informations de la requête actuelle.
     *
     * @return bool Retourne true si l'utilisateur a l'autorisation
     *              de supprimer l'élément, sinon false.
     *
     * @version 1.0
     * @date    2024-09-16
     */
    public function deleteItemPermissionsCheck($request)
    {
        return $this->getItemsPermissionsCheck($request);
    }
}
