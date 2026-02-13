<?php
session_start();
require_once("functions.php");
$id_map = $_GET['id'];
$map = get_map($id_map,$_SESSION['id_user']);
if($map!==false) {
    $count_markers = get_marker_count_map($id_map);
    $users = get_users($map['id_user']);
    $user_role = get_user_role($_SESSION['id_user']);
    $settings = get_settings();
    if($user_info['role']=='editor') {
        $editor_permissions = get_editor_permissions($_SESSION['id_user'],$id_map);
        if($editor_permissions['edit_map']==0) {
            $map=false;
        }
    }
    $_SESSION['id_map_sel'] = $id_map;
    $_SESSION['name_map_sel'] = $map['name'];
    if(empty($map['markers_color_hex'])) $map['markers_color_hex']=$map['main_color_hex'];
}
?>

<?php if(!$map): ?>
    <div class="text-center">
        <div class="error mx-auto" data-text="401">401</div>
        <p class="lead text-gray-800 mb-5"><?php echo _("Permission denied"); ?></p>
        <p class="text-gray-500 mb-0"><?php echo _("It looks like you found a glitch in the matrix..."); ?></p>
        <a href="index.php?p=dashboard">← <?php echo _("Back to Dashboard"); ?></a>
    </div>
    <script>
        $('.map_select_header').remove();
    </script>
<?php die(); endif; ?>

