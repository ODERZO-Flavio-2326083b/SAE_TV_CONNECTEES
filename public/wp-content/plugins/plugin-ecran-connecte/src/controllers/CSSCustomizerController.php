<?php

namespace Controllers;

use Models\CSSCustomizer;
use Views\CSSView;

class CSSCustomizerController extends Controller
{
    /**
     * @var CSSCustomizer
     */
    private $model;

    /**
     * @var CSSView
     */
    private $view;

    public function __construct()   {
        $this->view = new CSSView();
        $this->model = new CSSCustomizer();
    }

    public function useCssCustomizer()
    {
        $this->model->updateColor();
        $this->view->displayCssCustomizer();
    }

}