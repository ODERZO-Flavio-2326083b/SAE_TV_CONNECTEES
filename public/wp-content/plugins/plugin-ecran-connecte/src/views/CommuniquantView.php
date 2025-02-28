<?php

namespace views;

class CommuniquantView extends UserView
{
    public function displayFormCommunicant(array $dept, int $currDept,
                                          bool $isAdmin
    ): string {
        return '
        <h2>Compte communicant</h2>
        <p class="lead">Pour créer des communiquants, remplissez ce formulaire 
        avec les valeurs demandées.</p>
        ' . $this->displayBaseForm('Comm', $dept, $isAdmin, $currDept);
    }

    public function displayAllCommunicants(array $users,
                                          array $userDeptList
    ): string {
        $title = 'Communicant';
        $name = 'Comm';
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