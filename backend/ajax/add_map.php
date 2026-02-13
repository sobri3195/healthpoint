<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if((($_SERVER['SERVER_ADDR']=='5.9.29.89') && ($_SERVER['REMOTE_ADDR']!=$_SESSION['ip_developer'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require_once("../../db/connection.php");
$id_user = $_POST['id_user'];
$name = strip_tags($_POST['name']);
$code = uniqid();

$query = "INSERT INTO sml_maps(id_user,code,date_created,name) VALUES(?,?,NOW(),?);";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('iss',$id_user,$code,$name);
    $result = $smt->execute();
    if($result) {
        $insert_id = $mysqli->insert_id;
        $_SESSION['id_map_sel'] = $insert_id;
        $_SESSION['name_map_sel'] = $name;
        echo json_encode(array("status"=>"ok"));
    } else {
        echo json_encode(array("status"=>"error"));
    }
}