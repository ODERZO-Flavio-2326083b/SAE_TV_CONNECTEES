<?php

namespace views;

use models\Department;

/**
 * Class UserView
 *
 * Contient les méthodes pour afficher les vues liées aux utilisateurs.
 *
 * @package views
 */
class UserView extends View
{

    /**
     * Génère un formulaire de base pour la création d'un compte utilisateur.
     *
     * Cette méthode crée un formulaire HTML standard contenant des champs pour
     * le login, l'email, le mot de passe et la confirmation du mot de passe.
     * Le formulaire utilise des classes Bootstrap pour le style et inclut
     * également des messages d'aide pour informer l'utilisateur des exigences
     * concernant les valeurs saisies.
     *
     * @param string       $name     Le nom du type d'utilisateur (ex. "Prof",
     *                               "Tech", "Direc") utilisé pour personnaliser les
     *                               IDs et les noms des champs.
     * @param Department[] $allDepts Tous les Départements, pour le menu déroulant de
     *                               sélection
     *                               la base de données.
     * @param bool         $isAdmin  Un booléen correspondant à "true"
     *                               si l'utilisateur est un
     *                               administrateur, et "false" sinon.
     * @param int|null     $currDept Le numéro du département
     *                               actuel.
     *
     * @return string Le code HTML du formulaire.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    protected function displayBaseForm(string $name, array $allDepts,
        bool $isAdmin = false, int $currDept = null
    ): string {
        $disabled = $isAdmin ? '' : 'disabled';

        if (empty($allDepts)) {
            return '<h1> Il n\'y a pas de départements. <br> Merci d\'en créer un 
afin d\'acceder à cette page. </h1>';
        }

        return '
            <form method="post" class="cadre">
                <div class="form-group">
                    <label for="login' . $name . '">Login</label>
                    <input class="form-control" minlength="4" type="text" 
                    name="login' . $name . '" placeholder="Login" required="">
                    <small id="passwordHelpBlock" class="form-text text-muted">Votre 
                    login doit contenir entre 4 et 25 caractère</small>
                </div>
                <div class="form-group">
                    <label for="email' . $name . '">Email</label>
                    <input class="form-control" type="email" name="email' . $name
            . '" placeholder="Email" required="">
                </div>
                <div class="form-group">
                   <label for="pwd' . $name . '">Mot de passe</label>
                    <input class="form-control" minlength="8" maxlength="25" 
                    type="password" id="pwd' . $name . '" name="pwd' . $name . '" 
                    placeholder="Mot de passe" minlength="8" maxlength="25" 
                    required="" onkeyup=checkPwd("' . $name . '")>
                    <input class="form-control" minlength="8" maxlength="25" 
                    type="password" id="pwdConf' . $name . '" name="pwdConfirm'
            . $name . '" placeholder="Confirmer le Mot de passe" minlength="8" 
            maxlength="25" required="" onkeyup=checkPwd("' . $name . '")>
                    <small id="passwordHelpBlock" class="form-text text-muted">Votre 
                    mot de passe doit contenir entre 8 et 25 caractère</small>
                </div>
                <div class="form-group">
                <label for="departementDirec">Département</label>
                <br>    
                <select name="deptId'. $name .'" class="form-control"' . $disabled
            . '>
                    ' . $this->buildDepartmentOptions($allDepts, $currDept) . '
                </select>
            </div>
                <button type="submit" class="btn button_ecran" id="valid' . $name
            . '" name="create' . $name . '">Créer</button>
            </form>';
    }

    /**
     * Affiche le formulaire de modification du mot de passe.
     *
     * Cette méthode génère un formulaire HTML permettant à l'utilisateur
     * de modifier son mot de passe. Le formulaire comprend des champs pour
     * entrer le mot de passe actuel et le nouveau mot de passe.
     * Les classes Bootstrap sont utilisées pour le style.
     *
     * @return string Le code HTML du formulaire de modification du mot de passe.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayModifyPassword(): string
    {
        return '
            <form id="check" method="post">
                <h2>Modifier le mot de passe</h2>
                <label for="verifPwd">Votre mot de passe actuel</label>
                <input type="password" class="form-control text-center" 
                name="verifPwd" placeholder="Mot de passe" required="">
                <label for="newPwd">Votre nouveau mot de passe</label>
                <input type="password" class="form-control text-center" name="newPwd"
                 placeholder="Mot de passe" required="">
                <button type="submit" class="btn button_ecran" name="modifyMyPwd">
                Modifier</button>
            </form>';
    }

    /**
     * Affiche le formulaire de suppression de compte.
     *
     * Cette méthode génère un formulaire HTML permettant à l'utilisateur
     * de supprimer son compte. Le formulaire comprend un champ pour
     * entrer le mot de passe actuel afin de confirmer l'action de suppression.
     * Les classes Bootstrap sont utilisées pour le style.
     *
     * @return string Le code HTML du formulaire de suppression de compte.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayDeleteAccount(): string
    {
        return '
            <form id="check" method="post">
                <h2>Supprimer le compte</h2>
                <label for="verifPwd">Votre mot de passe actuel</label>
                <input type="password" class="form-control text-center" 
                name="verifPwd" placeholder="Mot de passe" required="">
                <button type="submit" class="btn button_ecran" 
                name="deleteMyAccount">Confirmer</button>
            </form>';
    }

    /**
     * Affiche le contexte pour la création d'utilisateurs.
     *
     * Cette méthode génère une section HTML contenant une description
     * des différents types d'utilisateurs qui peuvent être créés dans
     * le système. Chaque type d'utilisateur est brièvement décrit
     * en termes de ses fonctionnalités et accès.
     *
     * Le contenu est organisé en deux colonnes : une pour le texte
     * explicatif et l'autre pour une image d'illustration.
     *
     * @return string Le code HTML du contexte de création d'utilisateurs.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function contextCreateUser(): string
    {
        return '
        <hr class="half-rule">
        <div class="row">
            <div class="col-6 mx-auto col-md-6 order-md-2">
                <img src="' . TV_PLUG_PATH . '/public/img/user.png" alt="Logo 
                utilisateur" class="img-fluid mb-3 mb-md-0">
            </div>
            <div class="col-md-6 order-md-1 text-center text-md-left pr-md-5">
                <h2 class="mb-3 bd-text-purple-bright">Les utilisateurs</h2>
                <p class="lead">Vous pouvez créer ici les utilisateurs</p>
                <p class="lead">Il y a plusieurs types d\'utilisateurs : Les 
                secrétaires, agents d\'entretien, télévisions.</p>
                <p class="lead">Les secrétaires peuvent poster des alertes et des 
                informations. Ils peuvent aussi créer des utilisateurs.</p>
                <p class="lead">Les agents d\'entretien ont accès aux emplois du 
                temps des promotions.</p>
                <p class="lead">Les télévisions sont les utilisateurs utilisés pour 
                afficher ce site sur les téléviseurs. Les comptes télévisions peuvent
                 afficher autant d\'emplois du temps que souhaité.</p>
            </div>
        </div>
        <a href="'
            . esc_url(
                get_permalink(
                    get_page_by_title_custom('Gestion des utilisateurs')
                )
            ) . '">Voir les utilisateurs</a>';
    }

    /**
     * Affiche un formulaire pour entrer le code de suppression de compte.
     *
     * Cette méthode génère un formulaire HTML qui permet à l'utilisateur
     * d'entrer un code de suppression de compte. Ce code est nécessaire
     * pour confirmer la suppression de leur compte. Le formulaire
     * inclut un champ pour entrer le code et un bouton pour soumettre
     * la demande de suppression.
     *
     * @return string Le code HTML du formulaire d'entrée du code de suppression.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayEnterCode(): string
    {
        return '
        <form method="post">
            <label for="codeDelete"> Code de suppression de compte</label>
            <input type="text" class="form-control text-center" name="codeDelete" 
            placeholder="Code à rentrer" required="">
            <button type="submit" name="deleteAccount" class="btn button_ecran">
            Supprimer</button>
        </form>';
    }

    /**
     * Affiche un formulaire pour modifier les emplois du temps de l'utilisateur.
     *
     * Cette méthode génère un formulaire HTML contenant des sélecteurs pour
     * modifier l'année, le groupe et le demi-groupe associés à l'utilisateur.
     * Les options des sélecteurs sont remplies avec des valeurs provenant
     * des paramètres fournis. Les codes existants de l'utilisateur, s'ils
     * sont disponibles, seront pré-remplis dans les sélecteurs correspondants.
     *
     * @param array $codes      Un tableau contenant les codes existants de
     *                          l'utilisateur pour l'année, le groupe et le
     *                          demi-groupe.
     * @param array $years      Un tableau d'objets représentant les années
     *                          disponibles.
     * @param array $groups     Un tableau d'objets représentant les groupes
     *                          disponibles.
     * @param array $halfGroups Un tableau d'objets représentant les demi-groupes
     *                          disponibles.
     *
     * @return string Le code HTML du formulaire de modification des emplois du
     * temps.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayModifyMyCodes(array $codes, array $years, array $groups,
        array $halfGroups
    ): string {
        $form = '
        <form method="post">
            <h2> Modifier mes emplois du temps</h2>
            <label>Année</label>
            <select class="form-control" name="modifYear">';
        if (!empty($codes[0])) {
            $form .= '<option value="' . $codes[0]->getCode() . '">'
                . $codes[0]->getTitle() . '</option>';
        }

        $form .= '<option value="0">Aucun</option>
                  <optgroup label="Année">';

        foreach ($years as $year) {
            $form .= '<option value="' . $year->getCode() . '">' . $year->getTitle()
                . '</option >';
        }
        $form .= '
                </optgroup>
            </select>
            <label>Groupe</label>
            <select class="form-control" name="modifGroup">';

        if (!empty($codes[1])) {
            $form .= '<option value="' . $codes[1]->getCode() . '">'
                . $codes[1]->getTitle() . '</option>';
        }
        $form .= '<option value="0">Aucun</option>
                  <optgroup label="Groupe">';

        foreach ($groups as $group) {
            $form .= '<option value="' . $group->getCode() . '">'
                . $group->getTitle() . '</option>';
        }
        $form .= '
                </optgroup>
            </select>
            <label>Demi-groupe</label>
            <select class="form-control" name="modifHalfgroup">';

        if (!empty($codes[2])) {
            $form .= '<option value="' . $codes[2]->getCode() . '">'
                . $codes[2]->getTitle() . '</option>';
        }
        $form .= '<option value="0"> Aucun</option>
                  <optgroup label="Demi-Groupe">';

        foreach ($halfGroups as $halfGroup) {
            $form .= '<option value="' . $halfGroup->getCode() . '">'
                . $halfGroup->getTitle() . '</option>';
        }
        $form .= '
                </optgroup>
            </select>
            <button name="modifvalider" type="submit" class="btn button_ecran">
            Valider</button>
         </form>';

        return $form;
    }

    /**
     * Affiche un message pour sélectionner un emploi du temps
     *
     * Cette méthode retourne un message demandant à l'utilisateur de choisir
     * un emploi du temps à partir de la liste proposée.
     *
     * @return string Le message à afficher
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function displaySelectSchedule(): string
    {
        return '<p>Veuillez choisir un emploi du temps.</p>';
    }

    /**
     * Affiche la page d'accueil du site.
     *
     * Cette méthode génère le code HTML pour la section d'accueil de l'application,
     * incluant un logo et un message de bienvenue. Elle utilise Bootstrap pour le
     * style et s'assure que le contenu est bien aligné et réactif sur différentes
     * tailles d'écran.
     *
     * @return string Le code HTML de la page d'accueil.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayHome(): string
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
                <p class="lead">Bienvenue sur le site AMU des télévisions connectées 
                !</p>
                <p class="lead mb-4">Accédez à votre emploi du temps tant en recevant
                 diverses informations, ou alertes de la part de votre département.
                 </p>
            </div>
        </div>';
    }

    /**
     * Affiche un message pour la modification réussie du mot de passe
     *
     * Cette méthode génère un modal de succès pour informer l'utilisateur que
     * la modification de son mot de passe a été effectuée avec succès.
     *
     * @return void
     *
     * Affiche un modal avec un message de confirmation indiquant que la modification
     * du mot de passe a été réussie et redirige l'utilisateur vers la page
     * d'accueil.
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function displayModificationPassValidate(): void
    {
        $this->buildModal(
            'Modification du mot de passe', '<div class="alert 
alert-success" role="alert">La modification à été réussie !</div>', home_url()
        );
    }

    /**
     * Affiche un message si le mot de passe est incorrect
     *
     * Cette méthode affiche un message d'erreur dans une fenêtre modale
     * lorsque l'utilisateur entre un mot de passe incorrect.
     *
     * @return void
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function displayWrongPassword(): void
    {
        $this->buildModal(
            'Mot de passe incorrect', '<div class="alert alert-danger">
Mauvais mot de passe.</div>'
        );
    }

    /**
     * Affiche un message si le mail a été envoyé
     *
     * Cette méthode affiche un message de succès dans une fenêtre modale
     * lorsque le mail a été correctement envoyé à l'utilisateur avec un code de
     * vérification.
     *
     * @return void
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function displayMailSend(): void
    {
        $this->buildModal(
            'Mail envoyé', '<div class="alert alert-success"> Un mail 
a été envoyé à votre adresse mail, merci de bien vouloir entrer le code reçu.</div>'
        );
    }

    /**
     * Message pour prévenir qu'une inscription a échoué
     *
     * Cette méthode affiche un message d'erreur dans une fenêtre modale
     * lorsque l'inscription d'un utilisateur échoue à cause d'une erreur dans les
     * informations soumises.
     *
     * @return void
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function displayErrorCreation(): void
    {
        $this->buildModal(
            'Inscription échouée', '<div class="alert alert-danger">Il 
y a eu une erreur dans le formulaire, veuillez vérifier vos informations et 
réessayer.</div>'
        );
    }

    /**
     * Message pour prévenir qu'un login existe déjà
     *
     * Cette méthode affiche un message d'erreur dans une fenêtre modale
     * lorsque le login soumis par un utilisateur est déjà utilisé par un autre
     * compte.
     *
     * @return void
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function displayErrorLogin(): void
    {
        $this->buildModal(
            'Inscription échouée', '<div class="alert alert-danger"> Le
 login est déjà utilisé ! </div>'
        );
    }

    /**
     * Affiche un message pour indiquer qu'il n'y a pas de cours aujourd'hui
     *
     * Cette méthode retourne un message pour informer l'utilisateur qu'il n'y a pas
     * de cours
     * prévu pour la journée. Cela peut être utilisé dans un tableau de bord ou une
     * interface de gestion.
     *
     * @return string Le message à afficher
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function displayNoStudy(): string
    {
        return '<p>Vous n\'avez pas cours !</p>';
    }

    /**
     * Affiche un message d'erreur si l'utilisateur n'a pas de code d'enregistrement.
     *
     * Cette méthode génère un message informatif pour l'utilisateur courant,
     * l'informant qu'il est enregistré sans aucun emploi du temps. Elle
     * l'invite à se rendre sur son compte pour attribuer un code d'accès
     * à son emploi du temps.
     *
     * @return string Le code HTML du message d'erreur.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function errorMessageNoCodeRegister(): string
    {
        $current_user = wp_get_current_user();
        return '
        <h2>' . $current_user->user_login . '</h2>
        <p>Vous êtes enregistré sans aucun emploi du temps, rendez-vous sur votre 
        compte pour pouvoir vous attribuer un code afin d\'accéder à votre emploi du 
        temps.</p>';
    }

    /**
     * Affiche un message de succès lors du changement de code
     *
     * Cette méthode génère un modal de succès qui informe l'utilisateur que
     * le changement de groupe a bien été effectué et pris en compte.
     *
     * @return void
     *
     * Affiche un modal avec un message de confirmation sur la réussite de l'action.
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function successMesageChangeCode(): void
    {
        $this->buildModal(
            'Modification validée', '<div class="alert alert-success"> 
Le changement de groupe a été pris en compte.</div>'
        );
    }

    /**
     * Affiche un message d'erreur lors du changement de code
     *
     * Cette méthode affiche un message d'erreur sous forme de modal lorsque le
     * changement de code ou de groupe échoue. Elle informe l'utilisateur que la
     * modification n'a pas été prise en compte.
     *
     * @return void
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function errorMesageChangeCode(): void
    {
        $this->buildModal(
            'Modification échouée', '<div class="alert alert-danger"> 
Le changement de groupe n\'a pas été pris en compte.</div>'
        );
    }
}
