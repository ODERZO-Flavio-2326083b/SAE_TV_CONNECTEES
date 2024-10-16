<?php

namespace Views;

use Models\CodeAde;
use Models\User;

/**
 * Class StudentView
 *
 * Toutes les vues pour les étudiants (Formulaires, tableaux, messages)
 *
 * @package Views
 */
class StudentView extends UserView
{

    /**
     * Affiche le formulaire pour l'importation d'un fichier Excel d'étudiants.
     *
     * Cette méthode génère un code HTML qui fournit des instructions à l'utilisateur
     * pour télécharger un modèle de fichier Excel contenant des informations sur
     * les étudiants, ainsi qu'un formulaire pour télécharger ce fichier une fois
     * rempli. Lors de l'importation, un email est envoyé à chaque étudiant avec
     * ses informations de connexion.
     *
     * @return string Retourne le code HTML du formulaire d'importation.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayInsertImportFileStudent() {
        return '
        <h2>Compte étudiant</h2>
        <p class="lead">Pour créer des étudiants, commencer par télécharger le fichier Excel en cliquant sur le lien ci-dessous.</p>
        <p class="lead">Remplissez les colonnes par les valeurs demandées, une ligne est égale à un utilisateur.</p>
        <p class="lead">Lorsque vous avez remplis le fichier Excel, enregistrez le et cliquez sur "Parcourir" et sélectionnez votre fichier.</p>
        <p class="lead">Pour finir, validez l\'envoie du formulaire en cliquant sur "Importer le fichier"</p>
        <p class="lead">Lorsqu\'un élève est inscrit, un email lui est envoyé contenant son login et son mot de passe avec un lien du site.</p>
        <p class="lead">Lors de sa première connection, l\'étudiant devraz choisir son groupe pour avoir son emploi du temps.</p>
        <a href="' . TV_PLUG_PATH . 'public/files/Ajout Etus.xlsx" download="Ajout Etus.xlsx">Télécharger le fichier excel !</a>
        <form id="etu" method="post" enctype="multipart/form-data">
            <input type="file" name="excelEtu" class="inpFil" required=""/>
            <button type="submit" class="btn button_ecran" name="importEtu" value="Importer">Importer le fichier</button>
        </form>';
    }

    /**
     * Affiche la liste de tous les étudiants avec leurs détails.
     *
     * Cette méthode génère un tableau HTML affichant les informations des étudiants,
     * y compris leur numéro, année, groupe, demi-groupe et un lien pour modifier leurs
     * informations. Les étudiants sont extraits d'un tableau d'utilisateurs fourni
     * en paramètre. Chaque étudiant est associé à un ensemble de codes, dont seuls les
     * titres sont affichés dans le tableau.
     *
     * @param array $users Un tableau d'objets représentant les étudiants à afficher.
     *                     Chaque objet doit avoir des méthodes pour récupérer son ID,
     *                     son login et ses codes.
     *
     * @return string Retourne le code HTML du tableau des étudiants.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayAllStudent($users) {
        $page = get_page_by_title('Modifier un utilisateur');
        $linkManageUser = get_permalink($page->ID);

        $title = 'Étudiants';
        $name = 'Etu';
        $header = ['Numéro étudiant', 'Année', 'Groupe', 'Demi groupe', 'Modifier'];

        $row = array();
        $count = 0;

        foreach ($users as $user) {

            $codes = $user->getCodes();
            $codesTitle = array();
            foreach ($codes as $code) {
                if ($code instanceof CodeAde) {
                    $codesTitle[] = $code->getTitle();
                } else {
                    $codesTitle[] = $code;
                }
            }

            ++$count;
            $row[] = [$count, $this->buildCheckbox($name, $user->getId()), $user->getLogin(), $codesTitle[0], $codesTitle[1], $codesTitle[2], $this->buildLinkForModify($linkManageUser . '?id=' . $user->getId())];
        }

        return $this->displayAll($name, $title, $header, $row, 'student');
    }

    /**
     * Affiche le formulaire de modification des informations d'un étudiant.
     *
     * Cette méthode génère un formulaire HTML pour modifier les informations d'un étudiant
     * spécifié, y compris son année, groupe et demi-groupe. Les options de sélection sont
     * remplies à partir des paramètres fournis pour les années, groupes et demi-groupes.
     * Le formulaire inclut également des options pour valider ou annuler les modifications.
     *
     * @param object $user L'objet représentant l'étudiant dont les informations sont
     *                     modifiées. Doit avoir des méthodes pour récupérer son login
     *                     et ses codes associés.
     * @param array $years Un tableau d'objets représentant les années disponibles.
     *                     Chaque objet doit avoir des méthodes pour récupérer son code
     *                     et son titre.
     * @param array $groups Un tableau d'objets représentant les groupes disponibles.
     *                      Chaque objet doit avoir des méthodes pour récupérer son code
     *                      et son titre.
     * @param array $halfGroups Un tableau d'objets représentant les demi-groupes
     *                          disponibles. Chaque objet doit avoir des méthodes pour
     *                          récupérer son code et son titre.
     *
     * @return string Retourne le code HTML du formulaire de modification d'étudiant.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayModifyStudent($user, $years, $groups, $halfGroups) {
        $page = get_page_by_title('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);

        $form = '
        <a href="' . esc_url(get_permalink(get_page_by_title('Gestion des utilisateurs'))) . '">< Retour</a>
        <h2>' . $user->getLogin() . '</h2>
         <form method="post">
            <div class="form-group">
            	<label for="modifYear">Année</label>
            	<select id="modifYear" class="form-control" name="modifYear">';

        if ($user->getCodes()[0] instanceof CodeAde) {
            $form .= '<option value="' . $user->getCodes()[0]->getCode() . '">' . $user->getCodes()[0]->getTitle() . '</option>';
        }
        $form .= '
        <option value="0">Aucun</option>
        <optgroup label="Année">';

        foreach ($years as $year) {
            $form .= '<option value="' . $year->getCode() . '">' . $year->getTitle() . '</option >';
        }
        $form .= '
            	</optgroup>
        	</select>
        </div>
        <div class="form-group">
        	<label for="modifGroup">Groupe</label>
        	<select id="modifGroup" class="form-control" name="modifGroup">';

        if ($user->getCodes()[1] instanceof CodeAde) {
            $form .= '<option value="' . $user->getCodes()[1]->getCode() . '">' . $user->getCodes()[1]->getTitle() . '</option>';
        }
        $form .= '<option value="0">Aucun</option>
                 <optgroup label="Groupe">';

        foreach ($groups as $group) {
            $form .= '<option value="' . $group->getCode() . '">' . $group->getTitle() . '</option>';
        }
        $form .= '
            	</optgroup>
        	</select>
        </div>
        <div class="form-group">
        	<label for="modifHalfgroup">Demi-groupe</label>
        	<select id="modifHalfgroup" class="form-control" name="modifHalfgroup">';

        if ($user->getCodes()[2] instanceof CodeAde) {
            $form .= '<option value="' . $user->getCodes()[2]->getCode() . '">' . $user->getCodes()[2]->getTitle() . '</option>';
        }
        $form .= '<option value="0"> Aucun</option>
                  <optgroup label="Demi-Groupe">';

        foreach ($halfGroups as $halfGroup) {
            $form .= '<option value="' . $halfGroup->getCode() . '">' . $halfGroup->getTitle() . '</option>';
        }
        $form .= '
	        		</optgroup>
	        	</select>
	        </div>
	        <button name="modifvalider" class="btn button_ecran" type="submit" value="Valider">Valider</button>
	    	<a href="' . $linkManageUser . '">Annuler</a>
	    </form>';

        return $form;
    }
    /**
     * Affiche un modal pour la sélection des emplois du temps.
     *
     * Cette méthode génère un modal HTML qui permet à l'utilisateur de sélectionner
     * son année, groupe et demi-groupe. Les options de sélection sont remplies à partir
     * des paramètres fournis. Un formulaire est inclus pour soumettre les choix de
     * l'utilisateur.
     *
     * @param array $years Un tableau d'objets représentant les années disponibles.
     *                     Chaque objet doit avoir des méthodes pour récupérer son code
     *                     et son titre.
     * @param array $groups Un tableau d'objets représentant les groupes disponibles.
     *                      Chaque objet doit avoir des méthodes pour récupérer son code
     *                      et son titre.
     * @param array $halfGroups Un tableau d'objets représentant les demi-groupes
     *                          disponibles. Chaque objet doit avoir des méthodes pour
     *                          récupérer son code et son titre.
     *
     * @return void Affiche directement le modal pour la sélection des emplois du temps.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function selectSchedules($years, $groups, $halfGroups) {
        echo '
	    <div class="modal" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
	      <div class="modal-dialog modal-dialog-centered" role="document">
	        <div class="modal-content">
	          <div class="modal-header">
	            <h5 class="modal-title"> Choix des emplois du temps</h5>
	          </div>
	          <div class="modal-body">
		          <p>Bienvenue sur l\'écran connecté, sélectionnez vos emplois du temps afin de pouvoir utiliser ce site</p>
		          <form method="post">
		            <label for="selectYears">Sélectionne ton année</label>
		            <select class="form-control firstSelect" id="selectYears" name="selectYears">
		                <option value="0">Aucun</option>
		                <optgroup label="Année">';

        foreach ($years as $year) {
            echo '<option value="' . $year->getCode() . '">' . $year->getTitle() . '</option >';
        }
        echo '
			</optgroup>
	    </select>
	    <label for="selectGroups">Sélectionne ton groupe</label>
	    <select class="form-control firstSelect" id="selectGroups" name="selectGroups">
	        <option value="0">Aucun</option>
	        <optgroup label="Groupe">';

        foreach ($groups as $group) {
            echo '<option value="' . $group->getCode() . '">' . $group->getTitle() . '</option>';
        }
        echo '
			</optgroup>
	    </select>
	    <label for="selectHalfgroups">Sélectionne ton demi-groupe</label>
	    <select class="form-control firstSelect" id="selectHalfgroups" name="selectHalfgroups">
	        <option value="0">Aucun</option>
	        <optgroup label="Demi groupe">';

        foreach ($halfGroups as $halfgroup) {
            echo '<option value="' . $halfgroup->getCode() . '">' . $halfgroup->getTitle() . '</option>';
        }
        echo '
							</optgroup>
	                    </select>
	                    <button type="submit" class="btn button_ecran" name="addSchedules">Valider</button>
	                </form>
	            </div>
	        </div>
	      </div>
	    </div>

	    <script> $("#myModal").show() </script>';
    }
}
