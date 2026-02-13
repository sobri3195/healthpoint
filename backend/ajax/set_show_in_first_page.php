<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if((($_SERVER['SERVER_ADDR']=='5.9.29.89') && ($_SERVER['REMOTE_ADDR']!=$_SESSION['ip_developer'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require_once("../../db/connection.php");
session_write_close();
$id_map = $_POST['id_map'];
$show_in_first_page = $_POST['show_in_first_page'];
$mysqli->query("UPDATE sml_maps SET show_in_first_page=0;");
$query = "UPDATE sml_maps SET show_in_first_page=$show_in_first_page WHERE id=$id_map;";
$result = $mysqli->query($query);
if($result) {
    ob_end_clean();
    echo json_encode(array("status"=>"ok"));
} else {
    ob_end_clean();
    echo json_encode(array("status"=>"error"));
}