<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
require_once("functions.php");
$role = get_user_role($_SESSION['id_user']);
$settings = get_settings();
if (is_ssl()) { $protocol = 'https'; } else { $protocol = 'http'; }
$callback_url = $protocol ."://". $_SERVER['SERVER_NAME'] . str_replace("backend/index.php","backend/social_auth.php",$_SERVER['SCRIPT_NAME']);
$domain = $_SERVER['SERVER_NAME'];
?>

<?php if($role!='administrator'): ?>
    <div class="text-center">
        <div class="error mx-auto" data-text="401">401</div>
        <p class="lead text-gray-800 mb-5"><?php echo _("Permission denied"); ?></p>
        <p class="text-gray-500 mb-0"><?php echo _("It looks like that you do not have permission to access this page"); ?></p>
        <a href="index.php?p=dashboard">← <?php echo _("Back to Dashboard"); ?></a>
    </div>
<?php die(); endif; ?>

<?php if($_SESSION['input_license']==1) : ?>
    <div class="card bg-warning text-white shadow mb-3">
        <div class="card-body">
            <?php echo _("Please enter a valid purchase code to continue using the application."); ?>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card shadow mb-12">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-key"></i> <?php echo _("License"); ?></h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="purchase_code"><?php echo _("Purchase Code"); ?></label>
                            <input type="text" class="form-control" id="purchase_code" value="xxxxxxxxxxxxx" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label><?php echo _("Status"); ?></label><br>
                        <div id="license_status" class="mt-2"><i style='color: green' class="fas fa-circle"></i> Valid, Extended License</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row <?php echo ($_SESSION['input_license']==1) ? 'd-none' : ''; ?>">
    <div class="col-md-12 mb-4">
        <div class="card shadow mb-12">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fab fa-sketch"></i> <?php echo _("Branding"); ?></h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name"><?php echo _("Application Name"); ?></label>
                            <input type="text" class="form-control" id="name" value="<?php echo $settings['name']; ?>" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name"><?php echo _("Theme Color"); ?></label>
                            <input type="text" class="form-control" id="theme_color" value="<?php echo $settings['theme_color']; ?>" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="font_backend"><?php echo _("Font Backend"); ?></label><br>
                            <input type="text" class="form-control" id="font_backend" value="<?php echo $settings['font_backend']; ?>" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="welcome_msg"><?php echo _("Welcome Message"); ?> <i title="<?php echo _("leave empty for default welcome message"); ?>" class="help_t fas fa-question-circle"></i></label>
                            <div id="welcome_msg"><?php echo $settings['welcome_msg']; ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label><?php echo _("Logo"); ?></label>
                        <div style="background-color:#4e73df;display: none" id="div_image_logo" class="col-md-12">
                            <img style="width: 100%" src="assets/<?php echo $settings['logo']; ?>" />
                        </div>
                        <div style="display: none" id="div_delete_logo" class="col-md-12 mt-4">
                            <button <?php echo ($demo) ? 'disabled':''; ?> onclick="delete_b_logo();" class="btn btn-block btn-danger"><?php echo _("DELETE IMAGE"); ?></button>
                        </div>
                        <div style="display: none" id="div_upload_logo">
                            <form id="frm" action="ajax/upload_b_logo_image.php" method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="txtFile" name="txtFile" />
                                                <label class="custom-file-label text-left" for="txtFile"><?php echo _("Choose file"); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input <?php echo ($demo) ? 'disabled':''; ?> type="submit" class="btn btn-block btn-success" id="btnUpload" value="<?php echo _("Upload Logo Image"); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="preview text-center">
                                            <div id="progress_l" class="progress mb-3 mb-sm-3 mb-lg-0 mb-xl-0" style="height: 2.35rem;display: none">
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
                    <div class="col-md-6">
                        <label><?php echo _("Login image"); ?></label>
                        <div style="display: none" id="div_image_bg" class="col-md-12">
                            <img style="width: 100%" src="assets/<?php echo $settings['background']; ?>" />
                        </div>
                        <div style="display: none" id="div_delete_bg" class="col-md-12 mt-4">
                            <button <?php echo ($demo) ? 'disabled':''; ?> onclick="delete_b_bg();" class="btn btn-block btn-danger"><?php echo _("DELETE IMAGE"); ?></button>
                        </div>
                        <div style="display: none" id="div_upload_bg">
                            <form id="frm_b" action="ajax/upload_b_background_image.php" method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="txtFile_b" name="txtFile_b" />
                                                <label class="custom-file-label text-left" for="txtFile_b"><?php echo _("Choose file"); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input <?php echo ($demo) ? 'disabled':''; ?> type="submit" class="btn btn-block btn-success" id="btnUpload_b" value="<?php echo _("Upload Login Image"); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="preview text-center">
                                            <div id="progress_bl" class="progress mb-3 mb-sm-3 mb-lg-0 mb-xl-0" style="height: 2.35rem;display: none">
                                                <div class="progress-bar" id="progressBar_b" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                    0%
                                                </div>
                                            </div>
                                            <div style="display: none;padding: .38rem;" class="alert alert-danger" id="error_b"></div>
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
</div>

