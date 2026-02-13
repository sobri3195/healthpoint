<?php
session_start();
require_once("functions.php");
$id_marker = $_GET['id'];
$marker = get_marker($id_marker,$_SESSION['id_user']);
if($marker!==false) {
    $id_map = $marker['id_map'];
    $map = get_map($id_map,$_SESSION['id_user']);
    if($marker['marker_size']==0) $marker['marker_size']=$map['markers_size'];
    if (is_ssl()) { $protocol = 'https'; } else { $protocol = 'http'; }
    $link_marker = $protocol ."://". $_SERVER['SERVER_NAME'] . str_replace("backend/index.php","viewer/index.php?code=",$_SERVER['SCRIPT_NAME']);
    if($user_info['role']=='editor') {
        $editor_permissions = get_editor_permissions($_SESSION['id_user'],$id_map);
        if($editor_permissions['edit_markers']==0) {
            $marker=false;
        }
    }
}
?>

<?php if(!$marker): ?>
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

<ul class="nav bg-white nav-pills nav-fill mb-2">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="pill" href="#settings_tab"><i class="fas fa-cogs"></i> <?php echo strtoupper(_("SETTINGS")); ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="pill" href="#style_tab"><i class="fas fa-palette"></i> <?php echo strtoupper(_("STYLE")); ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="pill" href="#images_tab"><i class="fas fa-images"></i> <?php echo strtoupper(_("IMAGES")); ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="pill" href="#extra_tab"><i class="fab fa-wpforms"></i> <?php echo strtoupper(_("EXTRA INFORMATIONS")); ?></a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane active" id="settings_tab">
        <?php if($marker['to_validate']) { ?>
            <div class="card bg-warning text-white shadow mb-4">
                <div class="card-body">
                    <?php echo _("This marker was added by a person outside the system. Check the data entered before approving it."); ?>
                </div>
            </div>
        <?php } else { ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-link"></i> <?php echo _("Direct Link"); ?></h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php $link = $link_marker.$map['code']."&m=".$id_marker; ?>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input readonly type="text" class="form-control bg-white" id="link" value="<?php echo $link; ?>" />
                                            <div class="input-group-append">
                                                <a title="<?php echo _("OPEN LINK"); ?>" class="btn btn-success help_t" href="<?php echo $link; ?>" target="_blank">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                                <button title="<?php echo _("COPY TO CLIPBOARD"); ?>" class="btn_link btn btn-primary help_t" data-clipboard-target="#link">
                                                    <i class="far fa-clipboard"></i>
                                                </button>
                                                <button title="<?php echo _("QR CODE"); ?>" onclick="open_qr_code_modal('<?php echo $link; ?>');" class="btn btn-secondary help_t">
                                                    <i class="fas fa-qrcode"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="margin-top: 5px" class="a2a_kit a2a_kit_size_32 a2a_default_style" data-a2a-url="<?php echo $link; ?>">
                                        <a class="a2a_button_email"></a>
                                        <a class="a2a_button_whatsapp"></a>
                                        <a class="a2a_button_facebook"></a>
                                        <a class="a2a_button_twitter"></a>
                                        <a class="a2a_button_linkedin"></a>
                                        <a class="a2a_button_telegram"></a>
                                        <a class="a2a_button_facebook_messenger"></a>
                                        <a class="a2a_button_pinterest"></a>
                                        <a class="a2a_button_reddit"></a>
                                        <a class="a2a_button_line"></a>
                                        <a class="a2a_button_viber"></a>
                                        <a class="a2a_button_vk"></a>
                                        <a class="a2a_button_qzone"></a>
                                        <a class="a2a_button_wechat"></a>
                                    </div>
                                    <script async src="https://static.addtoany.com/menu/page.js"></script>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-cog"></i> <?php echo _("General"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="name"><?php echo _("Name"); ?> *</label>
                                    <input type="text" class="form-control" id="name" value="<?php echo $marker['name']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="active"><?php echo _("Active"); ?></label><br>
                                    <input <?php echo ($marker['active']) ? 'checked' : '' ; ?> type="checkbox" id="active" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="to_validate"><?php echo _("Validated"); ?> <i title="<?php echo _("it must be validated to be visible on the map"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input <?php echo ($marker['to_validate']) ? '' : 'checked' ; ?> type="checkbox" id="to_validate" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="centered"><?php echo _("Centered"); ?> <i title="<?php echo _("center the map on this marker"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input <?php echo ($marker['centered']) ? 'checked' : '' ; ?> type="checkbox" id="centered" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="featured"><?php echo _("Featured"); ?> <i title="<?php echo _("show this marker on featured list"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input <?php echo ($marker['featured']) ? 'checked' : '' ; ?> type="checkbox" id="featured" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="category"><?php echo _("Category"); ?></label>
                                    <select multiple id="category" data-live-search="true" data-actions-box="true" data-selected-text-format="count > 3" class="form-control selectpicker">
                                        <?php echo get_categories($id_map,$marker['id_categories']); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="street"><?php echo _("Street"); ?></label>
                                    <input type="text" class="form-control" id="street" value="<?php echo $marker['street']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="city"><?php echo _("City"); ?></label>
                                    <input type="text" class="form-control" id="city" value="<?php echo $marker['city']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="postal_code"><?php echo _("Postal Code"); ?></label>
                                    <input type="text" class="form-control" id="postal_code" value="<?php echo $marker['postal_code']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="country"><?php echo _("Country"); ?></label>
                                    <input type="text" class="form-control" id="country" value="<?php echo $marker['country']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="website"><?php echo _("Website Link"); ?></label>
                                    <input type="text" class="form-control" id="website" value="<?php echo $marker['website']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="website_caption"><?php echo _("Website Name"); ?></label>
                                    <input type="text" class="form-control" id="website_caption" value="<?php echo $marker['website_caption']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="phone"><?php echo _("Phone"); ?></label>
                                    <input type="text" class="form-control" id="phone" value="<?php echo $marker['phone']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="whatsapp"><?php echo _("Whatsapp"); ?></label>
                                    <input type="text" class="form-control" id="whatsapp" value="<?php echo $marker['whatsapp']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="email"><?php echo _("E-mail"); ?></label>
                                    <input type="text" class="form-control" id="email" value="<?php echo $marker['email']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hours"><?php echo _("Hours"); ?></label>
                                    <div id="hours_div"><?php echo $marker['hours']; ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description"><?php echo _("Description"); ?></label>
                                    <div id="description_div"><?php echo $marker['description']; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-crosshairs"></i> <?php echo _("Position"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="form-group">
                                    <label for="latitude_edit"><?php echo _("Latitude"); ?></label>
                                    <input type="text" class="form-control" id="latitude_edit" value="<?php echo $marker['lat']; ?>">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="form-group">
                                    <label for="longitude_edit"><?php echo _("Longitude"); ?></label>
                                    <input type="text" class="form-control" id="longitude_edit" value="<?php echo $marker['lon']; ?>">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="form-group">
                                    <label for="min_zoom_level"><?php echo _("Visible from Zoom Level"); ?></label>
                                    <select class="form-control" id="min_zoom_level">
                                        <?php
                                        for($i=0;$i<=20;$i++) {
                                            if($i==$marker['min_zoom_level']) {
                                                echo "<option selected id='$i'>$i</option>";
                                            } else {
                                                echo "<option id='$i'>$i</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label for="geofence_radius"><?php echo _("Geofence Radius"); ?></label>
                                    <input onchange="draw_geofence();" oninput="draw_geofence();" min="0" type="number" class="form-control" id="geofence_radius" value="<?php echo $marker['geofence_radius']; ?>">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label for="geofence_color"><?php echo _("Geofence Color"); ?></label>
                                    <input type="text" class="form-control" id="geofence_color" value="<?php echo $marker['geofence_color']; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <button onclick="address_to_position();" class="btn btn-block btn-primary mb-1"><?php echo _("ADDRESS"); ?> → <?php echo _("POSITION"); ?></button>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <button id="btn_set_position" onclick="set_marker_position();" class="btn btn-block btn-primary mb-1"><?php echo _("SET POSITION"); ?></button>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <button id="btn_get_position" onclick="get_current_position();" class="btn btn-block btn-warning mb-1"><?php echo _("GET GPS POSITION"); ?></button>
                            </div>
                        </div>
                        <div id="map_marker" class="map_marker"></div>
                        <div class="mt-2 text-center " style="width: 100%;">
                            <?php echo _("Latitude"); ?>: <b><span id="lat"><?php echo $marker['lat']; ?></span></b> -
                            <?php echo _("Longitude"); ?>: <b><span id="lon"><?php echo $marker['lon']; ?></span></b> -
                            <?php echo _("Zoom Level"); ?>: <b><span id="zoom_level">0</span></b><br>
                            <i><?php echo _("click/drag on map to change marker's position"); ?></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="style_tab">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-map-marker"></i> <?php echo _("Icon Style"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _("Type"); ?></label>
                                    <select id="icon_type" onchange="change_icon_type();" class="form-control">
                                        <option <?php echo ($marker['icon']=='' && $marker['icon_image']=='') ? 'selected' : '' ; ?> id="0"><?php echo _("None"); ?></option>
                                        <option <?php echo ($marker['icon']!='' && $marker['icon_image']=='') ? 'selected' : '' ; ?> id="1"><?php echo _("Default"); ?></option>
                                        <option <?php echo ($marker['icon_image']!='') ? 'selected' : '' ; ?> id="2"><?php echo _("Custom Icon Library"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div id="icon_type_1" class="col-md-6 <?php echo ($marker['icon']!='' && $marker['icon_image']=='') ? '' : 'd-none' ; ?>">
                                <div class="form-group">
                                    <label><?php echo _("Icon"); ?></label><br>
                                    <button class="btn btn-sm btn-primary" type="button" id="GetIconPicker" data-iconpicker-input="input#marker_icon_input" data-iconpicker-preview="i#marker_icon"><?php echo _("Select Icon"); ?></button>
                                    <input readonly type="text" id="marker_icon_input" name="Icon" value="<?php echo $marker['icon']; ?>" required="" placeholder="" autocomplete="off" spellcheck="false">
                                    <div class="icon-preview d-inline-block ml-1" data-toggle="tooltip" title="">
                                        <i id="marker_icon" class="<?php echo $marker['icon']; ?>"></i>
                                    </div>
                                </div>
                            </div>
                            <div id="icon_type_2" class="col-md-6 <?php echo ($marker['icon_image']!='') ? '' : 'd-none' ; ?>">
                                <div class="form-group">
                                    <label for="marker_library_icon"><?php echo _("Icon"); ?></label><br>
                                    <button data-toggle="modal" data-target="#modal_library_icons" class="btn btn-sm btn-primary" type="button" id="btn_library_icon"><?php echo _("Select Library Icon"); ?></button>
                                    <input type="hidden" id="marker_library_icon" value="<?php echo $marker['id_icon_library']; ?>">
                                    <img id="marker_library_icon_preview" style="height: 30px;display: <?php echo ($marker['icon_image']=='') ? 'none':'inline-block'; ?>" src="../viewer/icons/<?php echo $marker['icon_image']; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group <?php echo ($marker['icon_image']!='') ? 'disabled':''; ?>">
                                    <label><?php echo _("Background"); ?></label>
                                    <input type="text" class="form-control" id="color_hex" value="<?php echo $marker['color_hex']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group <?php echo ($marker['icon_image']!='') ? 'disabled':''; ?>">
                                    <label><?php echo _("Color"); ?></label>
                                    <input type="text" class="form-control" id="icon_color_hex" value="<?php echo $marker['icon_color_hex']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="marker_size"><?php echo _("Size"); ?> (<span id="marker_size_value"><?php echo $marker['marker_size']; ?><?php echo ($marker['marker_size']==$map['markers_size']) ? ' default' : ''; ?></span>)</label><br>
                                    <input oninput="change_marker_size();" onchange="change_marker_size();" type="range" step="0.1" min="0.5" max="2.0" class="form-control-range" id="marker_size" value="<?php echo $marker['marker_size']; ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow mb-4 <?php echo (!$map['sheet_detail']) ? 'disabled' : '' ; ?>">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary d-inline-block"><i class="fas fa-window-maximize"></i> <?php echo _("Detail Window"); ?> <i title="<?php echo _("opens the details window by clicking on this marker"); ?>" class="help_t fas fa-question-circle"></i></h6>&nbsp;&nbsp;
                         <input <?php echo ($marker['open_sheet']) ? 'checked' : '' ; ?> onchange="change_open_sheet_marker();" type="checkbox" id="open_sheet" />
                    </div>
                    <div id="sheet_details_settings" class="card-body <?php echo (!$marker['open_sheet']) ? 'disabled' : '' ; ?>">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group <?php echo (!$map['enable_directions']) ? 'disabled' : ''; ?>">
                                    <label for="view_directions"><?php echo _("Directions"); ?> <i title="<?php echo _("enable the button 'directions' for this marker"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input <?php echo ($marker['view_directions']) ? 'checked' : '' ; ?> type="checkbox" id="view_directions" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group <?php echo (!$map['enable_streetview']) ? 'disabled' : ''; ?>">
                                    <label for="view_street_view"><?php echo _("Street View"); ?> <i title="<?php echo _("enable the button 'street view' for this marker"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input <?php echo ($marker['view_street_view']) ? 'checked' : '' ; ?> type="checkbox" id="view_street_view" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group <?php echo (!$map['enable_reviews']) ? 'disabled' : ''; ?>">
                                    <label for="view_review"><?php echo _("Review"); ?> <i title="<?php echo _("enable the reviews for this marker"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input <?php echo ($marker['view_review']) ? 'checked' : '' ; ?> type="checkbox" id="view_review" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group <?php echo (!$map['enable_share']) ? 'disabled' : ''; ?>">
                                    <label for="view_share"><?php echo _("Share"); ?> <i title="<?php echo _("enable share buttons for this marker"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input <?php echo ($marker['view_share']) ? 'checked' : '' ; ?> type="checkbox" id="view_share" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow mb-4 <?php echo (!$map['enable_popup']) ? 'disabled' : '' ; ?>">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary d-inline-block"><i class="fas fa-window-maximize"></i> <?php echo _("Popup"); ?> <i title="<?php echo _("display popup on hover over this marker"); ?>" class="help_t fas fa-question-circle"></i></h6>&nbsp;&nbsp;
                        <input <?php echo ($marker['view_popup']) ? 'checked' : '' ; ?> onchange="change_popup_marker();" type="checkbox" id="view_popup" />
                    </div>
                    <div id="popup_settings" class="card-body <?php echo (!$marker['view_popup']) ? 'disabled' : '' ; ?>">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?php echo _("Image Height"); ?></label>
                                    <div class="input-group mb-3">
                                        <input min="0" type="number" class="form-control" id="popup_image_height" value="<?php echo $marker['popup_image_height']; ?>" />
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">px</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?php echo _("Background Color"); ?></label>
                                    <input type="text" class="form-control" id="popup_background" value="<?php echo $marker['popup_background']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?php echo _("Text Color"); ?></label>
                                    <input type="text" class="form-control" id="popup_color" value="<?php echo $marker['popup_color']; ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="images_tab">
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-images"></i> <?php echo _("Images"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form id="frm" action="ajax/upload_marker_image.php" method="POST" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <input type="file" class="form-control" id="txtFile" name="txtFile" />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <input <?php echo ($demo) ? 'disabled':''; ?> type="submit" class="btn btn-block btn-success" id="btnUpload" value="<?php echo _("Upload Image"); ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
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
                            <div id="image_list" class="col-md-12">
                                <div id="image_gallery"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-toolbox"></i> <?php echo _("Image Action"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div id="msg_sel_image" class="col-md-12">
                                <?php echo _("Select image first!"); ?>
                            </div>
                            <div id="btn_setmain_image" class="col-md-12 mb-4 d-none">
                                <button <?php echo ($demo) ? 'disabled':''; ?> onclick="set_as_main();" class="btn btn-block btn-primary"><?php echo _("SET AS MAIN"); ?></button>
                            </div>
                            <div id="btn_delete_image" class="col-md-12 d-none">
                                <button <?php echo ($demo) ? 'disabled':''; ?> onclick="delete_image();" class="btn btn-block btn-danger"><?php echo _("DELETE"); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="extra_tab">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-circle"></i> <?php echo _("Extra Button"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label><?php echo _("Button Icon"); ?></label><br>
                                            <button class="btn btn-sm btn-primary" type="button" id="GetIconPicker_b1" data-iconpicker-input="input#marker_icon_input_b1" data-iconpicker-preview="i#marker_icon_b1"><?php echo _("Select Icon"); ?></button>
                                            <input readonly type="text" id="marker_icon_input_b1" name="Icon" value="<?php echo $marker['extra_button_icon_1']; ?>" required="" placeholder="" autocomplete="off" spellcheck="false">
                                            <div class="icon-preview d-inline-block ml-1" data-toggle="tooltip" title="">
                                                <i id="marker_icon_b1" class="<?php echo $marker['extra_button_icon_1']; ?>"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="extra_button_title_1"><?php echo _("Button Title"); ?></label>
                                            <input type="text" class="form-control" id="extra_button_title_1" value="<?php echo $marker['extra_button_title_1']; ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="extra_button_value_1"><?php echo _("Custom Content"); ?>&nbsp;&nbsp;&nbsp;<span onclick="open_modal_content('extra_button_value_1','link2');" style="cursor:pointer;" class="badge badge-primary"><i class="fas fa-plus"></i> <?php echo _("link"); ?></span> <span onclick="open_modal_content('extra_button_value_1','embed_link');" style="cursor:pointer;" class="badge badge-primary"><i class="fas fa-plus"></i> <?php echo _("embed link"); ?></span> <span onclick="open_modal_content('extra_button_value_1','text');" style="cursor:pointer;" class="badge badge-primary"><i class="fas fa-plus"></i> <?php echo _("text"); ?></span></label>
                                    <div id="extra_button_value_1"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fab fa-wpforms"></i> <?php echo _("Extra Fields"); ?></h6>
                    </div>
                    <div class="card-body">
                        <?php
                        for($i=1;$i<=20;$i++) { ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo _("Field Icon"); ?> <?php echo $i; ?></label><br>
                                        <button class="btn btn-sm btn-primary field_icon_picker" type="button" id="GetIconPicker_e<?php echo $i; ?>" data-iconpicker-input="input#marker_icon_input_e<?php echo $i; ?>" data-iconpicker-preview="i#marker_icon_e<?php echo $i; ?>"><?php echo _("Select Icon"); ?></button>
                                        <input readonly type="text" id="marker_icon_input_e<?php echo $i; ?>" name="Icon" value="<?php echo $marker['extra_field_icon_'.$i]; ?>" required="" placeholder="" autocomplete="off" spellcheck="false">
                                        <div class="icon-preview d-inline-block ml-1" data-toggle="tooltip" title="">
                                            <i id="marker_icon_e<?php echo $i; ?>" class="<?php echo $marker['extra_field_icon_'.$i]; ?>"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="extra_field_value_<?php echo $i; ?>"><?php echo _("Field Value"); ?> <?php echo $i; ?>&nbsp;&nbsp;&nbsp;<span onclick="open_modal_content('<?php echo $i; ?>','link');" style="cursor:pointer;" class="badge badge-primary"><i class="fas fa-plus"></i> <?php echo _("link"); ?></span> <span onclick="open_modal_content('<?php echo $i; ?>','download');" style="cursor:pointer;" class="badge badge-primary"><i class="fas fa-plus"></i> <?php echo _("download"); ?></span> <span onclick="open_modal_content('<?php echo $i; ?>','text');" style="cursor:pointer;" class="badge badge-primary"><i class="fas fa-plus"></i> <?php echo _("text"); ?></span></label>
                                        <div class="extra_field_value" id="extra_field_value_<?php echo $i; ?>"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
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

<div id="modal_content_link" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="content_link_name"><?php echo _("Link Text"); ?></label>
                            <input type="text" class="form-control" id="content_link_name" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="content_link"><?php echo _("URL"); ?></label>
                            <input type="text" class="form-control" id="content_link" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success"><?php echo _("Insert"); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="modal_content_link2" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="content_link2"><?php echo _("URL"); ?></label>
                            <input type="text" class="form-control" id="content_link2" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success"><?php echo _("Insert"); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="modal_content_embed_link" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="content_embed_link"><?php echo _("URL"); ?></label>
                            <input type="text" class="form-control" id="content_embed_link" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success"><?php echo _("Insert"); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="modal_content_text" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="content_text"><?php echo _("Text"); ?></label>
                            <div id="content_text"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success"><?php echo _("Insert"); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="modal_content_download" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="content_download_name"><?php echo _("Link Text"); ?></label>
                            <input type="text" class="form-control" id="content_download_name" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="content_download"><?php echo _("File"); ?></label>
                            <form id="frm_d" action="ajax/upload_file.php" method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="file" class="form-control" id="txtFile_d" name="txtFile_d" />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input <?php echo ($demo) ? 'disabled':''; ?> type="submit" class="btn btn-block btn-success" id="btnUpload_d" value="<?php echo _("Upload File"); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="preview text-center">
                                            <div class="progress mb-3 mb-sm-3 mb-lg-0 mb-xl-0" style="height: 2.35rem;display: none">
                                                <div class="progress-bar" id="progressBar_d" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                    0%
                                                </div>
                                            </div>
                                            <div style="display: none;padding: .38rem;" class="alert alert-danger" id="error_d"></div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="content_download_link"><?php echo _("URL"); ?></label>
                            <input type="text" class="form-control" id="content_download_link" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success"><?php echo _("Insert"); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="modal_qrcode" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _("QR Code"); ?></h5>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-spin fa-spinner"></i>
                <img style="width: 100%;" src="" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict";
        window.id_marker = <?php echo $id_marker; ?>;
        window.marker_need_save = false;
        window.lon_marker = '<?php echo $marker['lon']; ?>';
        window.lat_marker = '<?php echo $marker['lat']; ?>';
        window.color_hex_spectrum = null;
        window.icon_color_hex_spectrum = null;
        window.geofence_color_spectrum = null;
        window.description_editor = null;
        window.hours_editor = null;
        window.default_marker_size = <?php echo $map['markers_size']; ?>;
        window.editor_html_extra_fields = [];
        window.editor_html_extra_button_1 = null;
        window.content_text_editor = null;
        window.popup_background_spectrum = null;
        window.popup_color_spectrum = null;

        var DirectionAttribute = Quill.import('attributors/attribute/direction');
        Quill.register(DirectionAttribute,true);
        var AlignClass = Quill.import('attributors/class/align');
        Quill.register(AlignClass,true);
        var BackgroundClass = Quill.import('attributors/class/background');
        Quill.register(BackgroundClass,true);
        var ColorClass = Quill.import('attributors/class/color');
        Quill.register(ColorClass,true);
        var DirectionClass = Quill.import('attributors/class/direction');
        Quill.register(DirectionClass,true);
        var FontClass = Quill.import('attributors/class/font');
        Quill.register(FontClass,true);
        var SizeClass = Quill.import('attributors/class/size');
        Quill.register(SizeClass,true);
        var AlignStyle = Quill.import('attributors/style/align');
        Quill.register(AlignStyle,true);
        var BackgroundStyle = Quill.import('attributors/style/background');
        Quill.register(BackgroundStyle,true);
        var ColorStyle = Quill.import('attributors/style/color');
        Quill.register(ColorStyle,true);
        var DirectionStyle = Quill.import('attributors/style/direction');
        Quill.register(DirectionStyle,true);
        var FontStyle = Quill.import('attributors/style/font');
        Quill.register(FontStyle,true);
        var SizeStyle = Quill.import('attributors/style/size');
        SizeStyle.whitelist = ['12px','14px','16px','18px','20px','22px'];
        Quill.register(SizeStyle,true);
        var LinkFormats = Quill.import("formats/link");
        Quill.register(LinkFormats,true);

        $(document).ready(function () {
            $('.help_t').tooltip();
            new ClipboardJS('.btn_link');
            $('.tooltip_arrows').tooltipster({
                delay: 10,
                hideOnClick: true
            });
            for(var i=1; i<=20; i++) {
                window.editor_html_extra_fields[i] = ace.edit('extra_field_value_'+i);
                window.editor_html_extra_fields[i].session.setMode("ace/mode/html");
                window.editor_html_extra_fields[i].session.setUseWrapMode(true);
                window.editor_html_extra_fields[i].session.setOption('indentedSoftWrap', false);
                window.editor_html_extra_fields[i].setOption('enableLiveAutocompletion',true);
            }
            window.editor_html_extra_fields[1].setValue("<?php echo $marker['extra_field_value_1']; ?>",-1);
            window.editor_html_extra_fields[2].setValue("<?php echo $marker['extra_field_value_2']; ?>",-1);
            window.editor_html_extra_fields[3].setValue("<?php echo $marker['extra_field_value_3']; ?>",-1);
            window.editor_html_extra_fields[4].setValue("<?php echo $marker['extra_field_value_4']; ?>",-1);
            window.editor_html_extra_fields[5].setValue("<?php echo $marker['extra_field_value_5']; ?>",-1);
            window.editor_html_extra_fields[6].setValue("<?php echo $marker['extra_field_value_6']; ?>",-1);
            window.editor_html_extra_fields[7].setValue("<?php echo $marker['extra_field_value_7']; ?>",-1);
            window.editor_html_extra_fields[8].setValue("<?php echo $marker['extra_field_value_8']; ?>",-1);
            window.editor_html_extra_fields[9].setValue("<?php echo $marker['extra_field_value_9']; ?>",-1);
            window.editor_html_extra_fields[10].setValue("<?php echo $marker['extra_field_value_10']; ?>",-1);
            window.editor_html_extra_fields[11].setValue("<?php echo $marker['extra_field_value_11']; ?>",-1);
            window.editor_html_extra_fields[12].setValue("<?php echo $marker['extra_field_value_12']; ?>",-1);
            window.editor_html_extra_fields[13].setValue("<?php echo $marker['extra_field_value_13']; ?>",-1);
            window.editor_html_extra_fields[14].setValue("<?php echo $marker['extra_field_value_14']; ?>",-1);
            window.editor_html_extra_fields[15].setValue("<?php echo $marker['extra_field_value_15']; ?>",-1);
            window.editor_html_extra_fields[16].setValue("<?php echo $marker['extra_field_value_16']; ?>",-1);
            window.editor_html_extra_fields[17].setValue("<?php echo $marker['extra_field_value_17']; ?>",-1);
            window.editor_html_extra_fields[18].setValue("<?php echo $marker['extra_field_value_18']; ?>",-1);
            window.editor_html_extra_fields[19].setValue("<?php echo $marker['extra_field_value_19']; ?>",-1);
            window.editor_html_extra_fields[20].setValue("<?php echo $marker['extra_field_value_20']; ?>",-1);
            window.editor_html_extra_button_1 = ace.edit('extra_button_value_1');
            window.editor_html_extra_button_1.session.setMode("ace/mode/html");
            window.editor_html_extra_button_1.session.setUseWrapMode(true);
            window.editor_html_extra_button_1.session.setOption('indentedSoftWrap', false);
            window.editor_html_extra_button_1.setOption('enableLiveAutocompletion',true);
            window.editor_html_extra_button_1.setValue("<?php echo $marker['extra_button_value_1']; ?>",-1);
            var toolbarOptions = [
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'size': ['12px','14px','16px','18px','20px','22px'] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'align': [] }],['link'],['image'],
                ['clean']
            ];
            hours_editor = new Quill('#hours_div', {
                modules: {
                    toolbar: toolbarOptions
                },
                theme: 'snow'
            });
            description_editor = new Quill('#description_div', {
                modules: {
                    toolbar: toolbarOptions
                },
                theme: 'snow'
            });
            content_text_editor = new Quill('#content_text', {
                modules: {
                    toolbar: toolbarOptions
                },
                theme: 'snow'
            });
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
                setTimeout(function () {
                    change_style_marker();
                },250);
                window.marker_need_save = true;
            });
            IconPicker.Run('.field_icon_picker', function(){
                window.marker_need_save = true;
            });
            IconPicker.Run('#GetIconPicker_b1', function(){
                window.marker_need_save = true;
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
            window.geofence_color_spectrum = $('#geofence_color').spectrum({
                type: "text",
                preferredFormat: "rgb",
                showAlpha: true,
                showButtons: false,
                allowEmpty: false
            });
            window.popup_background_spectrum = $('#popup_background').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: false,
                allowEmpty: true
            });
            window.popup_color_spectrum = $('#popup_color').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: false,
                allowEmpty: true
            });
            $("#color_hex").on('move.spectrum', function(e, color) {
                change_style_marker();
            });
            $("#icon_color_hex").on('move.spectrum', function(e, color) {
                change_style_marker();
            });
            $("#geofence_color").on('move.spectrum', function(e, color) {
                draw_geofence();
            });
            get_marker_images(window.id_marker);
            initialize_map_marker(window.lon_marker,window.lat_marker,'edit');
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
                        var count_images = $('#image_gallery a[data-main="1"]').length;
                        if(count_images==0) {
                            var main = '1';
                        } else {
                            var main = '0';
                        }
                        $('#image_gallery').append("<a data-id='0' data-action='insert' data-image='"+evt.target.responseText+"' data-main='"+main+"' href='#'><img src='../viewer/marker_images/thumb/"+evt.target.responseText+"'/></a>");
                        $('#image_gallery').justifiedGallery('norewind');
                        $('#image_gallery a').on('click',function () {
                            event.preventDefault();
                            select_image($(this));
                        });
                        window.marker_need_save = true;
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

        $('body').on('submit','#frm_d',function(e){
            e.preventDefault();
            $('#error_d').hide();
            var url = $(this).attr('action');
            var frm = $(this);
            var data = new FormData();
            if(frm.find('#txtFile_d[type="file"]').length === 1 ){
                data.append('file', frm.find( '#txtFile_d' )[0].files[0]);
            }
            var ajax  = new XMLHttpRequest();
            ajax.upload.addEventListener('progress',function(evt){
                var percentage = (evt.loaded/evt.total)*100;
                upadte_progressbar_d(Math.round(percentage));
            },false);
            ajax.addEventListener('load',function(evt){
                if(evt.target.responseText.toLowerCase().indexOf('error')>=0){
                    show_error_d(evt.target.responseText);
                } else {
                    if(evt.target.responseText!='') {
                        $('#content_download_link').val('content/'+evt.target.responseText);
                    }
                }
                upadte_progressbar_d(0);
                frm[0].reset();
            },false);
            ajax.addEventListener('error',function(evt){
                show_error_d('upload failed');
                upadte_progressbar_d(0);
            },false);
            ajax.addEventListener('abort',function(evt){
                show_error_d('upload aborted');
                upadte_progressbar_d(0);
            },false);
            ajax.open('POST',url);
            ajax.send(data);
            return false;
        });

        function upadte_progressbar_d(value){
            $('#progressBar_d').css('width',value+'%').html(value+'%');
            if(value==0){
                $('.progress').hide();
            }else{
                $('.progress').show();
            }
        }

        function show_error_d(error){
            $('.progress').hide();
            $('#error_d').show();
            $('#error_d').html(error);
        }

        $("input").change(function(){
            window.marker_need_save = true;
        });

        $(window).on('beforeunload', function(){
            if(window.marker_need_save) {
                var c=confirm();
                if(c) return true; else return false;
            }
        });
    })(jQuery);
</script>
