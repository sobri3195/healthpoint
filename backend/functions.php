<?php
require_once(__DIR__."/../db/connection.php");

function set_language($language,$domain) {
    session_start();
    if (function_exists('gettext')) {
        if(!isset($_SESSION['lang']) || $_SESSION['lang']=='') {
            $_SESSION['lang']=$language;
        } else {
            $language=$_SESSION['lang'];
        }
        if(defined('LC_MESSAGES')) {
            $result = setlocale(LC_MESSAGES, $language);
            if(!$result) {
                setlocale(LC_MESSAGES, $language.'.UTF-8');
            }
            if (function_exists('putenv')) {
                $result = putenv('LC_MESSAGES='.$language);
                if(!$result) {
                    putenv('LC_MESSAGES='.$language.'.UTF-8');
                }
            }
        } else {
            if (function_exists('putenv')) {
                $result = putenv('LC_ALL=' . $language);
                if (!$result) {
                    putenv('LC_ALL=' . $language . '.UTF-8');
                }
            }
        }
        $result = bindtextdomain($domain, "../locale");
        if(!$result) {
            $domain = "default";
            bindtextdomain($domain, "../locale");
        }
        bind_textdomain_codeset($domain, 'UTF-8');
        textdomain($domain);
    } else {
        function _($a) {
            echo $a;
        }
    }
}

function is_ssl() {
    if ( isset( $_SERVER['HTTPS'] ) ) {
        if ( 'on' == strtolower( $_SERVER['HTTPS'] ) ) {
            return true;
        }
        if ( '1' == $_SERVER['HTTPS'] ) {
            return true;
        }
    } elseif ( isset( $_SERVER['SERVER_PORT'] ) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
        return true;
    }
    return false;
}

function get_user_info($id_user) {
    global $mysqli;
    $return = array();
    $query = "SELECT * FROM sml_users WHERE id=? LIMIT 1;";
    if($smt = $mysqli->prepare($query)) {
        $smt->bind_param('i',$id_user);
        $result = $smt->execute();
        if($result) {
            $result = get_result($smt);
            if(count($result)==1) {
                $row = array_shift($result);
                $return=$row;
            }
        }
    }
    return $return;
}

function get_user_role($id_user) {
    global $mysqli;
    $return = array();
    $query = "SELECT * FROM sml_users WHERE id=? LIMIT 1;";
    if($smt = $mysqli->prepare($query)) {
        $smt->bind_param('i',$id_user);
        $result = $smt->execute();
        if($result) {
            $result = get_result($smt);
            if(count($result)==1) {
                $row = array_shift($result);
                $return=$row['role'];
            }
        }
    }
    return $return;
}

function get_settings() {
    global $mysqli;
    $return = array();
    $query = "SELECT * FROM sml_settings LIMIT 1;";
    if($smt = $mysqli->prepare($query)) {
        $result = $smt->execute();
        if($result) {
            $result = get_result($smt);
            if(count($result)==1) {
                $row = array_shift($result);
                if(empty($row['languages_enabled'])) {
                    $row['languages_enabled']=array();
                    $row['languages_enabled']['en_US']=1;
                } else {
                    $row['languages_enabled']=json_decode($row['languages_enabled'],true);
                }
                $row['languages_count']=0;
                foreach ($row['languages_enabled'] as $lang) {
                    if($lang==1) {
                        $row['languages_count']++;
                    }
                }
                if($row['languages_count']==0) {
                    $row['languages_enabled']=array();
                    $row['languages_enabled']['en_US']=1;
                    $row['languages_count']=1;
                }
                $return=$row;
            }
        }
    }
    return $return;
}

function check_language_enabled($lang,$languages_enabled) {
    if(empty($languages_enabled) && $lang=='en_US') {
        return true;
    } else if(empty($languages_enabled[$lang])) {
        return false;
    } else if($languages_enabled[$lang]==1) {
        return true;
    } else {
        return false;
    }
}

