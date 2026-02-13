<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if((($_SERVER['SERVER_ADDR']=='5.9.29.89') && ($_SERVER['REMOTE_ADDR']!=$_SESSION['ip_developer'])) || ($_SESSION['sml_si']!=session_id())) {
    die();
}
require_once("../../db/connection.php");
require_once("../functions.php");

$id_map = $_POST['id_map'];
$id_user = $_POST['id_user'];
$field = $_POST['field'];
$checked = $_POST['checked'];

$mysqli->query("UPDATE sml_assign_maps SET $field=$checked WHERE id_map=$id_map AND id_user=$id_user;");