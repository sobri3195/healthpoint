<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if((($_SERVER['SERVER_ADDR']=='5.9.29.89') && ($_SERVER['REMOTE_ADDR']!=$_SESSION['ip_developer'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require_once("../../db/connection.php");
$id_map = $_POST['id_map'];
$id_user = $_POST['id_user'];
$name = strip_tags($_POST['name']);
$main_color_hex = strip_tags($_POST['main_color_hex']);
$markers_size = $_POST['markers_size'];
$map_style = $_POST['map_style'];
$logo = $_POST['logo'];
$maptiler_api = strip_tags($_POST['maptiler_api']);
$ga_tracking_id = strip_tags($_POST['ga_tracking_id']);
$show_list = $_POST['show_list'];
$default_zoom = $_POST['default_zoom'];
$selected_zoom = $_POST['selected_zoom'];
$density_color = $_POST['density_color'];
$enable_reviews = $_POST['enable_reviews'];
$enable_directions = $_POST['enable_directions'];
$enable_streetview = $_POST['enable_streetview'];
$enable_share = $_POST['enable_share'];
$language = $_POST['language'];
$street_basemap = $_POST['url_street'];
$satellite_basemap = $_POST['url_sat'];
$street_maxzoom = $_POST['zoom_street'];
$satellite_maxzoom = $_POST['zoom_sat'];
$cluster_distance = $_POST['cluster_distance'];
if($cluster_distance=="") $cluster_distance=25;
$density_color_tolerance = $_POST['density_color_tolerance'];
if(empty($density_color_tolerance)) $density_color_tolerance=1;
$cat_filter = $_POST['cat_filter'];
$nav_markers = $_POST['nav_markers'];
$enable_add_marker = $_POST['enable_add_marker'];
$font_viewer = $_POST['font_viewer'];
$sheet_detail = $_POST['sheet_detail'];
$default_view = $_POST['default_view'];
$quality = $_POST['quality'];
$default_my_location = $_POST['default_my_location'];
$my_location_zoom = $_POST['my_location_zoom'];
$search_highlight = $_POST['search_highlight'];
$enable_search = $_POST['enable_search'];
$enable_search_location = $_POST['enable_search_location'];
$enable_list = $_POST['enable_list'];
$enable_categories = $_POST['enable_categories'];
$enable_story = $_POST['enable_story'];
$street_attributions = $_POST['street_attributions'];
$satellite_attributions = $_POST['satellite_attributions'];
$markers_color_hex = $_POST['markers_color_hex'];
$markers_icon_color_hex = $_POST['markers_icon_color_hex'];
if(empty($markers_icon_color_hex)) $markers_icon_color_hex="#ffffff";
$markers_id_icon_library = $_POST['markers_id_icon_library'];
$markers_icon = strip_tags($_POST['markers_icon']);
$order_by = $_POST['order_by'];
$geoJSON = $_POST['geoJSON'];
$weather = $_POST['weather'];
$cat_filter_type = $_POST['cat_filter_type'];
$enable_globe = $_POST['enable_globe'];
$enable_popup = $_POST['enable_popup'];
$query = "UPDATE sml_maps SET id_user=?,name=?,main_color_hex=?,map_style=?,logo=?,maptiler_api=?,ga_tracking_id=?,show_list=?,default_zoom=?,selected_zoom=?,density_color=?,markers_size=?,enable_reviews=?,enable_directions=?,enable_streetview=?,enable_share=?,language=?,street_basemap=?,satellite_basemap=?,street_maxzoom=?,satellite_maxzoom=?,cluster_distance=?,cat_filter=?,nav_markers=?,enable_add_marker=?,font_viewer=?,sheet_detail=?,default_view=?,quality=?,default_my_location=?,my_location_zoom=?,density_color_tolerance=?,enable_search=?,enable_list=?,enable_categories=?,enable_story=?,enable_search_location=?,street_attributions=?,satellite_attributions=?,markers_icon=?,markers_id_icon_library=?,markers_color_hex=?,markers_icon_color_hex=?,search_highlight=?,order_by=?,geoJSON=?,weather=?,cat_filter_type=?,enable_globe=?,enable_popup=? WHERE id=?;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('issssssiiiidiiiisssiiiiiisissiiiiiiiisssississssiii',$id_user,$name,$main_color_hex,$map_style,$logo,$maptiler_api,$ga_tracking_id,$show_list,$default_zoom,$selected_zoom,$density_color,$markers_size,$enable_reviews,$enable_directions,$enable_streetview,$enable_share,$language,$street_basemap,$satellite_basemap,$street_maxzoom,$satellite_maxzoom,$cluster_distance,$cat_filter,$nav_markers,$enable_add_marker,$font_viewer,$sheet_detail,$default_view,$quality,$default_my_location,$my_location_zoom,$density_color_tolerance,$enable_search,$enable_list,$enable_categories,$enable_story,$enable_search_location,$street_attributions,$satellite_attributions,$markers_icon,$markers_id_icon_library,$markers_color_hex,$markers_icon_color_hex,$search_highlight,$order_by,$geoJSON,$weather,$cat_filter_type,$enable_globe,$enable_popup,$id_map);
    $result = $smt->execute();
    if($result) {
        echo json_encode(array("status"=>"ok"));
    } else {
        echo json_encode(array("status"=>"error"));
    }
} else {
    echo json_encode(array("status"=>"error"));
}

