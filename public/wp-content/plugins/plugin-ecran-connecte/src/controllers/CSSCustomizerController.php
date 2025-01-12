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
            // Ajouter le nom du département au tableau
            $listDepName[] = $e->getName();
            if(!file_exists(WP_CONTENT_DIR.'/themes/theme-ecran-connecte/assets/css/global/global-'
                .$e->getName().'.css')){
                var_dump($e->getName());
                $cssDefault =
                    file_get_contents(WP_CONTENT_DIR.'/themes/theme-ecran-connecte/assets/css/global/global-default.css');
                file_put_contents(WP_CONTENT_DIR.'/themes/theme-ecran-connecte/assets/css/global/global-'.$e->getName().'.css',$cssDefault);
            }
        }
        $this->view->displayCssCustomizer($listDepName);
    }

}