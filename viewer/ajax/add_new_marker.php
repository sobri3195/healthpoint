<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
require_once("../../db/connection.php");
$id_map = (int)$_POST['id_map'];
$name = strip_tags($_POST['name']);
$street = strip_tags($_POST['street']);
$city = strip_tags($_POST['city']);
$postal_code = strip_tags($_POST['postal_code']);
$country = strip_tags($_POST['country']);
$website = strip_tags($_POST['website']);
$phone = strip_tags($_POST['phone']);
$whatsapp = strip_tags($_POST['whatsapp']);
$email = strip_tags($_POST['email']);
$hours = $_POST['h_desc'];
$description = $_POST['description'];
$lat = str_replace(",",".",$_POST['lat']);
$lon = str_replace(",",".",$_POST['lon']);
$array_images = $_POST['array_images'];
$active = 1;
$to_validate = 1;
$category = $_POST['category'];
$status = false;
$main_color_hex = "#000000";
$markers_size = 1.1;
$markers_icon_color_hex = "#ffffff";
$markers_icon = "";
$markers_id_icon_library = 0;

$query = "SELECT main_color_hex,markers_size,markers_color_hex,markers_icon_color_hex,markers_icon,markers_id_icon_library,enable_add_marker FROM sml_maps WHERE id=$id_map;";
$result = $mysqli->query($query);
if($result) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    if(empty($row['markers_color_hex'])) {
        if(!empty($row['main_color_hex'])) {
            $markers_color_hex = $row['main_color_hex'];
        }
    } else {
        $markers_color_hex = $row['markers_color_hex'];
    }
    if(!empty($row['markers_size'])) {
        $marker_size = $row['markers_size'];
    }
    if(!empty($row['markers_icon_color_hex'])) {
        $markers_icon_color_hex = $row['markers_icon_color_hex'];
    }
    if(!empty($row['markers_icon'])) {
        $markers_icon = $row['markers_icon'];
    }
    if(!empty($row['markers_id_icon_library'])) {
        $markers_id_icon_library = $row['markers_id_icon_library'];
    }
    $enable_add_marker = $row['enable_add_marker'];
    if($enable_add_marker==2) $to_validate=0;
}

$query = "SELECT (MAX(`order`)+1) as `order` FROM sml_markers WHERE id_map=$id_map;";
$result = $mysqli->query($query);
if($result) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $order = $row['order'];
}
if(empty($order)) $order=0;
$query = "INSERT INTO sml_markers(id_map,name,street,city,postal_code,country,website,phone,whatsapp,email,hours,description,lat,lon,active,to_validate,`order`,color_hex,marker_size,icon_color_hex,icon,id_icon_library) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('isssssssssssssiiisdssi',$id_map,$name,$street,$city,$postal_code,$country,$website,$phone,$whatsapp,$email,$hours,$description,$lat,$lon,$active,$to_validate,$order,$markers_color_hex,$marker_size,$markers_icon_color_hex,$markers_icon,$markers_id_icon_library);
    $result = $smt->execute();
    if($result) {
        $id_marker = $mysqli->insert_id;
        $status = true;
    }
}

if($status) {
    foreach ($category as $cat) {
        $query = "INSERT INTO sml_markers_categories_assoc(id_marker,id_category) VALUES(?,?);";
        if($smt = $mysqli->prepare($query)) {
            $smt->bind_param('ii',$id_marker,$cat);
            $smt->execute();
        }
    }
    $main = 1;
    foreach ($array_images as $image) {
        copy(dirname(__FILE__).'/../marker_images_tmp/'.$image,dirname(__FILE__).'/../marker_images/'.$image);
        copy(dirname(__FILE__).'/../marker_images_tmp/thumb/'.$image,dirname(__FILE__).'/../marker_images/thumb/'.$image);
        unlink(dirname(__FILE__).'/../marker_images_tmp/'.$image);
        unlink(dirname(__FILE__).'/../marker_images_tmp/thumb/'.$image);
        $query = "INSERT INTO sml_images(id_marker,main,image) VALUES(?,?,?);";
        if($smt = $mysqli->prepare($query)) {
            $smt->bind_param('iis',$id_marker,$main,$image);
            $smt->execute();
        }
        $main = 0;
    }
    echo json_encode(array("status"=>"ok"));
} else {
    echo json_encode(array("status"=>"error","error"=>$mysqli->error));
}

