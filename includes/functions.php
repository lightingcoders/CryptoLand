<?php
/**
 * Google Maps Draw Module
 * @package drawonmaps
 * 
 * Class: SiteConf
 * File: functions.php
 * Description: General functions of the tool
 */

function save_map($post_list) {
    $vars_tmp = array();
    $map_id = $post_list['map_id'];

    if ($map_id>0)	{
        try {
            $map = New Map($map_id);
        } catch (Exception $e) {
            $vars_tmp['error_msg'] = $e->getMessage();
        }
    }
    else {
        $map = New Map();
    }

    $map->title = trim($post_list['map_title']);
    $map->description = nl2br($post_list['map_description']);
    $map->center_lat = $post_list['map_center_lat'];
    $map->center_lng = $post_list['map_center_lng'];
    $map->zoom = intval($post_list['map_zoom']);
    $map->typeid = trim($post_list['map_typeid']);
    $map->user_id = intval($post_list['user_id']);

    $result = $map->save();

    if ($result) {
        //load the new created map
        $map = New Map($result);

        $vars_tmp['map'] = $map;
        $vars_tmp['error_msg'] = '';

        // for existing maps
        if ($post_list['objects_ids']) {
            $objects_id_arr = explode(",", $post_list['objects_ids']);
        }
        
        if ($post_list['marker_title']) {
            foreach( $post_list['marker_title'] as $key => $n ) {	// new map marker record created
                if (is_numeric($post_list['marker_id'][$key]) && $post_list['marker_id'][$key]>0) {
                    $get_object_id = $map->updateMapObject($n, $post_list['marker_coords'][$key], 1, $post_list['marker_id'][$key], $post_list['marker_icon'][$key]);
                    $objects_id_arr = array_diff($objects_id_arr, array($post_list['marker_id'][$key]));
                }
                else {
                    $get_object_id = $map->updateMapObject($n, $post_list['marker_coords'][$key], 1, null, $post_list['marker_icon'][$key]);
                }

                if (!$get_object_id[0])
                    $vars_tmp['error_msg'] .= $get_object_id[1];
            }
        }

        if ($post_list['line_title']) {
            foreach( $post_list['line_title'] as $key => $n ) {	// new map polyline record created
                if ($post_list['line_id'] && is_numeric($post_list['line_id'][$key]) && $post_list['line_id'][$key]>0) {
                    $get_object_id = $map->updateMapObject($n, $post_list['line_coords'][$key], 2, $post_list['line_id'][$key]);
                    $objects_id_arr = array_diff($objects_id_arr, array($post_list['line_id'][$key]));
                }
                else {
                    $get_object_id = $map->updateMapObject($n, $post_list['line_coords'][$key], 2);
                }                

                if (!$get_object_id[0])
                    $vars_tmp['error_msg'] .= $get_object_id[1];
            }
        }

        if ($post_list['poly_title']) {
            foreach( $post_list['poly_title'] as $key => $n ) {	// new map polygon record created
                if ($post_list['poly_id'] && is_numeric($post_list['poly_id'][$key]) && $post_list['poly_id'][$key]>0) {
                    $get_object_id = $map->updateMapObject($n, $post_list['poly_coords'][$key], 3, $post_list['poly_id'][$key]);
                    $objects_id_arr = array_diff($objects_id_arr, array($post_list['poly_id'][$key]));
                }
                else {
                    $get_object_id = $map->updateMapObject($n, $post_list['poly_coords'][$key], 3);
                }                

                if (!$get_object_id[0])
                    $vars_tmp['error_msg'] .= $get_object_id[1];
            }
        }

        if ($post_list['rect_title']) {
            foreach( $post_list['rect_title'] as $key => $n ) {	// new map rectagle record created
                if ($post_list['rect_id'] && is_numeric($post_list['rect_id'][$key]) && $post_list['rect_id'][$key]>0) {
                    $get_object_id = $map->updateMapObject($n, $post_list['rect_coords'][$key], 4, $post_list['rect_id'][$key]);
                    $objects_id_arr = array_diff($objects_id_arr, array($post_list['rect_id'][$key]));
                }
                else {
                    $get_object_id = $map->updateMapObject($n, $post_list['rect_coords'][$key], 4);
                }                

                if (!$get_object_id[0])
                    $vars_tmp['error_msg'] .= $get_object_id[1];
            }
        }

        if ($post_list['circle_title']) {
            foreach( $post_list['circle_title'] as $key => $n ) {	// new map circle record created
                if ($post_list['circle_id'] && is_numeric($post_list['circle_id'][$key]) && $post_list['circle_id'][$key]>0) {
                    $get_object_id = $map->updateMapObject($n, $post_list['circle_coords'][$key], 5, $post_list['circle_id'][$key]);
                    $objects_id_arr = array_diff($objects_id_arr, array($post_list['circle_id'][$key]));
                }
                else {
                    $get_object_id = $map->updateMapObject($n, $post_list['circle_coords'][$key], 5);
                }                

                if (!$get_object_id[0])
                    $vars_tmp['error_msg'] .= $get_object_id[1];
            }
        }
        
        if ($post_list['route_title']) {
            foreach( $post_list['route_title'] as $key => $n ) {	// new map route record created
                $route_coords = $post_list['route_start'][$key].','.$post_list['route_end'][$key];
                if (is_numeric($post_list['route_id'][$key]) && $post_list['route_id'][$key]>0) {
                    $get_object_id = $map->updateMapObject($n, $route_coords, 6, $post_list['route_id'][$key], $post_list['route_marker_icon'][$key]);
                    $objects_id_arr = array_diff($objects_id_arr, array($post_list['route_id'][$key]));
                }
                else {
                    $get_object_id = $map->updateMapObject($n, $route_coords, 6, null, $post_list['route_marker_icon'][$key]);
                }

                if (!$get_object_id[0])
                    $vars_tmp['error_msg'] .= $get_object_id[1];
            }
        }        
        
        // finally remove all objects which haven't been updated
        if (!empty($objects_id_arr)) {
            foreach ($objects_id_arr as $id) {
                $map->deleteMapObject($id);
            }
        }
                
    }
	
    $vars_tmp['success_msg'] = 'The map <strong>'.$map->title.'</strong> saved successfully';
    $vars_load = load_map($map->id);
	
    if (is_array($vars_load))
        $vars_tmp = array_merge($vars_tmp, $vars_load);
			
    return $vars_tmp;
} 