function get_map($id_map,$id_user) {
    global $mysqli;
    $return = array();
    $query = "SELECT m.*,i.image as markers_icon_image FROM sml_maps as m 
                LEFT JOIN sml_icons as i ON i.id=m.markers_id_icon_library
                WHERE m.id=? LIMIT 1;";
    if($smt = $mysqli->prepare($query)) {
        $smt->bind_param('i',$id_map);
        $result = $smt->execute();
        if($result) {
            $result = get_result($smt);
            if(count($result)==1) {
                $row = array_shift($result);
                $id_user_m = $row['id_user'];
                switch(get_user_role($id_user)) {
                    case 'customer':
                        if($id_user!=$id_user_m) return false;
                        break;
                }
                $row['markers_icon'] = explode("|",$row['markers_icon'])[0];
                if(empty($row['markers_icon_image'])) $row['markers_icon_image']='';
                $return=$row;
            }
        }
    }
    return $return;
}

function get_category($id_category) {
    global $mysqli;
    $return = array();
    $query = "SELECT c.*,i.image as markers_icon_image FROM sml_categories as c 
           LEFT JOIN sml_icons as i ON i.id=c.markers_id_icon_library
           WHERE c.id=? LIMIT 1;";
    if($smt = $mysqli->prepare($query)) {
        $smt->bind_param('i',$id_category);
        $result = $smt->execute();
        if($result) {
            $result = get_result($smt);
            if(count($result)==1) {
                $row = array_shift($result);
                $row['markers_icon'] = explode("|",$row['markers_icon'])[0];
                if(empty($row['markers_icon_image'])) $row['markers_icon_image']='';
                $return=$row;
            }
        }
    }
    return $return;
}

function get_marker_count_category($id_category) {
    global $mysqli;
    $return = 0;
    $query = "SELECT COUNT(*) as num FROM sml_markers_categories_assoc WHERE id_category=?;";
    if($smt = $mysqli->prepare($query)) {
        $smt->bind_param('i',$id_category);
        $result = $smt->execute();
        if($result) {
            $result = get_result($smt);
            if(count($result)==1) {
                $row = array_shift($result);
                $return = $row['num'];
            }
        }
    }
    return $return;
}

function get_marker_count_map($id_map) {
    global $mysqli;
    $return = 0;
    $query = "SELECT COUNT(*) as num FROM sml_markers WHERE id_map=?;";
    if($smt = $mysqli->prepare($query)) {
        $smt->bind_param('i',$id_map);
        $result = $smt->execute();
        if($result) {
            $result = get_result($smt);
            if(count($result)==1) {
                $row = array_shift($result);
                $return = $row['num'];
            }
        }
    }
    return $return;
}

function get_array_coordinates($id_map) {
    global $mysqli;
    $return = array();
    $query = "SELECT lat,lon FROM sml_markers WHERE (lat IS NOT NULL AND lat != '') AND (lon IS NOT NULL AND lon != '') AND id_map=?;";
    if($smt = $mysqli->prepare($query)) {
        $smt->bind_param('i',$id_map);
        $result = $smt->execute();
        if($result) {
            $result = get_result($smt);
            if(count($result)>0) {
                while($row = array_shift($result)) {
                    array_push($return,array($row['lat'],$row['lon']));
                }
            }
        }
    }
    return $return;
}

