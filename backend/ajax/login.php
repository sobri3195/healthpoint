<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if($_SESSION['sml_si_l']!=session_id()) {
    die();
}
require_once("../functions.php");
require_once("../../db/connection.php");
require_once("../../config/config.inc.php");
$username = strip_tags($_POST['username']);
$password = strip_tags($_POST['password']);
$key_enc = PASSWORDS_ENCRYPT_KEY;
$query = "SELECT * FROM sml_users WHERE username=? AND password=AES_ENCRYPT(?, ?) LIMIT 1;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('sss',$username,$password,$key_enc);
    $result = $smt->execute();
    if($result) {
        $result = get_result($smt);
        if(count($result)==1) {
            $row = array_shift($result);
            $_SESSION['id_user'] = $row['id'];
            unset($_SESSION['lang']);
            if($row['active']) {
                echo json_encode(array("status"=>"ok"));
            } else {
                echo json_encode(array("status"=>"blocked"));
            }
        } else {
            echo json_encode(array("status"=>"incorrect"));
        }
    } else {
        echo json_encode(array("status"=>"error"));
    }
} else {
    echo json_encode(array("status"=>"error"));
}

