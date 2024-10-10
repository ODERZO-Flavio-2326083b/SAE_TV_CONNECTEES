<?php
/**
 * Create a select with all codes ADE (Year, Groups, HalfGroups)
 */

use Models\CodeAde;

require_once( "../../../../../../wp-load.php" );

$model = new CodeAde();
$years = $model->getAllFromType('year');
$groups = $model->getAllFromType('group');
$halfGroups = $model->getAllFromType('halfGroup');

echo '<option value="0">Aucun</option>
      <optgroup label="Année">';
foreach ($years as $year) {
    echo '<option value="' . $year->getCode() . '">' . $year->getTitle() . '</option >';
}
echo '</optgroup>
     <optgroup label="Groupe">';
foreach ($groups as $group) {
    echo '<option value="' . $group->getCode() . '">' . $group->getTitle() . '</option>';
}
echo '</optgroup>
      <optgroup label="Demi groupe">';
foreach ($halfGroups as $halfGroup) {
    echo '<option value="' . $halfGroup->getCode() . '">' . $halfGroup->getTitle() . '</option>';
}
echo '</optgroup>';