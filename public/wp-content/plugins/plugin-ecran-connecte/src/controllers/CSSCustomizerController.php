<?php

namespace controllers;

use models\CSSCustomizer;
use models\Department;
use views\CSSView;

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
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->model->updateColor();
        }
        $departement = new Department();
        $listDepartement = $departement->getAllDepts();
        $listDepName = []; // Initialiser un tableau vide

        foreach ($listDepartement as $e) {
            $listDepName[] = $e->getName(); // Ajouter le nom du département au tableau
        }
        $this->view->displayCssCustomizer($listDepName);
    }

}