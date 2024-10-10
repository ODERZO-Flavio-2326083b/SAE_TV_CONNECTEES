<?php

namespace Controllers;

use Models\Information;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class InformationRestController extends WP_REST_Controller
{
    /**
     * Constructor for the REST controller
     */
    public function __construct() {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'information';
    }

    /**
     * Register the routes for the objects of the controller.
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
                    'args' => null,
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
     * Get a collection of items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
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
     * Creates a single information.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
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
     * Retrieves a single information.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
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
     * Updates a single information.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
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
     * Deletes a single information.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
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
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_items_permissions_check($request) {
        $current_user = wp_get_current_user();
        return in_array("administrator", $current_user->roles);
    }

    /**
     * Checks if a given request has access to create an information.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
     */
    public function create_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Checks if a given request has access to read an information.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access for the item, otherwise WP_Error object.
     */
    public function get_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Checks if a given request has access to update a single information.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has access to update the item, WP_Error object otherwise.
     */
    public function update_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Checks if a given request has access delete an information.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has access to delete the item, WP_Error object otherwise.
     */
    public function delete_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Retrieves the query params for collections.
     *
     * @return array Collection parameters.
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
