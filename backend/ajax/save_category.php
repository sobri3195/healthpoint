<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if((($_SERVER['SERVER_ADDR']=='5.9.29.89') && ($_SERVER['REMOTE_ADDR']!=$_SESSION['ip_developer'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
$id_map = $_SESSION['id_map_sel'];
require_once("../../db/connection.php");
$id_category = $_POST['id'];
$name = strip_tags($_POST['name']);
$id_category_parent = $_POST['id_category_parent'];
$default_selected = $_POST['default_selected'];

if($id_category_parent==0) $id_category_parent=NULL;

$status = false;
if($id_category==0) {
    $query = "INSERT INTO sml_categories(id_map,name,id_category_parent,default_selected) VALUES(?,?,?,?);";
    if($smt = $mysqli->prepare($query)) {
        $smt->bind_param('isii',$id_map,$name,$id_category_parent,$default_selected);
        $result = $smt->execute();
        if($result) {
            $status = true;
        }
    }
} else {
    $markers_size = $_POST['markers_size'];
    $markers_color_hex = $_POST['markers_color_hex'];
    $markers_icon_color_hex = $_POST['markers_icon_color_hex'];
    if(empty($markers_icon_color_hex)) $markers_icon_color_hex="#ffffff";
    $markers_id_icon_library = $_POST['markers_id_icon_library'];
    $markers_icon = strip_tags($_POST['markers_icon']);
    $query = "UPDATE sml_categories SET name=?,id_category_parent=?,default_selected=?,markers_size=?,markers_color_hex=?,markers_icon_color_hex=?,markers_id_icon_library=?,markers_icon=? WHERE id=?;";
    if($smt = $mysqli->prepare($query)) {
        $smt->bind_param('siidssisi',$name,$id_category_parent,$default_selected,$markers_size,$markers_color_hex,$markers_icon_color_hex,$markers_id_icon_library,$markers_icon,$id_category);
        $result = $smt->execute();
        if($result) {
            $status = true;
        }
    }
}

if($status) {
    echo json_encode(array("status"=>"ok"));
} else {
    echo json_encode(array("status"=>"error"));
}

