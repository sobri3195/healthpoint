<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
if(!file_exists("../config/config.inc.php")) {
    header("Location: ../install/start.php");
}
session_start();
require_once("functions.php");
$session_id = session_id();
$_SESSION['sml_si']=$session_id;
if(isset($_SESSION['id_user'])) {
    $id_user = $_SESSION['id_user'];
} else {
    header("Location:login.php");
}
$v = time();
$version = "4.0";
$settings = get_settings();
$need_update = false;
$user_info = get_user_info($id_user);
if(!empty($user_info['language'])) {
    set_language($user_info['language'],$settings['language_domain']);
} else {
    if(empty($_SESSION['lang'])) {
        $_SESSION['lang']=$settings['language'];
    }
    set_language($settings['language'],$settings['language_domain']);
}
if(isset($_GET['p'])) {
    $page = $_GET['p'];
} else {
    $page = "dashboard";
}
$_SESSION['ip_developer']='79.32.244.65';
if((($_SERVER['SERVER_ADDR']=='5.9.29.89') && ($_SERVER['REMOTE_ADDR']!=$_SESSION['ip_developer'])) || ($_SESSION['sml_si']!=session_id())) {
    $demo = true;
    if(!isset($_SESSION['id_map_sel'])) {
        $_SESSION['id_map_sel'] = 1;
        $_SESSION['name_virtualtour_sel'] = 'Simple Map Locator';
    }
} else {
    $demo = false;
}
$_SESSION['theme_color']=$settings['theme_color'];
$_SESSION['input_license']=0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta name="author" content="">
    <title><?php echo $settings['name']; ?></title>
    <?php if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'favicon.ico')) : ?>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <?php endif; ?>
    <link rel="stylesheet" type="text/css" href="../viewer/vendor/fontawesome-free/css/all.min.css">
    <link rel='stylesheet' type="text/css" id="font_backend_link" href="https://fonts.googleapis.com/css?family=<?php echo $settings['font_backend']; ?>">
    <link rel="stylesheet" type="text/css" href="css/sb-admin-2.min.css?v=2">
    <link rel="stylesheet" type="text/css" href="vendor/datatables/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../viewer/css/ol.css?v=7.3.0">
    <link rel="stylesheet" type="text/css" href="../viewer/css/ol-ext.min.css?v=4.0.7">
    <link rel="stylesheet" type="text/css" href="vendor/quill/quill.core.css">
    <link rel="stylesheet" type="text/css" href="vendor/quill/quill.snow.css">
    <link rel="stylesheet" type="text/css" href="vendor/quill/quill.bubble.css">
    <link rel="stylesheet" type="text/css" href="vendor/justifiedGallery/justifiedGallery.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/iconpicker/iconpicker-1.5.0.css" />
    <link rel="stylesheet" type="text/css" href="vendor/spectrum/spectrum.min.css?v=5">
    <link rel="stylesheet" type="text/css" href="vendor/dropzone/dropzone.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/dropzone/basic.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap-select/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/tooltipster/css/tooltipster.bundle.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-borderless.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/jquery.fontpicker/jquery.fontpicker.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap-select-country/css/bootstrap-select-country.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap4-toggle/bootstrap4-toggle.min.css">
    <link rel="stylesheet" type="text/css" id="css_theme" href="css/theme.php?v=<?php echo $v; ?>">
    <link rel="stylesheet" type="text/css" href="css/custom.css?v=<?php echo $v; ?>">
    <?php if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'custom_b.css')) : ?>
    <link rel="stylesheet" type="text/css" href="css/custom_b.css?v=<?php echo $v; ?>">
    <?php endif; ?>
    <script type="text/javascript" src="vendor/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="vendor/jquery-ui/jquery-ui.min.js"></script>
    <script type="text/javascript" src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script type="text/javascript" src="vendor/clipboard.js/clipboard.min.js"></script>
    <script type="text/javascript" src="../viewer/js/ol.js?v=7.3.0"></script>
    <script type="text/javascript" src="../viewer/js/ol-ext.min.js?v=4.0.7"></script>
    <script type="text/javascript" src="../viewer/js/gifler.min.js"></script>
    <script type="text/javascript" src="vendor/quill/quill.core.js"></script>
    <script type="text/javascript" src="vendor/quill/quill.js"></script>
    <script type="text/javascript" src="vendor/quill/quill.min.js"></script>
    <script type="text/javascript" src="vendor/spectrum/spectrum.min.js?v=5"></script>
    <script type="text/javascript" src="vendor/justifiedGallery/jquery.justifiedGallery.min.js"></script>
    <script type="text/javascript" src="vendor/iconpicker/iconpicker-1.5.0.js"></script>
    <script type="text/javascript" src="vendor/chart.js/Chart.min.js"></script>
    <script type="text/javascript" src="vendor/hchart/hs.min.js?v=1"></script>
    <script type="text/javascript" src="vendor/hchart/exporting.js"></script>
    <script type="text/javascript" src="vendor/hchart/accessibility.js"></script>
    <script type="text/javascript" src="vendor/dropzone/dropzone.min.js"></script>
    <script type="text/javascript" src="vendor/bootstrap-select/js/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="vendor/tooltipster/js/tooltipster.bundle.min.js"></script>
    <script type="text/javascript" src="vendor/jquery.fontpicker/jquery.fontpicker.min.js"></script>
    <script type="text/javascript" src="vendor/bootstrap/js/bs-custom-file-input.min.js"></script>
    <script type="text/javascript" src="vendor/bootstrap-select-country/js/bootstrap-select-country.min.js"></script>
    <script type="text/javascript" src="vendor/ace-editor/ace.js?v=3" charset="utf-8"></script>
    <script type="text/javascript" src="vendor/ace-editor/mode-css.js?v=3" charset="utf-8"></script>
    <script type="text/javascript" src="vendor/ace-editor/mode-html.js?v=3" charset="utf-8"></script>
    <script type="text/javascript" src="vendor/ace-editor/ext-language_tools.js?v=3" charset="utf-8"></script>
    <script type="text/javascript" src="vendor/bootstrap4-toggle/bootstrap4-toggle.min.js"></script>
    <script src="js/function.js?v=<?php echo time(); ?>"></script>
