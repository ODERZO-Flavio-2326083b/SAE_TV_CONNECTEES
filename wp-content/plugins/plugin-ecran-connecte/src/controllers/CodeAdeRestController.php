<?php

namespace Controllers;

use Models\CodeAde;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class CodeAdeRestController extends WP_REST_Controller
{
    /**
     * Constructor for the REST controller
     */
    public function __construct() {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'ade';
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
                    'args' => array(),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array($this, 'create_item'),
                    'permission_callback' => array($this, 'create_item_permissions_check'),
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
        // Get an instance of the ADE code manager
        $ade_code = new CodeAde();

        return new WP_REST_Response($ade_code->getList(), 200);
    }

    /**
     * Creates a single ADE code.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function create_item($request) {
        // Get an instance of the ADE code manager
        $ade_code = new CodeAde();

        // Set ADE code data
        $ade_code->setTitle($request->get_param('title'));
        $ade_code->setCode($request->get_param('code'));
        $ade_code->setType($request->get_param('type'));

        // Try to insert the ADE code
        if (($insert_id = $ade_code->insert()))
            return new WP_REST_Response(array('id' => $insert_id), 200);

        return new WP_REST_Response(array('message' => 'Could not insert the ADE code'), 400);
    }

    /**
     * Retrieves a single ADE code.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_item($request) {
        // Get an instance of the ADE code manager
        $ade_code = new CodeAde();

        // Grab the information from the database
        $requested_ade_code = $ade_code->get($request->get_param('id'));
        if (!$requested_ade_code)
            return new WP_REST_Response(array('message' => 'ADE code not found'), 404);

        return new WP_REST_Response($requested_ade_code, 200);
    }

    /**
     * Updates a single ADE code.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function update_item($request) {
        // Get an instance of the ADE code manager
        $ade_code = new CodeAde();

        // Grab the information from the database
        $requested_ade_code = $ade_code->get($request->get_param('id'));
        if (!$requested_ade_code)
            return new WP_REST_Response(array('message' => 'ADE code not found'), 404);

        // Update the information data
        if (is_string($request->get_json_params()['title']))
            $requested_ade_code->setTitle($request->get_json_params()['title']);

        if (is_string($request->get_json_params()['code']))
            $requested_ade_code->setCode($request->get_json_params()['code']);

        if (is_string($request->get_json_params()['type']))
            $requested_ade_code->setType($request->get_json_params()['type']);

        // Try to update the information
        if ($requested_ade_code->update() > 0)
            return new WP_REST_Response(null, 200);

        return new WP_REST_Response(array('message' => 'Could not update the ADE code'), 400);
    }

    /**
     * Deletes a single ADE code.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function delete_item($request) {
        // Get an instance of the ADE code manager
        $codeAde = new CodeAde();

        // Grab the information from the database
        $requested_ade_code = $codeAde->get($request->get_param('id'));
        if ($requested_ade_code && $requested_ade_code->delete())
            return new WP_REST_Response(null, 200);

        return new WP_REST_Response(array('message' => 'Could not delete the ADE code'), 400);
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
}