function delete_map($map_id) {
    $delete_map = false;
    $vars_tmp = array();

    if (is_numeric($map_id) && $map_id>0)	{
        try {
            $map = New Map($map_id);
        } catch (Exception $e) {
            $vars_tmp['error_msg'] = $e->getMessage();
        }

        if ($map && $map->canEdit()) {
            try {
                $vars_tmp['delete_map'] = $map->delete();
            } catch (Exception $e) {
                $vars_tmp['error_msg'] = $e->getMessage();
            }
        }
    }

    return $vars_tmp;	
} 

/**
 * Delete a user by ID, only from admin
 * 
 * @param type $user_id
 * @return type
 */
function user_delete($user_id) {
    $delete = false;
    $vars_tmp = array();

    if (User::isAdmin() && is_numeric($user_id) && $user_id>0)	{
        try {
            $user = New User($user_id);
        } catch (Exception $e) {
            $vars_tmp['error_msg'] = $e->getMessage();
        }

        try {
            $vars_tmp['user_delete'] = $user->delete();
        } catch (Exception $e) {
            $vars_tmp['error_msg'] = $e->getMessage();
        }
    }

    return $vars_tmp;	
} 

/**
 * Set or unset a user as admin, only from admin
 * 
 * @param type $user_id
 * @return type
 */
function user_make_admin($user_id) {
    $setunset = false;
    $vars_tmp = array();

    if (User::isAdmin() && is_numeric($user_id) && $user_id>0)	{
        try {
            $user = New User($user_id);
        } catch (Exception $e) {
            $vars_tmp['error_msg'] = $e->getMessage();
        }

        if (!User::isAdmin($user_id)) {
            $user->setAdmin(true);
        }
        else {
            $user->setAdmin(false);
        }
    }

    return $vars_tmp;	
}

function load_map($map_id) {
    $vars_tmp = array();

    try {
        $map = New Map($map_id);
    } catch (Exception $e) {
        $map = false;
        $vars_tmp['error_msg'] = $e->getMessage();
    }				

    if ($map)	{
        $vars_tmp['map'] = $map;
        $vars_tmp['map_objects'] = $map->getMapObjects();
        $vars_tmp['default_lan'] = $map->center_lat;
        $vars_tmp['default_lng'] = $map->center_lng;
        $vars_tmp['default_zoom'] = $map->zoom;
        $vars_tmp['default_typeid'] = $map->typeid;
        $vars_tmp['user_id'] = $map->user_id;
    }

    return $vars_tmp;	
} 

