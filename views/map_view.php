<?php 
/**
 * Google Maps Draw Module
 * @package drawonmaps
 * 
 * File: map_view.php
 * Description: View of single map
 */

	
if (isset($vars['map']))
    $map = $vars['map'];

$map_objects = $vars['map_objects'];

// get availble map objects for using
$available_objects = SiteConf::$MAP_OBJECTS_AVAILABLE;	

$map_markers_exists = false;
$map_polylines_exists = false;
$map_polygons_exists = false;
$map_rectangles_exists = false;
$map_circles_exists = false;	
$map_routes_exists = false;	

$marker_groups = array();
?>

<script>
    $(document).ready(function(){
        var objectsBounds = new google.maps.LatLngBounds();
    <?php
        foreach ($map_objects as $obj)	{
    ?>
            var tmp_coords = [];
            var tmp_icons = [];
            var coords = "<?php echo $obj["coords"]; ?>";
    <?php			
            if ($obj["object_id"] == 1 && is_object_enabled('marker', $available_objects))  {  // load markers if exist and are enable
                $map_markers_exists = true;
                $pieces = explode("/", $obj["marker_icon"]);

                // check for subdirectories, get these who have fileafter 3rd element
                if (isset($pieces[3]) && !in_array($pieces[2], $marker_groups)) { 
                    array_push($marker_groups, $pieces[2]);
    ?>
                    var markers_<?php echo $pieces[2]; ?> = [];
    <?php
                }
    ?>
                tmp_coords = getDataFromArray(coords);					
                newmarker = loadMarker(tmp_coords[0], tmp_coords[1], "<?php echo $obj["id"]; ?>", "<?php echo $obj["title"]; ?>", "<?php echo $obj["marker_icon"]; ?>", false);
                var myLatlng = new google.maps.LatLng(tmp_coords[0],tmp_coords[1]);
                objectsBounds.extend(myLatlng);
    <?php
                // push marker to group array
                if (isset($pieces[3])) { 
    ?>
                    markers_<?php echo $pieces[2]; ?>.push(newmarker);
    <?php
                }
            }
            else if ($obj["object_id"] == 2 && is_object_enabled('line', $available_objects))  {  // load polylines if exist and are enable
                $map_polylines_exists = true;
    ?>
                tmp_coords = getPathFromCoordsArray(coords);
                var getbounds = loadPolyline(tmp_coords, "<?php echo $obj["title"]; ?>");
                objectsBounds.union(getbounds);

    <?php
            }
            else if ($obj["object_id"] == 3 && is_object_enabled('polygon', $available_objects))  {  // load polygons if exist and are enable
                $map_polygons_exists = true;
    ?>
                tmp_coords = getPathFromCoordsArray(coords);
                var getbounds = loadPolygon(tmp_coords, "<?php echo $obj["title"]; ?>");
                objectsBounds.union(getbounds);
    <?php
            }			
            else if ($obj["object_id"] == 4 && is_object_enabled('rectangle', $available_objects))  {  // load rectangles if exist and are enable
                $map_rectangles_exists = true;
    ?>
                tmp_coords = getDataFromArray(coords);
                path_rect = [];
                path_rect.push(addCoordsToArray(tmp_coords[0], tmp_coords[1]));
                path_rect.push(addCoordsToArray(tmp_coords[2], tmp_coords[3]));					
                var getbounds = loadRectangle(path_rect, "<?php echo $obj["title"]; ?>");
                objectsBounds.union(getbounds);
    <?php
            }
            else if ($obj["object_id"] == 5 && is_object_enabled('circle', $available_objects))  {  // load circles if exist and are enable
                $map_circles_exists = true;
    ?>
                tmp_coords = getDataFromArray(coords);
                var getbounds = loadCircle(tmp_coords[0], tmp_coords[1], parseInt(tmp_coords[2]), "<?php echo $obj["title"]; ?>");
                objectsBounds.union(getbounds);
    <?php
            }	
            else if ($obj["object_id"] == 6)  {  // load routes
                $map_routes_exists = true;
        ?>
                tmp_coords = coords.split(",");
                var icons = "<?php echo $obj["marker_icon"]; ?>";
                tmp_icons = icons.split(",");
                var getbounds = loadRoute(tmp_coords[0], tmp_coords[1], tmp_coords[2], tmp_coords[3], "<?php echo $obj["title"]; ?>", "<?php echo $obj["id"]; ?>", tmp_icons[0], tmp_icons[1]);
                var myLatlng = new google.maps.LatLng(tmp_coords[0],tmp_coords[1]);
                objectsBounds.extend(myLatlng);
        <?php
            }              
        }	

        // set map visible with all shapes only in global map
        if (empty($map)) {
    ?>
            map.fitBounds(objectsBounds);
    <?php } ?>

    <?php
            foreach($marker_groups as $group) {
?>
                $('#chbx_<?php echo $group; ?>').click(function() {
                    if ($(this).is(':checked')) {
                        showOverlays(markers_<?php echo $group; ?>);
                        $("#chbx_markers").prop('checked', true);
                    }
                    else {
                        clearOverlays(markers_<?php echo $group; ?>);
                    }
                });	
    <?php		
            }
    ?>

                $('#chbx_markers').click(function() {
                    if ($(this).is(':checked')) {
                        showOverlays(map_markers);
    <?php 
                    foreach($marker_groups as $group) { ?>
                        $("#chbx_<?php echo $group; ?>").prop('checked', true);					
    <?php } ?>					
                    }
                    else {
                        clearOverlays(map_markers);
    <?php 
                    foreach($marker_groups as $group) { ?>
                        $("#chbx_<?php echo $group; ?>").prop('checked', false);					
    <?php } ?>	
                    }
                });	
    });
