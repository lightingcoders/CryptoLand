<?php
/**
 * Google Maps Draw Module
 * @package drawonmaps
 * 
 * File: index.php
 * Description: Controller and index file of the site
 */

session_start(); 

require_once dirname(__FILE__) . "/includes/config.php";
require_once dirname(__FILE__) . "/includes/db_connect.php";
require_once dirname(__FILE__) . "/includes/functions.php";

require_once dirname(__FILE__) . "/model/sitemaps.php";
require_once dirname(__FILE__) . "/model/user.php";
require_once dirname(__FILE__) . "/model/map.php";

$lng = (isset($_GET['lng']) ? $_GET['lng'] : SiteConf::$DEFAULT_LANG);
$page = (isset($_GET['p']) ? $_GET['p'] : SiteConf::$DEFAULT_LANG);

// set default lang if current is not valid
$lng = SiteConf::getSiteLanguage($lng);
include_once dirname(__FILE__) . '/languages/' . $lng . '.php';

// set default page if current is not valid
$page = SiteConf::getSitePage($page);

// initialize basic map variables
$vars = array();
// initialize errors params
$vars['error_msg'] = '';
$vars['error_load_page'] = '';
$vars['success_msg'] = '';
$vars['success_load_page'] = '';

// login if asked
if (isset($_POST['btn-login'])) {
    if (user_login($_POST['email'], $_POST['pass'])) {
        $vars['success_msg'] = $lang_msg['LOGIN_SUCCESS'];
        $page = 'home';
    } else {
        $vars['error_msg'] = $lang_msg['ERROR_LOGIN_INFO'];
        $vars['error_load_page'] = 'login';
    }
}

// signup if asked
if (isset($_POST['btn-signup'])) {
    if (user_signup($_POST['uname'], $_POST['email'], $_POST['pass'])) {
        $vars['success_msg'] = $lang_msg['SUCCESS_REGISTER'];
        $page = 'login';
    } else {
        $vars['error_msg'] = $lang_msg['ERROR_REGISTER_INFO'];
        $vars['error_load_page'] = 'register';
    }
}

// logout if asked
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['user']);
    $page = 'home';
}

// load logged-in user, if any 
if (isset($_SESSION['user']) && isset($_SESSION['user']) != "") {
    try {
        $user = New User($_SESSION['user']);
    } catch (Exception $e) {
        $user = false;
    }
    $vars['user'] = $user;
}

// require login for protected pages
if (isset($_SESSION['user']) == "" && in_array($page, SiteConf::$PAGES_PROTECTED)) {
    $vars['error_msg'] = $lang_msg['LOGIN_HAVE_TO'];
    $vars['error_load_page'] = 'login';
    $page = 'login';
}

$vars['default_lan'] = SiteConf::$DEFAULT_CENTER_LAN;
$vars['default_lng'] = SiteConf::$DEFAULT_CENTER_LNG;
$vars['default_zoom'] = SiteConf::$DEFAULT_ZOOM;
$vars['default_typeid'] = SiteConf::$DEFAULT_TYPEID;

