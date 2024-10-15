<?php

namespace Views;

use Models\CodeAde;
use Models\User;

/**
 * Class UserView
 *
 * Contient les méthodes pour afficher les vues liées aux utilisateurs.
 *
 * @package Views
 */
class UserView extends View
{
    /**
     * Affiche un formulaire de création
     *
     * @param string $name Le nom de l'utilisateur
     *
     * @return string Le code HTML du formulaire de création
     */
    protected function displayBaseForm($name) {
        return '
            <form method="post" class="cadre">
                <div class="form-group">
                    <label for="login' . $name . '">Login</label>
                    <input class="form-control" minlength="4" type="text" name="login' . $name . '" placeholder="Login" required="">
                    <small id="passwordHelpBlock" class="form-text text-muted">Votre login doit contenir entre 4 et 25 caractère</small>
                </div>
                <div class="form-group">
                    <label for="email' . $name . '">Email</label>
                    <input class="form-control" type="email" name="email' . $name . '" placeholder="Email" required="">
                </div>
                <div class="form-group">
               	<label for="pwd' . $name . '">Mot de passe</label>
                	<input class="form-control" minlength="8" maxlength="25" type="password" id="pwd' . $name . '" name="pwd' . $name . '" placeholder="Mot de passe" minlength="8" maxlength="25" required="" onkeyup=checkPwd("' . $name . '")>
                    <input class="form-control" minlength="8" maxlength="25" type="password" id="pwdConf' . $name . '" name="pwdConfirm' . $name . '" placeholder="Confirmer le Mot de passe" minlength="8" maxlength="25" required="" onkeyup=checkPwd("' . $name . '")>
                	<small id="passwordHelpBlock" class="form-text text-muted">Votre mot de passe doit contenir entre 8 et 25 caractère</small>                </div>
                <button type="submit" class="btn button_ecran" id="valid' . $name . '" name="create' . $name . '">Créer</button>
            </form>';
    }

    /**
     * Formulaire pour modifier le mot de passe
     *
     * @return string Le code HTML du formulaire de modification du mot de passe
     */
    public function displayModifyPassword() {
        return '
            <form id="check" method="post">
                <h2>Modifier le mot de passe</h2>
                <label for="verifPwd">Votre mot de passe actuel</label>
                <input type="password" class="form-control text-center" name="verifPwd" placeholder="Mot de passe" required="">
                <label for="newPwd">Votre nouveau mot de passe</label>
                <input type="password" class="form-control text-center" name="newPwd" placeholder="Mot de passe" required="">
                <button type="submit" class="btn button_ecran" name="modifyMyPwd">Modifier</button>
            </form>';
    }

    /**
     * Formulaire pour générer un code pour supprimer le compte
     *
     * @return string Le code HTML du formulaire de suppression de compte
     */
    public function displayDeleteAccount() {
        return '
            <form id="check" method="post">
                <h2>Supprimer le compte</h2>
                <label for="verifPwd">Votre mot de passe actuel</label>
                <input type="password" class="form-control text-center" name="verifPwd" placeholder="Mot de passe" required="">
                <button type="submit" class="btn button_ecran" name="deleteMyAccount">Confirmer</button>
            </form>';
    }

