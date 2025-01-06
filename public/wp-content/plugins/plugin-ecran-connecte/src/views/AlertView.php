<?php

namespace views;

use models\Alert;
use models\CodeAde;

/**
 * Class AlertView
 *
 * Gère toutes les vues liées aux alertes (formulaires, tableaux, messages).
 *
 * @package views
 */
class AlertView extends View
{

    /**
     * Affiche le formulaire de création d'une alerte.
     *
     * @param $years        array   Liste des années disponibles pour l'alerte.
     * @param $groups       array   Liste des groupes disponibles pour l'alerte.
     * @param $halfGroups   array   Liste des demi-groupes disponibles pour l'alerte.
     *
     * @return string
     * Retourne le code HTML du formulaire de création d'alerte.
     */
    public function creationForm($years, $groups, $halfGroups, $allDepts) : string {
        $dateMin = date('Y-m-d', strtotime("+1 day")); // Fixe la date minimale au jour suivant.

        return '
        <form method="post" id="alert">
            <div class="form-group">
                <label for="content">Contenu</label>
                <input class="form-control" type="text" id="content" name="content" placeholder="280 caractères au maximum" minlength="4" maxlength="280" required>
			</div>
            <div class="form-group">
				<label>Date d\'expiration</label>
				<input type="date" class="form-control" id="expirationDate" name="expirationDate" min="' . $dateMin . '" required>
			</div>
            <div class="form-group">
                <label for="selectAlert">Année, groupe, demi-groupes concernés</label>
                ' . $this->buildSelectCode($years, $groups, $halfGroups, $allDepts) . '
            </div>
            <input type="button" onclick="addButtonAlert()" class="btn button_ecran" value="+">
            <button type="submit" class="btn button_ecran" name="submit">Valider</button>
        </form>' . $this->contextCreateAlert();
    }

    /**
     * Explique le contexte d'affichage des alertes.
     *
     * @return string
     * Retourne le texte explicatif sur la gestion des alertes.
     */
    public function contextCreateAlert() : string {
        return '
		<hr class="half-rule">
		<div>
			<h2>Les alertes</h2>
			<p class="lead">Lors de la création de votre alerte, celle-ci sera postée directement sur tous les téléviseurs qui utilisent  ce site.</p>
			<p class="lead">Les alertes que vous créez seront affichées avec les alertes déjà présentes.</p>
			<p class="lead">Les alertes sont affichées les unes après les autres défilant à la chaîne en bas des téléviseurs.</p>
			<div class="text-center">
				<figure class="figure">
					<img src="' . TV_PLUG_PATH . 'public/img/presentation.png" class="figure-img img-fluid rounded" alt="Représentation d\'un téléviseur">
					<figcaption class="figure-caption">Représentation d\'un téléviseur</figcaption>
				</figure>
			</div>
		</div>';
    }

    /**
     * Affiche le formulaire pour modifier une alerte existante.
     *
     * @param $alert       Alert  Instance de l'alerte à modifier.
     * @param $years        array  Liste des années disponibles.
     * @param $groups       array  Liste des groupes disponibles.
     * @param $halfGroups   array  Liste des demi-groupes disponibles.
     *
     * @return string
     * Retourne le code HTML du formulaire de modification d'alerte.
     */
    public function modifyForm($alert, $years, $groups, $halfGroups, $allDepts) : string {
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        $codes = $alert->getCodes();

        $form = '
        <a href="' . esc_url(get_permalink(get_page_by_title_custom('Gestion des alertes'))) . '">< Retour</a>
        <form method="post" id="alert">
            <div class="form-group">
                <label for="content">Contenu</label>
                <input type="text" class="form-control" id="content" name="content" value="' . $alert->getContent() . '" placeholder="280 caractères au maximum" minlength="4" maxlength="280" required>
            </div>
            <div class="form-group">
                <label for="expirationDate">Date d\'expiration</label>
                <input type="date" class="form-control" id="expirationDate" name="expirationDate" min="' . $dateMin . '" value = "' . $alert->getExpirationDate() . '" required>
            </div>
            <div class="form-group">
                <label for="selectId1">Année, groupe, demi-groupes concernés</label>
            </div>';

        if (!$alert->getForEveryone()) {
            $count = 1;
            foreach ($codes as $code) {
                $form .= '
				<div class="row">' .
                    $this->buildSelectCode($years, $groups, $halfGroups, $allDepts, $code, $count)
                    . '<input type="button" id="selectId' . $count . '" onclick="deleteRowAlert(this.id)" class="button_ecran" value="Supprimer">
                  </div>';
                $count = $count + 1;
            }
        }

        $form .= '<input type="button" class = "btn button_ecran" onclick="addButtonAlert()" value="+">
                  <button type="submit" class="btn button_ecran" name="submit">Valider</button>
                  <button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette alerte ?\');">Supprimer</button>
                </form>' . $this->contextModify();

        return $form;
    }

