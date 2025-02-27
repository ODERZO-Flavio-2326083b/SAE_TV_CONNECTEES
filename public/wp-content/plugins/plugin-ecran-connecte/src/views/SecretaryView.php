<?php
/**
 * Fichier SecretaryView.php
 *
 * Ce fichier contient la classe 'SecretaryView',
 * qui est responsable de l'affichage des vues
 * liées à la gestion des secrétaires dans l'application.
 * Cette classe génère des formulaires
 * permettant de créer des comptes pour des secrétaires,
 * ainsi que des tableaux et des messages
 * pour gérer et afficher ces comptes.
 *
 * PHP version 8.3
 *
 * @category View
 * @package  Views
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/SecretaryView
 * Documentation de la classe
 * @since    2025-01-13
 */
namespace views;

use models\User;

/**
 * Classe SecretaryView
 *
 * Cette classe gère l'affichage des vues
 * liées à la gestion des comptes secrétaires.
 * Elle fournit des formulaires permettant de créer et de gérer les comptes des
 * secrétaires,
 * ainsi que des tableaux pour afficher
 * les secrétaires existants et leurs informations.
 *
 * @category View
 * @package  Views
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 1.0.0
 * @link     https://www.example.com/docs/SecretaryView Documentation de la classe
 * @since    2025-01-13
 */
class SecretaryView extends UserView
{