switch ($page) {
    case "maps":
        if (isset($_POST['map_id'])) {
            $vars_tmp = delete_map(intval($_POST['map_id']));
        }

        $user_mymaps = 0;
        if (isset($_GET['mymaps']) && $vars['user']) {
            $user_mymaps = $vars['user']->user_id;
            $vars['html_title'] = $lang_msg['MAPS_MY_TITLE'];
            $vars['page_title'] = $lang_msg['MAPS_MY_PAGE_TITLE'];
        } else {
            $vars['html_title'] = $lang_msg['MAPS_TITLE'];
            $vars['page_title'] = $lang_msg['MAPS_PAGE_TITLE'];
        }
        $vars['sitemaps'] = SiteMaps::getMaps($user_mymaps);
        break;

    case "map_view":
        if (isset($_GET['map_id'])) {
            $map_id = intval($_GET['map_id']);
        }

        if ($map_id <= 0)
            $vars['error_msg'] .= $lang_msg['ERROR_INVALID_MAP_ID'];

        $vars_tmp = load_map(intval($map_id));
        if (!count($vars_tmp) > 0)
            $vars['error_msg'] .= $lang_msg['ERROR_INVALID_MAP_ID'];

        $vars['html_title'] = $lang_msg['MAP_VIEW_TITLE'];
        $vars['page_title'] = $lang_msg['MAP_VIEW_PAGE_TITLE'];
        break;

    case "map_global":
        $vars['map_objects'] = SiteMaps::getMapsObjects();

        $vars['html_title'] = $lang_msg['MAP_GLOBAL_TITLE'];
        $vars['page_title'] = $lang_msg['MAP_GLOBAL_PAGE_TITLE'];

        // redirect the page to map view
        $page = 'map_view';
        break;

    case "map_create":
        $vars['html_title'] = $lang_msg['MAP_CREATE_TITLE'];
        $vars['page_title'] = $lang_msg['MAP_CREATE_PAGE_TITLE'];
        break;

    case "map_edit":
        if (isset($_POST['map_id'])) {
            $map_id = intval($_POST['map_id']);
        }

        if ($map_id > 0) {
            $vars_tmp = load_map(intval($map_id));

            // check if current user can edit this map
            if (!$vars_tmp['map']->canEdit())
                $vars['error_msg'] .= $lang_msg['ERROR_INVALID_ACCESS'];
        } else
            $vars['error_msg'] .= $lang_msg['ERROR_INVALID_MAP_ID'];

        $vars['html_title'] = $lang_msg['MAP_EDIT_TITLE'];
        $vars['page_title'] = $lang_msg['MAP_EDIT_PAGE_TITLE'];
        break;

    case "map_save":
        $post_list = array();
        $post_list['map_id'] = isset($_POST['map_id']) ? $_POST['map_id'] : 0;
        $post_list['map_title'] = isset($_POST['map_title']) ? $_POST['map_title'] : '';
        $post_list['map_price'] = isset($_POST['map_price']) ? $_POST['map_price'] : '';
        $post_list['map_description'] = isset($_POST['map_description']) ? $_POST['map_description'] : '';
        $post_list['map_center_lat'] = isset($_POST['map_center_lat']) ? $_POST['map_center_lat'] : 0;
        $post_list['map_center_lng'] = isset($_POST['map_center_lng']) ? $_POST['map_center_lng'] : 0;
        $post_list['map_zoom'] = isset($_POST['map_zoom']) ? $_POST['map_zoom'] : '';
        $post_list['map_typeid'] = isset($_POST['map_typeid']) ? $_POST['map_typeid'] : '';
        $post_list['marker_id'] = isset($_POST['marker_id']) ? $_POST['marker_id'] : '';
        $post_list['marker_title'] = isset($_POST['marker_title']) ? $_POST['marker_title'] : '';
        $post_list['marker_coords'] = isset($_POST['marker_coords']) ? $_POST['marker_coords'] : '';
        $post_list['marker_icon'] = isset($_POST['marker_icon']) ? $_POST['marker_icon'] : '';
        $post_list['line_id'] = isset($_POST['line_id']) ? $_POST['line_id'] : '';
        $post_list['line_title'] = isset($_POST['line_title']) ? $_POST['line_title'] : '';
        $post_list['line_coords'] = isset($_POST['line_coords']) ? $_POST['line_coords'] : '';
        $post_list['poly_id'] = isset($_POST['poly_id']) ? $_POST['poly_id'] : '';
        $post_list['poly_title'] = isset($_POST['poly_title']) ? $_POST['poly_title'] : '';
        $post_list['poly_coords'] = isset($_POST['poly_coords']) ? $_POST['poly_coords'] : '';
        $post_list['rect_id'] = isset($_POST['rect_id']) ? $_POST['rect_id'] : '';
        $post_list['rect_title'] = isset($_POST['rect_title']) ? $_POST['rect_title'] : '';
        $post_list['rect_coords'] = isset($_POST['rect_coords']) ? $_POST['rect_coords'] : '';
        $post_list['circle_id'] = isset($_POST['circle_id']) ? $_POST['circle_id'] : '';
        $post_list['circle_title'] = isset($_POST['circle_title']) ? $_POST['circle_title'] : '';
        $post_list['circle_coords'] = isset($_POST['circle_coords']) ? $_POST['circle_coords'] : '';
        $post_list['route_title'] = isset($_POST['route_title']) ? $_POST['route_title'] : '';
        $post_list['route_start'] = isset($_POST['route_start']) ? $_POST['route_start'] : '';
        $post_list['route_end'] = isset($_POST['route_end']) ? $_POST['route_end'] : '';
        $post_list['route_marker_icon'] = isset($_POST['route_marker_icon']) ? $_POST['route_marker_icon'] : '';
        $post_list['user_id'] = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
        $post_list['objects_ids'] = isset($_POST['objects_ids']) ? $_POST['objects_ids'] : '';

        if (trim($post_list['map_title']) == '') {
            $vars['error_msg'] = $lang_msg['ERROR_EMPTY_TITLE'];
        }

        if (empty($vars['error_msg'])) {
            $vars_tmp = save_map($post_list);

            // redirect the page to map view
            $page = 'map_view';
        }

        $vars['html_title'] = $lang_msg['MAP_VIEW_TITLE'];
        $vars['page_title'] = $lang_msg['MAP_VIEW_PAGE_TITLE'];
        break;

    case "login":
        $vars['html_title'] = $lang_msg['LOGIN_TITLE'];
        $vars['page_title'] = $lang_msg['LOGIN_PAGE_TITLE'];
        break;

    case "register":
        $vars['html_title'] = $lang_msg['REGISTER_TITLE'];
        $vars['page_title'] = $lang_msg['REGISTER_PAGE_TITLE'];
        break;
    
    case "users":
        if (isset($_POST['user_id']) && isset($_POST['what'])) {
            if ($_POST['what'] == 'delete')
                $vars_tmp = user_delete(intval($_POST['user_id']));
            else if ($_POST['what'] == 'make_admin')
                $vars_tmp = user_make_admin(intval($_POST['user_id']));
        }
        
        $vars['html_title'] = $lang_msg['USERS_TITLE'];
        $vars['page_title'] = $lang_msg['USERS_PAGE_TITLE'];
        
        $vars['siteusers'] = User::loadAllUsers();
        break;    

    default:
        $vars['html_title'] = '';
        $vars['page_title'] = $lang_msg['PAGE_TITLE'];
        break;
}