</script>

<?php 
    // initialize index table, if enabled
    $indextable = '';
    if (SiteConf::$ENABLE_INDEX_TABLE) {
        if ($map_markers_exists)
            $indextable .= '<div class="checkbox"><label><input type="checkbox" name="chbx_markers" id="chbx_markers" checked="checked">'.$lang_msg['MAP_VIEW_MARKERS'].'</label></div>';	
        if ($map_polylines_exists)
            $indextable .= '<div class="checkbox"><label><input type="checkbox" name="chbx_polylines" id="chbx_polylines" checked="checked">'.$lang_msg['MAP_VIEW_LINE'].'</label></div>';	
        if ($map_polygons_exists)
            $indextable .= '<div class="checkbox"><label><input type="checkbox" name="chbx_polygons" id="chbx_polygons" checked="checked">'.$lang_msg['MAP_VIEW_POLYGON'].'</label></div>';	
        if ($map_rectangles_exists)
            $indextable .= '<div class="checkbox"><label><input type="checkbox" name="chbx_rectangles" id="chbx_rectangles" checked="checked">'.$lang_msg['MAP_VIEW_RECTANGLE'].'</label></div>';	
        if ($map_circles_exists)
            $indextable .= '<div class="checkbox"><label><input type="checkbox" name="chbx_circles" id="chbx_circles" checked="checked">'.$lang_msg['MAP_VIEW_CIRCLE'].'</label></div>';	
    }

    $index_markers_table = '';
    foreach($marker_groups as $group) {
        $index_markers_table .= '<div class="checkbox"><label><input type="checkbox" name="chbx_'.$group.'" id="chbx_'.$group.'" checked="checked">'.$group.'</label></div>';	
    }
			
    // do not show all HTML if only map display option is asked
    if (!$vars['map_o']) 
    { 
?>
    <div class="container">
        <h3 class="doc_title"><?php echo $vars['page_title']; ?></h3>
        <!-- Main component for a primary marketing message or call to action -->
        <div class="jumbotron">
            <div class="row">
                <div class="col-lg-12">
                    <?php if (!empty($map)) { /* Do not display title and description on global map as we don't have */ ?>
                        <h4><?php echo $map->title; ?></h4>
                        <p><?php echo $map->description; ?></p>
                    <?php } ?>		
                </div>
                <div class="col-lg-12 index_table"><?php echo $indextable; ?></div>
                <div class="col-lg-3">
                    <div class="map_view_buttons">
                        <?php echo html_map_options((empty($map)?0:$map->id), $lang_msg, (empty($map)?true:false)); ?>
                    </div>
                    <div class="map_view_markers_groups">
                        <?php echo $index_markers_table; ?>
                    </div>					

                </div>
                <div class="col-lg-12">
                    <div class="popin">
                        <div id="map"></div>
                    </div>
                </div>
            </div>			
        </div>
    </div> <!-- /container -->
<?php 
    } 
    else {
?>
        <div id="map"></div>
<?php } ?>
