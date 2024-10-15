<?php
/**
 * Created by PhpStorm.
 * UserView: Lea Arnaud
 * Date: 13/06/2019
 * Time: 10:09
 */

require_once( "../../../../../../wp-load.php" );

$current_user = wp_get_current_user();
$result = $current_user->user_login;

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

echo json_encode($result);