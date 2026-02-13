<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if((($_SERVER['SERVER_ADDR']=='5.9.29.89') && ($_SERVER['REMOTE_ADDR']!=$_SESSION['ip_developer'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require_once("../../db/connection.php");
$id_map = $_POST['id_map'];
$id_marker = $_POST['id_marker'];

$query = "DELETE FROM sml_story WHERE id_marker=? AND id_map=?;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('ii',$id_marker,$id_map);
    $result = $smt->execute();
    if($result) {
        echo json_encode(array("status"=>"ok"));
    } else {
        echo json_encode(array("status"=>"error"));
    }
} else {
    echo json_encode(array("status"=>"error"));
}
