<?php
$id_user = $_SESSION['id_user'];
$reports = json_decode(get_reports($id_user),true);
require_once("vendor/RandomColor.php");
use \Colors\RandomColor;
$array_colors = RandomColor::many(count($reports['maps']));
$array_maps = $reports['maps'];
$array_num_markers = $reports['num_markers'];
if (!empty($settings['welcome_msg'])) {
    $welcome_msg = $settings['welcome_msg'];
} else {
    $welcome_msg = sprintf(_('Welcome to %s configuration panel, where you can create your virtual tours in a few simple steps.'), $settings['name']);
}
?>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card shadow mb-12">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="far fa-smile"></i> <?php echo _("Welcome"); ?></h6>
            </div>
            <div class="card-body">
                <span><?php echo $welcome_msg ?></span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><?php echo _("Maps"); ?></div>
                        <div id="num_maps" class="h5 mb-0 font-weight-bold text-gray-800">--</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-map fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1"><?php echo _("Markers"); ?></div>
                        <div id="num_markers" class="h5 mb-0 font-weight-bold text-gray-800">--</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 mt-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-line"></i> <?php echo _("Visitors N."); ?></h6>
            </div>
            <div id="list_visitors" class="card-body">
                <p><?php echo sprintf(_('No maps created yet. Go to %s and create a new one!'),'<a href="index.php?p=maps">'._("Maps").'</a>'); ?></p>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-map-marker-alt"></i> <?php echo _("Markers Count"); ?></h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="chart-pie">
                            <canvas id="num_markers_chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict";
        Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#858796';
        window.id_user = '<?php echo $id_user; ?>';
        $(document).ready(function () {
            get_dashboard_stats();
            var ctx = document.getElementById("num_markers_chart");
            var num_markers_chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [<?php echo '"'.implode('","', $array_maps).'"'; ?>],
                    datasets: [{
                        data: [<?php echo implode(',', $array_num_markers); ?>],
                        backgroundColor: [<?php echo '"'.implode('","', $array_colors).'"'; ?>],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    scales: {
                        yAxes: [{
                            display: true,
                            minBarLength: 5,
                            ticks: {
                                beginAtZero: true,
                            }
                        }],
                        xAxes: [{
                            barThickness: 'flex',
                            maxBarThickness: 50
                        }]
                    },
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: true,
                        caretPadding: 10,
                    },
                    legend: {
                        display: false
                    },
                    cutoutPercentage: 50,
                },
            });
        });
    })(jQuery);
</script>