</head>

<body id="page-top">
<style id="style_css">
    *{ font-family: '<?php echo $settings['font_backend']; ?>', sans-serif; }
</style>

<?php if($need_update) : ?>
    <div class="text-center mt-4">
        <h1 class="text-primary"><?php echo _("UPDATE IN PROGRESS"); ?>...</h1>
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only"><?php echo _("Loading"); ?>...</span>
        </div>
        <p class="lead text-gray-800 mt-4"><?php echo _("Not close this window"); ?></p>
    </div>
    <script>
        $.ajax({
            url: "ajax/update.php",
            type: "POST",
            data: {
                version: '<?php echo $version; ?>'
            },
            async: true,
            success: function () {
                location.reload();
            },
            timeout: 300000
        });
    </script>
    <?php
    exit;
endif;
?>

<div id="wrapper">
  <?php include_once("sidebar.php"); ?>
<div id="content-wrapper" class="d-flex flex-column">
  <div id="content">
      <?php include_once("topbar.php"); ?>
    <div class="container-fluid">
        <?php
        if($user_info['role']=='administrator' && empty($settings['license']) && !in_array(get_ip_server(),array('127.0.0.1','::1'))) {
            $_SESSION['input_license']=1;
            include_once("settings.php");
        } else {
            include_once("$page.php");
        }
        ?>
    </div>
  </div>
    <?php include_once("footer.php"); ?>
</div>
</div>
<a class="scroll-to-top rounded" href="#page-top">
<i class="fas fa-angle-up"></i>
</a>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="js/sb-admin-2.js?v=4"></script>
<script>
    window.backend_labels = {
        "edit":`<?php echo _("EDIT"); ?>`,
        "publish":`<?php echo _("PUBLISH"); ?>`,
        "preview":`<?php echo _("PREVIEW"); ?>`,
        "markers":`<?php echo _("MARKERS"); ?>`,
        "delete":`<?php echo _("DELETE"); ?>`,
        "duplicate":`<?php echo _("DUPLICATE"); ?>`,
        "main_image":`<?php echo _("MAIN IMAGE"); ?>`,
        "set_as_main":`<?php echo _("SET AS MAIN"); ?>`,
        "searching":`<?php echo _("SEARCHING"); ?>`,
        "get_gps_position":`<?php echo _("GET GPS POSITION"); ?>`,
        "no_icon_msg":`<?php echo _("No icon images in this library!"); ?>`,
        "delete_msg":`<?php echo _("Are you sure you want to delete?"); ?>`,
        "icons_assign_msg":`<?php echo _("Are you sure you want to assign this icon to all the markers?"); ?>`,
        "copied":`<?php echo _("copied"); ?>`,
    };
</script>
</body>
</html>