function get_markers_to_validate($id_user) {
    global $mysqli;
    $return = array();
    $where = "";
    $role = get_user_role($id_user);
    switch($role) {
        case 'administrator':
            $where = " WHERE 1=1 ";
            break;
        case 'customer':
            $where = " WHERE 1=1 AND id_user=? ";
            break;
        case 'editor':
            $where = " WHERE 1=1 AND id IN () ";
            $query = "SELECT GROUP_CONCAT(id_map) as ids FROM sml_assign_maps WHERE id_user=$id_user;";
            $result = $mysqli->query($query);
            if($result) {
                if($result->num_rows==1) {
                    $row=$result->fetch_array(MYSQLI_ASSOC);
                    $ids = $row['ids'];
                    $where = " WHERE 1=1 AND id IN ($ids) ";
                }
            }
            break;
    }
    $return = 0;
    $query = "SELECT COUNT(*) as num FROM sml_markers WHERE to_validate=1 AND id_map IN (SELECT id FROM sml_maps $where)";
    if($smt = $mysqli->prepare($query)) {
        if($role=='customer') {
            $smt->bind_param('i',$id_user);
        }
        $result = $smt->execute();
        if($result) {
            $result = get_result($smt);
            if(count($result)>0) {
                $row = array_shift($result);
                $return=$row['num'];
            }
        }
    }
    return $return;
}

function get_maps($id_user) {
    global $mysqli;
    $return = array();
    $where = "";
    $role = get_user_role($id_user);
    switch($role) {
        case 'administrator':
            $where = " WHERE 1=1 ";
            break;
        case 'customer':
            $where = " WHERE 1=1 AND id_user=? ";
            break;
        case 'editor':
            $where = " WHERE 1=1 AND id IN () ";
            $query = "SELECT GROUP_CONCAT(id_map) as ids FROM sml_assign_maps WHERE id_user=$id_user;";
            $result = $mysqli->query($query);
            if($result) {
                if($result->num_rows==1) {
                    $row=$result->fetch_array(MYSQLI_ASSOC);
                    $ids = $row['ids'];
                    $where = " WHERE 1=1 AND id IN ($ids) ";
                }
            }
            break;
    }
    $query = "SELECT * FROM sml_maps $where ORDER BY date_created DESC;";
    if($smt = $mysqli->prepare($query)) {
        if($role=='customer') {
            $smt->bind_param('i',$id_user);
        }
        $result = $smt->execute();
        if($result) {
            $result = get_result($smt);
            if(count($result)>0) {
                while($row = array_shift($result)) {
                    $return[]=$row;
                }
            }
        }
    }
    return $return;
}

function get_marker($id_marker,$id_user) {
    global $mysqli;
    $return = array();
    $query = "SELECT k.*,m.id_user,i.image as icon_image,GROUP_CONCAT(DISTINCT ca.id_category) as id_categories FROM sml_markers as k
                JOIN sml_maps as m ON m.id=k.id_map
                LEFT JOIN sml_icons as i ON i.id=k.id_icon_library
                LEFT JOIN sml_markers_categories_assoc as ca ON ca.id_marker=k.id
                WHERE k.id=? GROUP BY k.id LIMIT 1;";
    if($smt = $mysqli->prepare($query)) {
        $smt->bind_param('i',$id_marker);
        $result = $smt->execute();
        if($result) {
            $result = get_result($smt);
            if(count($result)==1) {
                $row = array_shift($result);
                $id_user_m = $row['id_user'];
                switch(get_user_role($id_user)) {
                    case 'customer':
                        if($id_user!=$id_user_m) return false;
                        break;
                }
                $row['icon'] = explode("|",$row['icon'])[0];
                if(empty($row['icon_image'])) $row['icon_image']='';
                for($i=1;$i<=20;$i++) {
                    $row['extra_field_value_'.$i] = htmlspecialchars_decode($row['extra_field_value_'.$i]);
                    $row['extra_field_value_'.$i] = str_replace(["\r\n","\r","\n"], "<br>", $row['extra_field_value_'.$i]);
                    $row['extra_field_value_'.$i] = str_replace('"', '\"', $row['extra_field_value_'.$i]);
                }
                $row['extra_button_value_1'] = htmlspecialchars_decode($row['extra_button_value_1']);
                $row['extra_button_value_1'] = str_replace(["\r\n","\r","\n"], "<br>", $row['extra_button_value_1']);
                $row['extra_button_value_1'] = str_replace('"', '\"', $row['extra_button_value_1']);
                $return=$row;
            }
        }
    }
    return $return;
}

