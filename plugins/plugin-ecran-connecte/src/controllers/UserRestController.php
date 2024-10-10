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
     * Constructor for the REST controller
     */
    public function __construct() {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'user';
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
     * Get a collection of items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_items($request) {
        // Get an instance of the ADE code manager
        $alert = new Alert();

        return new WP_REST_Response($alert->getList(), 200);
    }

    /**
     * Creates a single alert.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
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
     * Retrieves a single alert.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
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
     * Updates a single alert.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
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
     * Deletes a single alert.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
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
     * Finds ADE codes and test their validity in a string array
     *
     * @param Alert $alert Alert to find ADE codes for
     * @param array $codes Array of string containing the ADE codes
     * @return array|null The array of instantiated ADE codes, or null if an error occured
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
