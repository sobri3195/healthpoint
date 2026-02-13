<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
require_once("../../backend/functions.php");
require_once("../../db/connection.php");
$id_map = (int)$_POST['id_map'];
$order_by = $_POST['order_by'];
$array_markers = array();
switch($order_by) {
    case 'priority':
        $order_by = "ORDER BY m.order ASC, m.id DESC";
        break;
    case 'name':
        $order_by = "ORDER BY m.name ASC, m.id DESC";
        break;
    case 'city':
        $order_by = "ORDER BY m.city ASC, m.id DESC";
        break;
}
$query = "SELECT m.*,i.image as icon_image,AVG(r.rating) as rating,GROUP_CONCAT(DISTINCT ca.id_category) as id_categories,mp.markers_size as default_marker_size FROM sml_markers as m 
LEFT JOIN sml_icons as i ON i.id=m.id_icon_library
LEFT JOIN sml_reviews as r ON r.id_marker=m.id
LEFT JOIN sml_markers_categories_assoc as ca ON ca.id_marker=m.id
JOIN sml_maps as mp ON mp.id=m.id_map
WHERE m.id_map=? AND m.to_validate=0
GROUP BY m.id
$order_by";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('i',$id_map);
    $result = $smt->execute();
    if($result) {
        $result = get_result($smt);
        if(count($result)>0) {
            while($row = array_shift($result)) {
                $row['lat'] = str_replace(",",".",$row['lat']);
                $row['lon'] = str_replace(",",".",$row['lon']);
                if(!is_numeric($row['lat']) || !is_numeric($row['lon'])) {
                    continue;
                }
                $row['address'] = "";
                if(!empty($row['street'])) {
                    $row['address'] .= $row['street'].", ";
                }
                if(!empty($row['city'])) {
                    $row['address'] .= $row['city'].", ";
                }
                if(!empty($row['postal_code'])) {
                    $row['address'] .= $row['postal_code'].", ";
                }
                if(!empty($row['country'])) {
                    $row['address'] .= $row['country'].", ";
                }
                $row['address'] = rtrim($row['address'],", ");
                if(empty($row['website_caption'])) $row['website_caption']='';
                $row['main_image'] = "";
                $row['images'] = array();
                if ($row['hours'] == "<p><br></p>") {
                    $row['hours'] = "";
                }
                if ($row['description'] == "<p><br></p>") {
                    $row['description'] = "";
                }
                try {
                    $row['icon'] = explode("|",$row['icon'])[1];
                } catch (Exception $e) {
                    $row['icon'] = '';
                }
                if(empty($row['color_hex'])) $row['color_hex']='#ffffff';
                if(empty($row['name'])) $row['name']='';
                if(empty($row['description'])) $row['description']='';
                if(empty($row['icon_image'])) $row['icon_image']='';
                for($i=1;$i<=20;$i++) {
                    if(empty($row['extra_field_value_'.$i])) $row['extra_field_value_'.$i]='';
                    $row['extra_field_value_'.$i] = htmlspecialchars_decode($row['extra_field_value_'.$i]);
                    $row['extra_field_value_'.$i] = str_replace(["\r\n","\r","\n"], "<br>", $row['extra_field_value_'.$i]);
                }
                $row['extra_button_value_1'] = htmlspecialchars_decode($row['extra_button_value_1']);
                $row['extra_button_value_1'] = str_replace(["\r\n","\r","\n"], "<br>", $row['extra_button_value_1']);
                $query_i = "SELECT * FROM sml_images WHERE id_marker=".$row['id'];
                $result_i = $mysqli->query($query_i);
                if($result_i) {
                    if ($result_i->num_rows > 0) {
                        while ($row_i = $result_i->fetch_array(MYSQLI_ASSOC)) {
                            if($row_i['main']==1) {
                                $row['main_image'] = $row_i['image'];
                            }
                            $row_i['image'] = 'marker_images/'.$row_i['image'];
                            $row['images'][] = $row_i['image'];
                        }
                    }
                }
                if(empty($row['rating'])) $row['rating']=0;
                $row['darker_color_hex'] = adjustBrightness($row['color_hex'],-0.3);
                if(empty($row['id_categories'])) $row['id_categories']="";
                if($row['marker_size']==0) $row['marker_size']=$row['default_marker_size'];
                $row['icon_base64']='';
                if(!empty($row['icon_image'])) {
                    $ext = substr(strrchr($row['icon_image'], '.'), 1);
                    if(($ext=='gif') || ($ext=='svg')) {
                        $row['icon_base64']='';
                    } else {
                        $row['icon_base64']=resize_image($row['icon_image'],$row['marker_size']);
                    }
                }
                $array_markers[] = $row;
            }
        }
    }
}
$array_categories = array();
$query = "SELECT c.*,(SELECT COUNT(DISTINCT ca.id_marker) FROM sml_markers_categories_assoc as ca LEFT JOIN sml_categories as c1 ON c1.id=ca.id_category LEFT JOIN sml_categories as c2 ON c2.id=ca.id_category WHERE c1.id=c.id OR c2.id_category_parent=c.id) as count_m FROM sml_categories as c WHERE c.id_map=? AND (SELECT COUNT(DISTINCT ca.id_marker) FROM sml_markers_categories_assoc as ca LEFT JOIN sml_categories as c1 ON c1.id=ca.id_category LEFT JOIN sml_categories as c2 ON c2.id=ca.id_category WHERE c1.id=c.id OR c2.id_category_parent=c.id)>0 ORDER BY c.name ASC;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('i', $id_map);
    $result = $smt->execute();
    if ($result) {
        $result = get_result($smt);
        if (count($result) > 0) {
            while ($row = array_shift($result)) {
                if(empty($row['id_category_parent'])) $row['id_category_parent']=0;
                $array_categories[] = $row;
            }
        }
    }
}