function get_library_icons($id_map) {
    global $mysqli;
    $return = "";
    $query = "SELECT * FROM sml_icons WHERE id_map=?;";
    if($smt = $mysqli->prepare($query)) {
        $smt->bind_param('i',$id_map);
        $result = $smt->execute();
        if($result) {
            $result = get_result($smt);
            if(count($result)>0) {
                while($row = array_shift($result)) {
                    $id = $row['id'];
                    $image = $row['image'];
                    $return .= "<img onclick='select_icon_library($id,\"$image\");' style='display: inline-block;width:50px;padding: 3px;' src='../viewer/icons/$image' />";
                }
            } else {
                $return = "No icons in this library.";
            }
        }
    }
    return $return;
}

function get_editor_permissions($id_user,$id_map) {
    global $mysqli;
    $query = "SELECT * FROM sml_assign_maps WHERE id_user=$id_user AND id_map=$id_map LIMIT 1;";
    $result = $mysqli->query($query);
    $return = array();
    if($result) {
        if($result->num_rows==1) {
            $row=$result->fetch_array(MYSQLI_ASSOC);
            $return=$row;
        }
    }
    return $return;
}

function get_reports($id_user) {
    global $mysqli;
    $array_maps = array();
    $array_num_markers = array();
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
    $query = "SELECT COUNT(k.id) as num_markers,m.name as map FROM sml_markers as k
                JOIN sml_maps as m ON m.id=k.id_map
                $where AND k.to_validate=0
                GROUP BY m.id;";
    if($smt = $mysqli->prepare($query)) {
        if($role=='customer') {
            $smt->bind_param('i',$id_marker);
        }
        $result = $smt->execute();
        if($result) {
            $result = get_result($smt);
            if(count($result)>0) {
                while($row = array_shift($result)) {
                    array_push($array_maps,$row['map']);
                    array_push($array_num_markers,$row['num_markers']);
                }
            }
        }
    }
    return json_encode(array("maps"=>$array_maps,"num_markers"=>$array_num_markers));
}

function get_categories($id_map,$id_cat_sel) {
    global $mysqli;
    $return = array();
    $id_cat_sel = explode(",",$id_cat_sel);
    $query = "SELECT c.id,IF(cc.name IS NULL,c.name,CONCAT(cc.name,' - ',c.name)) as category,(SELECT COUNT(*) FROM sml_categories WHERE c.id=id_category_parent) as count_c
                FROM sml_categories as c
                LEFT JOIN sml_categories as cc ON c.id_category_parent=cc.id
                WHERE c.id_map=? 
                ORDER BY category;";
    if($smt = $mysqli->prepare($query)) {
        $smt->bind_param('i',$id_map);
        $result = $smt->execute();
        if($result) {
            $result = get_result($smt);
            if(count($result)>0) {
                while($row = array_shift($result)) {
                    $id = $row['id'];
                    $category = $row['category'];
                    if(in_array($id,$id_cat_sel)) {
                        $selected = "selected";
                    } else {
                        $selected = "";
                    }
                    if($row['count_c']>0) {
                        $disabled = "disabled";
                    } else {
                        $disabled = "";
                    }
                    $return .= "<option $disabled $selected id='$id'>$category</option>";
                }
            }
        }
    }
    return $return;
}

function get_parent_categories($id_map,$id_cat_sel) {
    global $mysqli;
    $return = array();
    $query = "SELECT id,name FROM sml_categories WHERE id_map=? AND id_category_parent IS NULL ORDER BY name;";
    if($smt = $mysqli->prepare($query)) {
        $smt->bind_param('i',$id_map);
        $result = $smt->execute();
        if($result) {
            $result = get_result($smt);
            if(count($result)>0) {
                while($row = array_shift($result)) {
                    $id = $row['id'];
                    $name = $row['name'];
                    if($id==$id_cat_sel) {
                        $selected = "selected";
                    } else {
                        $selected = "";
                    }
                    $return .= "<option $selected id='$id'>$name</option>";
                }
            }
        }
    }
    return $return;
}