// display map buttons
function html_map_options($map_id, $lang_msg, $global = false) {
    $map_options = '';

    if (!$global) { // single map view
        $map = New Map($map_id);

        $map_options .= '<a type="button" class="btn btn-info btn-xs" id="full_map_'.$map_id.'" href="index.php?p=map_view&map_id='.$map_id.'&map_o=1" target="_blank">'.$lang_msg['MAPS_FULL_VIEW'].'</a>';
        if ($map->canEdit()) {
            $map_options .= '<button type="button" class="btn btn-primary btn-xs" id="edit_map_'.$map_id.'" href="#" onclick="doMap('.$map_id.',\'edit\');">'.$lang_msg['MAPS_EDIT_MAP'].'</button>';
            $map_options .= '<a type="button" class="btn btn-danger btn-xs" id="del_map_'.$map_id.'" href="#" onclick="doMap('.$map_id.',\'delete\');">'.$lang_msg['MAPS_DELETE_MAP'].'</a>';
        }
    }
    else {	// global map view
        $map_options .= '<a type="button" class="btn btn-info btn-xs" id="full_map_global" href="index.php?p=map_global&map_o=1" target="_blank">'.$lang_msg['MAPS_FULL_VIEW'].'</a>';
    }

    return $map_options;
}  

// display user buttons
function html_user_options($user_id, $lang_msg, $current_user) {
    $options = '';
    
    if ($current_user->isAdmin()) {
        $options .= '<a type="button" class="btn btn-primary btn-xs" id="del_make_admin_'.$user_id.'" href="#" onclick="doUser('.$user_id.',\'make_admin\');">'.(User::isAdmin($user_id)?$lang_msg['USERS_REMOVE_ADMIN']:$lang_msg['USERS_MAKE_ADMIN']).'</a>';
        $options .= '<a type="button" class="btn btn-danger btn-xs" id="del_user_'.$user_id.'" href="#" onclick="doUser('.$user_id.',\'delete\');">'.$lang_msg['USERS_DELETE'].'</a>';
    }

    return $options;
}

// check if a given type of objects is enabled
function is_object_enabled($object, $available_objects) {
    if (in_array($object,$available_objects))
        return true;

    return false;
}

// get all markers on a given directory
function get_markers($dir) {
    $markers_list = '';

    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            $allowedExts = array("png", "PNG");

            while (false !== ($file = readdir($dh))) {
                if (is_dir($dir.'/'.$file) && $file!='.' && $file!='..') {
                    $markers_list .= get_markers($dir.'/'.$file);
                }
                else {
                    $tmp_arr = explode('.', $file);
                    $extension = end($tmp_arr);
                    if (in_array($extension, $allowedExts))	{
                        $markers_list .= '<img src="'.$dir.'/'.$file.'" id="'.$dir.'/'.$file.'" class="marker_list" onClick="setIcon($(this).attr(\'id\'));" />&nbsp;';
                    }
                }
            }
            closedir($dh);
        }
    }	

    return $markers_list;

}

// check login info of user and login
function user_login($email, $pass) {
    $db = DbConnect::getConnection();

    if (empty(trim($email)) || empty(trim($pass)))
        return false;

    $email = mysqli_real_escape_string($db,$email);
    $upass = mysqli_real_escape_string($db,$pass);
    $query = "SELECT * FROM users WHERE email='$email'";
    $res = mysqli_query($db, $query) or die(mysqli_error($db));
    $row = mysqli_fetch_assoc($res);	

    if ($row['password'] == md5($upass)) {
        $_SESSION['user'] = $row['user_id'];
        return true;
    }

    return false;
}

// check login info of user and login
function user_signup($uname, $email, $pass) {
    $db = DbConnect::getConnection();

    if (empty(trim($uname)) || empty(trim($email)) || empty(trim($pass)))
            return false;

    $uname = mysqli_real_escape_string($db,$uname);
    $email = mysqli_real_escape_string($db,$email);
    $upass = md5(mysqli_real_escape_string($db,$pass));
    $sql = "INSERT INTO users(username,email,password) VALUES('$uname','$email','$upass')";
    $rs = mysqli_query($db, $sql);

    if ($rs === FALSE)
        return false;
    else 
        return true;

}
