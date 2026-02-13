<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if($_SESSION['sml_si']!=session_id()) {
    die();
}
require_once("../../db/connection.php");
require_once("../functions.php");
$id_user = $_POST['id_user'];

$stats = array();
$stats['count_maps'] = 0;
$stats['count_markers'] = 0;
$stats['total_visitors'] = 0;
$stats['visitors'] = array();

$where = "";
$role = get_user_role($id_user);
switch($role) {
    case 'administrator':
        $where = " WHERE 1=1 ";
        break;
    case 'customer':
        $where = " WHERE 1=1 AND m.id_user=? ";
        break;
    case 'editor':
        $where = " WHERE 1=1 AND m.id IN () ";
        $query = "SELECT GROUP_CONCAT(id_map) as ids FROM sml_assign_maps WHERE id_user=$id_user;";
        $result = $mysqli->query($query);
        if($result) {
            if($result->num_rows==1) {
                $row=$result->fetch_array(MYSQLI_ASSOC);
                $ids = $row['ids'];
                $where = " WHERE 1=1 AND m.id IN ($ids) ";
            }
        }
        break;
}
$query = "SELECT COUNT(*) as num FROM sml_maps as m $where LIMIT 1";
if($smt = $mysqli->prepare($query)) {
    if($role=='customer') {
        $smt->bind_param('i',$id_user);
    }
    $result = $smt->execute();
    if($result) {
        $result = get_result($smt);
        if(count($result)==1) {
            $row = array_shift($result);
            $stats['count_maps'] = $row['num'];
        }
    }
}

$query = "SELECT COUNT(*) as num FROM sml_markers as k
JOIN sml_maps as m ON m.id = k.id_map
$where AND k.to_validate=0 LIMIT 1;";
if($smt = $mysqli->prepare($query)) {
    if($role=='customer') {
        $smt->bind_param('i', $id_user);
    }
    $result = $smt->execute();
    if($result) {
        $result = get_result($smt);
        if(count($result)==1) {
            $row = array_shift($result);
            $stats['count_markers'] = $row['num'];
        }
    }
}

$total_visitors = 0;
$query = "SELECT m.id,m.name,COUNT(a.id) as count FROM sml_maps as m
LEFT JOIN sml_access_log as a ON m.id = a.id_map
$where
GROUP BY m.id";
if($smt = $mysqli->prepare($query)) {
    if($role=='customer') {
        $smt->bind_param('i', $id_user);
    }
    $result = $smt->execute();
    if($result) {
        $result = get_result($smt);
        if(count($result)>0) {
            while($row = array_shift($result)) {
                $count = $row['count'];
                $total_visitors = $total_visitors + $count;
                $stats['visitors'][] = $row;
            }
            $stats['total_visitors'] = $total_visitors;
        }
    }
}

ob_end_clean();
echo json_encode($stats);