function get_markers_options($id_map,$id_marker_sel) {
    global $mysqli;
    $return = array();
    $query = "SELECT m.id,m.name FROM sml_markers as m WHERE m.id_map=? AND m.to_validate=0 ORDER BY m.name;";
    if($smt = $mysqli->prepare($query)) {
        $smt->bind_param('i',$id_map);
        $result = $smt->execute();
        if($result) {
            $result = get_result($smt);
            if(count($result)>0) {
                while($row = array_shift($result)) {
                    $id = $row['id'];
                    $name = $row['name'];
                    if($id==$id_marker_sel) {
                        $selected = "selected";
                    } else {
                        $selected = "";
                    }
                    $return .= "<option $selected id='$id'>$name</option>";
                }
            }
        }
    }
    return $return;
}

function get_users($id_user_sel) {
    global $mysqli;
    $options = "";
    $count = 0;
    $query = "SELECT id,username,role FROM sml_users WHERE role IN('customer','administrator') ORDER BY username;";
    if($smt = $mysqli->prepare($query)) {
        $result = $smt->execute();
        if($result) {
            $result = get_result($smt);
            $count = count($result);
            if($count>0) {
                while($row = array_shift($result)) {
                    $id_user = $row['id'];
                    $username = $row['username'];
                    $role = $row['role'];
                    if ($role == 'administrator') $username = $username . " (" . _("administrator") . ")";
                    if ($id_user == $id_user_sel) {
                        $options .= "<option selected id='$id_user'>$username</option>";
                    } else {
                        $options .= "<option id='$id_user'>$username</option>";
                    }
                }
            }
        }
    }
    return array("options"=>$options,"count"=>$count);
}

function get_maps_options_css() {
    global $mysqli;
    $return = "";
    $query = "SELECT code,name FROM sml_maps ORDER BY name ASC;";
    $result = $mysqli->query($query);
    if($result) {
        if($result->num_rows>0) {
            while($row=$result->fetch_array(MYSQLI_ASSOC)) {
                $code = $row['code'];
                $name = $row['name'];
                $return .= "<option id='css_custom_$code'>$name</option>";
            }
        }
    }
    return $return;
}

function get_editor_css_content($css) {
    if($css=='custom_b') {
        $url_css = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'backend'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'custom_b.css';
        if(file_exists($url_css)) {
            return @file_get_contents($url_css);
        } else {
            return '';
        }
    } else {
        $url_css = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'viewer'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.$css.'.css';
        if(file_exists($url_css)) {
            return @file_get_contents($url_css);
        } else {
            return '';
        }
    }
}

function get_maps_editors_css() {
    global $mysqli;
    $return = "";
    $query = "SELECT code FROM sml_maps;";
    $result = $mysqli->query($query);
    if($result) {
        if($result->num_rows>0) {
            while($row=$result->fetch_array(MYSQLI_ASSOC)) {
                $code = $row['code'];
                $return .= '<div style="display:none;position: relative;width: 100%;height: 400px;" class="editors_css" id="custom_'.$code.'">'.get_editor_css_content('custom_'.$code).'</div>';
            }
        }
    }
    return $return;
}

function get_next_prev_marker_id($id_marker,$id_map) {
    global $mysqli;
    $array_markers = array();
    $query = "SELECT id FROM sml_markers WHERE id_map=$id_map ORDER BY id;";
    $result = $mysqli->query($query);
    if($result) {
        while($row=$result->fetch_array(MYSQLI_ASSOC)) {
            $id = $row['id'];
            array_push($array_markers,$id);
        }
    }
    $index = array_search($id_marker,$array_markers);
    $len = count($array_markers);
    $prev_id = $array_markers[($index+$len-1)%$len];
    $next_id = $array_markers[($index+1)%$len];
    return [$next_id,$prev_id];
}