<div class="row <?php echo ($_SESSION['input_license']==1) ? 'd-none' : ''; ?>">
    <div class="col-md-12 mb-4">
        <div class="card shadow mb-12">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-language"></i> <?php echo _("Localization"); ?></h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="language"><?php echo _("Default Language"); ?></label>
                            <select class="form-control" id="language">
                                <option <?php echo ($settings['language']=='ar_SA') ? 'selected':''; ?> id="ar_SA">Arabic (ar_SA)</option>
                                <option <?php echo ($settings['language']=='zh_CN') ? 'selected':''; ?> id="zh_CN">Chinese simplified (zh_CN)</option>
                                <option <?php echo ($settings['language']=='zh_HK') ? 'selected':''; ?> id="zh_HK">Chinese traditional (zh_HK)</option>
                                <option <?php echo ($settings['language']=='zh_TW') ? 'selected':''; ?> id="zh_TW">Chinese traditional (zh_TW)</option>
                                <option <?php echo ($settings['language']=='cs_CZ') ? 'selected':''; ?> id="cs_CZ">Czech (cs_CZ)</option>
                                <option <?php echo ($settings['language']=='nl_NL') ? 'selected':''; ?> id="nl_NL">Dutch (nl_NL)</option>
                                <option <?php echo ($settings['language']=='en_US') ? 'selected':''; ?> id="en_US">English (en_US)</option>
                                <option <?php echo ($settings['language']=='fil_PH') ? 'selected':''; ?> id="fil_PH">Filipino (fil_PH)</option>
                                <option <?php echo ($settings['language']=='fr_FR') ? 'selected':''; ?> id="fr_FR">French (fr_FR)</option>
                                <option <?php echo ($settings['language']=='de_DE') ? 'selected':''; ?> id="de_DE">German (de_DE)</option>
                                <option <?php echo ($settings['language']=='hi_IN') ? 'selected':''; ?> id="hi_IN">Hindi (hi_IN)</option>
                                <option <?php echo ($settings['language']=='hu_HU') ? 'selected':''; ?> id="hu_HU">Hungarian (hu_HU)</option>
                                <option <?php echo ($settings['language']=='rw_RW') ? 'selected':''; ?> id="rw_RW">Kinyarwanda (rw_RW)</option>
                                <option <?php echo ($settings['language']=='ko_KR') ? 'selected':''; ?> id="ko_KR">Korean (ko_KR)</option>
                                <option <?php echo ($settings['language']=='id_ID') ? 'selected':''; ?> id="id_ID">Indonesian (id_ID)</option>
                                <option <?php echo ($settings['language']=='it_IT') ? 'selected':''; ?> id="it_IT">Italian (it_IT)</option>
                                <option <?php echo ($settings['language']=='ja_JP') ? 'selected':''; ?> id="ja_JP">Japanese (ja_JP)</option>
                                <option <?php echo ($settings['language']=='fa_IR') ? 'selected':''; ?> id="fa_IR">Persian (fa_IR)</option>
                                <option <?php echo ($settings['language']=='pl_PL') ? 'selected':''; ?> id="pl_PL">Polish (pl_PL)</option>
                                <option <?php echo ($settings['language']=='pt_BR') ? 'selected':''; ?> id="pt_BR">Portuguese Brazilian (pt_BR)</option>
                                <option <?php echo ($settings['language']=='pt_PT') ? 'selected':''; ?> id="pt_PT">Portuguese European (pt_PT)</option>
                                <option <?php echo ($settings['language']=='es_ES') ? 'selected':''; ?> id="es_ES">Spanish (es_ES)</option>
                                <option <?php echo ($settings['language']=='ro_RO') ? 'selected':''; ?> id="ro_RO">Romanian (ro_RO)</option>
                                <option <?php echo ($settings['language']=='ru_RU') ? 'selected':''; ?> id="ru_RU">Russian (ru_RU)</option>
                                <option <?php echo ($settings['language']=='sv_SE') ? 'selected':''; ?> id="sv_SE">Swedish (sv_SE)</option>
                                <option <?php echo ($settings['language']=='tg_TJ') ? 'selected':''; ?> id="tg_TJ">Tajik (tg_TJ)</option>
                                <option <?php echo ($settings['language']=='th_TH') ? 'selected':''; ?> id="th_TH">Thai (th_TH)</option>
                                <option <?php echo ($settings['language']=='tr_TR') ? 'selected':''; ?> id="tr_TR">Turkish (tr_TR)</option>
                                <option <?php echo ($settings['language']=='vi_VN') ? 'selected':''; ?> id="vi_VN">Vietnamese (vi_VN)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="language_domain"><?php echo _("Translation Type"); ?></label>
                            <select class="form-control" id="language_domain">
                                <option <?php echo ($settings['language_domain']=='default') ? 'selected':''; ?> id="default_lang">Default</option>
                                <option <?php echo ($settings['language_domain']=='custom') ? 'selected':''; ?> id="custom_lang">Custom</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="languages_enabled"><?php echo _("Languages Enabled"); ?></label>
                            <select style="height: 125px" multiple class="form-control selectpicker" id="languages_enabled" data-actions-box="true" data-selected-text-format="count > 3" data-count-selected-text="{0} <?php echo _("items selected"); ?>" data-deselect-all-text="<?php echo _("Deselect All"); ?>" data-select-all-text="<?php echo _("Select All"); ?>" data-none-selected-text="<?php echo _("Nothing selected"); ?>" data-none-results-text="<?php echo _("No results matched"); ?> {0}">
                                <option <?php echo (check_language_enabled('ar_SA',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_ar_SA">Arabic (ar_SA)</option>
                                <option <?php echo (check_language_enabled('zh_CN',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_zh_CN">Chinese simplified (zh_CN)</option>
                                <option <?php echo (check_language_enabled('zh_HK',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_zh_HK">Chinese traditional (zh_HK)</option>
                                <option <?php echo (check_language_enabled('zh_TW',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_zh_TW">Chinese traditional (zh_TW)</option>
                                <option <?php echo (check_language_enabled('cs_CZ',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_cs_CZ">Czech (cs_CZ)</option>
                                <option <?php echo (check_language_enabled('nl_NL',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_nl_NL">Dutch (nl_NL)</option>
                                <option <?php echo (check_language_enabled('en_US',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_en_US">English (en_US)</option>
                                <option <?php echo (check_language_enabled('fil_PH',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_fil_PH">Filipino (fil_PH)</option>
                                <option <?php echo (check_language_enabled('fr_FR',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_fr_FR">French (fr_FR)</option>
                                <option <?php echo (check_language_enabled('de_DE',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_de_DE">German (de_DE)</option>
                                <option <?php echo (check_language_enabled('hi_IN',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_hi_IN">Hindi (hi_IN)</option>
                                <option <?php echo (check_language_enabled('hu_HU',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_hu_HU">Hungarian (hu_HU)</option>
                                <option <?php echo (check_language_enabled('rw_RW',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_rw_RW">Kinyarwanda (rw_RW)</option>
                                <option <?php echo (check_language_enabled('ko_KR',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_ko_KR">Korean (ko_KR)</option>
                                <option <?php echo (check_language_enabled('id_ID',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_id_ID">Indonesian (id_ID)</option>
                                <option <?php echo (check_language_enabled('it_IT',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_it_IT">Italian (it_IT)</option>
                                <option <?php echo (check_language_enabled('ja_JP',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_ja_JP">Japanese (ja_JP)</option>
                                <option <?php echo (check_language_enabled('fa_IR',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_fa_IR">Persian (fa_IR)</option>
                                <option <?php echo (check_language_enabled('pl_PL',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_pl_PL">Polish (pl_PL)</option>
                                <option <?php echo (check_language_enabled('pt_BR',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_pt_BR">Portuguese Brazilian (pt_BR)</option>
                                <option <?php echo (check_language_enabled('pt_PT',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_pt_PT">Portuguese European (pt_PT)</option>
                                <option <?php echo (check_language_enabled('es_ES',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_es_ES">Spanish (es_ES)</option>
                                <option <?php echo (check_language_enabled('ro_RO',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_ro_RO">Romanian (ro_RO)</option>
                                <option <?php echo (check_language_enabled('ru_RU',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_ru_RU">Russian (ru_RU)</option>
                                <option <?php echo (check_language_enabled('sv_SE',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_sv_SE">Swedish (sv_SE)</option>
                                <option <?php echo (check_language_enabled('tg_TJ',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_tg_TJ">Tajik (tg_TJ)</option>
                                <option <?php echo (check_language_enabled('th_TH',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_th_TH">Thai (th_TH)</option>
                                <option <?php echo (check_language_enabled('tr_TR',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_tr_TR">Turkish (tr_TR)</option>
                                <option <?php echo (check_language_enabled('vi_VN',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_vi_VN">Vietnamese (vi_VN)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <p>
                            If you want to edit translation file you need to follow this instructions:<br>
                            1) NEW CUSTOM TRANSLATION: Copy the file <i>locale/lang_code/LC_MESSAGES/<b>default.po</b></i> to your computer and rename it to <b>custom.po</b><br>
                            or<br>
                            1) EXISTING CUSTOM TRANSLATION: Execute this command <b>msgmerge --update locale/lang_code/LC_MESSAGES/custom.po locale/sml.pot</b> to merge the new strings with your existing <b>custom.po</b> translation file<br>
                            2) Edit the file <b>custom.po</b> with a text editor or with a POEditor like <a target="_blank" href="https://poedit.net/">this one</a><br>
                            3) Compile and generate the file <b>custom.mo</b> with the POEditor or with this command <b>msgfmt custom.po --output-file=custom.mo</b><br>
                            4) Copy the files <b>custom.po</b> and <b>custom.mo</b> to <i>locale/lang_code/LC_MESSAGES/</i><br>
                            5) Change Translation Type to <b>Custom</b><br>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row <?php echo ($_SESSION['input_license']==1) ? 'd-none' : ''; ?>">
    <div class="col-md-12 mb-4">
        <div class="card shadow mb-12">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-ellipsis-h"></i> <?php echo _("Footer"); ?></h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="footer_link_1"><?php echo _("Name Item 1"); ?></label><br>
                            <input type="text" class="form-control" id="footer_link_1" value="<?php echo $settings['footer_link_1']; ?>" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="footer_value_1"><?php echo _("Content Item 1"); ?> <i title="<?php echo _("insert a textual content or a link to an external site"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                            <div id="footer_value_1"><?php echo $settings['footer_value_1']; ?></div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="footer_link_2"><?php echo _("Name Item 2"); ?></label><br>
                            <input type="text" class="form-control" id="footer_link_2" value="<?php echo $settings['footer_link_2']; ?>" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="footer_value_2"><?php echo _("Content Item 2"); ?> <i title="<?php echo _("insert a textual content or a link to an external site"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                            <div id="footer_value_2"><?php echo $settings['footer_value_2']; ?></div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="footer_link_3"><?php echo _("Name Item 3"); ?></label><br>
                            <input type="text" class="form-control" id="footer_link_3" value="<?php echo $settings['footer_link_3']; ?>" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="footer_value_3"><?php echo _("Content Item 3"); ?> <i title="<?php echo _("insert a textual content or a link to an external site"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                            <div id="footer_value_3"><?php echo $settings['footer_value_3']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row <?php echo ($_SESSION['input_license']==1) ? 'd-none' : ''; ?>">
    <div class="col-md-12 mb-4">
        <div class="card shadow mb-12">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fab fa-css3-alt"></i> <?php echo _("Custom Viewer CSS"); ?></h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-1">
                        <select onchange="change_editor_css();" class="form-control" id="css_name">
                            <option id="css_custom"><?php echo _("General (affects all maps)"); ?></option>
                            <?php echo get_maps_options_css(); ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <div style="position: relative;width: 100%;height: 400px;" class="editors_css" id="custom"><?php echo get_editor_css_content('custom'); ?></div>
                        <?php echo get_maps_editors_css(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row <?php echo ($_SESSION['input_license']==1) ? 'd-none' : ''; ?>">
    <div class="col-md-12 mb-4">
        <div class="card shadow mb-12">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fab fa-css3-alt"></i> <?php echo _("Custom Backend CSS"); ?></h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div style="position: relative;width: 100%;height: 400px;" class="editors_css" id="custom_b"><?php echo get_editor_css_content('custom_b'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict"; // Start of use strict
        window.settings_need_save = false;
        window.input_license = <?php echo $_SESSION['input_license']; ?>;
        window.b_logo_image = '<?php echo $settings['logo']; ?>';
        window.b_background_image = '<?php echo $settings['background']; ?>';
        window.current_language = '<?php echo $settings['language']; ?>';
        window.welcome_msg_editor = null;
        window.theme_color_spectrum = null;
        window.editors_css = [];
        window.footer_value_1 = null;
        window.footer_value_2 = null;
        window.footer_value_3 = null;
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
        Quill.register(SizeStyle,true);

        $(document).ready(function () {
            $('#font_backend').fontpicker({
                variants:false,
                localFonts: {},
                nrRecents: 0,
                onSelect: function (font) {
                    var font_family = font.fontFamily;
                    $('#font_backend_link').attr('href','https://fonts.googleapis.com/css?family='+font_family);
                    $('#style_css').html("*{ font-family:'"+font_family+"',sans-serif; }");
                }
            });
            bsCustomFileInput.init();
            $('.help_t').tooltip();
            if(window.b_logo_image=='') {
                $('#div_delete_logo').hide();
                $('#div_image_logo').hide();
                $('#div_upload_logo').show();
            } else {
                $('#div_delete_logo').show();
                $('#div_image_logo').show();
                $('#div_upload_logo').hide();
            }
            if(window.b_background_image=='') {
                $('#div_delete_bg').hide();
                $('#div_image_bg').hide();
                $('#div_upload_bg').show();
            } else {
                $('#div_delete_bg').show();
                $('#div_image_bg').show();
                $('#div_upload_bg').hide();
            }
            $(".editors_css").each(function() {
                var id = $(this).attr('id');
                window.editors_css[id] = ace.edit(id);
                window.editors_css[id].session.setMode("ace/mode/css");
                window.editors_css[id].setOption('enableLiveAutocompletion',true);
            });
            var toolbarOptions = [
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'align': [] }],
                ['clean']
            ];
            window.welcome_msg_editor = new Quill('#welcome_msg', {
                modules: {
                    toolbar: toolbarOptions
                },
                theme: 'snow'
            });
            window.theme_color_spectrum = $('#theme_color').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: true,
                allowEmpty: false,
                cancelText: "<?php echo _("Cancel"); ?>",
                chooseText: "<?php echo _("Choose"); ?>",
                change: function(color) {
                    var hex = color.toHexString();
                    set_session_theme_color(hex);
                }
            });
            window.footer_value_1 = new Quill('#footer_value_1', {
                modules: {
                    toolbar: toolbarOptions
                },
                theme: 'snow'
            });
            window.footer_value_2 = new Quill('#footer_value_2', {
                modules: {
                    toolbar: toolbarOptions
                },
                theme: 'snow'
            });
            window.footer_value_3 = new Quill('#footer_value_3', {
                modules: {
                    toolbar: toolbarOptions
                },
                theme: 'snow'
            });
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
                        window.settings_need_save = true;
                        window.b_logo_image = evt.target.responseText;
                        $('#div_image_logo img').attr('src','assets/'+window.b_logo_image);
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
                $('#progress_l').hide();
            }else{
                $('#progress_l').show();
            }
        }

        function show_error(error){
            $('#progress_l').hide();
            $('#error').show();
            $('#error').html(error);
        }

        $('body').on('submit','#frm_b',function(e){
            e.preventDefault();
            $('#error_b').hide();
            var url = $(this).attr('action');
            var frm = $(this);
            var data = new FormData();
            if(frm.find('#txtFile_b[type="file"]').length === 1 ){
                data.append('file', frm.find( '#txtFile_b' )[0].files[0]);
            }
            var ajax  = new XMLHttpRequest();
            ajax.upload.addEventListener('progress',function(evt){
                var percentage = (evt.loaded/evt.total)*100;
                upadte_progressbar_b(Math.round(percentage));
            },false);
            ajax.addEventListener('load',function(evt){
                if(evt.target.responseText.toLowerCase().indexOf('error')>=0){
                    show_error_b(evt.target.responseText);
                } else {
                    if(evt.target.responseText!='') {
                        window.settings_need_save = true;
                        window.b_background_image = evt.target.responseText;
                        $('#div_image_bg img').attr('src','assets/'+window.b_background_image);
                        $('#div_delete_bg').show();
                        $('#div_image_bg').show();
                        $('#div_upload_bg').hide();
                    }
                }
                upadte_progressbar_b(0);
                frm[0].reset();
            },false);
            ajax.addEventListener('error',function(evt){
                show_error_b('upload failed');
                upadte_progressbar_b(0);
            },false);
            ajax.addEventListener('abort',function(evt){
                show_error_b('upload aborted');
                upadte_progressbar_b(0);
            },false);
            ajax.open('POST',url);
            ajax.send(data);
            return false;
        });

        function upadte_progressbar_b(value){
            $('#progressBar_b').css('width',value+'%').html(value+'%');
            if(value==0){
                $('#progress_bl').hide();
            }else{
                $('#progress_bl').show();
            }
        }

        function show_error_b(error){
            $('#progress_bl').hide();
            $('#error_b').show();
            $('#error_b').html(error);
        }

        $("input").change(function(){
            window.settings_need_save = true;
        });

        $(window).on('beforeunload', function(){
            if(window.settings_need_save) {
                var c=confirm();
                if(c) return true; else return false;
            }
        });

    })(jQuery); // End of use strict
</script>