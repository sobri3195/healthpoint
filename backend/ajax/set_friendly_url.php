<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if((($_SERVER['SERVER_ADDR']=='5.9.29.89') && ($_SERVER['REMOTE_ADDR']!=$_SESSION['ip_developer'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require_once("../../db/connection.php");
require_once("../functions.php");
session_write_close();
$id = (int)$_POST['id'];
$friendly_url = str_replace("'","",strip_tags($_POST['friendly_url']));
$friendly_url = str_replace("\"","",$friendly_url);
$friendly_url = str_replace(" ","_",$friendly_url);
$friendly_url = strtolower($friendly_url);
if(!empty($friendly_url)) {
    $query_check = "SELECT id FROM sml_maps WHERE friendly_url='$friendly_url' AND id != $id;";
    $result_check = $mysqli->query($query_check);
    if($result_check) {
        if($result_check->num_rows>0) {
            ob_end_clean();
            echo json_encode(array("status"=>"error"));
            exit;
        }
    }
}
$query = "UPDATE sml_maps SET friendly_url=? WHERE id=?;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('si',$friendly_url,$id);
    $result = $smt->execute();
    if ($result) {
        ob_end_clean();
        echo json_encode(array("status"=>"ok"));
    } else {
        ob_end_clean();
        echo json_encode(array("status"=>"error"));
    }
} else {
    ob_end_clean();
    echo json_encode(array("status"=>"error"));
}