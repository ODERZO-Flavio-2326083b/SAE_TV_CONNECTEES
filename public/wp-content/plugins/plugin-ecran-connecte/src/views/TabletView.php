<?php

namespace views;

use models\CodeAde;
use models\Department;
use models\User;

class TabletView extends UserView
{
    public function displayFormTablet(array $classes, array $allDepts,
        int $currDept = null
    ): string {

        return '
        <h2> Compte tablettes</h2>
        <p class="lead">Pour créer des tablettes, remplissez ce formulaire avec les
         valeurs demandées.</p>
        <p class="lead">Vous pouvez choisir un emploi du temps par tablette</p>
        <form method="post" id="registerTvForm">
            <div class="form-group">
            	<label for="loginTa">Login</label>
            	<input type="text" class="form-control" name="loginTa" 
            	placeholder="Nom de compte" required="">
            	<small id="passwordHelpBlock" class="form-text text-muted">Votre 
            	login doit contenir entre 4 et 25 caractère</small>
            </div>
            <div class="form-group">
            	<label for="pwdTa">Mot de passe</label>
            	<input type="password" class="form-control" id="pwdTa" name="pwdTa" 
            	placeholder="Mot de passe" minlength="8" maxlength="25" required="" 
            	onkeyup=checkPwd("Ta")>
            	<input type="password" class="form-control" id="pwdConfTa" 
            	name="pwdConfirmTa" placeholder="Confirmer le Mot de passe" 
            	minlength="8" maxlength="25" required="" onkeyup=checkPwd("Ta")>
            	<small id="passwordHelpBlock" class="form-text text-muted">Votre mot 
            	de passe doit contenir entre 8 et 25 caractère</small>
            </div>
            <div class="form-group">
                <label for="deptIdTa">Département</label>
                <br>    
                <select id="deptIdTa" name="deptIdTa" class="form-control">
                    ' . $this->buildCodesOptions($allDepts, $currDept) . '
                </select>
            </div>
            <div class="form-group">
            	<label>Choisir l\'emploi du temps</label>' .
            $this->buildSelectCode($classes, $allDepts) . '
            </div>
            <button type="submit" class="btn button_ecran" id="validTa" 
            name="createTa">Créer</button>
        </form>';
    }

    public static function buildSelectCode(array $classes, array $allDepts,
        CodeAde $code = null,
        int $count = 0
    ): string {
        $select = '<select class="form-control firstSelect" id="selectId' . $count
            . '" name="selectTa[]" required="">';

        if (!is_null($code)) {
            $select .= '<option value="' . $code->getCode() . '">'
                . $code->getTitle() . '</option>';
        } else {
            $select .= '<option disabled selected value>Sélectionnez un code ADE
</option>';
        }

        $allOptions = [];

        foreach ($classes as $class) {
            $allOptions[$class->getDeptId()][] = [
                'code' => $class->getCode(),
                'title' => $class->getTitle(),
                'type' => 'Salle'
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

            //trier les options au sein de chaque département par type puis par titre
            usort(
                $options, function ($a, $b) {
                    return [$a['type'], $a['title']] <=> [$b['type'], $b['title']];
                }
            );

            foreach ($options as $option) {
                $select .= '<option value="' . $option['code'] . '">'
                    . $option['type'] . ' - ' . $option['title']
                    . '</option>';
            }

            $select .= '</optgroup>';
        }

        $select .= '</select>';

        return $select;
    }

    public function displayAllTablets(array $users, array $userDeptList)
    {
        $title = 'Tablette';
        $name = 'Tablette';
        $header = ['Login', 'Département'];

        $row = array();
        $count = 0;
        foreach ($users as $user) {
            ++$count;
            $row[] = [$count, $this->buildCheckbox($name, $user->getId()),
                $user->getLogin(), $userDeptList[$count-1]];
        }

        return $this->displayAll($name, $title, $header, $row, $name);
    }
}
