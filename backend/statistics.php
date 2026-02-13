<?php
session_start();
$id_map_sel = $_SESSION['id_map_sel'];
$settings = get_settings();
$theme_color = $settings['theme_color'];
if(empty($_SESSION['lang'])) {
    $lang = $settings['language'];
} else {
    $lang = $_SESSION['lang'];
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-line"></i> <?php echo _("Map Accesses"); ?></h6>
            </div>
            <div class="card-body">
                <div id="chart_visitor_map"></div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="far fa-eye"></i> <?php echo _("Marker views"); ?></h6>
            </div>
            <div id="chart_marker_views" class="card-body">

            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict";
        window.id_user = '<?php echo $id_user; ?>';
        window.id_map = '<?php echo $id_map_sel; ?>';
        window.theme_color = '<?php echo $theme_color; ?>';
        $(document).ready(function () {
            Highcharts.setOptions({
                global : {
                    useUTC : false
                },
                lang: {
                    loading: "<?php echo _("Loading..."); ?>",
                    months: ["<?php echo _("January"); ?>", "<?php echo _("February"); ?>", "<?php echo _("March"); ?>", "<?php echo _("April"); ?>", "<?php echo _("May"); ?>", "<?php echo _("June"); ?>", "<?php echo _("July"); ?>", "<?php echo _("August"); ?>", "<?php echo _("September"); ?>", "<?php echo _("October"); ?>", "<?php echo _("November"); ?>", "<?php echo _("December"); ?>"],
                    weekdays: ["<?php echo _("Sunday"); ?>", "<?php echo _("Monday"); ?>", "<?php echo _("Tuesday"); ?>", "<?php echo _("Wednesday"); ?>", "<?php echo _("Thursday"); ?>", "<?php echo _("Friday"); ?>", "<?php echo _("Saturday"); ?>"],
                    shortMonths: ["<?php echo formatTime("%b",$lang,strtotime('2000-01-01')); ?>", "<?php echo formatTime("%b",$lang,strtotime('2000-02-01')); ?>", "<?php echo formatTime("%b",$lang,strtotime('2000-03-01')); ?>", "<?php echo formatTime("%b",$lang,strtotime('2000-04-01')); ?>", "<?php echo formatTime("%b",$lang,strtotime('2000-05-01')); ?>", "<?php echo formatTime("%b",$lang,strtotime('2000-06-01')); ?>", "<?php echo formatTime("%b",$lang,strtotime('2000-07-01')); ?>", "<?php echo formatTime("%b",$lang,strtotime('2000-08-01')); ?>", "<?php echo formatTime("%b",$lang,strtotime('2000-09-01')); ?>", "<?php echo formatTime("%b",$lang,strtotime('2000-10-01')); ?>", "<?php echo formatTime("%b",$lang,strtotime('2000-11-01')); ?>", "<?php echo formatTime("%b",$lang,strtotime('2000-12-01')); ?>"],
                    exportButtonTitle: "<?php echo _("Export"); ?>",
                    printButtonTitle: "<?php echo _("Import"); ?>",
                    rangeSelectorFrom: "<?php echo _("From"); ?>",
                    rangeSelectorTo: "<?php echo _("To"); ?>",
                    rangeSelectorZoom: "<?php echo _("Period"); ?>",
                    downloadPNG: "<?php echo _("Download image PNG"); ?>",
                    downloadJPEG: "<?php echo _("Download image JPEG"); ?>",
                    downloadPDF: "<?php echo _("Download document PDF"); ?>",
                    downloadSVG: "<?php echo _("Download image SVG"); ?>",
                    printChart: "<?php echo _("Print"); ?>",
                    thousandsSep: ".",
                    decimalPoint: ','
                }
            });
            get_statistics('chart_visitor_map');
            get_statistics('chart_marker_views');
        });
    })(jQuery);
</script>