function print_map_selector() {
    $id_user = $_SESSION['id_user'];
    $maps = get_maps($id_user);
    $count_maps = count($maps);
    $array_list_map = array();
    if ($count_maps==1) {
        $id_map_sel = $maps[0]['id'];
        $name_map_sel = $maps[0]['name'];
        $author_map_sel = $maps[0]['author'];
        $_SESSION['id_map_sel'] = $id_map_sel;
        $_SESSION['name_map_sel'] = $name_map_sel;
        $array_list_map[] = array("id"=>$id_map_sel,"name"=>$name_map_sel,"author"=>$author_map_sel);
    } else {
        if(isset($_GET['id_map'])) {
            $id_map_sel = $_GET['id_map'];
            $name_map_sel = get_map($_GET['id_map'],$id_user)['name'];
            $_SESSION['id_map_sel'] = $id_map_sel;
            $_SESSION['name_map_sel'] = $name_map_sel;
        } else {
            if(isset($_SESSION['id_map_sel'])) {
                $id_map_sel = $_SESSION['id_map_sel'];
            } else {
                $id_map_sel = $maps[0]['id'];
                $name_map_sel = $maps[0]['name'];
                $_SESSION['id_map_sel'] = $id_map_sel;
                $_SESSION['name_map_sel'] = $name_map_sel;
            }
        }
        foreach ($maps as $map) {
            $id_map = $map['id'];
            $name_map = $map['name'];
            $author_map = $map['author'];
            $array_list_map[] = array("id"=>$id_map,"name"=>$name_map,"author"=>$author_map);
        }
    }
    $return = '<div class="map_select_header">';
    $return .= '<i id="loading_header" class="fas fa-spin fa-spinner"></i>';
    $return .= '<select onchange="change_map();" id="map_selector" class="selectpicker" data-container="body" data-width="fit" data-live-search="true" data-style="map_selector_btn" data-none-results-text="'._("No results matched").' {0}">';
    foreach ($array_list_map as $map) {
        $map['name'] = htmlentities($map['name']);
        $map['author'] = htmlentities($map['author']);
        $name_map = strlen($map['name']) > 30 ? substr($map['name'],0,30)."..." : $map['name'];
        $return .= "<option data-subtext=\"".$map['author']."\" ".(($id_map_sel==$map['id']) ? 'selected' : '')." id='".$map['id']."'>".$name_map."</option>";
    }
    $return .= '</select>';
    $return .= '<a href="index.php?p=edit_map&id='.$id_map_sel.'" class="quick_action"><i class="fas fa-edit"></i></a>';
    $return .= '</div>';
    return $return;
}