$array_markers_conn = array();
$query = "SELECT mc.color,mc.width,m1.lat as lat_source,m1.lon as lon_source,m2.lat as lat_dest,m2.lon as lon_dest,m1.id as id_source,m2.id as id_dest,mc.title,mc.description FROM sml_markers_connects as mc
JOIN sml_markers as m1 ON m1.id=mc.id_marker_source
JOIN sml_markers as m2 ON m2.id=mc.id_marker_dest
WHERE id_marker_source IN (SELECT id FROM sml_markers WHERE id_map=?);";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('i', $id_map);
    $result = $smt->execute();
    if ($result) {
        $result = get_result($smt);
        if (count($result) > 0) {
            while ($row = array_shift($result)) {
                if(empty($row['title'])) $row['title']='';
                if(empty($row['description'])) $row['description']='';
                $row['description'] = htmlspecialchars_decode($row['description']);
                $row['description'] = str_replace("\'","'",$row['description']);
                $row['description'] = str_replace(["\r\n","\r","\n"], "<br>", $row['description']);
                $array_markers_conn[] = $row;
            }
        }
    }
}
ob_end_clean();
echo json_encode(array("markers"=>$array_markers,"markers_connections"=>$array_markers_conn,"categories"=>$array_categories));

function adjustBrightness($hexCode, $adjustPercent) {
    $hexCode = ltrim($hexCode, '#');
    if (strlen($hexCode) == 3) {
        $hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
    }
    $hexCode = array_map('hexdec', str_split($hexCode, 2));
    foreach ($hexCode as & $color) {
        $adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
        $adjustAmount = ceil($adjustableLimit * $adjustPercent);

        $color = str_pad(dechex($color + $adjustAmount), 2, '0', STR_PAD_LEFT);
    }
    return '#'.implode($hexCode);
}

$k=0;
function resize_image($img_name,$scale) {
    global $k;
    $base64 = '';
    list($width, $height, $type, $attr) = getimagesize("../icons/$img_name");
    $new_w = ($width/10)*$scale;
    $new_h = ($height/10)*$scale;
    $ext = substr(strrchr($img_name, '.'), 1);
    if(!strcmp("jpg",$ext) || !strcmp("jpeg",$ext)){
        $src_img=imagecreatefromjpeg("../icons/$img_name");
    }
    if(!strcmp("png",$ext)){
        $src_img=imagecreatefrompng("../icons/$img_name");
    }
    $old_x=imageSX($src_img);
    $old_y=imageSY($src_img);

    if($new_h <= 0){
        $wRatio = $new_w / $old_x ;
        $thumb_h = ceil($wRatio * $old_y);
        $thumb_w = $new_w;
    }else{
        $thumb_w = $new_w;
    }

    if($new_w <= 0){
        $hRatio = $new_h / $old_y ;
        $thumb_w = ceil($hRatio * $old_x);
        $thumb_h = $new_h;
    }else{
        $thumb_h = $new_h;
    }

    $dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
    if(!strcmp("png",$ext)) {
        imagealphablending($dst_img, false);
        imagesavealpha($dst_img, true);
    }
    imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);

   $dest_file = '../icons/tmp_'.time().$k.'.'.$ext;
    if(!strcmp("gif",$ext)){
        imagegif($dst_img,$dest_file);
    } else if(!strcmp("png",$ext)){
        imagepng($dst_img,$dest_file);
    } else{
        imagejpeg($dst_img,$dest_file);
    }

    $type = pathinfo($dest_file, PATHINFO_EXTENSION);
    $data = file_get_contents($dest_file);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    unlink($dest_file);

    imagedestroy($dst_img);
    imagedestroy($src_img);
    $k++;
    return $base64;
}