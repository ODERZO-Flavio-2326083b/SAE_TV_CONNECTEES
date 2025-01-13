<?php

namespace controllers;

use models\CSSCustomizer;
use models\Department;
use views\CSSView;

/**
 * Class CSSCustomizerController
 *
 * Contrôleur pour la personnalisation CSS.
 * Gère les interactions entre le modèle de personnalisation CSS et les vues associées.
 * Permet aux utilisateurs de modifier les couleurs via un formulaire.
 *
 * @package controllers
 */
class CSSCustomizerController extends Controller
{

    /**
     * @var CSSCustomizer
     */
    private CSSCustomizer $_model;


    /**
     * @var CSSView
     */
    private CSSView $_view;

    /**
     * Constructeur de la classe CSSCustomizerController.
     *
     * Initialise les instances de vue et de modèle pour la personnalisation CSS.
     * La vue est utilisée pour afficher l'interface utilisateur, et le modèle
     * gère les opérations de personnalisation CSS.
     *
     * @version 1.0
     * @date    2024-10-16
     */
    public function __construct()
    {
        $this->_view = new CSSView();
        $this->_model = new CSSCustomizer();
    }

    /**
     * Gère l'affichage et la personnalisation des styles CSS.
     *
     * Cette méthode vérifie si une requête POST a été effectuée pour mettre à jour
     * les couleurs via le modèle. Elle récupère ensuite la liste des départements,
     * extrait leurs noms et transmet ces données à la vue pour afficher
     * l'interface de personnalisation CSS.
     *
     * @return void
     *
     * @version 1.0
     * @date    2024-10-16
     */
    public function useCssCustomizer(): void
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->_model->updateColor();
        }
        $departement = new Department();
        $listDepartement = $departement->getAllDepts();
        $listDepName = []; // Initialiser un tableau vide

        foreach ($listDepartement as $e) {
            // Ajouter le nom du département au tableau
            $listDepName[] = $e->getName();
            if(!file_exists(WP_CONTENT_DIR
                            .'/themes/theme-ecran-connecte/assets/css/global/global-'
                            .$e->getName().'.css')){
                $cssDefault = file_get_contents(
                    WP_CONTENT_DIR.'/themes/theme-ecran-connecte/assets/
                    css/global/global-default.css');
                file_put_contents(WP_CONTENT_DIR.'/themes/theme-ecran-connecte
                /assets/css/global/global-'.$e->getName().'.css',$cssDefault);
            }
        }
        $this->_view->displayContextCSS();
        $this->_view->displayCssCustomizer($listDepName);
    }

}
