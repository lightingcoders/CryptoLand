<?php
/**
 * Google Maps Draw Module
 * @package drawonmaps
 * 
 * File: html_head.php
 * Description: HTML Header
 */
?>

<!DOCTYPE html>
<html lang='en' xmlns='http://www.w3.org/1999/xhtml'>
    <head>
        <title><?php echo ($vars['html_title']?$vars['html_title'].' - '.$lang_msg['HTML_TITLE']:$lang_msg['HTML_TITLE']); ?></title>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Google Maps Draw Module is a tool for web developers who want to build web apps for creating maps and draw on maps in the front end. Module provides a web graphical interface to users for adding markers and drawing polygons, rectangles, polylines and circles in order to create maps online. Users are able to add/edit/delete objects on Google Maps.">
        <meta name="keywords" content="google maps, maps api, api v3 draw, create map, online, jquery, bootstrap, marker, polyline, polygon, circle, rectangle, draw shapes map" />
        <meta name="author" content="AgoraMap.com">

        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <!--<script type="text/javascript" src="//maps.google.com/maps/api/js?sensor=true"></script>  <!-- obs-->
        <script type="text/javascript" src="//maps.googleapis.com/maps/api/js?v=3.exp&signed_in=false&libraries=geometry&key=<?php echo SiteConf::$GOOGLE_API_KEY; ?>"></script>

        <!-- Set initial values for map -->
        <script type="text/javascript">
            var map_default_lat = <?php echo $vars['default_lan']; ?>;
            var map_default_lng = <?php echo $vars['default_lng']; ?>;
            var map_default_zoom = <?php echo $vars['default_zoom']; ?>;
            var map_default_typeid = "<?php echo $vars['default_typeid']; ?>";
        </script>

        <!-- Drawonmaps JS files -->
        <script type="text/javascript" src="js/drawonmaps_app.js"></script>
        <script type="text/javascript" src="js/gmaps.js"></script>
        <script type="text/javascript" src="js/prettify.js"></script>
        <script type="text/javascript" src="js/drawonmaps_markers.js"></script>

        <?php	// include 	map JS only if required
                if (in_array($page, SiteConf::$PAGES_MAP)) {
        ?>
                        <script type="text/javascript" src="js/drawonmaps.js"></script>
                        <script type="text/javascript" src="js/drawonmaps_map_display.js"></script>
        <?php } ?>

        <!-- Bootstrap JS -->
        <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
        <!--<script type="text/javascript" src="bootstrap/bootstrap-select/bootstrap-select.min.js"></script>-->
        <script type="text/javascript" src="bootstrap/js/validator.min.js"></script>

        <!-- Drawonmaps CSS files -->
<?php 
	// do not show all CSS if only map display option is asked
	if ($vars['map_o']) { 
?>		
		<link href="css/drawonmaps_map_o.css" rel="stylesheet" type="text/css" />
<?php } else { ?>
		<link href="css/drawonmaps.css" rel="stylesheet" type="text/css" />
<?php } ?>		

        <!-- Bootstrap CSS -->
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="bootstrap/bootstrap-select/bootstrap-select.min.css" rel="stylesheet">

        <!-- Just for debugging purposes. Don\'t actually copy these 2 lines! -->
        <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
        <script src="js/ie-emulation-modes-warning.js"></script>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <script type="text/javascript"> $(document).ready(function () { $('.dropdown-toggle').dropdown(); }); </script>
    </head>

    <body>

 