    /**
     * Affiche le contexte pour créer un utilisateur
     *
     * @return string Le code HTML du contexte de création d'utilisateur
     */
    public function contextCreateUser() {
        return '
        <hr class="half-rule">
        <div class="row">
            <div class="col-6 mx-auto col-md-6 order-md-2">
                <img src="' . TV_PLUG_PATH . '/public/img/user.png" alt="Logo utilisateur" class="img-fluid mb-3 mb-md-0">
            </div>
            <div class="col-md-6 order-md-1 text-center text-md-left pr-md-5">
                <h2 class="mb-3 bd-text-purple-bright">Les utilisateurs</h2>
                <p class="lead">Vous pouvez créer ici les utilisateurs</p>
                <p class="lead">Il y a plusieurs types d\'utilisateur : Les étudiants, enseignants, directeurs d\'études, secrétaires, techniciens, télévisions.</p>
                <p class="lead">Les étudiants ont accès à leur emploi du temps et reçoivent les alertes les concernant et les informations.</p>
                <p class="lead">Les enseignants ont accès à leur emploi du temps et peuvent poster des alertes.</p>
                <p class="lead">Les directeurs d\'études ont accès à leur emploi du temps et peuvent poster des alertes et des informations.</p>
                <p class="lead">Les secrétaires peuvent poster des alertes et des informations. Ils peuvent aussi créer des utilisateurs.</p>
                <p class="lead">Les techniciens ont accès aux emplois du temps des promotions.</p>
                <p class="lead">Les télévisions sont les utilisateurs utilisés pour afficher ce site sur les téléviseurs. Les comptes télévisions peuvent afficher autant d\'emplois du temps que souhaité.</p>
            </div>
        </div>
        <a href="' . esc_url(get_permalink(get_page_by_title('Gestion des utilisateurs'))) . '">Voir les utilisateurs</a>';
    }

    /**
     * Formulaire pour entrer le code de suppression de compte
     *
     * @return string Le code HTML du formulaire d'entrée du code
     */
    public function displayEnterCode() {
        return '
        <form method="post">
            <label for="codeDelete"> Code de suppression de compte</label>
            <input type="text" class="form-control text-center" name="codeDelete" placeholder="Code à rentrer" required="">
            <button type="submit" name="deleteAccount" class="btn button_ecran">Supprimer</button>
        </form>';
    }

    /**
     * Affiche le bouton de souscription
     *
     * @return string Le code HTML du bouton de souscription
     */
    public function displayButtonSubscription() {
        $wpnonce = wp_create_nonce('wp_rest');

        return '
        <a href="#" id="my-notification-button" class="btn btn-danger">Recevoir des notifications</a></br>
        <input id="wpnonce" type="hidden" value="' . $wpnonce . '" />';
    }

    /**
     * Affiche un formulaire pour modifier ses propres codes
     *
     * @param CodeAde[] $codes        Les codes à modifier
     * @param CodeAde[] $years        Les années disponibles
     * @param CodeAde[] $groups       Les groupes disponibles
     * @param CodeAde[] $halfGroups   Les demi-groupes disponibles
     *
     * @return string Le code HTML du formulaire de modification des codes
     */
    public function displayModifyMyCodes($codes, $years, $groups, $halfGroups) {
        $form = '
        <form method="post">
            <h2> Modifier mes emplois du temps</h2>
            <label>Année</label>
            <select class="form-control" name="modifYear">';
        if (!empty($codes[0])) {
            $form .= '<option value="' . $codes[0]->getCode() . '">' . $codes[0]->getTitle() . '</option>';
        }

        $form .= '<option value="0">Aucun</option>
                  <optgroup label="Année">';

        foreach ($years as $year) {
            $form .= '<option value="' . $year->getCode() . '">' . $year->getTitle() . '</option >';
        }
        $form .= '
                </optgroup>
            </select>
            <label>Groupe</label>
            <select class="form-control" name="modifGroup">';

        if (!empty($codes[1])) {
            $form .= '<option value="' . $codes[1]->getCode() . '">' . $codes[1]->getTitle() . '</option>';
        }
        $form .= '<option value="0">Aucun</option>
                  <optgroup label="Groupe">';

        foreach ($groups as $group) {
            $form .= '<option value="' . $group->getCode() . '">' . $group->getTitle() . '</option>';
        }
        $form .= '
                </optgroup>
            </select>
            <label>Demi-groupe</label>
            <select class="form-control" name="modifHalfgroup">';

        if (!empty($codes[2])) {
            $form .= '<option value="' . $codes[2]->getCode() . '">' . $codes[2]->getTitle() . '</option>';
        }
        $form .= '<option value="0"> Aucun</option>
                  <optgroup label="Demi-Groupe">';

        foreach ($halfGroups as $halfGroup) {
            $form .= '<option value="' . $halfGroup->getCode() . '">' . $halfGroup->getTitle() . '</option>';
        }
        $form .= '
                </optgroup>
            </select>
            <button name="modifvalider" type="submit" class="btn button_ecran">Valider</button>
         </form>';

        return $form;
    }

