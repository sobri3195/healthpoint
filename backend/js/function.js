(function($) {
    "use strict";
    var marker_pos, map_view, map, vectorLayer;

    window.login = function () {
        var username = $('#username_l').val();
        var password = $('#password_l').val();
        $('#btn_login').addClass("disabled");
        $.ajax({
            url: "ajax/login.php",
            type: "POST",
            data: {
                username: username,
                password: password
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                if(rsp.status=='ok') {
                    location.href="index.php";
                } else {
                    $('#username_l').addClass("error-highlight");
                    $('#password_l').addClass("error-highlight");
                    $('#btn_login').removeClass("disabled");
                }
            }
        });
    }

    window.logout = function () {
        $.ajax({
            url: "ajax/logout.php",
            type: "POST",
            async: true,
            success: function (json) {
                location.href="login.php";
            }
        });
    }

    window.get_dashboard_stats = function () {
        $.ajax({
            url: "ajax/get_dashboard_stats.php",
            type: "POST",
            data: {
                id_user: window.id_user
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                $('#num_maps').html(rsp.count_maps);
                $('#num_markers').html(rsp.count_markers);
                var total_visitors = rsp.total_visitors;
                var visitors = rsp.visitors;
                var html_visitors = "";
                jQuery.each(visitors, function(index, visitor) {
                    var m_name = visitor.name;
                    var count = visitor.count;
                    var perc = count / total_visitors * 100;
                    perc = Math.round(perc);
                    html_visitors += '<h4 style="margin-bottom:1px" class="small font-weight-bold">'+m_name+' <span class="float-right">'+count+'/<b>'+total_visitors+'</b></span></h4>\n' +
                        '                <div class="progress mb-2">\n' +
                        '                    <div class="progress-bar bg-primary" role="progressbar" style="width: '+perc+'%" aria-valuenow="'+perc+'" aria-valuemin="0" aria-valuemax="100"></div>\n' +
                        '                </div>';
                });
                if(html_visitors!='') {
                    $('#list_visitors').html(html_visitors);
                }
            }
        });
    };

    window.get_maps = function () {
        $.ajax({
            url: "ajax/get_maps.php",
            type: "POST",
            data: {
                id_user: window.id_user
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                parse_map_list(rsp);
            }
        });
    };

    function parse_map_list(maps) {
        var html = '';
        jQuery.each(maps, function(index, map) {
            var id = map.id;
            var name = map.name;
            var date_created = map.date_created;
            var username = map.username;
            var edit_permission = map.edit_permission;
            var publish_permission = map.publish_permission;
            var count_markers = parseInt(map.count_markers);
            if(count_markers>0) {
                var badge_marker_style = 'badge-secondary';
            } else {
                var badge_marker_style = 'badge-light';
            }
            var btn_duplicate = '';
            var btn_delete = '';
            if(window.can_create==1) {
                btn_duplicate = '<a title="'+window.backend_labels.duplicate+'" href="#" onclick="modal_duplicate_map('+id+');return false;" class="btn btn-secondary btn-sm">\n' +
                    '                            <i class="fas fa-copy"></i>\n' +
                    '                        </a>\n';
                btn_delete = '<a title="'+window.backend_labels.delete+'" href="#" onclick="modal_delete_map('+id+');return false;" class="btn btn-danger btn-sm">\n' +
                '                            <i class="fas fa-trash"></i>\n' +
                '                        </a>\n';
            }
            var btn_edit = '';
            var btn_publish = '';
            if(edit_permission) {
                btn_edit = '<a title="'+window.backend_labels.edit+'" href="index.php?p=edit_map&id='+id+'" class="btn btn-warning btn-sm">\n' +
                    '                            <i class="fas fa-edit"></i>\n' +
                    '                        </a>\n';
            }
            if(publish_permission) {
                btn_publish = '<a title="'+window.backend_labels.publish+'" href="index.php?p=publish&id_map='+id+'" class="btn btn-dark btn-sm">\n' +
                    '                            <i class="fas fa-paper-plane"></i>\n' +
                    '                        </a>\n';
            }
            html += '<div class="div_map card mb-2 py-1">\n' +
                '            <div class="card-body" style="padding-top: 0;padding-bottom: 0;">\n' +
                '                <div class="row">\n' +
                '                    <div class="col-md-8 text-center text-sm-center text-md-left text-lg-left">\n' +
                '                        <b>'+name+'</b><br><span style="font-size:12px">'+date_created+' - '+username+'</span>\n' +
                '                    </div>\n' +
                '                    <div class="map_buttons col-md-4 pt-1 text-center text-sm-center text-md-right text-lg-right">\n' + btn_edit +
                '                        <a title="'+window.backend_labels.markers+'" href="index.php?p=markers&id_map='+id+'" class="btn btn-info btn-sm position-relative">\n' +
                '                            <i class="fas fa-map-marker-alt"></i>\n' +
                '                             <span style="top:-8px;right:-5px" class="badge badge-pill '+badge_marker_style+' position-absolute">'+count_markers+'</span>\n' +
                '                        </a>\n' +
                '                        <a title="'+window.backend_labels.preview+'" href="index.php?p=preview&id_map='+id+'" class="btn btn-dark btn-sm position-relative">\n' +
                '                            <i class="fas fa-eye"></i>\n' +
                '                        </a>\n' + btn_publish + btn_duplicate + btn_delete +
                '                    </div>\n' +
                '                </div>\n' +
                '            </div>\n' +
                '        </div>';
        });
        $('#maps_list').html(html).promise().done(function() {
            $('#maps_list .btn').tooltipster({
                delay: 10,
                hideOnClick: true
            });
        });
    }

    window.modal_delete_map = function(id) {
        $('#btn_delete_map').attr('onclick','delete_map('+id+');');
        $('#modal_delete_map').modal("show");
    }

    window.delete_map = function (id) {
        $('#modal_delete_map button').addClass("disabled");
        $.ajax({
            url: "ajax/delete_map.php",
            type: "POST",
            data: {
                id_user: window.id_user,
                id_map: id
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                if (rsp.status == "ok") {
                    $('#modal_delete_map button').removeClass("disabled");
                    $('#modal_delete_map').modal("hide");
                    location.reload();
                } else {
                    $('#modal_delete_map button').removeClass("disabled");
                }
            }
        });
    };

    window.modal_duplicate_map = function(id) {
        $('#btn_duplicate_map').attr('onclick','duplicate_map('+id+');');
        $('#modal_duplicate_map').modal("show");
    }

    window.duplicate_map = function (id) {
        $('#modal_duplicate_map button').addClass("disabled");
        $.ajax({
            url: "ajax/duplicate_map.php",
            type: "POST",
            data: {
                id_user: window.id_user,
                id_map: id
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                if (rsp.status == "ok") {
                    $('#modal_duplicate_map button').removeClass("disabled");
                    $('#modal_duplicate_map').modal("hide");
                    location.reload();
                } else {
                    $('#modal_duplicate_map button').removeClass("disabled");
                }
            }
        });
    };

    window.modal_delete_marker = function(id) {
        $('#btn_delete_marker').attr('onclick','delete_marker('+id+');');
        $('#modal_delete_marker').modal("show");
    }

    window.modal_clone_marker = function(id) {
        $('#btn_clone_marker').attr('onclick','clone_marker('+id+');');
        $('#modal_clone_marker').modal("show");
    }

    window.clone_marker = function (id) {
        $('#modal_clone_marker button').addClass("disabled");
        $.ajax({
            url: "ajax/duplicate_marker.php",
            type: "POST",
            data: {
                id_map: window.id_map,
                id_marker: id
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                if (rsp.status == "ok") {
                    $('#modal_clone_marker button').removeClass("disabled");
                    $('#modal_clone_marker').modal("hide");
                    location.href = 'index.php?p=markers';
                } else {
                    $('#modal_clone_marker button').removeClass("disabled");
                }
            }
        });
    }

    window.delete_marker = function (id) {
        $('#modal_delete_marker button').addClass("disabled");
        $.ajax({
            url: "ajax/delete_marker.php",
            type: "POST",
            data: {
                id_map: window.id_map,
                id_marker: id
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                if (rsp.status == "ok") {
                    $('#modal_delete_marker button').removeClass("disabled");
                    $('#modal_delete_marker').modal("hide");
                    location.href = 'index.php?p=markers';
                } else {
                    $('#modal_delete_marker button').removeClass("disabled");
                }
            }
        });
    };

    window.modal_delete_category = function(id) {
        $('#btn_delete_category').attr('onclick','delete_category('+id+');');
        $('#modal_delete_category').modal("show");
    }

    window.delete_category = function (id) {
        $('#modal_delete_category button').addClass("disabled");
        $.ajax({
            url: "ajax/delete_category.php",
            type: "POST",
            data: {
                id_category: id
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                if (rsp.status == "ok") {
                    $('#modal_delete_category button').removeClass("disabled");
                    $('#modal_delete_category').modal("hide");
                    location.href = 'index.php?p=categories';
                } else {
                    $('#modal_delete_category button').removeClass("disabled");
                }
            }
        });
    };

    window.modal_delete_user = function(id) {
        $('#btn_delete_user').attr('onclick','delete_user('+id+');');
        $('#modal_delete_user').modal("show");
    }

    window.delete_user = function (id) {
        $('#modal_delete_user button').addClass("disabled");
        $.ajax({
            url: "ajax/delete_user.php",
            type: "POST",
            data: {
                id_user: id
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                if (rsp.status == "ok") {
                    $('#modal_delete_user button').removeClass("disabled");
                    $('#modal_delete_user').modal("hide");
                    location.href = 'index.php?p=users';
                } else {
                    $('#modal_delete_user button').removeClass("disabled");
                }
            }
        });
    };

    window.add_map = function () {
        var complete = true;
        var name = $('#name').val();
        if(name=='') {
            complete = false;
            $('#name').addClass("error-highlight");
        } else {
            $('#name').removeClass("error-highlight");
        }
        if(complete) {
            $('#modal_new_map button').addClass("disabled");
            $.ajax({
                url: "ajax/add_map.php",
                type: "POST",
                data: {
                    id_user: window.id_user,
                    name: name
                },
                async: true,
                success: function (json) {
                    var rsp = JSON.parse(json);
                    if (rsp.status == "ok") {
                        $('#modal_new_map button').removeClass("disabled");
                        $('#modal_new_map').modal("hide");
                        location.reload();
                    } else {
                        $('#modal_new_map button').removeClass("disabled");
                    }
                }
            });
        }
    };

    window.set_session_m = function (id) {
        $.ajax({
            url: "ajax/set_session_m.php",
            type: "POST",
            data: {
                id_map: id
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                if (rsp.status == "ok") {
                    location.reload();
                }
            }
        });
    }

    window.preview = function(id,container_h) {
        $.ajax({
            url: "ajax/get_code.php",
            type: "POST",
            data: {
                id_map: id
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                var code = rsp.code;
                if(code!="") {
                    $('#iframe_div').show();
                    $("#iframe_div").html("<iframe allow='geolocation' " +
                        "width=\"100%\" height=\""+container_h+"px\" frameborder=\"0\" scrolling=\"no\" " +
                        "marginheight=\"0\" marginwidth=\"0\" " +
                        "src=\"../viewer/index.php?code="+code+"\"" +
                        "></iframe>");
                }
            }
        });
    }

    window.save_map = function() {
        var complete = true;
        var name = $('#name').val();
        var id_user = $('#user option:selected').attr('id');
        var maptiler_api = $('#maptiler_api').val();
        if(name=='') {
            complete = false;
            $('#name').addClass("error-highlight");
        } else {
            $('#name').removeClass("error-highlight");
        }
        var main_color_hex = window.main_color_hex_spectrum.spectrum('get').toHexString();
        var markers_size = $('#markers_size').val();
        var map_style = $('.style-highlight').parent().attr('id');
        var ga_tracking_id = $('#ga_tracking_id').val();
        var show_list = $('#show_list').is(':checked');
        if(show_list) show_list=1; else show_list=0;
        var enable_reviews = $('#enable_reviews').is(':checked');
        if(enable_reviews) enable_reviews=1; else enable_reviews=0;
        var density_color = $('#density_color').is(':checked');
        if(density_color) density_color=1; else density_color=0;
        var enable_directions = $('#enable_directions').is(':checked');
        if(enable_directions) enable_directions=1; else enable_directions=0;
        var enable_add_marker = parseInt($('#enable_add_marker option:selected').attr('id'));
        var enable_streetview = $('#enable_streetview').is(':checked');
        if(enable_streetview) enable_streetview=1; else enable_streetview=0;
        var enable_share = $('#enable_share').is(':checked');
        if(enable_share) enable_share=1; else enable_share=0;
        var default_zoom = $('#default_zoom option:selected').attr('id');
        var selected_zoom = $('#selected_zoom option:selected').attr('id');
        var search_highlight = $('#search_highlight option:selected').attr('id');
        var language = $('#language option:selected').attr('id');
        var street_basemap = $('#street_basemap').val();
        var satellite_basemap = $('#satellite_basemap').val();
        var street_maxzoom = $('#street_maxzoom').val();
        var satellite_maxzoom = $('#satellite_maxzoom').val();
        var cluster_distance = $('#cluster_distance').val();
        var density_color_tolerance = $('#density_color_tolerance').val();
        var cat_filter = $('#cat_filter').is(':checked');
        if(cat_filter) cat_filter=1; else cat_filter=0;
        var nav_markers = $('#nav_markers').is(':checked');
        if(nav_markers) nav_markers=1; else nav_markers=0;
        var font_viewer = $('#font_viewer').val();
        var sheet_detail = $('#sheet_detail').is(':checked');
        if(sheet_detail) sheet_detail=1; else sheet_detail=0;
        var default_view = $('#default_view option:selected').attr('id');
        var quality = $('#quality option:selected').attr('id');
        var order_by = $('#order_by option:selected').attr('id');
        var default_my_location = $('#default_my_location').is(':checked');
        if(default_my_location) default_my_location=1; else default_my_location=0;
        var my_location_zoom = $('#my_location_zoom option:selected').attr('id');
        var enable_search = $('#enable_search').is(':checked');
        if(enable_search) enable_search=1; else enable_search=0;
        var enable_search_location = $('#enable_search_location').is(':checked');
        if(enable_search_location) enable_search_location=1; else enable_search_location=0;
        var enable_list = $('#enable_list').is(':checked');
        if(enable_list) enable_list=1; else enable_list=0;
        var enable_categories = $('#enable_categories').is(':checked');
        if(enable_categories) enable_categories=1; else enable_categories=0;
        var enable_story = $('#enable_story').is(':checked');
        if(enable_story) enable_story=1; else enable_story=0;
        var enable_popup = $('#enable_popup').is(':checked');
        if(enable_popup) enable_popup=1; else enable_popup=0;
        var street_attributions = $('#street_attributions').val();
        var satellite_attributions = $('#satellite_attributions').val();
        var color_hex = $('#color_hex').val();
        var icon_color_hex = $('#icon_color_hex').val();
        var icon = $('#marker_icon_input').val();
        var unicode = window.getComputedStyle(document.getElementById("marker_icon"), ':before').content.replace(/'|"/g, '').charCodeAt(0).toString(16);
        if(unicode=='6e')  {
            icon='';
        } else {
            icon = icon + '|' + unicode;
        }
        var id_icon_library = $('#marker_library_icon').val();
        var icon_type = parseInt($('#icon_type option:selected').attr('id'));
        switch (icon_type) {
            case 0:
                icon = '';
                id_icon_library = 0;
                break;
            case 1:
                id_icon_library = 0;
                break;
            case 2:
                icon = '';
                break;
        }
        var geoJSON = $('#geoJSON').val();
        var weather = $('#weather option:selected').attr('id');
        var cat_filter_type = $('#cat_filter_type option:selected').attr('id');
        var enable_globe = $('#enable_globe option:selected').attr('id');
        if(complete) {
            $('#save_btn .icon i').removeClass('far fa-circle').addClass('fas fa-circle-notch fa-spin');
            $('#save_btn').addClass("disabled");
            $.ajax({
                url: "ajax/save_map.php",
                type: "POST",
                data: {
                    id_map: id_map,
                    id_user: id_user,
                    name: name,
                    main_color_hex: main_color_hex,
                    markers_size: markers_size,
                    markers_color_hex: color_hex,
                    markers_icon_color_hex: icon_color_hex,
                    markers_icon: icon,
                    markers_id_icon_library: id_icon_library,
                    map_style: map_style,
                    maptiler_api: maptiler_api,
                    logo: window.logo,
                    ga_tracking_id: ga_tracking_id,
                    show_list: show_list,
                    default_zoom: default_zoom,
                    selected_zoom: selected_zoom,
                    search_highlight: search_highlight,
                    density_color: density_color,
                    density_color_tolerance: density_color_tolerance,
                    enable_reviews: enable_reviews,
                    enable_directions: enable_directions,
                    enable_streetview: enable_streetview,
                    enable_share: enable_share,
                    language: language,
                    url_street: street_basemap,
                    url_sat: satellite_basemap,
                    zoom_street: street_maxzoom,
                    zoom_sat: satellite_maxzoom,
                    street_attributions: street_attributions,
                    satellite_attributions: satellite_attributions,
                    cluster_distance: cluster_distance,
                    cat_filter: cat_filter,
                    nav_markers: nav_markers,
                    enable_add_marker: enable_add_marker,
                    font_viewer: font_viewer,
                    sheet_detail: sheet_detail,
                    default_view: default_view,
                    quality: quality,
                    order_by: order_by,
                    default_my_location: default_my_location,
                    my_location_zoom: my_location_zoom,
                    enable_search: enable_search,
                    enable_search_location: enable_search_location,
                    enable_list: enable_list,
                    enable_categories: enable_categories,
                    enable_story: enable_story,
                    geoJSON: geoJSON,
                    weather: weather,
                    cat_filter_type: cat_filter_type,
                    enable_globe: enable_globe,
                    enable_popup: enable_popup
                },
                async: true,
                success: function (json) {
                    var rsp = JSON.parse(json);
                    if(rsp.status=="ok") {
                        window.map_need_save = false;
                        $('#save_btn .icon i').removeClass('fas fa-circle-notch fa-spin').addClass('fas fa-check');
                        setTimeout(function () {
                            $('#save_btn .icon i').removeClass('fas fa-check').addClass('far fa-circle');
                            $('#save_btn').removeClass("disabled");
                        },1000);
                    } else {
                        $('#save_btn .icon i').removeClass('fas fa-circle-notch fa-spin').addClass('fas fa-times');
                        $('#save_btn').removeClass('btn-success').addClass('btn-danger');
                        setTimeout(function () {
                            $('#save_btn .icon i').removeClass('fas fa-times').addClass('far fa-circle');
                            $('#save_btn').removeClass('btn-danger').addClass('btn-success');
                            $('#save_btn').removeClass("disabled");
                        },1000);
                    }
                }
            });
        }
    }

    window.delete_logo = function () {
        window.logo = '';
        window.map_need_save = true;
        $('#div_delete_logo').hide();
        $('#div_image_logo').hide();
        $('#div_upload_logo').show();
        $('#div_image_logo img').attr('src','');
    }

    window.change_style_marker = function () {
        var color_hex = $('#color_hex').val();
        var icon_color = $('#icon_color_hex').val();
        var icon_type = parseInt($('#icon_type option:selected').attr('id'));
        var marker_size = $('#marker_size').val();
        var unicode = window.getComputedStyle(document.getElementById("marker_icon"), ':before').content.replace(/'|"/g, '').charCodeAt(0).toString(16);
        var marker_library_icon = $('#marker_library_icon').val();
        var icon_image = $('#marker_library_icon_preview').attr('src');
        if(icon_type==2 && marker_library_icon==0) icon_type=0;
        if(color_hex=='') color_hex=window.main_color_hex;
        if(icon_color=='') icon_color='#ffffff';
        if(unicode=='6e')  {
            unicode='';
        }
        switch(icon_type) {
            case 0:
                if(marker_size==0) {
                    var scale = window.markers_size/10;
                } else {
                    var scale = marker_size/10;
                }
                var radius = 140*scale;
                marker_pos.setStyle(new ol.style.Style({
                    image: new ol.style.Circle({
                        radius: radius,
                        stroke: new ol.style.Stroke({
                            color: color_hex,
                            width: 3,
                        }),
                        fill: new ol.style.Fill({
                            color: color_hex,
                            weight: 1
                        })
                    })
                }));
                break;
            case 1:
                if(marker_size==0) {
                    var scale = window.markers_size/10;
                } else {
                    var scale = marker_size/10;
                }
                var radius = 140*scale;
                var font_size = 120*scale;
                marker_pos.setStyle(new ol.style.Style({
                    image: new ol.style.Circle({
                        radius: radius,
                        stroke: new ol.style.Stroke({
                            color: color_hex,
                            width: 3,
                        }),
                        fill: new ol.style.Fill({
                            color: color_hex,
                            weight: 1
                        })
                    }),
                    text: new ol.style.Text({
                        text: String.fromCharCode("0x" + unicode),
                        font: '900 '+font_size+'px "Font Awesome 5 Free"',
                        fill: new ol.style.Fill({
                            color: icon_color
                        })
                    })
                }));
                break;
            case 2:
                marker_pos.setStyle(null);
                if(marker_size==0) {
                    var scale = window.markers_size/10;
                } else {
                    var scale = marker_size/10;
                }
                marker_pos.setStyle(new ol.style.Style({
                    image: new ol.style.Icon({
                        anchor: [0.5, 0.5],
                        anchorXUnits: 'fraction',
                        anchorYUnits: 'fraction',
                        scale: scale,
                        opacity: 1,
                        src: icon_image,
                    })
                }));
                break;
        }
    }

    window.initialize_map_marker = function (lon,lat,mode) {
        marker_pos = new ol.Feature({
            geometry: new ol.geom.Point(ol.proj.fromLonLat([lon,lat]))
        });
        change_style_marker();
        var vectorSource = new ol.source.Vector({
            features: [marker_pos]
        });
        switch(mode) {
            case 'edit':
                var zoom = 14;
                vectorLayer = new ol.layer.Vector({
                    source: vectorSource,
                    zIndex: 2
                });
                break;
            case 'add':
                if(lat=='0') {
                    var zoom = 1;
                } else {
                    var zoom = 10;
                }
                vectorLayer = new ol.layer.Vector({
                    source: vectorSource,
                    zIndex: 2,
                    visible: false
                });
                break;
        }
        map_view = new ol.View({
            center: ol.proj.fromLonLat([lon,lat]),
            zoom: zoom
        });
        map = new ol.Map({
            target: 'map_marker',
            controls : ol.control.defaults.defaults({
                attribution : false,
                zoom : true,
            }),
            layers: [
                new ol.layer.Tile({
                    source: new ol.source.OSM()
                })
            ,vectorLayer],
            view: map_view
        });
        var currZoom = parseInt(map.getView().getZoom());
        $('#zoom_level').html(currZoom);
        map.on('moveend', function(e) {
            var newZoom = parseInt(map.getView().getZoom());
            if (currZoom != newZoom) {
                $('#zoom_level').html(newZoom);
                currZoom = newZoom;
            }
        });
        map.on('click', function(evt){
            vectorLayer.setVisible(true);
            marker_pos.getGeometry().setCoordinates(evt.coordinate);
            var lat_lon = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
            window.lon_marker = parseFloat(lat_lon[0]).toFixed(6);
            window.lat_marker = parseFloat(lat_lon[1]).toFixed(6);
            $('#lon').html(window.lon_marker);
            $('#lat').html(window.lat_marker);
            $('#longitude_edit').val(window.lon_marker);
            $('#latitude_edit').val(window.lat_marker);
            draw_geofence();
            window.marker_need_save = true;
        });
        var drag_interaction = new ol.interaction.Translate({layers: [vectorLayer]});
        vectorLayer.on('change',function(evt) {
            var coordinates = marker_pos.getGeometry().getCoordinates();
            var lat_lon = ol.proj.transform(coordinates, 'EPSG:3857', 'EPSG:4326');
            window.lon_marker = parseFloat(lat_lon[0]).toFixed(6);
            window.lat_marker = parseFloat(lat_lon[1]).toFixed(6);
            $('#lon').html(window.lon_marker);
            $('#lat').html(window.lat_marker);
            $('#longitude_edit').val(window.lon_marker);
            $('#latitude_edit').val(window.lat_marker);
            draw_geofence();
            window.marker_need_save = true;
        });
        map.addInteraction(drag_interaction);
        draw_geofence();
    };

    window.set_marker_position = function() {
        var lat = $('#latitude_edit').val();
        var lon = $('#longitude_edit').val();
        if(lat!='' && lon!='') {
            lat = lat.replace(",",".");
            lon = lon.replace(",",".");
            marker_pos.getGeometry().setCoordinates(ol.proj.fromLonLat([lon,lat]));
            var coordinates = marker_pos.getGeometry().getCoordinates();
            var size = map.getSize();
            var w = size[0]/2;
            var h = size[1]/2;
            map_view.centerOn(coordinates, size, [w,h]);
            window.lon_marker = parseFloat(lon).toFixed(6);
            window.lat_marker = parseFloat(lat).toFixed(6);
            $('#lon').html(window.lon_marker);
            $('#lat').html(window.lat_marker);
            $('#longitude_edit').val(window.lon_marker);
            $('#latitude_edit').val(window.lat_marker);
            draw_geofence();
            window.marker_need_save = true;
            vectorLayer.setVisible(true);
        }
    }

    window.get_marker_images = function(id) {
        $('#image_gallery').empty();
        $('#msg_sel_image').show();
        $('#btn_setmain_image').addClass('d-none');
        $('#btn_delete_image').addClass('d-none');
        $.ajax({
            url: "ajax/get_marker_images.php",
            type: "POST",
            data: {
                id_marker: id
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                jQuery.each(rsp, function(index, marker_image) {
                    $('#image_gallery').append("<a data-id='"+marker_image.id+"' data-action='update' data-image='"+marker_image.image+"' data-main='"+marker_image.main+"' href='#'><img src='../viewer/marker_images/thumb/"+marker_image.image+"'/></a>");
                });

                $("#image_gallery").justifiedGallery({
                    rowHeight: 100
                });
                $('#image_gallery a').on('click',function () {
                    event.preventDefault();
                    select_image($(this));
                });
            }
        });
    }

    var sel_elem = null;
    window.select_image = function (elem) {
        sel_elem = elem;
        var main = elem.data("main");
        $('#image_gallery a').removeClass("image-highlight");
        elem.addClass("image-highlight");
        if(main=='1') {
            $('#btn_setmain_image button').html('<i class="fa fa-check"></i> '+window.backend_labels.main_image);
            $('#btn_setmain_image button').prop('disabled',true);
        } else {
            $('#btn_setmain_image button').html(window.backend_labels.set_as_main);
            $('#btn_setmain_image button').prop('disabled',false);
        }
        $('#msg_sel_image').hide();
        $('#btn_setmain_image').removeClass('d-none');
        $('#btn_delete_image').removeClass('d-none');
    }

    window.delete_image = function () {
        window.marker_need_save = true;
        var main = sel_elem.data("main");
        sel_elem.attr('data-action', 'delete');
        sel_elem.data('action', 'delete');
        sel_elem.attr('data-main', '0');
        sel_elem.data('main', '0');
        sel_elem.css('background-color','red');
        sel_elem.css('pointer-events','none');
        sel_elem.css('opacity',0.2);
        sel_elem.removeClass('image-highlight');
        $('#btn_setmain_image').addClass('d-none');
        $('#btn_delete_image').addClass('d-none');
        $('#msg_sel_image').show();
        if(main=='1') {
            $("#image_gallery a").each(function(index) {
                if($(this).data('action')!='delete') {
                    $(this).attr('data-main', '1');
                    $(this).data('main', '1');
                    return false;
                }
            });
        }
    }

    window.set_as_main = function () {
        window.marker_need_save = true;
        $("#image_gallery a").each(function(index) {
            $(this).attr('data-main', '0');
            $(this).data('main', '0');
        });
        sel_elem.attr('data-main', '1');
        sel_elem.data('main', '1');
        select_image(sel_elem);
    }

    window.delete_icon = function () {
        $("#marker_icon").removeClass();
        $('#marker_icon_input').val('');
        $('#btn_delete_icon').prop('disabled',true);
        window.marker_need_save = true;
    }

    window.address_to_position = function () {
        var street = $('#street').val();
        var city = $('#city').val();
        var postal_code = $('#postal_code').val();
        var country = $('#country').val();
        var q = street + ',' + city + ',' + postal_code + ',' + country;
        q = q.replace(/\ /g, '+');
        $.get("https://nominatim.openstreetmap.org/search?q="+q+"&format=json&polygon=1&addressdetails=1", function(data, status){
            try {
                $('#latitude_edit').val(data[0].lat);
                $('#longitude_edit').val(data[0].lon);
                set_marker_position();
                map_view.setZoom(16);
            } catch (e) {}
        });
    }

    window.save_marker = function () {
        var complete = true;
        var name = $('#name').val();
        var categories_t = $('#category option');
        var categories = [];
        $(categories_t).each(function(index, elem){
            var id = $(this).attr('id');
            if($(this).is(':selected') && !$(this).is(':disabled')) {
                categories.push(id);
            }
        });
        var street = $('#street').val();
        var city = $('#city').val();
        var postal_code = $('#postal_code').val();
        var country = $('#country').val();
        var website = $('#website').val();
        var website_caption = $('#website_caption').val();
        var email = $('#email').val();
        var phone = $('#phone').val();
        var whatsapp = $('#whatsapp').val();
        var color_hex = $('#color_hex').val();
        var icon_color_hex = $('#icon_color_hex').val();
        var marker_size = $('#marker_size').val();
        var icon = $('#marker_icon_input').val();
        var unicode = window.getComputedStyle(document.getElementById("marker_icon"), ':before').content.replace(/'|"/g, '').charCodeAt(0).toString(16);
        if(unicode=='6e')  {
            icon='';
        } else {
            icon = icon + '|' + unicode;
        }
        var id_icon_library = $('#marker_library_icon').val();
        var icon_type = parseInt($('#icon_type option:selected').attr('id'));
        switch (icon_type) {
            case 0:
                icon = '';
                id_icon_library = 0;
                break;
            case 1:
                id_icon_library = 0;
                break;
            case 2:
                icon = '';
                break;
        }
        var icon_e1 = $('#marker_icon_input_e1').val();
        var icon_e2 = $('#marker_icon_input_e2').val();
        var icon_e3 = $('#marker_icon_input_e3').val();
        var icon_e4 = $('#marker_icon_input_e4').val();
        var icon_e5 = $('#marker_icon_input_e5').val();
        var icon_e6 = $('#marker_icon_input_e6').val();
        var icon_e7 = $('#marker_icon_input_e7').val();
        var icon_e8 = $('#marker_icon_input_e8').val();
        var icon_e9 = $('#marker_icon_input_e9').val();
        var icon_e10 = $('#marker_icon_input_e10').val();
        var icon_e11 = $('#marker_icon_input_e11').val();
        var icon_e12 = $('#marker_icon_input_e12').val();
        var icon_e13 = $('#marker_icon_input_e13').val();
        var icon_e14 = $('#marker_icon_input_e14').val();
        var icon_e15 = $('#marker_icon_input_e15').val();
        var icon_e16 = $('#marker_icon_input_e16').val();
        var icon_e17 = $('#marker_icon_input_e17').val();
        var icon_e18 = $('#marker_icon_input_e18').val();
        var icon_e19 = $('#marker_icon_input_e19').val();
        var icon_e20 = $('#marker_icon_input_e20').val();
        var icon_b1 = $('#marker_icon_input_b1').val();
        var title_b1 = $('#extra_button_title_1').val();
        var field_e1 = window.editor_html_extra_fields[1].getValue();
        var field_e2 = window.editor_html_extra_fields[2].getValue();
        var field_e3 = window.editor_html_extra_fields[3].getValue();
        var field_e4 = window.editor_html_extra_fields[4].getValue();
        var field_e5 = window.editor_html_extra_fields[5].getValue();
        var field_e6 = window.editor_html_extra_fields[6].getValue();
        var field_e7 = window.editor_html_extra_fields[7].getValue();
        var field_e8 = window.editor_html_extra_fields[8].getValue();
        var field_e9 = window.editor_html_extra_fields[9].getValue();
        var field_e10 = window.editor_html_extra_fields[10].getValue();
        var field_e11 = window.editor_html_extra_fields[11].getValue();
        var field_e12 = window.editor_html_extra_fields[12].getValue();
        var field_e13 = window.editor_html_extra_fields[13].getValue();
        var field_e14 = window.editor_html_extra_fields[14].getValue();
        var field_e15 = window.editor_html_extra_fields[15].getValue();
        var field_e16 = window.editor_html_extra_fields[16].getValue();
        var field_e17 = window.editor_html_extra_fields[17].getValue();
        var field_e18 = window.editor_html_extra_fields[18].getValue();
        var field_e19 = window.editor_html_extra_fields[19].getValue();
        var field_e20 = window.editor_html_extra_fields[20].getValue();
        var value_b1 = window.editor_html_extra_button_1.getValue();
        var active = $('#active').is(':checked');
        if(active) active=1; else active=0;
        var open_sheet = $('#open_sheet').is(':checked');
        if(open_sheet) open_sheet=1; else open_sheet=0;
        var view_popup = $('#view_popup').is(':checked');
        if(view_popup) view_popup=1; else view_popup=0;
        var centered = $('#centered').is(':checked');
        if(centered) centered=1; else centered=0;
        var featured = $('#featured').is(':checked');
        if(featured) featured=1; else featured=0;
        if(window.id_marker==0) {
            var to_validate = 0;
        } else {
            var to_validate = $('#to_validate').is(':checked');
            if(to_validate) to_validate=0; else to_validate=1;
        }
        var view_directions = $('#view_directions').is(':checked');
        if(view_directions) view_directions=1; else view_directions=0;
        var view_street_view = $('#view_street_view').is(':checked');
        if(view_street_view) view_street_view=1; else view_street_view=0;
        var view_review = $('#view_review').is(':checked');
        if(view_review) view_review=1; else view_review=0;
        var view_share = $('#view_share').is(':checked');
        if(view_share) view_share=1; else view_share=0;
        if(name=='') {
            complete = false;
            $('#name').addClass("error-highlight");
        } else {
            $('#name').removeClass("error-highlight");
        }
        if($('#latitude_edit').val()=='') {
            complete = false;
            $('#latitude_edit').addClass("error-highlight");
        } else {
            $('#latitude_edit').removeClass("error-highlight");
        }
        if($('#longitude_edit').val()=='') {
            complete = false;
            $('#longitude_edit').addClass("error-highlight");
        } else {
            $('#longitude_edit').removeClass("error-highlight");
        }
        var hours = window.hours_editor.root.innerHTML;
        var description = window.description_editor.root.innerHTML;
        var array_images = [];
        $("#image_gallery a").each(function(index) {
            var id = $(this).data("id");
            var main = $(this).data("main");
            var action = $(this).data("action");
            var image = $(this).data("image");
            array_images.push({id:id,main:main,action:action,image:image});
        });
        var array_images_json = JSON.stringify(array_images);
        var min_zoom_level = $('#min_zoom_level option:selected').attr('id');
        var geofence_radius = $('#geofence_radius').val();
        var geofence_color = $('#geofence_color').val();
        var popup_image_height = $('#popup_image_height').val();
        var popup_background = $('#popup_background').val();
        var popup_color = $('#popup_color').val();
        if(complete) {
            $('#save_btn .icon i').removeClass('far fa-circle').addClass('fas fa-circle-notch fa-spin');
            $('#save_btn').addClass("disabled");
            $.ajax({
                url: "ajax/save_marker.php",
                type: "POST",
                data: {
                    id: window.id_marker,
                    name: name,
                    id_categories: categories,
                    street: street,
                    city: city,
                    postal_code: postal_code,
                    country: country,
                    website: website,
                    website_caption: website_caption,
                    email: email,
                    phone: phone,
                    whatsapp: whatsapp,
                    h_desc: hours,
                    description: description,
                    lat: window.lat_marker,
                    lon: window.lon_marker,
                    min_zoom_level: min_zoom_level,
                    array_images: array_images_json,
                    active: active,
                    open_sheet: open_sheet,
                    view_popup: view_popup,
                    to_validate: to_validate,
                    centered: centered,
                    featured: featured,
                    view_directions: view_directions,
                    view_street_view: view_street_view,
                    view_review: view_review,
                    view_share: view_share,
                    color_hex: color_hex,
                    icon_color_hex: icon_color_hex,
                    marker_size: marker_size,
                    icon: icon,
                    id_icon_library: id_icon_library,
                    icon_e1: icon_e1,
                    icon_e2: icon_e2,
                    icon_e3: icon_e3,
                    icon_e4: icon_e4,
                    icon_e5: icon_e5,
                    icon_e6: icon_e6,
                    icon_e7: icon_e7,
                    icon_e8: icon_e8,
                    icon_e9: icon_e9,
                    icon_e10: icon_e10,
                    icon_e11: icon_e11,
                    icon_e12: icon_e12,
                    icon_e13: icon_e13,
                    icon_e14: icon_e14,
                    icon_e15: icon_e15,
                    icon_e16: icon_e16,
                    icon_e17: icon_e17,
                    icon_e18: icon_e18,
                    icon_e19: icon_e19,
                    icon_e20: icon_e20,
                    field_e1: field_e1,
                    field_e2: field_e2,
                    field_e3: field_e3,
                    field_e4: field_e4,
                    field_e5: field_e5,
                    field_e6: field_e6,
                    field_e7: field_e7,
                    field_e8: field_e8,
                    field_e9: field_e9,
                    field_e10: field_e10,
                    field_e11: field_e11,
                    field_e12: field_e12,
                    field_e13: field_e13,
                    field_e14: field_e14,
                    field_e15: field_e15,
                    field_e16: field_e16,
                    field_e17: field_e17,
                    field_e18: field_e18,
                    field_e19: field_e19,
                    field_e20: field_e20,
                    icon_b1: icon_b1,
                    title_b1: title_b1,
                    value_b1: value_b1,
                    geofence_radius: geofence_radius,
                    geofence_color: geofence_color,
                    popup_image_height: popup_image_height,
                    popup_background: popup_background,
                    popup_color: popup_color
                },
                async: true,
                success: function (json) {
                    var rsp = JSON.parse(json);
                    if(rsp.status=="ok") {
                        if(window.id_marker==0) {
                            window.marker_need_save = false;
                            location.href='index.php?p=markers';
                        } else {
                            window.marker_need_save = false;
                            $('#save_btn .icon i').removeClass('fas fa-circle-notch fa-spin').addClass('fas fa-check');
                            setTimeout(function () {
                                $('#save_btn .icon i').removeClass('fas fa-check').addClass('far fa-circle');
                                $('#save_btn').removeClass("disabled");
                            },1000);
                            get_marker_images(window.id_marker);
                        }

                    } else {
                        $('#save_btn .icon i').removeClass('fas fa-circle-notch fa-spin').addClass('fas fa-times');
                        $('#save_btn').removeClass('btn-success').addClass('btn-danger');
                        setTimeout(function () {
                            $('#save_btn .icon i').removeClass('fas fa-times').addClass('far fa-circle');
                            $('#save_btn').removeClass('btn-danger').addClass('btn-success');
                            $('#save_btn').removeClass("disabled");
                        },1000);
                    }
                }
            });
        }
    }

    var layerCircle;
    function addCircle(lon,lat,radius,color) {
        var rgb = color.replace('rgba(','').replace(')','').split(',');
        rgb = "rgb("+rgb[0]+","+rgb[1]+","+rgb[2]+")";
        var centerLongitudeLatitude = ol.proj.fromLonLat([lon, lat]);
        layerCircle = new ol.layer.Vector({
            source: new ol.source.Vector({
                features: [new ol.Feature(new ol.geom.Circle(centerLongitudeLatitude, radius))]
            }),
            style: [
                new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: rgb,
                        width: 1
                    }),
                    fill: new ol.style.Fill({
                        color: color
                    })
                })
            ]
        });
        layerCircle.setZIndex(1);
        map.addLayer(layerCircle);
    }

    window.draw_geofence = function() {
        map.removeLayer(layerCircle);
        var geofence_radius = parseInt($('#geofence_radius').val());
        var geofence_color = $('#geofence_color').val();
        if(geofence_radius>0) {
            var lat = parseFloat(window.lat_marker);
            var lon = parseFloat(window.lon_marker);
            addCircle(lon, lat, geofence_radius, geofence_color);
        }
    }

    window.assign_icon_to_markers = function (type,id) {
        $('.btn_assign_icons').addClass('disavled');
        var retVal = confirm(window.backend_labels.icons_assign_msg);
        if( retVal == true ) {
            var markers_size = $('#markers_size').val();
            var color_hex = $('#color_hex').val();
            var icon_color_hex = $('#icon_color_hex').val();
            var icon = $('#marker_icon_input').val();
            var unicode = window.getComputedStyle(document.getElementById("marker_icon"), ':before').content.replace(/'|"/g, '').charCodeAt(0).toString(16);
            if(unicode=='6e')  {
                icon='';
            } else {
                icon = icon + '|' + unicode;
            }
            var id_icon_library = $('#marker_library_icon').val();
            var icon_type = parseInt($('#icon_type option:selected').attr('id'));
            switch (icon_type) {
                case 0:
                    icon = '';
                    id_icon_library = 0;
                    break;
                case 1:
                    id_icon_library = 0;
                    break;
                case 2:
                    icon = '';
                    break;
            }
            $.ajax({
                url: "ajax/assign_icon_to_markers.php",
                type: "POST",
                data: {
                    type: type,
                    id: id,
                    markers_size: markers_size,
                    markers_color_hex: color_hex,
                    markers_icon_color_hex: icon_color_hex,
                    markers_icon: icon,
                    markers_id_icon_library: id_icon_library,
                },
                async: false,
                success: function (json) {
                    $('.btn_assign_icons').removeClass('disabled');
                }
            });
        } else {
            $('.btn_assign_icons').removeClass('disavled');
            return false;
        }
    }

    window.save_category = function () {
        var complete = true;
        var name = $('#name').val();
        var id_category_parent = $('#id_category_parent option:selected').attr('id');
        var default_selected = $('#default_selected').is(':checked');
        if(default_selected) default_selected=1; else default_selected=0;
        if(name=='') {
            complete = false;
            $('#name').addClass("error-highlight");
        } else {
            $('#name').removeClass("error-highlight");
        }
        if(complete) {
            $('#save_btn .icon i').removeClass('far fa-circle').addClass('fas fa-circle-notch fa-spin');
            $('#save_btn').addClass("disabled");
            if(window.id_category!=0) {
                var markers_size = $('#markers_size').val();
                var color_hex = $('#color_hex').val();
                var icon_color_hex = $('#icon_color_hex').val();
                var icon = $('#marker_icon_input').val();
                var unicode = window.getComputedStyle(document.getElementById("marker_icon"), ':before').content.replace(/'|"/g, '').charCodeAt(0).toString(16);
                if(unicode=='6e')  {
                    icon='';
                } else {
                    icon = icon + '|' + unicode;
                }
                var id_icon_library = $('#marker_library_icon').val();
                var icon_type = parseInt($('#icon_type option:selected').attr('id'));
                switch (icon_type) {
                    case 0:
                        icon = '';
                        id_icon_library = 0;
                        break;
                    case 1:
                        id_icon_library = 0;
                        break;
                    case 2:
                        icon = '';
                        break;
                }
            } else {
                var markers_size = '';
                var color_hex = '';
                var icon_color_hex = '';
                var icon = '';
                var id_icon_library = '';
            }
            $.ajax({
                url: "ajax/save_category.php",
                type: "POST",
                data: {
                    id: window.id_category,
                    name: name,
                    id_category_parent: id_category_parent,
                    default_selected: default_selected,
                    markers_size: markers_size,
                    markers_color_hex: color_hex,
                    markers_icon_color_hex: icon_color_hex,
                    markers_icon: icon,
                    markers_id_icon_library: id_icon_library,
                },
                async: true,
                success: function (json) {
                    var rsp = JSON.parse(json);
                    if(rsp.status=="ok") {
                        if(window.id_category==0) {
                            window.category_need_save = false;
                            location.href='index.php?p=categories';
                        } else {
                            window.category_need_save = false;
                            $('#save_btn .icon i').removeClass('fas fa-circle-notch fa-spin').addClass('fas fa-check');
                            setTimeout(function () {
                                $('#save_btn .icon i').removeClass('fas fa-check').addClass('far fa-circle');
                                $('#save_btn').removeClass("disabled");
                            },1000);
                        }

                    } else {
                        $('#save_btn .icon i').removeClass('fas fa-circle-notch fa-spin').addClass('fas fa-times');
                        $('#save_btn').removeClass('btn-success').addClass('btn-danger');
                        setTimeout(function () {
                            $('#save_btn .icon i').removeClass('fas fa-times').addClass('far fa-circle');
                            $('#save_btn').removeClass('btn-danger').addClass('btn-success');
                            $('#save_btn').removeClass("disabled");
                        },1000);
                    }
                }
            });
        }
    }

    window.add_user = function () {
        var complete = true;
        var username = $('#username').val();
        var role = $('#role option:selected').attr('id');
        var password = $('#password').val();
        var password2 = $('#repeat_password').val();
        if(username=='') {
            complete = false;
            $('#username').addClass("error-highlight");
        } else {
            $('#username').removeClass("error-highlight");
        }
        if(password=='') {
            complete = false;
            $('#password').addClass("error-highlight");
        } else {
            $('#password').removeClass("error-highlight");
        }
        if(password2=='') {
            complete = false;
            $('#repeat_password').addClass("error-highlight");
        } else {
            $('#repeat_password').removeClass("error-highlight");
        }
        if((password!='') && (password2!='')) {
            if(password!=password2) {
                complete = false;
                $('#password').addClass("error-highlight");
                $('#repeat_password').addClass("error-highlight");
            } else {
                $('#password').removeClass("error-highlight");
                $('#repeat_password').removeClass("error-highlight");
            }
        }
        if(complete) {
            $('#modal_new_user button').addClass("disabled");
            $.ajax({
                url: "ajax/add_user.php",
                type: "POST",
                data: {
                    username: username,
                    password: password,
                    role: role
                },
                async: true,
                success: function (json) {
                    var rsp = JSON.parse(json);
                    if (rsp.status == "ok") {
                        $('#modal_new_user button').removeClass("disabled");
                        $('#modal_new_user').modal("hide");
                        location.reload();
                    } else {
                        $('#modal_new_user button').removeClass("disabled");
                    }
                }
            });
        }
    };

    window.get_user_stats = function (id_user_edit) {
        $.ajax({
            url: "ajax/get_user_stats.php",
            type: "POST",
            data: {
                id_user: id_user_edit
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                $('#num_maps').html(rsp.count_maps);
                $('#num_markers').html(rsp.count_markers);
            }
        });
    };

    window.change_password = function () {
        var complete = true;
        var password = $('#password').val();
        var password2 = $('#repeat_password').val();
        if(password=='') {
            complete = false;
            $('#password').addClass("error-highlight");
        } else {
            $('#password').removeClass("error-highlight");
        }
        if(password2=='') {
            complete = false;
            $('#repeat_password').addClass("error-highlight");
        } else {
            $('#repeat_password').removeClass("error-highlight");
        }
        if((password!='') && (password2!='')) {
            if(password!=password2) {
                complete = false;
                $('#password').addClass("error-highlight");
                $('#repeat_password').addClass("error-highlight");
            } else {
                $('#password').removeClass("error-highlight");
                $('#repeat_password').removeClass("error-highlight");
            }
        }
        if(complete) {
            $('#modal_change_password button').addClass("disabled");
            $.ajax({
                url: "ajax/change_password.php",
                type: "POST",
                data: {
                    id: window.id_user_edit,
                    password: password,
                },
                async: true,
                success: function (json) {
                    var rsp = JSON.parse(json);
                    if (rsp.status == "ok") {
                        window.user_need_save = false;
                        $('#modal_change_password button').removeClass("disabled");
                        $('#modal_change_password').modal("hide");
                        $('#password').val('');
                        $('#repeat_password').val('');
                    } else {
                        $('#modal_change_password button').removeClass("disabled");
                    }
                }
            });
        }
    };

    window.save_user = function(id_user_edit) {
        var complete = true;
        var username = $('#username').val();
        var role = $('#role option:selected').attr('id');
        var active = $('#active').is(':checked');
        if(active) active=1; else active=0;
        if(username=='') {
            complete = false;
            $('#username').addClass("error-highlight");
        } else {
            $('#username').removeClass("error-highlight");
        }
        if(complete) {
            $('#save_btn .icon i').removeClass('far fa-circle').addClass('fas fa-circle-notch fa-spin');
            $('#save_btn').addClass("disabled");
            $.ajax({
                url: "ajax/save_user.php",
                type: "POST",
                data: {
                    id: id_user_edit,
                    username: username,
                    role: role,
                    active: active,
                },
                async: true,
                success: function (json) {
                    var rsp = JSON.parse(json);
                    if(rsp.status=="ok") {
                        window.user_need_save = false;
                        $('#save_btn .icon i').removeClass('fas fa-circle-notch fa-spin').addClass('fas fa-check');
                        setTimeout(function () {
                            $('#save_btn .icon i').removeClass('fas fa-check').addClass('far fa-circle');
                            $('#save_btn').removeClass("disabled");
                        },1000);
                    } else {
                        $('#save_btn .icon i').removeClass('fas fa-circle-notch fa-spin').addClass('fas fa-times');
                        $('#save_btn').removeClass('btn-success').addClass('btn-danger');
                        setTimeout(function () {
                            $('#save_btn .icon i').removeClass('fas fa-times').addClass('far fa-circle');
                            $('#save_btn').removeClass('btn-danger').addClass('btn-success');
                            $('#save_btn').removeClass("disabled");
                        },1000);
                    }
                }
            });
        }
    }

    window.change_friendly_url = function(id_elem,id) {
        var friendly_url = $('#'+id_elem).val();
        if(friendly_url=='') {
            $('#'+id_elem).parent().find('.input-group-append').addClass('disabled');
        } else {
            $('#'+id_elem).parent().find('.input-group-append').removeClass('disabled');
        }
        var url = window.link_f+friendly_url;
        $.ajax({
            url: 'ajax/set_friendly_url.php',
            type: "POST",
            data: {
                id: id,
                friendly_url: friendly_url
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                if(rsp.status=='ok') {
                    $('#link_open').attr('href',url);
                    $('#link_copy').attr('data-clipboard-text',url);
                    $('#link_qr').attr('onclick','open_qr_code_modal(\''+url+'\')');
                    $('#'+id_elem).removeClass('error-highlight');
                    $('#'+id_elem).css('color','unset');
                } else {
                    $('#'+id_elem).addClass('error-highlight');
                    $('#'+id_elem).css('color','red');
                }
            }
        })
    }

    window.delete_image_meta = function (id) {
        window.image_meta = '';
        $('#div_delete_image_meta').hide();
        $('#div_upload_image_meta').show();
        if(window.image_meta_default=='') {
            $('.facebook-preview__image').addClass('d-none');
            $('.facebook-preview__image').attr('src','');
        } else {
            $('.facebook-preview__image').removeClass('d-none');
            $('.facebook-preview__image').attr('src','../viewer/content/'+window.image_meta_default);
        }
        save_metadata(id);
    }

    window.change_meta_title = function() {
        var meta_title = $('#meta_title').val();
        if(meta_title=='') {
            if(window.title_meta_default=='') {
                $('.facebook-preview__title').html('');
            } else {
                $('.facebook-preview__title').html(title_meta_default);
            }
        } else {
            $('.facebook-preview__title').html(meta_title);
        }
    }

    window.change_meta_description = function() {
        var meta_description = $('#meta_description').val();
        if(meta_description=='') {
            if(window.description_meta_default=='') {
                $('.facebook-preview__description').html('');
            } else {
                $('.facebook-preview__description').html(description_meta_default);
            }
        } else {
            $('.facebook-preview__description').html(meta_description);
        }
    }

    window.save_metadata = function(id) {
        var meta_title = $('#meta_title').val();
        var meta_description = $('#meta_description').val();
        $.ajax({
            url: 'ajax/save_metadata.php',
            type: "POST",
            data: {
                id: id,
                meta_title: meta_title,
                meta_description: meta_description,
                meta_image: window.image_meta
            },
            async: true,
            success: function (json) {}
        })
    }

    window.set_status_map = function(status) {
        $.ajax({
            url: "ajax/set_status_map.php",
            type: "POST",
            data: {
                id_map: window.id_map,
                status: status
            },
            async: true,
            success: function () {}
        });
    }

    window.open_qr_code_modal = function (link) {
        var qrcode_src = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data="+link;
        $('#modal_qrcode').modal('show');
        $('#modal_qrcode i').show();
        $('#modal_qrcode img').hide();
        $('#modal_qrcode img').on("load", function() {
            $('#modal_qrcode i').hide();
            $('#modal_qrcode img').show();
        }).attr("src", qrcode_src);
    }

    window.get_current_position = function () {
        if ("geolocation" in navigator){
            $('#btn_get_position').addClass('disabled');
            $('#btn_get_position').html(window.backend_labels.searching+' ...');
            navigator.geolocation.getCurrentPosition(show_location, show_error, {timeout:10000, enableHighAccuracy: true}); //position request
        }
    }

    function show_location(position){
        $('#latitude_edit').val(position.coords.latitude);
        $('#longitude_edit').val(position.coords.longitude);
        $('#btn_get_position').removeClass('disabled');
        $('#btn_get_position').html(window.backend_labels.get_gps_position);
        set_marker_position();
    }

    function show_error(error){
        $('#btn_get_position').removeClass('disabled');
        $('#btn_get_position').html(window.backend_labels.get_gps_position);
        switch(error.code) {
            case error.PERMISSION_DENIED:
                alert("Permission denied by user.");
                break;
            case error.POSITION_UNAVAILABLE:
                alert("Location position unavailable.");
                break;
            case error.TIMEOUT:
                alert("Request timeout.");
                break;
            case error.UNKNOWN_ERROR:
                alert("Unknown error.");
                break;
        }
    }

    window.get_icon_images = function(id_map) {
        $.ajax({
            url: "ajax/get_icon_images.php",
            type: "POST",
            data: {
                id_map: id_map
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                if(rsp.length>0) {
                    parse_icon_images(rsp);
                } else {
                    $('#list_images').html("<p>"+window.backend_labels.no_icon_msg+"</p>");
                }
            }
        });
    }

    function parse_icon_images(images_array) {
        var html = '';
        jQuery.each(images_array, function(index, image) {
            var id = image.id;
            var image = image.image;
            html += '<div data-id="'+id+'" style="position: relative" class="image_icon float-left mb-2 ml-1 mr-1"><div class="remove_image_icon"><i onclick="remove_image_icon('+id+');" style="font-size: 24px;color: white" class="fas fa-trash-alt"></i></div><img style="height: 100px;" src="../viewer/icons/'+image+'" /></div>';
        });
        $('#list_images').html(html).promise().done(function () {

        });
    }

    window.remove_image_icon = function(id) {
        var retVal = confirm(window.backend_labels.delete_msg);
        if( retVal == true ) {
            $.ajax({
                url: "ajax/delete_image_to_icon.php",
                type: "POST",
                data: {
                    id: id
                },
                async: false,
                success: function (json) {
                    get_icon_images(window.id_map);
                }
            });
        } else {
            return false;
        }
    }

    window.add_image_to_icon = function(id_map,image) {
        $.ajax({
            url: "ajax/add_image_to_icon.php",
            type: "POST",
            data: {
                id_map: id_map,
                image: image
            },
            async: true,
            success: function (json) {
                get_icon_images(id_map);
            }
        });
    }

    window.select_icon_library = function(id,image) {
        $('#marker_library_icon').val(id);
        $('#marker_library_icon_preview').attr('src','../viewer/icons/'+image);
        $('#marker_library_icon_preview').show();
        $('#modal_library_icons').modal('hide');
        $('#color_hex').parent().parent().addClass("disabled");
        $('#icon_color_hex').parent().parent().addClass("disabled");
        change_style_marker();
    }

    window.remove_icon_library = function() {
        $('#marker_library_icon').val(0);
        $('#marker_library_icon_preview').attr('src','');
        $('#marker_library_icon_preview').hide();
        $('#modal_library_icons').modal('hide');
        $('#color_hex').parent().parent().removeClass("disabled");
        $('#icon_color_hex').parent().parent().removeClass("disabled");
        $('#GetIconPicker').parent().removeClass("disabled");
    }

    window.set_password_map = function() {
        var password = $('#map_password').val();
        $('#btn_protect').addClass('disabled');
        $.ajax({
            url: "ajax/set_password_map.php",
            type: "POST",
            data: {
                id_map: window.id_map,
                password: password
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                $('#btn_protect').removeClass('disabled');
            }
        });
    }

    window.delete_review = function (id) {
        var answer = window.confirm(window.backend_labels.delete_msg);
        if (answer) {
            $.ajax({
                url: "ajax/delete_review.php",
                type: "POST",
                data: {
                    id: id
                },
                async: true,
                success: function (json) {
                    $('#reviews_table').DataTable().ajax.reload();
                }
            });
        }
    }

    window.get_statistics = function (elem) {
        $.ajax({
            url: "ajax/get_statistics.php",
            type: "POST",
            data: {
                id_map: window.id_map,
                elem: elem
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                switch (elem) {
                    case 'chart_visitor_map':
                        Highcharts.stockChart('chart_visitor_map', {
                            tooltip: {
                                xDateFormat: '%e %B %Y',
                                pointFormatter: function() {
                                    var point = this;
                                    return '<span style="color:' + point.color + '">\u25CF</span> <b>' + point.y.toFixed(0) + '</b>';
                                }
                            },
                            yAxis: {
                                opposite:false
                            },
                            chart: {
                                height: 400,
                            },
                            title: {
                                text: ''
                            },
                            subtitle: {
                                text: ''
                            },
                            rangeSelector: {
                                inputDateFormat: '%d/%m/%Y',
                                selected: 4,
                                buttons: [{
                                    type: 'day',
                                    count: 7,
                                    text: '1W',
                                },{
                                    type: 'month',
                                    count: 1,
                                    text: '1M',
                                }, {
                                    type: 'month',
                                    count: 3,
                                    text: '3M'
                                }, {
                                    type: 'month',
                                    count: 6,
                                    text: '6M'
                                }, {
                                    type: 'year',
                                    count: 1,
                                    text: '1A'
                                }, {
                                    type: 'all',
                                    text: 'ALL'
                                }]
                            },
                            plotOptions: {
                                series: {
                                    color: window.theme_color,
                                    dataGrouping: {
                                        approximation: "sum",
                                        units: [
                                            ['day', [1]],
                                            ['week', [1]],
                                            ['month', [1]],
                                        ],
                                        groupPixelWidth: 100
                                    }
                                }
                            },
                            series: [{
                                name: '',
                                data: rsp.data,
                                type: 'areaspline',
                                threshold: null,
                                tooltip: {
                                    valueDecimals: 0
                                }
                            }],
                            responsive: {
                                rules: [{
                                    condition: {
                                        maxWidth: 500
                                    },
                                    chartOptions: {
                                        chart: {
                                            height: 300
                                        },
                                        subtitle: {
                                            text: null
                                        },
                                        navigator: {
                                            enabled: false
                                        }
                                    }
                                }]
                            }
                        });
                        break;
                    case 'chart_marker_views':
                        var total_access = rsp.total_markers;
                        var html_markers = '';
                        jQuery.each(rsp.markers, function(index, marker) {
                            var name = marker.name;
                            var count = marker.access_count;
                            var perc = count / total_access * 100;
                            perc = Math.round(perc);
                            html_markers += '<h4 style="margin-bottom:1px;" class="small font-weight-bold">'+name+' <span class="float-right"><b>'+count+'</b>/'+total_access+'</span></h4>\n' +
                                '                <div class="progress mb-2">\n' +
                                '                    <div class="progress-bar bg-primary" role="progressbar" style="width: '+perc+'%" aria-valuenow="'+perc+'" aria-valuemin="0" aria-valuemax="100"></div>\n' +
                                '                </div>';
                        });
                        $('#chart_marker_views').html(html_markers);
                        break;
                }
            }
        });
    }

    window.delete_b_bg = function () {
        window.b_background_image = '';
        window.settings_need_save = true;
        $('#div_delete_bg').hide();
        $('#div_image_bg').hide();
        $('#div_upload_bg').show();
        $('#div_image_bg img').attr('src','');
    }

    window.delete_b_logo = function () {
        window.b_logo_image = '';
        window.settings_need_save = true;
        $('#div_delete_logo').hide();
        $('#div_image_logo').hide();
        $('#div_upload_logo').show();
        $('#div_image_logo img').attr('src','');
    }

    window.set_session_theme_color = function(theme_color) {
        if(theme_color!='') {
            $.ajax({
                url: "ajax/set_session_theme_color.php",
                type: "POST",
                data: {
                    theme_color: theme_color
                },
                async: true,
                success: function (json) {
                    var current_href = $('#css_theme').attr("href").split('?')[0];
                    $('#css_theme').attr("href", current_href + "?v=" + new Date().getMilliseconds());
                }
            });
        }
    }

    window.switch_language = function (lang) {
        $.ajax({
            url: "ajax/switch_language.php",
            type: "POST",
            data: {
                lang: lang
            },
            async: false,
            success: function () {
                location.reload();
            }
        });
    }

    window.save_settings = function (validate_mail) {
        var complete = true;
        var name = $('#name').val();
        var theme_color = $('#theme_color').val();
        var font_backend = $('#font_backend').val();
        var welcome_msg = window.welcome_msg_editor.root.innerHTML;
        var language = $('#language option:selected').attr('id');
        var language_domain = $('#language_domain option:selected').attr('id');
        var languages_enabled_t = $('#languages_enabled option');
        var languages_enabled = {};
        $(languages_enabled_t).each(function(index, elem){
            var id = $(this).attr('id').replace('ls_','');
            if($(this).is(':selected')) {
                languages_enabled[id]=1;
            } else {
                languages_enabled[id]=0;
            }
        });
        var languages_enabled_json = JSON.stringify(languages_enabled);
        var css_array = {};
        $(".editors_css").each(function() {
            var id = $(this).attr('id');
            css_array[id]=window.editors_css[id].getValue();
        });
        var css_array_json = JSON.stringify(css_array);
        var footer_link_1 = $('#footer_link_1').val();
        var footer_link_2 = $('#footer_link_2').val();
        var footer_link_3 = $('#footer_link_3').val();
        var footer_value_1 = window.footer_value_1.root.innerHTML;
        var footer_value_2 = window.footer_value_2.root.innerHTML;
        var footer_value_3 = window.footer_value_3.root.innerHTML;
        if(name=='') {
            complete = false;
            $('#name').addClass("error-highlight");
        } else {
            $('#name').removeClass("error-highlight");
        }
        if(complete) {
            $('#save_btn .icon i').removeClass('far fa-circle').addClass('fas fa-circle-notch fa-spin');
            $('#save_btn').addClass("disabled");
            $.ajax({
                url: "ajax/save_settings.php",
                type: "POST",
                data: {
                    name: name,
                    theme_color: theme_color,
                    font_backend: font_backend,
                    welcome_msg: welcome_msg,
                    logo: window.b_logo_image,
                    background: window.b_background_image,
                    background_reg: window.b_background_reg_image,
                    language: language,
                    language_domain: language_domain,
                    languages_enabled: languages_enabled_json,
                    css_array: css_array_json,
                    footer_link_1: footer_link_1,
                    footer_link_2: footer_link_2,
                    footer_link_3: footer_link_3,
                    footer_value_1: footer_value_1,
                    footer_value_2: footer_value_2,
                    footer_value_3: footer_value_3
                },
                async: true,
                success: function (json) {
                    var rsp = JSON.parse(json);
                    if (rsp.status == "ok") {
                        window.settings_need_save = false;
                        $('#save_btn .icon i').removeClass('fas fa-circle-notch fa-spin').addClass('fas fa-check');
                        setTimeout(function () {
                            $('#save_btn .icon i').removeClass('fas fa-check').addClass('far fa-circle');
                            $('#save_btn').removeClass("disabled");
                        }, 1000);
                        if(window.current_language!=language) {
                            location.reload();
                        }
                    } else {
                        $('#save_btn .icon i').removeClass('fas fa-circle-notch fa-spin').addClass('fas fa-times');
                        $('#save_btn').removeClass('btn-success').addClass('btn-danger');
                        setTimeout(function () {
                            $('#save_btn .icon i').removeClass('fas fa-times').addClass('far fa-circle');
                            $('#save_btn').removeClass('btn-danger').addClass('btn-success');
                            $('#save_btn').removeClass("disabled");
                        }, 1000);
                    }
                }
            });
        }
    }

    window.save_profile = function(id_user_edit,complete_profile) {
        var complete = true;
        var username = $('#username').val();
        var language = $('#language option:selected').attr('id');
        if(username=='') {
            complete = false;
            $('#username').addClass("error-highlight");
        } else {
            $('#username').removeClass("error-highlight");
        }
        if(complete) {
            $('#save_btn .icon i').removeClass('far fa-circle').addClass('fas fa-circle-notch fa-spin');
            $('#save_btn').addClass("disabled");
            $('#btn_save_continue_profile').addClass('disabled');
            $.ajax({
                url: "ajax/save_profile.php",
                type: "POST",
                data: {
                    id: id_user_edit,
                    username: username,
                    language: language
                },
                async: true,
                success: function (json) {
                    var rsp = JSON.parse(json);
                    if(rsp.status=="ok") {
                        window.user_need_save = false;
                        $('#save_btn .icon i').removeClass('fas fa-circle-notch fa-spin').addClass('fas fa-check');
                        setTimeout(function () {
                            $('#save_btn .icon i').removeClass('fas fa-check').addClass('far fa-circle');
                            $('#save_btn').removeClass("disabled");
                            if(rsp.reload_page==1) {
                                location.href = 'index.php?p=edit_profile';
                            }
                        },200);
                    } else {
                        $('#save_btn .icon i').removeClass('fas fa-circle-notch fa-spin').addClass('fas fa-times');
                        $('#save_btn').removeClass('btn-success').addClass('btn-danger');
                        setTimeout(function () {
                            $('#save_btn .icon i').removeClass('fas fa-times').addClass('far fa-circle');
                            $('#save_btn').removeClass('btn-danger').addClass('btn-success');
                            $('#save_btn').removeClass("disabled");
                        }, 1000);
                    }
                }
            });
        }
    }

    window.change_editor_css = function () {
        var id = $('#css_name option:selected').attr('id').replace('css_','');
        $('.editors_css').hide();
        $('#custom_b').show();
        $('#'+id).show();
    }

    window.change_markers_size = function () {
        var markers_size = $('#markers_size').val();
        $('#markers_size_value').html(markers_size);
    }

    window.change_marker_size = function () {
        var marker_size = $('#marker_size').val();
        if(window.default_marker_size==marker_size) {
            $('#marker_size_value').html(marker_size+' default');
        } else {
            $('#marker_size_value').html(marker_size);
        }
        change_style_marker();
    }

    window.add_markers_connections = function () {
        $('#modal_add_markers_connections button').addClass("disabled");
        var marker_source = $('#marker_source option:selected').attr('id');
        var marker_dest = $('#marker_dest option:selected').attr('id');
        var color = $('#color').val();
        var width = $('#width').val();
        var title = $('#title_mc').val();
        $.ajax({
            url: "ajax/add_markers_connections.php",
            type: "POST",
            data: {
                marker_source: marker_source,
                marker_dest: marker_dest,
                color: color,
                width: width,
                title: title,
                description: window.editor_html_description.getValue()
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                if (rsp.status == "ok") {
                    $('#modal_add_markers_connections button').removeClass("disabled");
                    $('#modal_add_markers_connections').modal("hide");
                    $('#markers_connections_table').DataTable().ajax.reload();
                    window.editor_html_description.getSession().setValue('');
                } else {
                    $('#modal_add_markers_connections button').removeClass("disabled");
                }
            }
        });
    };

    window.delete_markers_connections = function (id) {
        var answer = window.confirm(window.backend_labels.delete_msg);
        if (answer) {
            $.ajax({
                url: "ajax/delete_markers_connections.php",
                type: "POST",
                data: {
                    id: id
                },
                async: true,
                success: function (json) {
                    $('#modal_edit_markers_connections button').removeClass("disabled");
                    $('#modal_edit_markers_connections').modal("hide");
                    $('#markers_connections_table').DataTable().ajax.reload();
                }
            });
        }
    }

    window.save_markers_connections = function (id) {
        $('#modal_edit_markers_connections button').addClass("disabled");
        var marker_source = $('#marker_source_edit option:selected').attr('id');
        var marker_dest = $('#marker_dest_edit option:selected').attr('id');
        var color = $('#color_edit').val();
        var width = $('#width_edit').val();
        var title = $('#title_mc_edit').val();
        $.ajax({
            url: "ajax/save_markers_connections.php",
            type: "POST",
            data: {
                id: id,
                marker_source: marker_source,
                marker_dest: marker_dest,
                color: color,
                width: width,
                title: title,
                description: window.editor_html_description_edit.getValue()
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                if (rsp.status == "ok") {
                    $('#modal_edit_markers_connections button').removeClass("disabled");
                    $('#modal_edit_markers_connections').modal("hide");
                    $('#markers_connections_table').DataTable().ajax.reload();
                } else {
                    $('#modal_edit_markers_connections button').removeClass("disabled");
                }
            }
        });
    };

    window.add_markers_story = function () {
        $('#modal_add_markers_story button').addClass("disabled");
        var id_marker = $('#marker_story_add option:selected').attr('id');
        var zoom = $('#zoom_story_add option:selected').attr('id');
        $.ajax({
            url: "ajax/add_markers_story.php",
            type: "POST",
            data: {
                id_map: window.id_map,
                id_marker: id_marker,
                zoom: zoom,
                description: window.editor_html_description.getValue()
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                if (rsp.status == "ok") {
                    $('#modal_add_markers_story button').removeClass("disabled");
                    $('#modal_add_markers_story').modal("hide");
                    $('#markers_story_table').DataTable().ajax.reload();
                    window.editor_html_description.getSession().setValue('');
                    get_markers_story_add();

                } else {
                    $('#modal_add_markers_story button').removeClass("disabled");
                }
            }
        });
    };

    window.delete_markers_story = function (id) {
        var answer = window.confirm(window.backend_labels.delete_msg);
        if (answer) {
            $.ajax({
                url: "ajax/delete_marker_story.php",
                type: "POST",
                data: {
                    id_map: window.id_map,
                    id_marker: id
                },
                async: true,
                success: function (json) {
                    $('#modal_edit_markers_story button').removeClass("disabled");
                    $('#modal_edit_markers_story').modal("hide");
                    $('#markers_story_table').DataTable().ajax.reload();
                    get_markers_story_add();
                }
            });
        }
    };

    window.save_markers_story = function (id) {
        $('#modal_edit_markers_story button').addClass("disabled");
        var zoom = $('#zoom_story_edit option:selected').attr('id');
        $.ajax({
            url: "ajax/save_markers_story.php",
            type: "POST",
            data: {
                id_map: window.id_map,
                id_marker: id,
                zoom: zoom,
                description: window.editor_html_description_edit.getValue()
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                if (rsp.status == "ok") {
                    $('#modal_edit_markers_story button').removeClass("disabled");
                    $('#modal_edit_markers_story').modal("hide");
                    $('#markers_story_table').DataTable().ajax.reload();
                } else {
                    $('#modal_edit_markers_story button').removeClass("disabled");
                }
            }
        });
    };

    window.change_icon_type = function () {
        var icon_type = parseInt($('#icon_type option:selected').attr('id'));
        switch (icon_type) {
            case 0:
                $('#icon_type_1').addClass('d-none');
                $('#icon_type_2').addClass('d-none');
                $('#color_hex').parent().parent().removeClass("disabled");
                $('#icon_color_hex').parent().parent().removeClass("disabled");
                break;
            case 1:
                $('#icon_type_1').removeClass('d-none');
                $('#icon_type_2').addClass('d-none');
                $('#color_hex').parent().parent().removeClass("disabled");
                $('#icon_color_hex').parent().parent().removeClass("disabled");
                break;
            case 2:
                $('#icon_type_1').addClass('d-none');
                $('#icon_type_2').removeClass('d-none');
                $('#color_hex').parent().parent().addClass("disabled");
                $('#icon_color_hex').parent().parent().addClass("disabled");
                break;
        }
        change_style_marker();
    }

    window.assign_map_editor = function (id,checked) {
        $.ajax({
            url: "ajax/assign_map_editor.php",
            type: "POST",
            data: {
                id_map: id,
                id_user: window.id_user_edit,
                checked: checked
            },
            async: false,
            success: function() {
                if(checked==1) {
                    $('#assign_map_table').DataTable().ajax.reload();
                }
            }
        });
    }

    window.set_permission_map_editor = function (id,field,checked) {
        $.ajax({
            url: "ajax/set_permission_map_editor.php",
            type: "POST",
            data: {
                id_map: id,
                id_user: window.id_user_edit,
                field: field,
                checked: checked
            },
            async: true,
            success: function() {}
        });
    }

    window.change_map = function() {
        var id = $('#map_selector option:selected').attr('id');
        $.ajax({
            url: "ajax/set_session_m.php",
            type: "POST",
            data: {
                id_map: id
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                if (rsp.status == "ok") {
                    var url = window.location.href;
                    location.href = url;
                }
            }
        });
    }

    window.change_order = function(id_marker,direction) {
        $.ajax({
            url: "ajax/change_order.php",
            type: "POST",
            data: {
                id_map: window.id_map,
                id_marker: id_marker,
                direction: direction
            },
            async: false,
            success: function (json) {
                $('#markers_table').DataTable().ajax.reload();
            }
        });
    }

    window.change_order_story = function(id_marker,direction) {
        $.ajax({
            url: "ajax/change_order_story.php",
            type: "POST",
            data: {
                id_map: window.id_map,
                id_marker: id_marker,
                direction: direction
            },
            async: false,
            success: function (json) {
                $('#markers_story_table').DataTable().ajax.reload();
            }
        });
    }

    window.get_markers_story_add = function () {
        $('#btn_add_markers_story').addClass('disabled');
        $.ajax({
            url: "ajax/get_markers_story_add.php",
            type: "POST",
            data: {
                id_map: window.id_map
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                var markers = rsp.markers;
                var options = '';
                if(markers.length>0) {
                    jQuery.each(markers, function(index, marker) {
                        options += '<option id="'+marker.id+'">'+marker.name+'</option>';
                    });
                    $('#btn_add_markers_story').removeClass('disabled');
                }
                $('#marker_story_add').html(options).promise().done(function () {
                    $('#marker_story_add').selectpicker('refresh');
                    $('#marker_story_add').selectpicker('render');
                });
            }
        });
    }

    window.set_show_in_first_page = function(show_in_first_page) {
        $.ajax({
            url: "ajax/set_show_in_first_page.php",
            type: "POST",
            data: {
                id_map: window.id_map,
                show_in_first_page: show_in_first_page
            },
            async: false,
            success: function (json) {}
        });
    }

    window.bulk_select = function () {
        window.bulk_select_mode = true;
        $('.marker_add_bar').hide();
        $('.marker_bulk_select_bar').show();
        $('#markers_table').DataTable().page.len(-1).draw();
    }

    window.exit_bulk_select = function () {
        window.bulk_select_mode = false;
        $('.marker_add_bar').show();
        $('.marker_bulk_select_bar').hide();
        $('#markers_table').DataTable().page.len(100).draw();
    }

    window.exec_bulk_delete = function () {
        var ids = '';
        $('.check_bulk_marker').each(function() {
            if ($(this).is(':checked')) {
                ids += $(this).attr('id').replace("marker_","")+",";
            }
        });
        var lastChar = ids.slice(-1);
        if (lastChar == ',') {
            ids = ids.slice(0, -1);
        }
        $.ajax({
            url: "ajax/bulk_delete_markers.php",
            type: "POST",
            data: {
                id_map: window.id_map,
                ids: ids
            },
            async: false,
            success: function (json) {
                window.bulk_select_mode = false;
                $('.marker_add_bar').show();
                $('.marker_bulk_select_bar').hide();
                $('#markers_table').DataTable().page.len(100);
                $('#markers_table').DataTable().ajax.reload();
            }
        });
    }

    window.exec_bulk_activate = function () {
        var ids = '';
        $('.check_bulk_marker').each(function() {
            if ($(this).is(':checked')) {
                ids += $(this).attr('id').replace("marker_","")+",";
            }
        });
        var lastChar = ids.slice(-1);
        if (lastChar == ',') {
            ids = ids.slice(0, -1);
        }
        $.ajax({
            url: "ajax/bulk_activate_markers.php",
            type: "POST",
            data: {
                id_map: window.id_map,
                ids: ids
            },
            async: false,
            success: function (json) {
                $('#markers_table').DataTable().page.len(100);
                $('#markers_table').DataTable().ajax.reload();
            }
        });
    }

    window.exec_bulk_disable = function () {
        var ids = '';
        $('.check_bulk_marker').each(function() {
            if ($(this).is(':checked')) {
                ids += $(this).attr('id').replace("marker_","")+",";
            }
        });
        var lastChar = ids.slice(-1);
        if (lastChar == ',') {
            ids = ids.slice(0, -1);
        }
        $.ajax({
            url: "ajax/bulk_disable_markers.php",
            type: "POST",
            data: {
                id_map: window.id_map,
                ids: ids
            },
            async: false,
            success: function (json) {
                $('#markers_table').DataTable().page.len(100);
                $('#markers_table').DataTable().ajax.reload();
            }
        });
    }

    window.exec_bulk_validate = function () {
        var ids = '';
        $('.check_bulk_marker').each(function() {
            if ($(this).is(':checked')) {
                ids += $(this).attr('id').replace("marker_","")+",";
            }
        });
        var lastChar = ids.slice(-1);
        if (lastChar == ',') {
            ids = ids.slice(0, -1);
        }
        $.ajax({
            url: "ajax/bulk_validate_markers.php",
            type: "POST",
            data: {
                id_map: window.id_map,
                ids: ids
            },
            async: false,
            success: function (json) {
                $('#markers_table').DataTable().page.len(100);
                $('#markers_table').DataTable().ajax.reload();
            }
        });
    }

    window.exec_bulk_add_featured = function () {
        var ids = '';
        $('.check_bulk_marker').each(function() {
            if ($(this).is(':checked')) {
                ids += $(this).attr('id').replace("marker_","")+",";
            }
        });
        var lastChar = ids.slice(-1);
        if (lastChar == ',') {
            ids = ids.slice(0, -1);
        }
        $.ajax({
            url: "ajax/bulk_add_featured_markers.php",
            type: "POST",
            data: {
                id_map: window.id_map,
                ids: ids
            },
            async: false,
            success: function (json) {
                $('#markers_table').DataTable().page.len(100);
                $('#markers_table').DataTable().ajax.reload();
            }
        });
    }

    window.exec_bulk_remove_featured = function () {
        var ids = '';
        $('.check_bulk_marker').each(function() {
            if ($(this).is(':checked')) {
                ids += $(this).attr('id').replace("marker_","")+",";
            }
        });
        var lastChar = ids.slice(-1);
        if (lastChar == ',') {
            ids = ids.slice(0, -1);
        }
        $.ajax({
            url: "ajax/bulk_remove_featured_markers.php",
            type: "POST",
            data: {
                id_map: window.id_map,
                ids: ids
            },
            async: false,
            success: function (json) {
                $('#markers_table').DataTable().page.len(100);
                $('#markers_table').DataTable().ajax.reload();
            }
        });
    }

    window.select_all_markers = function () {
        $('.check_bulk_marker').prop('checked',true);
        var num = 0;
        $('.check_bulk_marker').each(function() {
            if ($(this).is(':checked')) {
                num++;
            }
        });
        $('#markers_selected').html(num);
        if(num!=0) {
            $('.btn_bulk_action').removeClass('disabled');
        } else {
            $('.btn_bulk_action').addClass('disabled');
        }
    }

    window.deselect_all_markers = function () {
        $('.check_bulk_marker').prop('checked',false);
        var num = 0;
        $('.check_bulk_marker').each(function() {
            if ($(this).is(':checked')) {
                num++;
            }
        });
        $('#markers_selected').html(num);
        if(num!=0) {
            $('.btn_bulk_action').removeClass('disabled');
        } else {
            $('.btn_bulk_action').addClass('disabled');
        }
    }

    window.save_draw_geometries = function(draw_geometries) {
        $.ajax({
            url: "ajax/save_draw_geometries.php",
            type: "POST",
            data: {
                id_map: window.id_map,
                draw_geometries: draw_geometries
            },
            async: false,
            success: function (json) {}
        });
    }

    window.open_modal_content = function(target,type) {
        switch(type) {
            case 'embed_link':
                $('#content_embed_link').val('');
                $('#modal_content_embed_link').modal('show');
                $('#modal_content_embed_link .btn-success').attr('onclick','insert_content_embed_link();');
                break;
            case 'link2':
                $('#content_link2').val('');
                $('#modal_content_link2').modal('show');
                $('#modal_content_link2 .btn-success').attr('onclick','insert_content_link2();');
                break;
            case 'link':
                $('#content_link').val('');
                $('#content_link_name').val('');
                $('#modal_content_link').modal('show');
                $('#modal_content_link .btn-success').attr('onclick','insert_content_link(\''+target+'\');');
                break;
            case 'text':
                window.content_text_editor.root.innerHTML = '';
                $('#modal_content_text').modal('show');
                $('#modal_content_text .btn-success').attr('onclick','insert_content_text(\''+target+'\');');
                break;
            case 'download':
                $('#content_download_link').val('');
                $('#content_download_name').val('');
                $('#modal_content_download').modal('show');
                $('#modal_content_download .btn-success').attr('onclick','insert_content_download(\''+target+'\');');
                break;
        }
    }

    window.insert_content_link = function(target) {
        var link = $('#content_link').val();
        var name = $('#content_link_name').val();
        if(link!='') {
            if(name=='') name = link;
            var content = '<a target="_blank" href="'+link+'">'+name+'</a>';
            window.editor_html_extra_fields[target].setValue(content,-1);
            $('#modal_content_link').modal('hide');
        }
    }

    window.insert_content_link2 = function() {
        var link = $('#content_link2').val();
        if(link!='') {
            window.editor_html_extra_button_1.setValue(link,-1);
            $('#modal_content_link2').modal('hide');
        }
    }

    window.insert_content_text = function(target) {
        var content = window.content_text_editor.root.innerHTML;
        if(content!='<p><br></p>') {
            if(target=='extra_button_value_1') {
                window.editor_html_extra_button_1.setValue(content,-1);
            } else {
                window.editor_html_extra_fields[target].setValue(content,-1);
            }
            $('#modal_content_text').modal('hide');
        }
    }

    window.insert_content_embed_link = function() {
        var link = $('#content_embed_link').val();
        if(link!='') {
            var content = '<iframe allowfullscreen allow="*" width="100%" height="100%" frameborder="0" scrolling="yes" marginheight="0" marginwidth="0" src="'+link+'"></iframe>';
            window.editor_html_extra_button_1.setValue(content,-1);
            $('#modal_content_embed_link').modal('hide');
        }
    }

    window.insert_content_download = function(target) {
        var link = $('#content_download_link').val();
        var name = $('#content_download_name').val();
        if(link!='' && name !='') {
            var content = '<a download href="'+link+'">'+name+'</a>';
            window.editor_html_extra_fields[target].setValue(content,-1);
            $('#modal_content_download').modal('hide');
        }
    }

    window.change_open_sheet_marker = function() {
        var open_sheet = $('#open_sheet').is(':checked');
        if(open_sheet) {
            $('#sheet_details_settings').removeClass('disabled');
        } else {
            $('#sheet_details_settings').addClass('disabled');
        }
    }

    window.change_popup_marker = function() {
        var view_popup = $('#view_popup').is(':checked');
        if(view_popup) {
            $('#popup_settings').removeClass('disabled');
        } else {
            $('#popup_settings').addClass('disabled');
        }
    }

})(jQuery);