    /**
     * Affiche le formulaire pour créer un compte secrétaire.
     *
     * Cette méthode génère un formulaire HTML permettant de créer des comptes pour
     * des secrétaires. Elle fournit une brève description et appelle une méthode
     * auxiliaire pour afficher le formulaire de base.
     *
     * @param array    $allDepts Une liste de tous les départements présents
     *                           dans la base de données.
     * @param bool     $isAdmin  Un booléen correspondant à "true"
     *                           si l'utilisateur est un
     *                           administrateur, et "false" sinon.
     * @param int|null $currDept Le numéro du département actuel.
     *
     * @return string Retourne le code HTML du formulaire de création de compte
     *                secrétaire.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayFormSecretary(array $allDepts, bool $isAdmin,
        int $currDept
    ): string {
        return '
        <h2> Compte secrétaire </h2>
        <p class="lead">Pour créer des secrétaires, remplissez ce formulaire avec les
         valeurs demandées.</p>
        ' . $this->displayBaseForm('Secre', $allDepts, $isAdmin, $currDept);
    }

    /**
     * Affiche la page d'accueil de l'administrateur.
     *
     * Cette méthode génère un contenu HTML d'accueil pour les administrateurs,
     * fournissant des informations sur les actions qu'ils peuvent effectuer
     * sur le site.
     * Elle inclut des sections pour créer des informations et des alertes, gérer les
     * informations et alertes existantes, ajouter des utilisateurs, et mettre à jour
     * le système.
     * Les boutons de navigation permettent de rediriger vers les pages appropriées.
     *
     * @return string Retourne le code HTML de la page d'accueil de l'administrateur.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayWelcomeAdmin(): string
    {
        return '
        <div class="row">
            <div class="col-6 mx-auto col-md-6 order-md-1">
                <img src="' . TV_PLUG_PATH . '/public/img/background.png" 
                alt="Logo Amu" class="img-fluid mb-3 mb-md-0">
            </div>
            <div class="col-md-6 order-md-2 text-center text-md-left pr-md-5">
                <h1 class="mb-3 bd-text-purple-bright">' . get_bloginfo("name")
            . '</h1>
                <p class="lead">
                    Bienvenue sur la page d\'accueil des TV connectées d\'AMU !
                    Créer des informations pour toutes les télévisions connectées, 
                    les informations seront affichées sur chaque télévision en plus 
                    des informations déjà publiées sous forme de diaporama.
                    Les informations des télévisions peuvent contenir du texte, des 
                    images, des pdf et même des vidéos.
                </p>
                <p class="lead mb-4">Vous pouvez faire de même avec les alertes des 
                télévisions connectées.</p>
                <p class="lead mb-4">Les informations seront affichés dans la partie 
                de droite des télévisions et les alertes dans la partie rouge en bas 
                des téléviseurs.</p>
                <p class="lead mb-4">Les vidéos sont affichées par-dessus l\'emploi 
                du temps sous forme de diaporama également.</p>

            </div>
        </div>
        <div class="masthead-followup row m-0 border border-white">
            <div class="col-md-6 p-3 p-md-5 bg-light border border-white">
                <h3><img src="' . TV_PLUG_PATH . '/public/img/+.png" 
                alt="Ajouter une information/alerte" class="logo">Ajouter</h3>
                <p>Ajouter une information ou une alerte. Elles seront affichées le 
                lendemain sur les télévisions selon leur département.</p>
                <a href="'
            . esc_url(
                get_permalink(
                    get_page_by_title_custom("Créer une information")
                )
            )
            . '" class="btn btn-lg button_presentation_ecran w-100 mb-3">
                Créer une information</a>
                <hr class="half-rule">
                <a href="'
            . esc_url(
                get_permalink(
                    get_page_by_title_custom("Créer une alerte")
                )
            )
            . '" class="btn btn-lg button_presentation_ecran w-100 mb-3">
                Créer une alerte</a>
            </div>
            <div class="col-md-6 p-3 p-md-5 bg-light border border-white">
                <h3><img src="' . TV_PLUG_PATH . '/public/img/gestion.png" 
                alt="voir les informations/alertes" class="logo">Gérer</h3>
                <p>Voir toutes les informations et alertes déjà publiées. Vous pouvez
                 les supprimer, les modifier ou bien juste les regarder.</p>
                <a href="'
            . esc_url(
                get_permalink(
                    get_page_by_title_custom("Gestion des informations")
                )
            )
            . '" class="btn btn-lg button_presentation_ecran w-100 mb-3">Voir mes 
                informations</a>
                <hr class="half-rule">
                <a href="'
            . esc_url(
                get_permalink(
                    get_page_by_title_custom("Gestion des alertes")
                )
            )
            . '" class="btn btn-lg button_presentation_ecran w-100 mb-3">Voir mes 
                alertes</a>
            </div>
        </div>
        <div class="row">
            <div class="col-6 mx-auto col-md-6 order-md-2">
                <img src="' . TV_PLUG_PATH . '/public/img/user.png" 
                alt="Logo utilisateur" class="img-fluid mb-3 mb-md-0">
            </div>
            <div class="col-md-6 order-md-1 text-center text-md-left pr-md-5">
                <h2 class="mb-3 bd-text-purple-bright">Les utilisateurs</h2>
                <p class="lead">Vous pouvez ajouter des utilisateurs qui pourront à 
                leur tour ajouter des informations et des alertes.</p>
                <p class="lead mb-4">Ils pourront aussi gérer leurs informations et 
                leurs alertes.</p>
                <div class="row mx-n2">
                    <div class="col-md px-2">
                        <a href="'
            . esc_url(
                get_permalink(
                    get_page_by_title_custom("Créer un utilisateur")
                )
            )
            . '" class="btn btn-lg button_presentation_ecran w-100 mb-3">Créer un 
                        utilisateur</a>
                    </div>
                    <div class="col-md px-2">
                        <a href="'
            . esc_url(
                get_permalink(
                    get_page_by_title_custom("Gestion des utilisateurs")
                )
            )
            . '" class="btn btn-lg button_presentation_ecran w-100 mb-3">Voir les 
                        utilisateurs</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6 mx-auto col-md-6 order-md-1">
                <img src="' . TV_PLUG_PATH . '/public/img/update.png" 
                alt="Logo mise à jour" class="img-fluid mb-3 mb-md-0">
            </div>
            <div class="col-md-6 order-md-2 text-center text-md-left pr-md-5">
                <h2 class="mb-3 bd-text-purple-bright">Mettre à jour</h2>
                <p class="lead">Vous pouvez mettre à jour les emplois du temps du 
                site.</p>
                <p class="lead mb-4">Mettre à jour permet aussi de synchroniser les 
                informations et les alertes postées depuis le site de 
                l\'administration</p>
                <form method="post">
                    <button type="submit" 
                    class="btn btn-lg button_presentation_ecran" 
                    name="updatePluginEcranConnecte">Mettre à jour</button>
                </form>
            </div>
        </div>';
    }

    /**
     * Affiche une liste de tous les secrétaires.
     *
     * Cette méthode génère un tableau affichant tous les secrétaires
     * enregistrés sur le système. Pour chaque secrétaire, un numéro de
     * ligne, une case à cocher pour sélectionner l'utilisateur et
     * leur identifiant de connexion (login) sont affichés.
     *
     * @param array $users        Tableau d'objets utilisateur contenant les
     *                            informations des secrétaires.
     * @param array $userDeptList Liste des noms de départements dans le même ordre
     *                            que les users
     * 
     * @return string Retourne le code HTML du tableau listant les secrétaires.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayAllSecretary(array $users, array $userDeptList): string
    {
        $title = 'Secrétaires';
        $name = 'Secre';
        $header = ['Login', 'Département'];

        $row = array();
        $count = 0;
        foreach ($users as $user) {
            ++$count;
            $row[] = [$count, $this->buildCheckbox($name, $user->getId()),
                $user->getLogin(), $userDeptList[$count-1]];
        }

        return $this->displayAll($name, $title, $header, $row, 'Secre');
    }

    /**
     * Affiche un message d'erreur lorsque aucun utilisateur n'est sélectionné.
     *
     * Cette méthode génère un message d'alerte indiquant à l'utilisateur
     * qu'il doit choisir un utilisateur avant de poursuivre.
     * Le message est stylisé pour être affiché comme une alerte rouge.
     *
     * @return string Retourne le code HTML du message d'alerte.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayNoUser(): string
    {
        return '<p class="alert alert-danger">Veuillez choisir un utilisateur.</p>';
    }
}
