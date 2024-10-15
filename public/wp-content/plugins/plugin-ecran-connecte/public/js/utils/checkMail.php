<?php

use Models\User;

require_once( "../../../../../../wp-load.php" );

$mail = $_POST['mail'];

$model = new User();

return $model->verifyMail($mail);