    /**
     * Explique le contexte de modification d'une alerte.
     *
     * @return string
     * Retourne le texte explicatif sur la modification des alertes.
     */
    public function contextModify() : string {
        return '
		<hr class="half-rule">
		<div>
			<p class="lead">La modification d\'une alerte prend effet comme pour la création, le lendemain.</p>
			<p class="lead">Vous pouvez donc prolonger le temps d\'expiration ou bien modifier le contenu de votre alerte.</p>
		</div>';
    }

    /**
     * Affiche toutes les alertes créées sur le site.
     *
     * @return string
     * Retourne le code HTML de la section affichant toutes les alertes.
     */
    public function contextDisplayAll() : string {
        return '
		<div class="row">
			<div class="col-6 mx-auto col-md-6 order-md-2">
				<img src="' . TV_PLUG_PATH . 'public/img/alert.png" alt="Logo alerte" class="img-fluid mb-3 mb-md-0">
			</div>
			<div class="col-md-6 order-md-1 text-center text-md-left pr-md-5">
				<p class="lead">Vous pouvez retrouver ici toutes les alertes qui ont été créées sur ce site.</p>
				<p class="lead mb-4">Les alertes sont triées de la plus vieille à la plus récente.</p>
				<p class="lead mb-4">Vous pouvez modifier une alerte en cliquant sur "Modifier" à la ligne correspondante à l\'alerte.</p>
				<p class="lead mb-4">Vous souhaitez supprimer une / plusieurs alerte(s) ? Cochez les cases des alertes puis cliquez sur "Supprimer" le bouton ce situe en bas du tableau.</p>
			</div>
		</div>
		<a href="' . esc_url(get_permalink(get_page_by_title_custom('Créer une alerte'))) . '">Créer une alerte</a>
		<hr class="half-rule">';
    }

    /**
     * Affiche les alertes principales.
     *
     * @param $texts      array  Liste des textes des alertes à afficher.
     */
    public function displayAlertMain($texts) {
        echo '
        <div class="alerts" id="alert">
             <div class="ti_wrapper">
                <div class="ti_slide">
                    <div class="ti_content">';
        for ($i = 0; $i < sizeof($texts); ++$i) {
            echo '<div class="ti_news"><span>' . $texts[$i] . '</span></div>';
        }
        echo '
                    </div>
                </div>
            </div>
        </div>
        ';
    }

