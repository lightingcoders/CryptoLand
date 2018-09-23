<?php
/**
 * Google Maps Draw Module
 * @package drawonmaps
 * 
 * Class: SiteConf
 * File: db_connect.php
 * Description: Database connector
 */
 
require_once dirname(__FILE__) . "/db_options.php";

class DbConnect {
    private static $db;
    public static function getConnection($reuse = true)
    {
        if (!DbConnect::$db || !$reuse) {
            $db = mysqli_connect(Options::$DBHOST, Options::$DBUSER, Options::$DBPASSWORD, Options::$DBNAME);
            if (!$db) {
                die (mysqli_connect_error());
            }
            mysqli_set_charset($db, "UTF8");
            DbConnect::$db = $db;
        }
        return DbConnect::$db;
    }
}

