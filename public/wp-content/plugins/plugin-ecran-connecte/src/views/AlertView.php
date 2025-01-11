<?php
// TODO : Ajouter la doc du fichier
namespace views;

use models\Alert;
use models\CodeAde;

/**
 * TODO : Ajouter les tags @author, @category, @license et @link
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
     * Cette méthode génère un formulaire HTML permettant à l'utilisateur de créer
     * une alerte. Le formulaire permet à l'utilisateur
     * de saisir un contenu pour l'alerte, de définir une date d'expiration et de
     * sélectionner les années, groupes et demi-groupes
     * concernés par l'alerte. La date minimale de l'alerte est fixée au jour suivant
     * la date actuelle.
     *
     * @param array $years      Liste des années disponibles pour
     *                          l'alerte.
     * @param array $groups     Liste des groupes disponibles pour l'alerte.
     * @param array $halfGroups Liste des demi-groupes disponibles pour l'alerte.
     * @param array $allDepts   Liste de tous les
     *                          départements.
     *
     * @return string Le code HTML du formulaire de création d'alerte.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function creationForm($years, $groups, $halfGroups, $allDepts) : string
    {
        // Fixe la date minimale au jour suivant.
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        return '
        <form method="post" id="alert">
            <div class="form-group">
                <label for="content">Contenu</label>
                <input class="form-control" type="text" id="content" name="content" 
                placeholder="280 caractères au maximum" minlength="4" maxlength="280"
                 required>
			</div>
            <div class="form-group">
				<label>Date d\'expiration</label>
				<input type="date" class="form-control" id="expirationDate" 
				name="expirationDate" min="' . $dateMin . '" required>
			</div>
            <div class="form-group">
                <label for="selectAlert">Année, groupe, demi-groupes concernés
                </label>
                ' . $this->buildSelectCode($years, $groups, $halfGroups, $allDepts)
            . '
            </div>
            <input type="button" onclick="addButtonAlert()" class="btn button_ecran" 
            value="+">
            <button type="submit" class="btn button_ecran" name="submit">Valider
            </button>
        </form>' . $this->contextCreateAlert();
    }

    /**
     * Explique le contexte d'affichage des alertes.
     *
     * Cette méthode génère un texte explicatif sur la gestion des alertes et leur
     * affichage sur les téléviseurs connectés
     * au site. Le texte comprend une explication sur la manière dont les alertes
     * sont affichées et sur le fait qu'elles
     * seront postées directement sur tous les téléviseurs. Elle inclut également
     * une illustration de l'affichage des alertes
     * sur un téléviseur.
     *
     * @return string Le texte explicatif sur la gestion des alertes, incluant
     * l'illustration et les détails d'affichage.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function contextCreateAlert() : string
    {
        return '
		<hr class="half-rule">
		<div>
			<h2>Les alertes</h2>
			<p class="lead">Lors de la création de votre alerte, celle-ci sera postée
			 directement sur tous les téléviseurs qui utilisent  ce site.</p>
			<p class="lead">Les alertes que vous créez seront affichées avec les 
			alertes déjà présentes.</p>
			<p class="lead">Les alertes sont affichées les unes après les autres 
			défilant à la chaîne en bas des téléviseurs.</p>
			<div class="text-center">
				<figure class="figure">
					<img src="' . TV_PLUG_PATH . 'public/img/presentation.png" 
					class="figure-img img-fluid rounded" 
					alt="Représentation d\'un téléviseur">
					<figcaption class="figure-caption">Représentation d\'un 
					téléviseur</figcaption>
				</figure>
			</div>
		</div>';
    }

    /**
     * TODO : Ajouter la doc pour les paramètres "$allDepts"
     * TODO : Mettre la doc des paramètres dans l'ordre
     * Affiche le formulaire pour modifier une alerte existante.
     *
     * Cette méthode génère le code HTML du formulaire permettant à un utilisateur de
     * modifier une alerte existante.
     * Elle inclut les champs nécessaires pour changer le contenu de l'alerte, sa
     * date d'expiration, ainsi que les années, groupes, ou demi-groupes concernés.
     * La méthode prend également en compte les alertes destinées à tout le monde ou
     * à des groupes spécifiques.
     *
     * Une option pour supprimer l'alerte est incluse dans le formulaire avec une
     * confirmation pour éviter des suppressions accidentelles.
     *
     * @param Alert $alert      Instance de l'alerte à modifier, contenant les
     *                          informations actuelles de l'alerte.
     * @param array $years      Liste des années disponibles pour l'alerte.
     * @param array $groups     Liste des groupes disponibles pour l'alerte.
     * @param array $halfGroups Liste des demi-groupes disponibles pour l'alerte.
     *
     * @return string Le code HTML du formulaire de modification d'alerte, prêt à
     * être affiché.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function modifyForm($alert, $years, $groups, $halfGroups,
        $allDepts
    ) : string {
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        $codes = $alert->getCodes();

        $form = '
        <a href="'
            . esc_url(
                get_permalink(
                    get_page_by_title_custom('Gestion des alertes')
                )
            ) . '">< Retour</a>
        <form method="post" id="alert">
            <div class="form-group">
                <label for="content">Contenu</label>
                <input type="text" class="form-control" id="content" name="content" 
                value="' . $alert->getContent() . '" 
                placeholder="280 caractères au maximum" minlength="4" maxlength="280"
                 required>
            </div>
            <div class="form-group">
                <label for="expirationDate">Date d\'expiration</label>
                <input type="date" class="form-control" id="expirationDate" 
                name="expirationDate" min="' . $dateMin . '" value = "'
            . $alert->getExpirationDate() . '" required>
            </div>
            <div class="form-group">
                <label for="selectId1">Année, groupe, demi-groupes concernés</label>
            </div>';

        $count = 1;
        foreach ($codes as $code) {
            $form .= '
			<div class="row">' .
                $this->buildSelectCode(
                    $years, $groups, $halfGroups, $allDepts,
                    $code, $count
                )
                . '<input type="button" id="selectId' . $count . '" 
			onclick="deleteRowAlert(this.id)" class="button_ecran" value="Supprimer">
              </div>';
            $count = $count + 1;
        }


        $form .= '<input type="button" class = "btn button_ecran" 
onclick="addButtonAlert()" value="+">
                  <button type="submit" class="btn button_ecran" 
                  name="submit">Valider</button>
                  <button type="submit" class="btn delete_button_ecran" name="delete"
                   onclick="return confirm(\' Voulez-vous supprimer cette alerte ?\'
                   );">Supprimer</button>
                </form>' . $this->contextModify();

        return $form;
    }

    /**
     * Explique le contexte de modification d'une alerte.
     *
     * Cette méthode fournit un texte explicatif sur la gestion des modifications des
     * alertes.
     * Elle informe l'utilisateur que les modifications effectuées sur une alerte
     * prendront effet le lendemain.
     * Le texte précise également les possibilités offertes, telles que la
     * prolongation de la durée d'expiration ou la modification du contenu de
     * l'alerte.
     *
     * @return string Le texte explicatif en format HTML.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function contextModify() : string
    {
        return '
		<hr class="half-rule">
		<div>
			<p class="lead">La modification d\'une alerte prend effet comme pour la 
			création, le lendemain.</p>
			<p class="lead">Vous pouvez donc prolonger le temps d\'expiration ou bien
			 modifier le contenu de votre alerte.</p>
		</div>';
    }

    /**
     * Affiche toutes les alertes créées sur le site.
     *
     * Cette méthode génère une section HTML contenant un aperçu des alertes créées
     * sur le site.
     * Elle inclut une description expliquant le tri des alertes par ordre
     * chronologique (de la plus ancienne à la plus récente) et fournit des
     * instructions pour modifier ou supprimer des alertes.
     *
     * Un lien est également inclus pour accéder au formulaire de création d'une
     * nouvelle alerte.
     *
     * @return string Le code HTML de la section affichant toutes les alertes.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function contextDisplayAll() : string
    {
        return '
		<div class="row">
			<div class="col-6 mx-auto col-md-6 order-md-2">
				<img src="' . TV_PLUG_PATH . 'public/img/alert.png" alt="Logo alerte"
				 class="img-fluid mb-3 mb-md-0">
			</div>
			<div class="col-md-6 order-md-1 text-center text-md-left pr-md-5">
				<p class="lead">Vous pouvez retrouver ici toutes les alertes qui ont 
				été créées sur ce site.</p>
				<p class="lead mb-4">Les alertes sont triées de la plus vieille à la 
				plus récente.</p>
				<p class="lead mb-4">Vous pouvez modifier une alerte en cliquant sur 
				"Modifier" à la ligne correspondante à l\'alerte.</p>
				<p class="lead mb-4">Vous souhaitez supprimer une / plusieurs 
				alerte(s) ? Cochez les cases des alertes puis cliquez sur "Supprimer"
				 le bouton ce situe en bas du tableau.</p>
			</div>
		</div>
		<a href="'
            . esc_url(
                get_permalink(
                    get_page_by_title_custom('Créer une alerte')
                )
            ) . '">Créer une alerte</a>
		<hr class="half-rule">';
    }

    /**
     * Affiche les alertes principales.
     *
     * Cette méthode génère une section HTML contenant les alertes principales à
     * afficher sous forme de texte défilant.
     * Elle utilise une structure de conteneurs pour permettre un défilement fluide
     * des alertes dans une animation.
     *
     * Les alertes sont fournies sous forme d'un tableau de chaînes de caractères et
     * sont insérées individuellement dans la structure HTML.
     *
     * @param array $texts Liste des textes des alertes à afficher.
     *
     * @return void
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function displayAlertMain($texts)
    {
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
     * TODO : Ajouter la doc pour les paramètres "$years" et "$allDepts"
     * TODO : Mettre la doc des paramètres dans l'ordre
     * Construit un élément select HTML contenant les codes ADE organisés par
     * catégories.
     *
     * Cette méthode génère un menu déroulant HTML (`<select>`) permettant de
     * sélectionner parmi les codes ADE regroupés en catégories : années, groupes et
     * demi-groupes. Elle prend en charge la présélection d'un code ou l'affichage de
     * l'option "Tous" lorsque l'alerte est destinée à tout le monde.
     *
     * @param CodeAde[]    $year        Liste des années disponibles, chaque entrée
     *                                  étant une instance de `CodeAde`.
     * @param CodeAde[]    $groups      Liste des groupes disponibles, chaque
     *                                  entrée étant une instance de `CodeAde`.
     * @param CodeAde[]    $halfGroups  Liste des demi-groupes disponibles, chaque
     *                                  entrée étant une instance de `CodeAde`.
     * @param CodeAde|null $code        Code ADE présélectionné (optionnel). Peut
     *                                  être null si aucune présélection n'est
     *                                  requise.
     * @param int          $count       Compteur unique utilisé pour générer un
     *                                  identifiant unique pour l'élément `<select>`.
     * @param int          $forEveryone Indicateur permettant de savoir si l'option
     *                                  "Tous" doit être sélectionnée par défaut.
     *                                  Valeur 1 : "Tous" est sélectionné, 0 sinon.
     *
     * @return string Retourne le code HTML complet de l'élément `<select>`, incluant
     * les options organisées par catégories.
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public static function buildSelectCode($years, $groups, $halfGroups, $allDepts,
        $code = null, $count = 0
    ) : string {
        $select = '<select class="form-control firstSelect" id="selectId' . $count
            . '" name="selectAlert[]" required="">';

        if (!is_null($code)) {
            $select .= '<option value="' . $code->getCode() . '">'
                . $code->getTitle() . '</option>';
        } else {
            $select .= '<option disabled selected value>Sélectionnez un code ADE
</option>';
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

            // trier les options au sein de chaque département par type puis par
            // titre
            usort(
                $options, function ($a, $b) {
                    return [$a['type'], $a['title']] <=> [$b['type'], $b['title']];
                }
            );

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
     * Génère le message HTML affiché lorsqu'une alerte n'a pas été trouvée.
     *
     * Cette méthode produit un message clair et informatif pour l'utilisateur
     * indiquant que l'alerte demandée
     * n'existe pas. Elle inclut des liens vers la page de gestion des alertes et
     * la page de création d'une nouvelle alerte.
     *
     * @return string
     * Retourne le code HTML contenant le message d'alerte non trouvée et les liens
     * d'orientation.
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function noAlert() : string
    {
        return '
		<a href="'
            . esc_url(
                get_permalink(
                    get_page_by_title_custom('Gestion des alertes')
                )
            ) . '">< Retour</a>
		<div>
			<h3>Alerte non trouvée</h3>
			<p>Cette alerte n\'éxiste pas, veuillez bien vérifier d\'avoir bien 
			cliqué sur une alerte.</p>
			<a href="'
            . esc_url(
                get_permalink(
                    get_page_by_title_custom('Créer une alerte')
                )
            ) . '">Créer une alerte</a>
		</div>';
    }

    /**
     * Génère le message HTML affiché lorsque l'utilisateur n'a pas les droits pour
     * modifier une alerte.
     *
     * Cette méthode informe l'utilisateur qu'il ne dispose pas des autorisations
     * nécessaires pour modifier l'alerte en question. Elle propose également des
     * liens vers la page de gestion des alertes et la page de création d'une
     * nouvelle alerte.
     *
     * @return string
     * Retourne le code HTML contenant le message d'accès refusé et les liens
     * d'orientation.
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function alertNotAllowed() : string
    {
        return '
		<a href="'
            . esc_url(
                get_permalink(
                    get_page_by_title_custom('Gestion des alertes')
                )
            ) . '">< Retour</a>
		<div>
			<h3>Vous ne pouvez pas modifier cette alerte</h3>
			<p>Cette alerte appartient à quelqu\'un d\'autre, vous ne pouvez donc pas
			 modifier cette alerte.</p>
			<a href="'
            . esc_url(
                get_permalink(
                    get_page_by_title_custom('Créer une alerte')
                )
            ) . '">Créer une alerte</a>
		</div>';
    }

    /**
     * Affiche une modale pour confirmer la création d'une alerte.
     *
     * Cette méthode utilise une modale pour informer l'utilisateur que la création
     * de l'alerte a été réussie. Le message de succès est affiché, et un lien est
     * fourni pour rediriger vers la page de gestion des alertes.
     *
     * @return void
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function displayAddValidate()
    {
        $this->buildModal(
            'Ajout d\'alerte', '<div class="alert alert-success"> Votre
 alerte a été envoyée !</div>', esc_url(
                get_permalink(get_page_by_title_custom('Gestion des alertes'))
            )
        );
    }

    /**
     * Affiche une modale pour confirmer la modification d'une alerte.
     *
     * Cette méthode utilise une modale pour informer l'utilisateur que la
     * modification de l'alerte a été effectuée avec succès. Le message de succès est
     * affiché, et un lien est fourni pour rediriger vers la page de gestion des
     * alertes.
     *
     * @return void
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function displayModifyValidate()
    {
        $page = get_page_by_title_custom('Gestion des alertes');
        $linkManageAlert = get_permalink($page->ID);
        $this->buildModal(
            'Ajout d\'alerte', '<div class="alert alert-success"> Votre
 alerte a été modifiée ! </div>', $linkManageAlert
        );
    }
}