function formatTime($format, $language = null, $timestamp = null) {
    switch ($language) {
        case 'ar_SA':
            setlocale(LC_TIME, 'ar_SA.utf8', 'ar_SA.UTF-8', 'ar_SA', 'ar');
            break;
        case 'zh_CN':
            setlocale(LC_TIME, 'zh_CN.utf8', 'zh_CN.UTF-8', 'zh_CN', 'zh');
            break;
        case 'zh_HK':
            setlocale(LC_TIME, 'zh_HK.utf8', 'zh_HK.UTF-8', 'zh_HK', 'zh');
            break;
        case 'zh_TW':
            setlocale(LC_TIME, 'zh_TW.utf8', 'zh_TW.UTF-8', 'zh_TW', 'zh');
            break;
        case 'ja_JP':
            setlocale(LC_TIME, 'ja_JP.utf8', 'ja_JP.UTF-8', 'ja_JP', 'ja');
            break;
        case 'hu_HU':
            setlocale(LC_TIME, 'hu_HU.utf8', 'hu_HU.UTF-8', 'hu_HU', 'hr');
            break;
        case 'ru_RU':
            setlocale(LC_TIME, 'ru_RU.utf8', 'ru_RU.UTF-8', 'ru_RU', 'ru');
            break;
        case 'de_DE':
            setlocale(LC_TIME, 'de_DE.utf8', 'de_DE.UTF-8', 'de_DE', 'de');
            break;
        case 'fr_FR':
            setlocale(LC_TIME, 'fr_FR.utf8', 'fr_FR.UTF-8', 'fr_FR', 'fr');
            break;
        case 'pl_PL':
            setlocale(LC_TIME, 'pl_PL.utf8', 'pl_PL.UTF-8', 'pl_PL', 'pl');
            break;
        case 'tr_TR':
            setlocale(LC_TIME, 'tr_TR.utf8', 'tr_TR.UTF-8', 'tr_TR', 'tr');
            break;
        case 'cs_CZ':
            setlocale(LC_TIME, 'cs_CZ.utf8', 'cs_CZ.UTF-8', 'cs_CZ', 'cz');
            break;
        case 'rw_RW':
            setlocale(LC_TIME, 'rw_RW.utf8', 'rw_RW.UTF-8', 'rw_RW', 'rw');
            break;
        case 'fil_PH':
            setlocale(LC_TIME, 'fil_PH.utf8', 'fil_PH.UTF-8', 'fil_PH', 'ph');
            break;
        case 'fa_IR':
            setlocale(LC_TIME, 'fa_IR.utf8', 'fa_IR.UTF-8', 'fa_IR', 'ir');
            break;
        case 'tg_TJ':
            setlocale(LC_TIME, 'tg_TJ.utf8', 'tg_TJ.UTF-8', 'tg_TJ', 'tj');
            break;
        default:
            setlocale(LC_TIME, $language);
            break;
    }
    if (!is_numeric($timestamp)) {
        $datetime = strftime($format);
    } else {
        $datetime = strftime($format, $timestamp);
    }
    return $datetime;
}

function get_ip_server() {
    $server_ip = '';
    $server_name = $_SERVER['SERVER_NAME'];
    if(array_key_exists('SERVER_ADDR', $_SERVER)) {
        $server_ip = $_SERVER['SERVER_ADDR'];
        if(!filter_var($server_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $server_ip = gethostbyname($server_name);
        }
    } elseif(array_key_exists('LOCAL_ADDR', $_SERVER)) {
        $server_ip = $_SERVER['LOCAL_ADDR'];
    } elseif(array_key_exists('SERVER_NAME', $_SERVER)) {
        $server_ip = gethostbyname($_SERVER['SERVER_NAME']);
    } else {
        if(stristr(PHP_OS, 'WIN')) {
            $server_ip = gethostbyname(php_uname("n"));
        } else {
            $ifconfig = shell_exec('/sbin/ifconfig eth0');
            preg_match('/addr:([\d\.]+)/', $ifconfig, $match);
            $server_ip = $match[1];
        }
    }
    return $server_ip;
}

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function get_result(\mysqli_stmt $statement) {
    $result = array();
    $statement->store_result();
    for ($i = 0; $i < $statement->num_rows; $i++)
    {
        $metadata = $statement->result_metadata();
        $params = array();
        while ($field = $metadata->fetch_field())
        {
            $params[] = &$result[$i][$field->name];
        }
        call_user_func_array(array($statement, 'bind_result'), $params);
        $statement->fetch();
    }
    return $result;
}

function curl_get_file_contents($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT, base64_decode('c21sX3VzZXJfYWdlbnQ='));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function getCenterLatLng($coordinates) {
    $x = $y = $z = 0;
    $n = count($coordinates);
    foreach ($coordinates as $point) {
        $lt = $point[0] * pi() / 180;
        $lg = $point[1] * pi() / 180;
        $x += cos($lt) * cos($lg);
        $y += cos($lt) * sin($lg);
        $z += sin($lt);
    }
    $x /= $n;
    $y /= $n;
    return [atan2(($z / $n), sqrt($x * $x + $y * $y)) * 180 / pi(), atan2($y, $x) * 180 / pi()];
}