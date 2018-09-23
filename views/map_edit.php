<?php 
/**
 * Google Maps Draw Module
 * @package drawonmaps
 * 
 * File: map_edit.php
 * Description: Edit map page
 */

$map = $vars['map'];

$map_markers_exists = false;
$map_polylines_exists = false;
$map_polygons_exists = false;
$map_rectangles_exists = false;
$map_circles_exists = false;	

$map_description = $map->description;
if ($map_description) {
    $map_description = str_replace('<br />','',$map_description);
}

// initialize objects array
$objects_id_arr = array();
?>

<script> 
    $(document).ready(function()	{
        //var objectsBounds = new google.maps.LatLngBounds();
        <?php
            foreach ($vars['map_objects'] as $obj)	{
                array_push($objects_id_arr, $obj["id"]);
        ?>
                var tmp_coords = [];
                var coords = "<?php echo $obj["coords"]; ?>";
        <?php			
                if ($obj["object_id"] == 1)  {  // load markers
        ?>
                    tmp_coords = getDataFromArray(coords);					
                    loadMarker(tmp_coords[0], tmp_coords[1], "<?php echo $obj["id"]; ?>", "<?php echo $obj["title"]; ?>", "<?php echo $obj["marker_icon"]; ?>", true);
                    var myLatlng = new google.maps.LatLng(tmp_coords[0],tmp_coords[1]);
        <?php
                }
                else if ($obj["object_id"] == 2)  {  // load polylines 
        ?>
                    tmp_coords = getPathFromCoordsArray(coords);
                    var getbounds = loadPolyline(tmp_coords, "<?php echo $obj["title"]; ?>", "<?php echo $obj["id"]; ?>");

        <?php
                }
                else if ($obj["object_id"] == 3)  {  // load polygons
        ?>
                    tmp_coords = getPathFromCoordsArray(coords);
                    var getbounds = loadPolygon(tmp_coords, "<?php echo $obj["title"]; ?>", "<?php echo $obj["id"]; ?>");
        <?php
                }			
                else if ($obj["object_id"] == 4)  {  // load rectangles
        ?>
                    tmp_coords = getDataFromArray(coords);
                    path_rect = [];
                    path_rect.push(addCoordsToArray(tmp_coords[0], tmp_coords[1]));
                    path_rect.push(addCoordsToArray(tmp_coords[2], tmp_coords[3]));					
                    var getbounds = loadRectangle(path_rect, "<?php echo $obj["title"]; ?>", "<?php echo $obj["id"]; ?>");
        <?php
                }
                else if ($obj["object_id"] == 5)  {  // load circles
        ?>
                    tmp_coords = getDataFromArray(coords);
                    var getbounds = loadCircle(tmp_coords[0], tmp_coords[1], parseInt(tmp_coords[2]), "<?php echo $obj["title"]; ?>", "<?php echo $obj["id"]; ?>");
        <?php
                }
                else if ($obj["object_id"] == 6)  {  // load routes
            ?>
                    tmp_coords = coords.split(",");          
                    var getbounds = loadRoute(tmp_coords[0], tmp_coords[1], tmp_coords[2], tmp_coords[3], "<?php echo $obj["title"]; ?>", "<?php echo $obj["id"]; ?>");
        <?php
                }                
            }
            
            $objects_id_str = implode(",", $objects_id_arr);
        ?>
    });
</script>

<div class="container">
    <h3 class="doc_title"><?php echo $vars['page_title']; ?></h3>
    <!-- Main component for a primary marketing message or call to action -->
    <div class="jumbotron">
        <div class="row">
            <div class="col-lg-3">
                <?php include dirname(__FILE__) . '/html_draw_cpanel.php';?>
            </div>	
            <div class="col-lg-9">
                <div class="popin">
                    <div id="map"></div>
                </div>

                <div id="map_form">
                    <form  method="post" action="index.php?p=map_save" role="form" data-toggle="validator">
                        <div class="row" id="allmarks"></div>  
                        <div class="row" id="map_info">

                            <div class="form-group">
                                <label for="map_title"><?php echo $lang_msg['MAP_CREATE_FORM_TITLE']; ?></label>
                                <input type="text" class="form-control" id="map_title" name="map_title" value="<?php echo $map->title; ?>" required>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <label for="map_description"><?php echo $lang_msg['MAP_CREATE_FORM_DESCRIPTION']; ?></label>
                                <textarea class="form-control" rows="3" id="map_description" name="map_description"><?php echo $map_description; ?></textarea>
                            </div>
                            <input type="hidden" id="map_center_lat" name="map_center_lat" value="<?php echo $map->center_lat; ?>">
                            <input type="hidden" id="map_center_lng" name="map_center_lng" value="<?php echo $map->center_lng; ?>">
                            <input type="hidden" id="map_zoom" name="map_zoom" value="<?php echo $map->zoom; ?>">
                            <input type="hidden" id="map_typeid" name="map_typeid" value="<?php echo $map->typeid; ?>">
                            <input type="hidden" id="map_id" name="map_id" value="<?php echo $map->id; ?>">
                            <input type="hidden" id="user_id" name="user_id" value="<?php echo (isset($map->user_id)?$map->user_id:0); ?>">
                            <input type="hidden" id="objects_ids" name="objects_ids" value="<?php echo $objects_id_str; ?>">
                            <a href="index.php?p=map_view&map_id=<?php echo $map->id; ?>" class="btn btn-warning btn-sm">Cancel</a>
                            <button type="submit" class="btn btn-default" id="addmap_btn"><?php echo $lang_msg['MAP_CREATE_FORM_DESCRIPTION_SUBMIT']; ?></button>
                        </div>  
                    </form>
                </div> 
            </div> 
        </div>
    </div>
</div> <!-- /container -->

