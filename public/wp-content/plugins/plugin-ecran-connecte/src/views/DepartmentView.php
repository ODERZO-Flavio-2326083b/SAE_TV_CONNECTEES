<?php

namespace views;

use Views\View;

class DepartmentView extends View {

	/**
	 * Rendu du formulaire d'ajout de Département
	 *
	 * @return string
	 */
	public function renderAddForm() {
		return '
        <form method="post">
            <div class="form-group">
                <label for="dept_name">Nom du département</label>
                <input class="form-control" type="text" id="dept_name" name="dept_name" placeholder="Nom du département" required="" minlength="5" maxlength="60">
            </div>
            <div class="form-group">
            	<label for="dept_lat">Latitude du département</label>
            	<input class="form-control" type="text" id="dept_lat" name="dept_lat" placeholder="Latitude" required="" minlength="4" maxlength="10">
            	<label for="dept_long">Longitude du département</label>
            	<input class="form-control" type="text" id="dept_long" name="dept_long" placeholder="Latitude" required="" minlength="4" maxlength="10">
            </div>
          <button type="submit" class="btn button_ecran" name="submit">Ajouter</button>
        </form>';
	}

	/**
	 * Rendu du formulaire de modification d'un Département
	 *
	 * @param string $name
	 * @param int $lat
	 * @param int $long
	 *
	 * @return string
	 */
	public function renderModifForm(string $name, int $lat, int $long) {
		$returnPage = get_page_by_title('Gestion des départements');
		$linkManageCode = get_permalink($returnPage->ID);

		return '
		<a href="' . esc_url($linkManageCode) . '">Retour</a>
        <form method="post">
            <div class="form-group">
                <label for="dept_name">Nom du département</label>
                <input class="form-control" type="text" id="dept_name" name="dept_name" placeholder="Nom du département" required="" minlength="5" maxlength="60">
            </div>
            <div class="form-group">
            	<label for="dept_lat">Latitude du département</label>
            	<input class="form-control" type="text" id="dept_lat" name="dept_lat" placeholder="Latitude" required="" minlength="4" maxlength="10">
            	<label for="dept_long">Longitude du département</label>
            	<input class="form-control" type="text" id="dept_long" name="dept_long" placeholder="Latitude" required="" minlength="4" maxlength="10">
            </div>
          <button type="submit" class="btn button_ecran" name="submit">Ajouter</button>
        </form>';
	}

	/**
	 * Affiche un modal avec un message de confirmation
	 *
	 * @return void
	 */
	public function successCreation() {
		$this->buildModal("Ajout d'un département", "<p>Le département a bien été créé!</p>");
	}

	/**
	 * Affiche un modal d'erreur s'il y en a une lors de la création
	 * du département.
	 *
	 * @return void
	 */
	public function errorCreation() {
		$this->buildModal('Erreur lors de la création du département', '<p>Le département a rencontré une erreur lors de son ajout</p>');
	}


	/**
	 * Affiche un message d'erreur si le département existe déjà
	 *
	 * @return void
	 */
	public function errorDuplicate() {
		echo '<p class="alert alert-danger"> Ce département existe déjà </p>';
	}

	public function errorNothing() {
		$page = get_page_by_title("Gestion des départements");
		$returnLink = get_permalink($page->ID);
		echo '<p>Il n\'y a rien par ici</p><a href="' . $returnLink . '">Retour</a>';
	}
}