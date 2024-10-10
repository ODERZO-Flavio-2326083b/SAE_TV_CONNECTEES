<?php

namespace Api;

use WP_REST_Controller;

class InformationRestController extends WP_REST_Controller
{
    public function __construct()
    {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'information';
    }
}
