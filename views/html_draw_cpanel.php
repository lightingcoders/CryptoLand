<?php
/**
 * Google Maps Draw Module
 * @package drawonmaps
 * 
 * File: html_draw_cpanel.php
 * Description: Toolbox for editing maps pages
 */
 
// retrieve markers from marker directory
$dir = 'graphics/markers';

// get the list of available markers
$markers_list = get_markers($dir);

// set default marker
$default_marker = $dir.'/default.png';
if (!file_exists($default_marker))
    $default_marker = "http://maps.gstatic.com/mapfiles/api-3/images/spotlight-poi.png";	

// get availble map objects for using
$available_objects = SiteConf::$MAP_OBJECTS_AVAILABLE;
?>
<!-- temporary store location of current marker -->
<span id="current_marker_path"><?php echo $default_marker; ?></span>

<!-- div below is used for displaying markers -->
<div id="markers_list_content" style="display:none;">
    <span id="markers_list"><?php echo $markers_list; ?></span>
    <span id="what_todo" style="display:none;"></span>
    <script>
        function setIcon(myvalue) {
            var what = $('#what_todo').text();

            if (what) {
                map.markers[what].setIcon(myvalue);
                $('#marker_icon_'+what).val(myvalue);
                $('#marker_'+what+'_chm').popover('hide');
            }
            else {
                $('#current_marker').html('<img src="'+myvalue+'" alt="Current marker" />');
                $('#current_marker_path').html(myvalue);
                $('#change_icon_btn').popover('hide');
            }
        }
    </script>
</div>


<div class="searchbox" >
    <form method="post" id="geocoding_form" class="form-inline" role="form">
        <div class="form-group input">
            <input type="text" class="form-control" id="address" name="address" placeholder="<?php echo $lang_msg['MAP_CREATE_ADDRESS']; ?>">
            <input type="submit" class="btn btn-primary" value="<?php echo $lang_msg['MAP_CREATE_ADDRESS_SEARCH']; ?>" />
        </div>	
        <label id="add_marker_on_loc_found_lbl"><input type="checkbox" id="add_marker_on_loc_found" value="add_marker_on_loc_found">Add marker on location found</label>
    </form>
</div>	
<div class="left_cpanel">

<h5>Zone select type :</h5> 
    <div c="basic_options">
        <div class="radio">
            <?php if (is_object_enabled('marker', $available_objects)) { ?>
                    <label for="drawwhat_markers"><input type="radio" name="drawwhat" id="drawwhat_markers" value="markers" checked><?php echo $lang_msg['MAP_CREATE_MARKERS']; ?></label>
            <?php } ?>
            <?php if (is_object_enabled('line', $available_objects)) { ?>
                    <label for="drawwhat_line"><input type="radio" name="drawwhat" id="drawwhat_line" value="line" ><?php echo $lang_msg['MAP_CREATE_LINE']; ?></label>
            <?php } ?>
            <?php if (is_object_enabled('polygon', $available_objects)) { ?>
                    <label for="drawwhat_polygon"><input type="radio" name="drawwhat" id="drawwhat_polygon" value="polygon"><?php echo $lang_msg['MAP_CREATE_POLYGON']; ?></label>     	
            <?php } ?>
            <?php if (is_object_enabled('rectangle', $available_objects)) { ?>
                    <label for="drawwhat_rectangle"><input type="radio" name="drawwhat" id="drawwhat_rectangle" value="rectangle"><?php echo $lang_msg['MAP_CREATE_RECTANGLE']; ?></label>    
            <?php } ?>
            <?php if (is_object_enabled('circle', $available_objects)) { ?>
                    <label for="drawwhat_circle"><input type="radio" name="drawwhat" id="drawwhat_circle" value="circle"><?php echo $lang_msg['MAP_CREATE_CIRCLE']; ?></label>    
            <?php } ?>
            <?php if (is_object_enabled('route', $available_objects)) { ?>
                    <label for="drawwhat_route"><input type="radio" name="drawwhat" id="drawwhat_route" value="route"><?php echo $lang_msg['MAP_CREATE_ROUTE']; ?></label>    
                    <input type="hidden" name="route_point" id="route_point" value="">
            <?php } ?>                    
            <div id="helpbox" class="text-info"></div>	  
        </div>
    </div>
    <div id="basic_buttons">
        <button type="button" id="drawwhat_clearall" class="btn btn-danger"><?php echo $lang_msg['MAP_CREATE_BTN_CLEAR']; ?></button>
        <button type="button" disabled="disabled" id="add_btn" class="btn btn-primary"><?php echo $lang_msg['MAP_CREATE_BTN_ADD']; ?></button> 
        <button type="button" disabled="disabled" id="save_btn" class="btn btn-success"><?php echo $lang_msg['MAP_CREATE_BTN_SAVE']; ?></button> 
        <button type="button" disabled="disabled" id="cancel_editing" class="btn btn-warning"><?php echo $lang_msg['MAP_CREATE_BTN_CANCEL']; ?></button>

        <?php if (is_object_enabled('marker', $available_objects)) { ?>
            <div id="markers_cpanel">
                <button type="button" id="add_current_loc_btn" class="btn btn-primary"><?php echo $lang_msg['MAP_CREATE_BTN_ADD_CURRENT_LOC']; ?></button>

                <div>
                    <button type="button" id="change_icon_btn" class="btn btn-primary" data-toggle="popover" data-placement="bottom" title="<?php echo $lang_msg['MAP_CREATE_ICON_SELECT']; ?>" data-content="" ><?php echo $lang_msg['MAP_CREATE_ICON_CHANGE']; ?></button>
                    <span id="current_marker"><img src="<?php echo $default_marker; ?>" alt="<?php echo $lang_msg['MAP_CREATE_ICON_CURRENT']; ?>" /></span>
                    <span id="current_loc_status"></span>
                </div>
            </div>
        <?php } ?>
    </div>
</div>			