    /**
     * Construit un select HTML avec tous les codes Ade.
     *
     * @param $years        CodeAde[]  Liste des années.
     * @param $groups       CodeAde[]  Liste des groupes.
     * @param $halfGroups   CodeAde[]  Liste des demi-groupes.
     * @param $code         CodeAde    Code sélectionné (optionnel).
     * @param $count        int        Compteur pour l'identifiant du select.
     * @param $forEveryone  int        Indicateur si l'alerte est pour tout le monde.
     *
     * @return string
     * Retourne le code HTML du select.
     */
    public static function buildSelectCode($years, $groups, $halfGroups, $allDepts, $code = null, $count = 0, $forEveryone = 0) : string {
	    $select = '<select class="form-control firstSelect" id="selectId' . $count . '" name="selectAlert[]" required="">';

	    if (!is_null($code)) {
		    $select .= '<option value="' . $code->getCode() . '">' . $code->getTitle() . '</option>';
	    } else {
		    $select .= '<option disabled selected value>Sélectionnez un code ADE</option>';
	    }

	    $allOptions = [];

	    foreach ($years as $year) {
		    $allOptions[$year->getDeptId()][] = [
			    'code' => $year->getCode(),
			    'title' => $year->getTitle(),
			    'type' => 'Année'
		    ];
	    }

	    foreach ($groups as $group) {
		    $allOptions[$group->getDeptId()][] = [
			    'code' => $group->getCode(),
			    'title' => $group->getTitle(),
			    'type' => 'Groupe'
		    ];
	    }

	    foreach ($halfGroups as $halfGroup) {
		    $allOptions[$halfGroup->getDeptId()][] = [
			    'code' => $halfGroup->getCode(),
			    'title' => $halfGroup->getTitle(),
			    'type' => 'Demi groupe'
		    ];
	    }

	    // trier les départements par id
	    ksort($allOptions);

	    foreach ($allOptions as $deptId => $options) {
		    $deptName = 'Département inconnu';
		    foreach ($allDepts as $dept) {
			    if ($dept->getIdDepartment() === $deptId) {
				    $deptName = $dept->getName();
				    break;
			    }
		    }
		    $select .= '<optgroup label="Département ' . $deptName . '">';

		    // trier les options au sein de chaque département par type puis par titre
		    usort($options, function ($a, $b) {
			    return [$a['type'], $a['title']] <=> [$b['type'], $b['title']];
		    });

		    foreach ($options as $option) {
			    $select .= '<option value="' . $option['code'] . '">'
			               . $option['type'] . ' - ' . $option['title'] . '</option>';
		    }

		    $select .= '</optgroup>';
	    }

	    $select .= '</select>';

	    return $select;
    }

    /**
     * Affiche un message lorsque l'alerte n'a pas été trouvée.
     *
     * @return string
     * Retourne le message d'alerte non trouvée.
     */
    public function noAlert() : string {
        return '
		<a href="' . esc_url(get_permalink(get_page_by_title_custom('Gestion des alertes'))) . '">< Retour</a>
		<div>
			<h3>Alerte non trouvée</h3>
			<p>Cette alerte n\'éxiste pas, veuillez bien vérifier d\'avoir bien cliqué sur une alerte.</p>
			<a href="' . esc_url(get_permalink(get_page_by_title_custom('Créer une alerte'))) . '">Créer une alerte</a>
		</div>';
    }

    /**
     * Affiche un message lorsque l'utilisateur n'a pas les droits pour modifier l'alerte.
     *
     * @return string
     * Retourne le message d'alerte non modifiable.
     */
    public function alertNotAllowed() : string {
        return '
		<a href="' . esc_url(get_permalink(get_page_by_title_custom('Gestion des alertes'))) . '">< Retour</a>
		<div>
			<h3>Vous ne pouvez pas modifier cette alerte</h3>
			<p>Cette alerte appartient à quelqu\'un d\'autre, vous ne pouvez donc pas modifier cette alerte.</p>
			<a href="' . esc_url(get_permalink(get_page_by_title_custom('Créer une alerte'))) . '">Créer une alerte</a>
		</div>';
    }

    /**
     * Affiche une modale pour valider la création d'une alerte.
     */
    public function displayAddValidate() {
        $this->buildModal('Ajout d\'alerte', '<div class="alert alert-success"> Votre alerte a été envoyée !</div>', esc_url(get_permalink(get_page_by_title_custom('Gestion des alertes'))));
    }

    /**
     * Affiche une modale pour valider la modification d'une alerte.
     */
    public function displayModifyValidate() {
        $page = get_page_by_title_custom('Gestion des alertes');
        $linkManageAlert = get_permalink($page->ID);
        $this->buildModal('Ajout d\'alerte', '<div class="alert alert-success"> Votre alerte a été modifiée ! </div>', $linkManageAlert);
    }
}
