<?php
/**
 * Google Maps Draw Module
 * @package drawonmaps
 * 
 * Class: SiteConf
 * File: config.php
 * Description: Basic configuration options of the tool
 */
  
class SiteConf {
    // site related variables
    public static $DEFAULT_LANG = 'en';			// set default site language 
    public static $LANGS = array('en', 'el');           // list of available language translation. 
    public static $DEFAULT_PAGE = 'home';		// set default page
    public static $PAGES = array('home', 'map_create', 'maps', 'map_view', 'map_edit', 'map_save', 'map_global', 'login', 'register', 'users','faq','deposit','orders');		// list of available and pages views
    public static $PAGES_MAP = array('map_create', 'map_edit', 'map_view', 'map_global', 'route_create');	// list of pages which required map JS
    public static $PAGES_PROTECTED = array('map_create', 'map_edit', 'map_save','orders');		// list of pages which require login
    //public static $PAGES_PROTECTED = array();		// list of pages which require login

    // maps related variables
    public static $GOOGLE_API_KEY = 'AIzaSyDOrq-CBoek9ZWuQ0LAsX5XSIcvEwSBNGw';  // Google Api Key
    public static $DEFAULT_CENTER_LAN = 40.6955246;  // latitude of default center location
    public static $DEFAULT_CENTER_LNG = -73.9888796;  // longitude of default center location
    public static $DEFAULT_ZOOM = 12;               // possible values: from 0 to 19
    public static $DEFAULT_TYPEID = 'roadmap';      // possible values: 'roadmap', 'satellite', 'osm'
    public static $ENABLE_INDEX_TABLE = true;       // enable/disable a list of check boxes with available objects on each map, possible values: true / false
    public static $MAP_OBJECTS_AVAILABLE = array(   // list of available map objects for adding on maps, remove any you don't wish to provide
        'marker', 	
        'line', 
        'polygon', 
        'rectangle', 
        'circle',
        'route',
    );		


    /******************************************************
    ** You don't have to modify anything below this line **
    ******************************************************/ 

    // validate and set current language depending on input 
    public static function getSiteLanguage($lng) {
        $langs = self::$LANGS;

        if (!in_array($lng, $langs))
                $lng = self::$DEFAULT_LANG;		

        return $lng;	
    } 

    // validate and set current page depending on input 
    public static function getSitePage($page) {
        $pages = self::$PAGES;

        if (!in_array($page, $pages))
                $page = self::$DEFAULT_PAGE;		

        return $page;	
    } 

}
?>
