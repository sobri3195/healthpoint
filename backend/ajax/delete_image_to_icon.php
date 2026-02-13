<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if((($_SERVER['SERVER_ADDR']=='5.9.29.89') && ($_SERVER['REMOTE_ADDR']!=$_SESSION['ip_developer'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require_once("../../db/connection.php");
$id = $_POST['id'];

$query = "DELETE FROM sml_icons WHERE id=$id;";
$result = $mysqli->query($query);

if($result) {
    $mysqli->query("UPDATE sml_markers SET id_icon_library=0 WHERE id_icon_library=$id;");
    $mysqli->query("ALTER TABLE sml_icons AUTO_INCREMENT = 1;");
    ob_end_clean();
    echo json_encode(array("status"=>"ok"));
} else {
    ob_end_clean();
    echo json_encode(array("status"=>"error"));
}

