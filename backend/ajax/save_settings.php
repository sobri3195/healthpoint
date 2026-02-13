<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if((($_SERVER['SERVER_ADDR']=='5.9.29.89') && ($_SERVER['REMOTE_ADDR']!=$_SESSION['ip_developer'])) || ($_SESSION['sml_si']!=session_id())) {
    die();
}
require_once("../../db/connection.php");
require_once("../functions.php");
$id_user = $_SESSION['id_user'];
unset($_SESSION['lang']);
if(!get_user_role($id_user)=='administrator') {
    ob_end_clean();
    echo json_encode(array("status"=>"error"));
    exit;
}
$logo_exist = "";
$query = "SELECT logo FROM sml_settings LIMIT 1;";
$result = $mysqli->query($query);
if($result) {
    if($result->num_rows==1) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $logo_exist = $row['logo'];
    }
}
$name_app = str_replace("'","\'",strip_tags($_POST['name']));
$theme_color = $_POST['theme_color'];
if(empty($theme_color)) $theme_color='#0b5394';
$font_backend = $_POST['font_backend'];
if(empty($font_backend)) $font_backend='Nunito';
$welcome_msg = str_replace("'","\'",$_POST['welcome_msg']);
if($welcome_msg=='<p><br></p>') $welcome_msg="";
$logo = $_POST['logo'];
$background = $_POST['background'];
$language = $_POST['language'];
$language_domain = $_POST['language_domain'];
$language_domain = str_replace("_lang","",$language_domain);
$languages_enabled = $_POST['languages_enabled'];
$css_array = json_decode($_POST['css_array'],true);
$footer_link_1 = str_replace("'","\'",$_POST['footer_link_1']);
$footer_link_2 = str_replace("'","\'",$_POST['footer_link_2']);
$footer_link_3 = str_replace("'","\'",$_POST['footer_link_3']);
$footer_value_1 = str_replace("'","\'",$_POST['footer_value_1']);
$footer_value_2 = str_replace("'","\'",$_POST['footer_value_2']);
$footer_value_3 = str_replace("'","\'",$_POST['footer_value_3']);
if(strpos(get_string_between($footer_value_1, '<p>', '</p>'), 'http') === 0) {
    $footer_value_1 = str_replace(['<p>','</p>'],'',$footer_value_1);
}
if(strpos(get_string_between($footer_value_2, '<p>', '</p>'), 'http') === 0) {
    $footer_value_2 = str_replace(['<p>','</p>'],'',$footer_value_2);
}
if(strpos(get_string_between($footer_value_3, '<p>', '</p>'), 'http') === 0) {
    $footer_value_3 = str_replace(['<p>','</p>'],'',$footer_value_3);
}

foreach ($css_array as $name=>$content) {
    if($name=='custom_b') {
        $url_css = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'backend'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'custom_b.css';
    } else {
        $url_css = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'viewer'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.$name.'.css';
    }
    if(file_exists($url_css) && $content=='') {
        @unlink($url_css);
    } else {
        if($content!='') {
            @file_put_contents($url_css,$content);
        }
    }
}

$query = "UPDATE sml_settings SET name='$name_app',theme_color='$theme_color',font_backend='$font_backend',welcome_msg='$welcome_msg',logo='$logo',background='$background',language='$language',language_domain='$language_domain',languages_enabled='$languages_enabled',footer_link_1='$footer_link_1',footer_link_2='$footer_link_2',footer_link_3='$footer_link_3',footer_value_1='$footer_value_1',footer_value_2='$footer_value_2',footer_value_3='$footer_value_3';";
$result = $mysqli->query($query);

if($result) {
    ob_end_clean();
    echo json_encode(array("status"=>"ok"));
} else {
    ob_end_clean();
    echo json_encode(array("status"=>"error $query"));
}