// check option to show only map
$vars['map_o'] = false;
if ($page == 'map_view' && isset($_GET['map_o']) && $_GET['map_o'] == 1) {
    $vars['map_o'] = true;
}

if (isset($vars_tmp) && is_array($vars_tmp))
    $vars = array_merge($vars, $vars_tmp);

// include language file
include_once dirname(__FILE__) . '/languages/' . $lng . '.php';

// include html header
require_once dirname(__FILE__) . "/views/html_head.php";

// include top menu, do not show top menu if only map display option is asked
if (!$vars['map_o']) {
    require_once dirname(__FILE__) . "/views/html_menu_top.php";
}

if (!empty($vars['error_msg'])) {
    // obs$vars['page_title'] = $lang_msg['ERROR_PAGE_TITLE'];		
    include_once dirname(__FILE__) . '/views/error_page.php';

    if (!empty($vars['error_load_page']))
        include_once dirname(__FILE__) . '/views/' . $vars['error_load_page'] . '.php';
}
else {  // include required view as main page
    if (!empty($vars['success_msg']))
        include_once dirname(__FILE__) . '/views/success_page.php';

    include_once dirname(__FILE__) . '/views/' . $page . '.php';
}

// include html footer
include_once dirname(__FILE__) . '/views/html_footer.php';
?>
