<?php
$dashboard_active = "";
$statistics_active = "";
$maps_active = "";
$categories_active = "";
$markers_active = "";
$markers_connections_active = "";
$geometries_active = "";
$story_active = "";
$reviews_active = "";
$icons_active = "";
$preview_active = "";
$publish_active = "";
$users_active = "";
$settings_active = "";
$updater_active = "";
switch ($page) {
    case 'dashboard':
        $dashboard_active = "active";
        break;
    case 'statistics':
        $statistics_active = "active";
        break;
    case 'edit_map':
    case 'maps':
        $maps_active = "active";
        break;
    case 'edit_category':
    case 'add_category':
    case 'categories':
        $categories_active = "active";
        break;
    case 'edit_marker':
    case 'add_marker':
    case 'markers':
    case 'import':
        $markers_active = "active";
        break;
    case 'markers_connections':
        $markers_connections_active = "active";
        break;
    case 'story':
        $story_active = "active";
        break;
    case 'reviews':
        $reviews_active = "active";
        break;
    case 'icons_library':
        $icons_active = "active";
        break;
    case 'preview':
        $preview_active = "active";
        break;
    case 'publish':
        $publish_active = "active";
        break;
    case 'users':
    case 'edit_user':
        $users_active = "active";
        break;
    case 'settings':
        $settings_active = "active";
        break;
    case 'updater':
        $updater_active = "active";
        break;
    case 'draw':
        $geometries_active = "active";
        break;
}
$maps = get_maps($id_user);
$count_maps = count($maps);
if($count_maps==0) {
    $m_disabled = "disabled";
} else {
    $m_disabled = "";
}
$settings = get_settings();
$to_validate = get_markers_to_validate($id_user);
?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php?p=dashboard">
        <div class="sidebar-brand-icon">
            <?php if($settings['logo']!='') { ?>
                <img style="max-height:50px;max-width:80px;width:auto;height:auto" src="assets/<?php echo $settings['logo']; ?>" />
            <?php } else { ?>
                <?php echo strtoupper($settings['name']); ?>
            <?php } ?>
        </div>
    </a>
    <hr class="sidebar-divider my-0">
    <?php if($demo) : ?>
        <li class="nav-item">
            <div style="cursor:default;border-radius:0;width:100%;" class="card bg-danger text-white p-1 text-center border-0 noselect d-inline-block">
                <?php echo _("DEMO MODE"); ?> <i title="<?php echo _("ADD / SAVE features are disabled."); ?>" class="help_d fas fa-question-circle"></i>
            </div>
        </li>
        <script>
            $('.help_d').tooltipster({
                delay: 0,
                position: 'right'
            });
        </script>
    <?php endif; ?>
    <li class="nav-item <?php echo $dashboard_active; ?>">
        <a class="nav-link" href="index.php?p=dashboard">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span><?php echo _("Dashboard"); ?></span></a>
    </li>
    <li class="nav-item <?php echo $statistics_active; ?>">
        <a class="nav-link" href="index.php?p=statistics">
            <i class="fas fa-fw fa-chart-area"></i>
            <span><?php echo _("Statistics"); ?></span></a>
    </li>
    <hr class="sidebar-divider">
    <li class="nav-item <?php echo $maps_active; ?>">
        <a class="nav-link" href="index.php?p=maps">
            <i class="fas fa-fw fa-map"></i>
            <span><?php echo _("Maps"); ?></span></a>
    </li>
    <li class="nav-item <?php echo $markers_active; ?>">
        <a class="nav-link <?php echo $m_disabled; ?>" href="index.php?p=markers">
            <i class="fas fa-fw fa-map-marker-alt"></i>
            <span><?php echo _("Markers"); ?> <?php echo ($to_validate>0) ? '<i style="color:white;font-size:11px;" class="fas fa-exclamation-circle"></i>' : ''; ?></span></a>
    </li>
    <li class="nav-item <?php echo $markers_connections_active; ?>">
        <a class="nav-link <?php echo $m_disabled; ?>" href="index.php?p=markers_connections">
            <i class="fas fa-fw fa-exchange-alt"></i>
            <span><?php echo _("Connections"); ?></span></a>
    </li>
    <li class="nav-item <?php echo $geometries_active; ?>">
        <a class="nav-link <?php echo $m_disabled; ?>" href="index.php?p=draw">
            <i class="fas fa-fw fa-draw-polygon"></i>
            <span><?php echo _("Geometries"); ?></span></a>
    </li>
    <li class="nav-item <?php echo $story_active; ?>">
        <a class="nav-link <?php echo $m_disabled; ?>" href="index.php?p=story">
            <i class="fas fa-fw fa-list-ol"></i>
            <span><?php echo _("Story"); ?></span></a>
    </li>
    <hr class="sidebar-divider">
    <li class="nav-item <?php echo $categories_active; ?>">
        <a class="nav-link <?php echo $m_disabled; ?>" href="index.php?p=categories">
            <i class="fas fa-fw fa-stream"></i>
            <span><?php echo _("Categories"); ?></span></a>
    </li>
    <li class="nav-item <?php echo $icons_active; ?>">
        <a class="nav-link <?php echo $m_disabled; ?>" href="index.php?p=icons_library">
            <i class="fas fa-fw fa-icons"></i>
            <span><?php echo _("Icons Library"); ?></span></a>
    </li>
    <li class="nav-item <?php echo $reviews_active; ?>">
        <a class="nav-link <?php echo $m_disabled; ?>" href="index.php?p=reviews">
            <i class="fas fa-fw fa-feather-alt"></i>
            <span><?php echo _("Reviews"); ?></span></a>
    </li>
    <hr class="sidebar-divider">
    <li class="nav-item <?php echo $preview_active; ?>">
        <a class="nav-link <?php echo $m_disabled; ?>" href="index.php?p=preview">
            <i class="fas fa-fw fa-eye"></i>
            <span><?php echo _("Preview"); ?></span></a>
    </li>
    <li class="nav-item <?php echo $publish_active; ?>">
        <a class="nav-link <?php echo $m_disabled; ?>" href="index.php?p=publish">
            <i class="fas fa-fw fa-paper-plane"></i>
            <span><?php echo _("Publish"); ?></span></a>
    </li>
    <?php if($user_info['role']=='administrator') : ?>
        <hr class="sidebar-divider d-none d-md-block">
        <li class="nav-item <?php echo $settings_active; ?>">
            <a class="nav-link" href="index.php?p=settings">
                <i class="fas fa-fw fa-cog"></i>
                <span><?php echo _("Settings"); ?></span></a>
        </li>
        <li class="nav-item <?php echo $updater_active; ?>">
            <a class="nav-link" href="index.php?p=updater">
                <i class="fas fa-fw fa-download"></i>
                <span><?php echo _("Upgrade"); ?> <?php echo (version_compare($version,$latest_version)==-1) ? '<i style="color:white;font-size:11px;" class="fas fa-exclamation-circle"></i>' : ''; ?></span></a>
        </li>
        <li class="nav-item <?php echo $users_active; ?>">
            <a class="nav-link" href="index.php?p=users">
                <i class="fas fa-fw fa-user"></i>
                <span><?php echo _("Users"); ?></span></a>
        </li>
    <?php endif; ?>
    <hr class="sidebar-divider d-none d-md-block">
    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>

<script>
    if ($(window).width() < 768) {
        $(".nav-item.active .collapse").removeClass('show');
    }
    $("#sidebarToggle").click(function(){
        if($('#accordionSidebar').hasClass('toggled')) {
            sessionStorage.setItem("sidebar_accord", 1);
            $(".nav-item.active .collapse").addClass('show');
        } else {
            sessionStorage.setItem("sidebar_accord", 0);
            $(".collapse").removeClass('show');
        }
    });
    if ("sidebar_accord" in sessionStorage) {
        if(sessionStorage.getItem('sidebar_accord') == 0) {
            $('#accordionSidebar').addClass('toggled');
            $(".collapse").removeClass('show');
        } else {
            if ($(window).width() >= 768) {
                $(".nav-item.active .collapse").addClass('show');
            }
        }
    }
</script>