    /**
     * Affiche un message pour sélectionner un emploi du temps
     *
     * @return string Le message à afficher
     */
    public function displaySelectSchedule() {
        return '<p>Veuillez choisir un emploi du temps.</p>';
    }

    /**
     * Affiche la page d'accueil
     *
     * @return string Le code HTML de la page d'accueil
     */
    public function displayHome() {
        return '
        <div class="row">
            <div class="col-6 mx-auto col-md-6 order-md-1">
                <img src="' . TV_PLUG_PATH . '/public/img/background.png" alt="Logo Amu" class="img-fluid mb-3 mb-md-0">
            </div>
            <div class="col-md-6 order-md-2 text-center text-md-left pr-md-5">
                <h1 class="mb-3 bd-text-purple-bright">' . get_bloginfo("name") . '</h1>
                <p class="lead">Bienvenue sur le site de l\'écran connecté !</p>
                <p class="lead mb-4">Accédez à votre emploi du temps tant en recevant diverses informations de la part de votre département.</p>
            </div>
        </div>';
    }

    /**
     * Affiche un message pour la modification réussie du mot de passe
     */
    public function displayModificationPassValidate() {
        $this->buildModal('Modification du mot de passe', '<div class="alert alert-success" role="alert">La modification à été réussie !</div>', home_url());
    }

    /**
     * Affiche un message si le mot de passe est incorrect
     */
    public function displayWrongPassword() {
        $this->buildModal('Mot de passe incorrect', '<div class="alert alert-danger">Mauvais mot de passe</div>');
    }

    /**
     * Affiche un message si le mail a été envoyé
     */
    public function displayMailSend() {
        $this->buildModal('Mail envoyé', '<div class="alert alert-success"> Un mail a été envoyé à votre adresse mail, merci de bien vouloir entrer le code reçu</div>');
    }

    /**
     * Message pour prévenir qu'une inscription a échoué
     */
    public function displayErrorCreation() {
        $this->buildModal('Inscription échouée', '<div class="alert alert-danger">Il y a eu une erreur dans le formulaire, veuillez vérifier vos informations et réessayer</div>');
    }

    /**
     * Message pour prévenir qu'un login existe déjà
     */
    public function displayErrorLogin() {
        $this->buildModal('Inscription échouée', '<div class="alert alert-danger"> Le login est déjà utilisé ! </div>');
    }

    /**
     * Affiche un message pour indiquer qu'il n'y a pas de cours aujourd'hui
     *
     * @return string Le message à afficher
     */
    public function displayNoStudy() {
        return '<p>Vous n\'avez pas cours!</p>';
    }

    /**
     * Affiche un message d'erreur lorsque l'utilisateur n'a pas de code enregistré
     *
     * @return string Le message d'erreur à afficher
     */
    public function errorMessageNoCodeRegister() {
        $current_user = wp_get_current_user();
        return '
        <h2>' . $current_user->user_login . '</h2>
        <p>Vous êtes enregistré sans aucun emploi du temps, rendez-vous sur votre compte pour pouvoir vous attribuer un code afin d\'accéder à votre emploi du temps</p>';
    }

    /**
     * Affiche un message de succès lors du changement de code
     */
    public function successMesageChangeCode() {
        $this->buildModal('Modification validée', '<div class="alert alert-success"> Le changement de groupe a été pris en compte</div>');
    }

    /**
     * Affiche un message d'erreur lors du changement de code
     */
    public function errorMesageChangeCode() {
        $this->buildModal('Modification échouée', '<div class="alert alert-danger"> Le changement de groupe n\'a pas été pris en compte</div>');
    }
}