<div class="row">
    <div class="col-md-8 p-0">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-cog"></i> <?php echo _("Settings"); ?></h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name"><?php echo _("Name"); ?> *</label>
                                <input type="text" class="form-control" id="name" value="<?php echo $map['name']; ?>" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="user"><?php echo _("User"); ?> <i title="<?php echo _("owner of the map"); ?>" class="help_t fas fa-question-circle"></i></label>
                                <select id="user" class="form-control" <?php echo ($user_role=='administrator' && $users['count']>1) ? '' : 'disabled' ?> >
                                    <?php echo $users['options']; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="language"><?php echo _("Language"); ?></label>
                                <select class="form-control" id="language">
                                    <option <?php echo ($map['language']=='') ? 'selected':''; ?> id=""><?php echo _("Default Language"); ?></option>
                                    <?php if (check_language_enabled('ar_SA',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='ar_SA') ? 'selected':''; ?> id="ar_SA">Arabic (ar_SA)</option><?php endif; ?>
                                    <?php if (check_language_enabled('zh_CN',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='zh_CN') ? 'selected':''; ?> id="zh_CN">Chinese simplified (zh_CN)</option><?php endif; ?>
                                    <?php if (check_language_enabled('zh_HK',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='zh_HK') ? 'selected':''; ?> id="zh_HK">Chinese traditional (zh_HK)</option><?php endif; ?>
                                    <?php if (check_language_enabled('zh_TW',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='zh_TW') ? 'selected':''; ?> id="zh_TW">Chinese traditional (zh_TW)</option><?php endif; ?>
                                    <?php if (check_language_enabled('cs_CZ',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='cs_CZ') ? 'selected':''; ?> id="cs_CZ">Czech (cs_CZ)</option><?php endif; ?>
                                    <?php if (check_language_enabled('nl_NL',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='nl_NL') ? 'selected':''; ?> id="nl_NL">Dutch (nl_NL)</option><?php endif; ?>
                                    <?php if (check_language_enabled('en_US',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='en_US') ? 'selected':''; ?> id="en_US">English (en_US)</option><?php endif; ?>
                                    <?php if (check_language_enabled('fil_PH',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='fil_PH') ? 'selected':''; ?> id="fil_PH">Filipino (fil_PH)</option><?php endif; ?>
                                    <?php if (check_language_enabled('fr_FR',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='fr_FR') ? 'selected':''; ?> id="fr_FR">French (fr_FR)</option><?php endif; ?>
                                    <?php if (check_language_enabled('de_DE',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='de_DE') ? 'selected':''; ?> id="de_DE">German (de_DE)</option><?php endif; ?>
                                    <?php if (check_language_enabled('hi_IN',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='hi_IN') ? 'selected':''; ?> id="hi_IN">Hindi (hi_IN)</option><?php endif; ?>
                                    <?php if (check_language_enabled('kw_KW',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='kw_KW') ? 'selected':''; ?> id="kw_KW">Kinyarwanda (kw_KW)</option><?php endif; ?>
                                    <?php if (check_language_enabled('ko_KR',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='ko_KR') ? 'selected':''; ?> id="ko_KR">Korean (ko_KR)</option><?php endif; ?>
                                    <?php if (check_language_enabled('id_ID',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='id_ID') ? 'selected':''; ?> id="id_ID">Indonesian (id_ID)</option><?php endif; ?>
                                    <?php if (check_language_enabled('hu_HU',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='hu_HU') ? 'selected':''; ?> id="hu_HU">Hungarian (hu_HU)</option><?php endif; ?>
                                    <?php if (check_language_enabled('it_IT',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='it_IT') ? 'selected':''; ?> id="it_IT">Italian (it_IT)</option><?php endif; ?>
                                    <?php if (check_language_enabled('ja_JP',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='ja_JP') ? 'selected':''; ?> id="ja_JP">Japanese (ja_JP)</option><?php endif; ?>
                                    <?php if (check_language_enabled('fa_IR',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='fa_IR') ? 'selected':''; ?> id="fa_IR">Persian (fa_IR)</option><?php endif; ?>
                                    <?php if (check_language_enabled('pl_PL',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='pl_PL') ? 'selected':''; ?> id="pl_PL">Polish (pl_PL)</option><?php endif; ?>
                                    <?php if (check_language_enabled('pt_BR',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='pt_BR') ? 'selected':''; ?> id="pt_BR">Portuguese Brazilian (pt_BR)</option><?php endif; ?>
                                    <?php if (check_language_enabled('pt_PT',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='pt_PT') ? 'selected':''; ?> id="pt_PT">Portuguese European (pt_PT)</option><?php endif; ?>
                                    <?php if (check_language_enabled('es_ES',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='es_ES') ? 'selected':''; ?> id="es_ES">Spanish (es_ES)</option><?php endif; ?>
                                    <?php if (check_language_enabled('ro_RO',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='ro_RO') ? 'selected':''; ?> id="ro_RO">Romanian (ro_RO)</option><?php endif; ?>
                                    <?php if (check_language_enabled('ru_RU',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='ru_RU') ? 'selected':''; ?> id="ru_RU">Russian (ru_RU)</option><?php endif; ?>
                                    <?php if (check_language_enabled('sv_SE',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='sv_SE') ? 'selected':''; ?> id="sv_SE">Swedish (sv_SE)</option><?php endif; ?>
                                    <?php if (check_language_enabled('tg_TJ',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='tg_TJ') ? 'selected':''; ?> id="tg_TJ">Tajik (tg_TJ)</option><?php endif; ?>
                                    <?php if (check_language_enabled('th_TH',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='th_TH') ? 'selected':''; ?> id="th_TH">Thai (th_TH)</option><?php endif; ?>
                                    <?php if (check_language_enabled('tr_TR',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='tr_TR') ? 'selected':''; ?> id="tr_TR">Turkish (tr_TR)</option><?php endif; ?>
                                    <?php if (check_language_enabled('vi_VN',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='vi_VN') ? 'selected':''; ?> id="vi_VN">Vietnamese (vi_VN)</option><?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="enable_search"><?php echo _("Search"); ?> <i title="<?php echo _("enable the search box in the map"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <input type="checkbox" id="enable_search" <?php echo ($map['enable_search']) ? 'checked' : ''; ?> />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="enable_search_location"><?php echo _("Search Locations"); ?> <i title="<?php echo _("enables the search for a location / city"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <input type="checkbox" id="enable_search_location" <?php echo ($map['enable_search_location']) ? 'checked' : ''; ?> />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="enable_list"><?php echo _("List"); ?> <i title="<?php echo _("enables the list of markers in the map"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <input type="checkbox" id="enable_list" <?php echo ($map['enable_list']) ? 'checked' : ''; ?> />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="enable_categories"><?php echo _("Categories"); ?> <i title="<?php echo _("enable the category filter in the map"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <input type="checkbox" id="enable_categories" <?php echo ($map['enable_categories']) ? 'checked' : ''; ?> />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="show_list"><?php echo _("Auto show List"); ?> <i title="<?php echo _("automatically open the marker's list at first map loading"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <input type="checkbox" id="show_list" <?php echo ($map['show_list']) ? 'checked' : ''; ?> />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cat_filter"><?php echo _("Auto show Categories"); ?> <i title="<?php echo _("automatically open the categories filter"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <input type="checkbox" id="cat_filter" <?php echo ($map['cat_filter']) ? 'checked' : ''; ?> />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="default_my_location"><?php echo _("Auto show My Location"); ?> <i title="<?php echo _("automatically geolocate and show the location"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <input type="checkbox" id="default_my_location" <?php echo ($map['default_my_location']) ? 'checked' : ''; ?> />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nav_markers"><?php echo _("Control Arrows Markers"); ?> <i title="<?php echo _("show controls arrows to loop through the markers"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <input type="checkbox" id="nav_markers" <?php echo ($map['nav_markers']) ? 'checked' : ''; ?> />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="enable_story"><?php echo _("Story"); ?> <i title="<?php echo _("enables the story feature in the map"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <input type="checkbox" id="enable_story" <?php echo ($map['enable_story']) ? 'checked' : ''; ?> />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="enable_popup"><?php echo _("Popup"); ?> <i title="<?php echo _("display popup on hover over markers"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <input type="checkbox" id="enable_popup" <?php echo ($map['enable_popup']) ? 'checked' : ''; ?> />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sheet_detail"><?php echo _("Details Window"); ?> <i title="<?php echo _("opens the details window by clicking on markers"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <input type="checkbox" id="sheet_detail" <?php echo ($map['sheet_detail']) ? 'checked' : ''; ?> />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="enable_directions"><?php echo _("Directions"); ?> <i title="<?php echo _("enable the button 'directions' for markers"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <input type="checkbox" id="enable_directions" <?php echo ($map['enable_directions']) ? 'checked' : ''; ?> />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="enable_streetview"><?php echo _("Street View"); ?> <i title="<?php echo _("enable the button 'street view' for markers"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <input type="checkbox" id="enable_streetview" <?php echo ($map['enable_streetview']) ? 'checked' : ''; ?> />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="enable_reviews"><?php echo _("Reviews"); ?> <i title="<?php echo _("enable the reviews for markers"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <input type="checkbox" id="enable_reviews" <?php echo ($map['enable_reviews']) ? 'checked' : ''; ?> />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="enable_share"><?php echo _("Share"); ?> <i title="<?php echo _("enable share buttons for markers"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <input type="checkbox" id="enable_share" <?php echo ($map['enable_share']) ? 'checked' : ''; ?> />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="enable_add_marker"><?php echo _("Add Markers from Map"); ?> <i title="<?php echo _("enables adding markers directly from the map"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <select id="enable_add_marker" class="form-control">
                                    <option <?php echo ($map['enable_add_marker']==0) ? 'selected' : ''; ?> id="0"><?php echo _("Disabled"); ?></option>
                                    <option <?php echo ($map['enable_add_marker']==1) ? 'selected' : ''; ?> id="1"><?php echo _("Enabled"); ?></option>
                                    <option <?php echo ($map['enable_add_marker']==2) ? 'selected' : ''; ?> id="2"><?php echo _("Enabled (Automatic Approval)"); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="density_color"><?php echo _("Density color"); ?> <i title="<?php echo _("displays different colors based on the density of marker grouping"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <input type="checkbox" id="density_color" <?php echo ($map['density_color']) ? 'checked' : ''; ?> />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="density_color_tolerance"><?php echo _("Density Color Tolerance"); ?> <i title="<?php echo _("tolerance to color the grouped markers"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <input min="0" step="1" type="number" class="form-control" id="density_color_tolerance" value="<?php echo $map['density_color_tolerance']; ?>" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cluster_distance"><?php echo _("Density Distance"); ?> <i title="<?php echo _("distance in pixels within which features will be clustered together. 0 to disable clustering"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <input min="0" step="1" type="number" class="form-control" id="cluster_distance" value="<?php echo $map['cluster_distance']; ?>" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="default_zoom"><?php echo _("Initial Zoom"); ?> <i title="<?php echo _("initial zoom when the map is loaded (1 far - 20 near)"); ?>" class="help_t fas fa-question-circle"></i></label>
                                <select id="default_zoom" class="form-control">
                                    <option <?php echo ($map['default_zoom']==0) ? 'selected' : ''; ?> id="0"><?php echo _("Fit Markers"); ?></option>
                                    <?php
                                    for($i=1;$i<=20;$i++) {
                                        if($map['default_zoom']==$i) {
                                            echo "<option selected id='$i'>$i</option>";
                                        } else {
                                            echo "<option id='$i'>$i</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="selected_zoom"><?php echo _("Marker Zoom"); ?> <i title="<?php echo _("zoom level when the marker is clicked (1 far - 20 near)"); ?>" class="help_t fas fa-question-circle"></i></label>
                                <select id="selected_zoom" class="form-control">
                                    <option <?php echo ($map['selected_zoom']==0) ? 'selected' : ''; ?> id="0"><?php echo _("No change"); ?></option>
                                    <?php
                                    for($i=1;$i<=20;$i++) {
                                        if($map['selected_zoom']==$i) {
                                            echo "<option selected id='$i'>$i</option>";
                                        } else {
                                            echo "<option id='$i'>$i</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="my_location_zoom"><?php echo _("My Location Zoom"); ?> <i title="<?php echo _("zoom level when enabling geolocation (1 far - 20 near)"); ?>" class="help_t fas fa-question-circle"></i></label>
                                <select id="my_location_zoom" class="form-control">
                                    <option <?php echo ($map['my_location_zoom']==0) ? 'selected' : ''; ?> id="0"><?php echo _("No change"); ?></option>
                                    <?php
                                    for($i=1;$i<=20;$i++) {
                                        if($map['my_location_zoom']==$i) {
                                            echo "<option selected id='$i'>$i</option>";
                                        } else {
                                            echo "<option id='$i'>$i</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search_highlight"><?php echo _("Highlight Markers (Search)"); ?> <i title="<?php echo _("highlights the position of the marker when you hover over the search results (1 far - 20 near)"); ?>" class="help_t fas fa-question-circle"></i></label>
                                <select id="search_highlight" class="form-control">
                                    <option <?php echo ($map['search_highlight']==-1) ? 'selected' : ''; ?> id="-1"><?php echo _("Disabled"); ?></option>
                                    <option <?php echo ($map['search_highlight']==0) ? 'selected' : ''; ?> id="0"><?php echo _("Keep Zoom Level"); ?></option>
                                    <?php
                                    for($i=1;$i<=20;$i++) {
                                        if($map['search_highlight']==$i) {
                                            echo "<option selected id='$i'>$i</option>";
                                        } else {
                                            echo "<option id='$i'>$i</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="order_by"><?php echo _("Markers Order by"); ?></label><br>
                                <select id="order_by" class="form-control">
                                    <option <?php echo ($map['order_by']=='priority') ? 'selected' : ''; ?> id="priority"><?php echo _("Priority"); ?></option>
                                    <option <?php echo ($map['order_by']=='name') ? 'selected' : ''; ?> id="name"><?php echo _("Name"); ?></option>
                                    <option <?php echo ($map['order_by']=='city') ? 'selected' : ''; ?> id="city"><?php echo _("City"); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cat_filter_type"><?php echo _("Categories Filter Type"); ?></label><br>
                                <select id="cat_filter_type" class="form-control">
                                    <option <?php echo ($map['cat_filter_type']=='or') ? 'selected' : ''; ?> id="or"><?php echo _("OR"); ?></option>
                                    <option <?php echo ($map['cat_filter_type']=='and') ? 'selected' : ''; ?> id="and"><?php echo _("AND"); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="quality"><?php echo _("Quality"); ?> <i title="<?php echo _("map quality (high can slow movement)"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <select id="quality" class="form-control">
                                    <option <?php echo ($map['quality']=='low') ? 'selected' : ''; ?> id="low"><?php echo _("Low"); ?></option>
                                    <option <?php echo ($map['quality']=='medium') ? 'selected' : ''; ?> id="medium"><?php echo _("Medium"); ?></option>
                                    <option <?php echo ($map['quality']=='high') ? 'selected' : ''; ?> id="high"><?php echo _("High"); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="weather"><?php echo _("Weather"); ?></label><br>
                                <select id="weather" class="form-control">
                                    <option <?php echo ($map['weather']=='none') ? 'selected' : ''; ?> id="none"><?php echo _("None"); ?></option>
                                    <option <?php echo ($map['weather']=='celsius') ? 'selected' : ''; ?> id="celsius"><?php echo _("Celsius"); ?></option>
                                    <option <?php echo ($map['weather']=='fahrenheit') ? 'selected' : ''; ?> id="fahrenheit"><?php echo _("Fahrenheit"); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ga_tracking_id"><?php echo _("Google Analytics"); ?> <i title="<?php echo _("Google Analytics Tracking ID. Note: Use the Friendly URL in Google Analytics's property url setting."); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                <input type="text" class="form-control" id="ga_tracking_id" value="<?php echo $map['ga_tracking_id']; ?>" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-map"></i> <?php echo _("Map"); ?></h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="map_style col-lg-2 col-md-3 col-sm-4 col-xs-6" id="osm_free">
                            <img src="img/default.jpg">
                        </div>
                        <div class="map_style col-lg-2 col-md-3 col-sm-4 col-xs-6" id="custom">
                            <img src="img/custom.jpg">
                        </div>
                        <div class="map_style col-lg-2 col-md-3 col-sm-4 col-xs-6" id="basic">
                            <img src="img/basic.png">
                        </div>
                        <div class="map_style col-lg-2 col-md-3 col-sm-4 col-xs-6" id="bright">
                            <img src="img/bright.png">
                        </div>
                        <div class="map_style col-lg-2 col-md-3 col-sm-4 col-xs-6" id="outdoor">
                            <img src="img/outdoor.png">
                        </div>
                        <div class="map_style col-lg-2 col-md-3 col-sm-4 col-xs-6" id="pastel">
                            <img src="img/pastel.png">
                        </div>
                        <div class="map_style col-lg-2 col-md-3 col-sm-4 col-xs-6" id="hybrid">
                            <img src="img/hybrid.png">
                        </div>
                        <div class="map_style col-lg-2 col-md-3 col-sm-4 col-xs-6" id="streets">
                            <img src="img/streets.png">
                        </div>
                        <div class="map_style col-lg-2 col-md-3 col-sm-4 col-xs-6" id="topo">
                            <img src="img/topo.png">
                        </div>
                        <div class="map_style col-lg-2 col-md-3 col-sm-4 col-xs-6" id="topographique">
                            <img src="img/topographique.png">
                        </div>
                        <div class="map_style col-lg-2 col-md-3 col-sm-4 col-xs-6" id="voyager">
                            <img src="img/voyager.png">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-4">
                            <div class="form-group">
                                <label for="maptiler_api">Maptiler API KEY <i title="<?php echo _("only needed if map style is different from default or custom version"); ?>" class="help_t fas fa-question-circle"></i> (<a target="_blank" href="https://www.maptiler.com/cloud/plans/"><?php echo _("link"); ?></a>)</label>
                                <input <?php echo ($map['map_style']=='osm_free' || $map['map_style']=='custom') ? 'disabled' : ''; ?> type="text" class="form-control" id="maptiler_api" value="<?php echo $map['maptiler_api']; ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="street_basemap"><?php echo _("Street Url"); ?></label>
                                <input <?php echo ($map['map_style']!='custom') ? 'disabled' : ''; ?> type="text" class="form-control" id="street_basemap" value="<?php echo $map['street_basemap']; ?>" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="street_maxzoom"><?php echo _("Street Max Zoom"); ?></label>
                                <input <?php echo ($map['map_style']!='custom') ? 'disabled' : ''; ?> type="text" class="form-control" id="street_maxzoom" value="<?php echo $map['street_maxzoom']; ?>" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="street_attributions"><?php echo _("Street Attributions"); ?></label>
                                <input <?php echo ($map['map_style']!='custom') ? 'disabled' : ''; ?> type="text" class="form-control" id="street_attributions" value="<?php echo $map['street_attributions']; ?>" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="satellite_basemap"><?php echo _("Satellite Url"); ?></label>
                                <input <?php echo ($map['map_style']!='custom') ? 'disabled' : ''; ?> type="text" class="form-control" id="satellite_basemap" value="<?php echo $map['satellite_basemap']; ?>" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="satellite_maxzoom"><?php echo _("Satellite Max Zoom"); ?></label>
                                <input <?php echo ($map['map_style']!='custom') ? 'disabled' : ''; ?> type="text" class="form-control" id="satellite_maxzoom" value="<?php echo $map['satellite_maxzoom']; ?>" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="satellite_attributions"><?php echo _("Satellite Attributions"); ?></label>
                                <input <?php echo ($map['map_style']!='custom') ? 'disabled' : ''; ?> type="text" class="form-control" id="satellite_attributions" value="<?php echo $map['satellite_attributions']; ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="default_view"><?php echo _("Default view"); ?></label><br>
                                <select id="default_view" class="form-control">
                                    <option <?php echo ($map['default_view']=='street') ? 'selected' : ''; ?> id="street"><?php echo _("Street"); ?></option>
                                    <option <?php echo ($map['default_view']=='satellite') ? 'selected' : ''; ?> id="satellite"><?php echo _("Satellite"); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="enable_globe"><?php echo _("Globe view"); ?></label><br>
                                <select id="enable_globe" class="form-control">
                                    <option <?php echo ($map['enable_globe']==0) ? 'selected' : ''; ?> id="0"><?php echo _("Disabled"); ?></option>
                                    <option <?php echo ($map['enable_globe']==1) ? 'selected' : ''; ?> id="1"><?php echo _("Enabled"); ?></option>
                                    <option <?php echo ($map['enable_globe']==2) ? 'selected' : ''; ?> id="2"><?php echo _("Enabled (autostart)"); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-palette"></i> <?php echo _("Style"); ?></h6>
            </div>
            <div class="card-body">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="main_color_hex"><?php echo _("Main color"); ?></label>
                        <input type="text" class="form-control" id="main_color_hex" value="<?php echo $map['main_color_hex']; ?>" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="font_viewer"><?php echo _("Font Viewer"); ?></label><br>
                        <input type="text" class="form-control" id="font_viewer" value="<?php echo $map['font_viewer']; ?>" />
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-image"></i> <?php echo _("Logo"); ?> (PNG)</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div style="display: none" id="div_image_logo" class="col-md-12">
                        <img style="width: 100%" src="../viewer/logos/<?php echo $map['logo']; ?>" />
                    </div>
                    <div style="display: none" id="div_delete_logo" class="col-md-12 mt-4">
                        <button <?php echo ($demo) ? 'disabled':''; ?> onclick="delete_logo();" class="btn btn-block btn-danger"><?php echo _("DELETE LOGO"); ?></button>
                    </div>
                    <div style="display: none" class="mt-4" id="div_upload_logo">
                        <form id="frm" action="ajax/upload_logo_image.php" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="file" class="form-control" id="txtFile" name="txtFile" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input <?php echo ($demo) ? 'disabled':''; ?> type="submit" class="btn btn-block btn-success" id="btnUpload" value="<?php echo _("Upload Logo Image"); ?>" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="preview text-center">
                                        <div class="progress mb-3 mb-sm-3 mb-lg-0 mb-xl-0" style="height: 2.35rem;display: none">
                                            <div class="progress-bar" id="progressBar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                0%
                                            </div>
                                        </div>
                                        <div style="display: none;padding: .38rem;" class="alert alert-danger" id="error"></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-map-marker-alt"></i> <?php echo _("Markers Icon"); ?></h6>
            </div>
            <div class="card-body">
                <div class="col-md-12">
                    <div class="form-group">
                        <label><?php echo _("Default Markers Icon Type"); ?></label>
                        <select id="icon_type" onchange="change_icon_type();" class="form-control">
                            <option <?php echo ($map['markers_icon']=='' && $map['markers_icon_image']=='') ? 'selected' : '' ; ?> id="0"><?php echo _("None"); ?></option>
                            <option <?php echo ($map['markers_icon']!='' && $map['markers_icon_image']=='') ? 'selected' : '' ; ?> id="1"><?php echo _("Default"); ?></option>
                            <option <?php echo ($map['markers_icon_image']!='') ? 'selected' : '' ; ?> id="2"><?php echo _("Custom Icon Library"); ?></option>
                        </select>
                    </div>
                </div>
                <div id="icon_type_1" class="col-md-12 <?php echo ($map['markers_icon']!='' && $map['markers_icon_image']=='') ? '' : 'd-none' ; ?>">
                    <div class="form-group">
                        <label><?php echo _("Default Markers Icon"); ?></label><br>
                        <button class="btn btn-sm btn-primary" type="button" id="GetIconPicker" data-iconpicker-input="input#marker_icon_input" data-iconpicker-preview="i#marker_icon"><?php echo _("Select Icon"); ?></button>
                        <input readonly type="text" id="marker_icon_input" name="Icon" value="<?php echo $map['markers_icon']; ?>" required="" placeholder="" autocomplete="off" spellcheck="false">
                        <div class="icon-preview d-inline-block ml-1" data-toggle="tooltip" title="">
                            <i id="marker_icon" class="<?php echo $map['markers_icon']; ?>"></i>
                        </div>
                    </div>
                </div>
                <div id="icon_type_2" class="col-md-12 <?php echo ($map['markers_icon_image']!='') ? '' : 'd-none' ; ?>">
                    <div class="form-group">
                        <label for="marker_library_icon"><?php echo _("Default Markers Icon"); ?></label><br>
                        <button data-toggle="modal" data-target="#modal_library_icons" class="btn btn-sm btn-primary" type="button" id="btn_library_icon"><?php echo _("Select Library Icon"); ?></button>
                        <input type="hidden" id="marker_library_icon" value="<?php echo $map['markers_id_icon_library']; ?>">
                        <img id="marker_library_icon_preview" style="height: 30px;display: <?php echo ($map['markers_icon_image']=='') ? 'none':'inline-block'; ?>" src="../viewer/icons/<?php echo $map['markers_icon_image']; ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group <?php echo ($map['markers_icon_image']!='') ? 'disabled':''; ?>">
                        <label><?php echo _("Default Markers Icon Background"); ?></label>
                        <input type="text" class="form-control" id="color_hex" value="<?php echo $map['markers_color_hex']; ?>" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group <?php echo ($map['markers_icon_image']!='') ? 'disabled':''; ?>">
                        <label><?php echo _("Default Markers Icon Color"); ?></label>
                        <input type="text" class="form-control" id="icon_color_hex" value="<?php echo $map['markers_icon_color_hex']; ?>" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="markers_size"><?php echo _("Default Markers Size"); ?> (<span id="markers_size_value"><?php echo $map['markers_size']; ?></span>)</label><br>
                        <input oninput="change_markers_size();" onchange="change_markers_size();" type="range" step="0.1" min="0.5" max="2.0" class="form-control-range" id="markers_size" value="<?php echo $map['markers_size']; ?>" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <button id="btn_assign_icons" onclick="assign_icon_to_markers('map',<?php echo $id_map; ?>)" class="btn btn-block btn-primary <?php echo ($demo) ? 'disabled':''; ?> <?php echo ($count_markers==0) ? 'disabled' : ''; ?>"><?php echo _("ASSIGN ICON TO")." ".$count_markers." "._("MARKERS"); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-atlas"></i> GeoJSON</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <label><?php echo _("Link or upload Json"); ?></label>
                        <input type="text" class="form-control" id="geoJSON" value="<?php echo $map['geoJSON']; ?>" />
                    </div>
                    <div class="mt-4" id="div_upload_geojson">
                        <form id="frm_geojson" action="ajax/upload_geojson.php" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="file" class="form-control" id="txtFile_geojson" name="txtFile_geojson" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input <?php echo ($demo) ? 'disabled':''; ?> type="submit" class="btn btn-block btn-success" id="btnUpload_geojson" value="<?php echo _("Upload Json"); ?>" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="preview text-center">
                                        <div class="progress mb-3 mb-sm-3 mb-lg-0 mb-xl-0" style="height: 2.35rem;display: none">
                                            <div class="progress-bar" id="progressBar_geojson" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                0%
                                            </div>
                                        </div>
                                        <div style="display: none;padding: .38rem;" class="alert alert-danger" id="error_geojson"></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal_library_icons" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _("Library Icons"); ?></h5>
            </div>
            <div class="modal-body">
                <?php echo get_library_icons($id_map); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict";
        window.id_map = <?php echo $id_map; ?>;
        var map_style = '<?php echo $map['map_style']; ?>';
        window.logo = '<?php echo $map['logo']; ?>';
        window.main_color_hex_spectrum = null;
        window.color_hex_spectrum = null;
        window.icon_color_hex_spectrum = null;
        window.map_need_save = false;
        $(document).ready(function () {
            $('.help_t').tooltip();
            if(logo=='') {
                $('#div_delete_logo').hide();
                $('#div_image_logo').hide();
                $('#div_upload_logo').show();
            } else {
                $('#div_delete_logo').show();
                $('#div_image_logo').show();
                $('#div_upload_logo').hide();
            }
            $('#font_viewer').fontpicker({
                variants:false,
                localFonts: {},
                nrRecents: 0
            });
            window.main_color_hex_spectrum = $('#main_color_hex').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: false,
                allowEmpty: false
            });
            $('#'+map_style+' img').addClass("style-highlight");
            IconPicker.Init({
                jsonUrl: 'vendor/iconpicker/iconpicker-1.5.0.json',
                searchPlaceholder: '<?php echo _("Search Icon"); ?>',
                showAllButton: '<?php echo _("Show All"); ?>',
                cancelButton: '<?php echo _("Cancel"); ?>',
                noResultsFound: '<?php echo _("No results found."); ?>',
                borderRadius: '20px',
            });
            IconPicker.Run('#GetIconPicker', function(){
                $('#btn_delete_icon').prop('disabled',false);
                window.map_need_save = true;
            });
            window.color_hex_spectrum = $('#color_hex').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: false,
                allowEmpty: true
            });
            window.icon_color_hex_spectrum = $('#icon_color_hex').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: false,
                allowEmpty: true
            });
        });
        $('.map_style').on('click',function () {
            $('.map_style img').removeClass("style-highlight");
            $(this).find('img').addClass("style-highlight");
            var map_style = $('.style-highlight').parent().attr('id');
            if(map_style=='osm_free' || map_style=='custom') {
                $('#maptiler_api').prop('disabled',true);
            } else {
                $('#maptiler_api').prop('disabled',false);
            }
            if(map_style=='custom') {
                $('#street_basemap').prop('disabled',false);
                $('#street_maxzoom').prop('disabled',false);
                $('#satellite_basemap').prop('disabled',false);
                $('#satellite_maxzoom').prop('disabled',false);
                $('#street_attributions').prop('disabled',false);
                $('#satellite_attributions').prop('disabled',false);
            } else {
                $('#street_basemap').prop('disabled',true);
                $('#street_maxzoom').prop('disabled',true);
                $('#satellite_basemap').prop('disabled',true);
                $('#satellite_maxzoom').prop('disabled',true);
                $('#street_attributions').prop('disabled',true);
                $('#satellite_attributions').prop('disabled',true);
            }
            window.map_need_save = true;
        });
        $('body').on('submit','#frm',function(e){
            e.preventDefault();
            $('#error').hide();
            var url = $(this).attr('action');
            var frm = $(this);
            var data = new FormData();
            if(frm.find('#txtFile[type="file"]').length === 1 ){
                data.append('file', frm.find( '#txtFile' )[0].files[0]);
            }
            var ajax  = new XMLHttpRequest();
            ajax.upload.addEventListener('progress',function(evt){
                var percentage = (evt.loaded/evt.total)*100;
                upadte_progressbar(Math.round(percentage));
            },false);
            ajax.addEventListener('load',function(evt){
                if(evt.target.responseText.toLowerCase().indexOf('error')>=0){
                    show_error(evt.target.responseText);
                } else {
                    if(evt.target.responseText!='') {
                        window.map_need_save = true;
                        window.logo = evt.target.responseText;
                        $('#div_image_logo img').attr('src','../viewer/logos/'+window.logo);
                        $('#div_delete_logo').show();
                        $('#div_image_logo').show();
                        $('#div_upload_logo').hide();
                    }
                }
                upadte_progressbar(0);
                frm[0].reset();
            },false);
            ajax.addEventListener('error',function(evt){
                show_error('upload failed');
                upadte_progressbar(0);
            },false);
            ajax.addEventListener('abort',function(evt){
                show_error('upload aborted');
                upadte_progressbar(0);
            },false);
            ajax.open('POST',url);
            ajax.send(data);
            return false;
        });

        function upadte_progressbar(value){
            $('#progressBar').css('width',value+'%').html(value+'%');
            if(value==0){
                $('.progress').hide();
            }else{
                $('.progress').show();
            }
        }

        function show_error(error){
            $('.progress').hide();
            $('#error').show();
            $('#error').html(error);
        }

        $('body').on('submit','#frm_geojson',function(e){
            e.preventDefault();
            $('#error_geojson').hide();
            var url = $(this).attr('action');
            var frm = $(this);
            var data = new FormData();
            if(frm.find('#txtFile_geojson[type="file"]').length === 1 ){
                data.append('file', frm.find( '#txtFile_geojson' )[0].files[0]);
            }
            var ajax  = new XMLHttpRequest();
            ajax.upload.addEventListener('progress',function(evt){
                var percentage = (evt.loaded/evt.total)*100;
                upadte_progressbar_geojson(Math.round(percentage));
            },false);
            ajax.addEventListener('load',function(evt){
                if(evt.target.responseText.toLowerCase().indexOf('error')>=0){
                    show_error_geojson(evt.target.responseText);
                } else {
                    if(evt.target.responseText!='') {
                        window.map_need_save = true;
                        $('#geoJSON').val(evt.target.responseText);
                    }
                }
                upadte_progressbar_geojson(0);
                frm[0].reset();
            },false);
            ajax.addEventListener('error',function(evt){
                show_error_geojson('upload failed');
                upadte_progressbar_geojson(0);
            },false);
            ajax.addEventListener('abort',function(evt){
                show_error_geojson('upload aborted');
                upadte_progressbar_geojson(0);
            },false);
            ajax.open('POST',url);
            ajax.send(data);
            return false;
        });

        function upadte_progressbar_geojson(value){
            $('#progressBar_geojson').css('width',value+'%').html(value+'%');
            if(value==0){
                $('.progress').hide();
            }else{
                $('.progress').show();
            }
        }

        function show_error_geojson(error){
            $('.progress').hide();
            $('#error_geojson').show();
            $('#error_geojson').html(error);
        }

        $("input").change(function(){
            window.map_need_save = true;
        });

        $(window).on('beforeunload', function(){
            if(window.map_need_save) {
                var c=confirm();
                if(c) return true; else return false;
            }
        });
    })(jQuery);
</script>
