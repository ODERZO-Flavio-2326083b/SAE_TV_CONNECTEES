<?php

namespace controllers;

use Controllers\Controller;

class CSSCustomizerController extends Controller
{
    public function __construct($view,$model)   {
        $this->view = $view;
        $this->model = $model;
    }

    public function displayCssCustomizer()
    {

        $this->view->displayCssCustomizer();
    }

}