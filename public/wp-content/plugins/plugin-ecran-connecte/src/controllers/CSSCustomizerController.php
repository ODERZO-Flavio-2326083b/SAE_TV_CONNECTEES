<?php

namespace Controllers;

use Models\CSSCustomizer;
use Models\Department;
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
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->model->updateColor();
        }
        $this->departement = new Department();
        $listDepartement = $this->departement->getAllDepts();
        $listDepName = []; // Initialiser un tableau vide

        foreach ($listDepartement as $e) {
            $listDepName[] = $e->getName(); // Ajouter le nom du département au tableau
        }
        $this->view->displayCssCustomizer($listDepName);
    }

}