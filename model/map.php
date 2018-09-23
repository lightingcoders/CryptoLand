<?php
/**
 * Google Maps Draw Module
 * @package drawonmaps
 * 
 * Class: Map
 * File: map.php
 * Description: Handler of map's objects
 */

class Map {

    public $id = 0;
    public $title = NULL;
    public $description = NULL;
    public $center_lat = 0;
    public $center_lng = 0;
    public $zoom = 0;
    public $typeid = 0;
    public $user_id = 0;

    public function __construct($map_id = 0) {

        if (is_numeric($map_id) && $map_id > 0) {
            $map = array();
            $map = $this->loadMap($map_id);

            if ($map) {
                $this->id = $map[0]['id'];
                $this->title = $map[0]['title'];
                $this->description = $map[0]['description'];
                $this->center_lat = $map[0]['center_lat'];
                $this->center_lng = $map[0]['center_lng'];
                $this->zoom = $map[0]['zoom'];
                $this->typeid = $map[0]['typeid'];
                $this->user_id = $map[0]['user_id'];
            } else {
                throw new Exception('Map not found. Invalid map ID: ' . $map_id);
            }
        } else {
            $this->title = 'Map Title';
            $this->description = '';
            $this->center_lat = SiteConf::$DEFAULT_CENTER_LAN;
            $this->center_lng = SiteConf::$DEFAULT_CENTER_LNG;
            $this->zoom = SiteConf::$DEFAULT_ZOOM;
            $this->typeid = SiteConf::$DEFAULT_TYPEID;
            $this->user_id = 0;
        }
    }

    // method for saving a map object on database
    public function save() {
        $db = DbConnect::getConnection();

        if ($this->id) {
            $sql = "update maps set "
                    . "title='" . mysqli_real_escape_string($db, $this->title) . "', "
                    . "description='" . mysqli_real_escape_string($db, $this->description) . "', "
                    . "center_lat=$this->center_lat, "
                    . "center_lng=$this->center_lng, "
                    . "zoom=$this->zoom, "
                    . "user_id=$this->user_id, "
                    . "typeid='" . mysqli_real_escape_string($db, $this->typeid) . "' "
                    . "where id=" . $this->id;
        } else {
            $sql = "insert into maps (title, description, center_lat, center_lng, zoom, typeid, user_id) values ("
                    . "'" . mysqli_real_escape_string($db, $this->title) . "', "
                    . "'" . mysqli_real_escape_string($db, $this->description) . "', "
                    . " " . $this->center_lat . ", "
                    . " " . $this->center_lng . ", "
                    . " " . $this->zoom . ", "
                    . "'" . mysqli_real_escape_string($db, $this->typeid) . "', "
                    . " " . $this->user_id . " )"
            ;
        }

        $rs = mysqli_query($db, $sql);
        if ($rs === FALSE) {
            throw new Exception('Error while saving the map: ' . mysqli_error($db));
            return false;
        }

        if (!$this->id) {
            $this->id = $db->insert_id;
        }

        return $this->id;
    }

    // method for deleting a map and the map's objects
    public function delete() {

        $db = DbConnect::getConnection();

        $sql = "delete from maps where id=$this->id";
        $rs = mysqli_query($db, $sql);
        if ($rs == FALSE) {
            throw new Exception('The deletion of map failed: ' . mysqli_error($db));
            return false;
        } else {
            $sql = "delete from map_objects where map_id=$this->id";
            $rs = mysqli_query($db, $sql);
            if ($rs == FALSE) {
                throw new Exception('Error while deleting the map: ' . mysqli_error($db));
                return false;
            }
        }

        return true;
    }

    // retrieve a map from database
    public function loadMap($map_id) {
        $db = DbConnect::getConnection();
        $sql = "SELECT * FROM maps WHERE id=$map_id";
        $result = mysqli_query($db, $sql) or die(mysqli_error($db));
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        return $rows;
    }

    // retrieve map's objects of map from database
    public function getMapObjects() {
        $db = DbConnect::getConnection();
        $sql = "SELECT * FROM map_objects WHERE map_id=$this->id";
        $result = mysqli_query($db, $sql) or die(mysqli_error($db));
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        return $rows;
    }

    // delete map's objects of map
    public function deleteMapObjects() {
        $db = DbConnect::getConnection();

        $sql = "delete from map_objects where map_id=$this->id";
        $rs = mysqli_query($db, $sql);
        if ($rs == FALSE) {
            throw new Exception('Error while deleting objects of map: ' . mysqli_error($db));
        }

        return true;
    }
    
    // delete map's object by id
    public function deleteMapObject($object_id) {
        $object_id = intval($object_id);
        
        if ($object_id < 1) {
            return array(false, 'Invalid map object on deleteMapObject');
        }        
        
        $db = DbConnect::getConnection();
        $sql = "delete from map_objects where id={$object_id}";
        $rs = mysqli_query($db, $sql);
        if ($rs == FALSE) {
            throw new Exception('Error while deleting object '.$object_id.' of map: ' . mysqli_error($db));
        }

        return true;
    }
    
    // save map's objects on database
    function updateMapObject($title, $coords, $object_id, $id = null, $marker_icon = '') {
        $id = intval($id);
        $coords = trim($coords);
        $object_id = intval($object_id);
        $marker_icon = trim($marker_icon);

        if ($object_id < 1) {
            return array(false, 'Invalid map object on updateMapObject');
        }

        $db = DbConnect::getConnection();
        if ($id > 0) {
            $sql = "update map_objects set title='" . mysqli_real_escape_string($db, $title) . "', coords='" . mysqli_real_escape_string($db, $coords) . "', object_id=" . $object_id . ", map_id=" . $this->id . ", marker_icon='" . mysqli_real_escape_string($db, $marker_icon) . "' where id=" . $id;
        } else {
            $sql = "insert into map_objects (title, coords, object_id, map_id, marker_icon) values ('" . mysqli_real_escape_string($db, $title) . "', '" . mysqli_real_escape_string($db, $coords) . "', " . $object_id . ", " . $this->id . ", '" . mysqli_real_escape_string($db, $marker_icon) . "')";
        }

        $rs = mysqli_query($db, $sql);
        if ($rs === FALSE) {
            return array(false, 'Error while saving objects of map: ' . mysqli_error($db));
        }

        return array(true, $db->insert_id);
    }

    // check if current user can edit current map
    public function canEdit() {
        // load logged-in user, if any 
        if (isset($_SESSION['user']) && isset($_SESSION['user']) != "") {
            try {
                $user = New User($_SESSION['user']);
            } catch (Exception $e) {
                $user = false;
            }

            if ($user->isAdmin() || $user->user_id == $this->user_id) {
                return true;
            }
        }

        return false;
    }

}

?>
