<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if((($_SERVER['SERVER_ADDR']=='5.9.29.89') && ($_SERVER['REMOTE_ADDR']!=$_SESSION['ip_developer'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require_once("../../db/connection.php");
$id_category = $_POST['id_category'];

$query = "DELETE FROM sml_categories WHERE id=?;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('i',$id_category);
    $result = $smt->execute();
    if($result) {
        $mysqli->query("DELETE FROM sml_markers_categories_assoc WHERE id_category=$id_category;");
        $mysqli->query("ALTER TABLE sml_categories AUTO_INCREMENT = 1;");
        echo json_encode(array("status"=>"ok"));
    } else {
        echo json_encode(array("status"=>"error"));
    }
} else {
    echo json_encode(array("status"=>"error"));
}
