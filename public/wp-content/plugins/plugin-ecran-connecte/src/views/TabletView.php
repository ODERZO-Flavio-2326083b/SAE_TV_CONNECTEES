<?php

namespace views;

class TabletView extends UserView
{
    public function displayFormTablet(array $dept, int $currDept, bool $isAdmin) {
        return '
        <h2>Compte Tablette</h2>
        <p class="lead">Pour créer des tablettes, remplissez ce formulaire 
        avec les valeurs demandées.</p>
        ' . $this->displayBaseForm('Tablette', $dept, $isAdmin, $currDept);
    }

    public function displayAllTablets(array $users, array $userDeptList) {
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