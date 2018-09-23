<?php
/**
 * Google Maps Draw Module
 * @package drawonmaps
 * 
 * Class: Uers
 * File: user.php
 * Description: Handler of website users
 */

class User {

    public $user_id = 0;
    public $username = NULL;
    public $email = NULL;
    public $password = NULL;

    public function __construct($user_id = 0) {
        if (is_numeric($user_id) && $user_id > 0) {
            $user = array();
            $user = $this->loadUser($user_id);

            if ($user) {
                $this->user_id = $user[0]['user_id'];
                $this->username = $user[0]['username'];
                $this->email = $user[0]['email'];
                $this->balance = $user[0]['balance'];
                $this->password = $user[0]['password'];
                $this->is_admin = $user[0]['is_admin'];
            } else {
                throw new Exception('User not found. Invalid User ID: ' . $user_id);
            }
        } 
        else {
            $this->username = '';
            $this->email = '';
            $this->password = '';
            $this->balance = 'error fetching balance';
            $this->is_admin = 0;
        }
    }

    // method for saving a user
    public function save() {
        $db = DbConnect::getConnection();
        if ($this->user_id) {
            $sql = "update users set "
                . "username='" . mysqli_real_escape_string($db, $this->username) . "', "
                . "email='" . mysqli_real_escape_string($db, $this->email) . "', "
                . "password='" . md5(mysqli_real_escape_string($db, $this->password)) . "', "
                . "is_admin=" . $this->is_admin . " "
                . "where user_id=" . $this->user_id
            ;
        } 
        else {
            $sql = "insert into users (username, email, password) values ("
                . "'" . mysqli_real_escape_string($db, $this->username) . "', "
                . "'" . mysqli_real_escape_string($db, $this->email) . "', "
                . "'" . md5(mysqli_real_escape_string($db, $this->password)) . "'"
            ;
        }

        $rs = mysqli_query($db, $sql);
        if ($rs === FALSE) {
            throw new Exception('Error while saving the user: ' . mysqli_error($db));
            return false;
        }

        if (!$this->user_id) {
            $this->user_id = $db->insert_id;
        }

        return $this->user_id;
    }

    // method for deleting a user
    public function delete() {

        $db = DbConnect::getConnection();

        $sql = "delete from users where user_id=$this->user_id";
        $rs = mysqli_query($db, $sql);
        if ($rs == FALSE) {
            throw new Exception('The deletion of map failed: ' . mysqli_error($db));
            return false;
        } else {
            // delete also user maps
        }

        return true;
    }

    // retrieve a user from database
    public function loadUser($user_id) {
        $db = DbConnect::getConnection();
        $sql = "SELECT * FROM users WHERE user_id=$user_id";
        $result = mysqli_query($db, $sql) or die(mysqli_error($db));
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        return $rows;
    }
    
    // retrieve a user from database
    public function setAdmin($state) {
        if ($state)
            $this->is_admin = 1;
        else
            $this->is_admin = 0;

        $this->save();
    }    

    // retrieve a user from database
    public function loadAllUsers() {
        $db = DbConnect::getConnection();
        $sql = "SELECT * FROM users";
        $result = mysqli_query($db, $sql) or die(mysqli_error($db));
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        return $rows;
    }
    
    /**
     * Check if a given user or this user or loggedin user is admin
     * 
     * @param type $user_id
     * @return boolean
     */
    public function isAdmin($user_id = 0) {
        if (is_numeric($user_id) && $user_id > 0) {
            $db = DbConnect::getConnection();
            $sql = "SELECT * FROM users WHERE user_id={$user_id}";
            $result = mysqli_query($db, $sql) or die(mysqli_error($db));
            $rows = array();
            if ($row = mysqli_fetch_assoc($result)) {
                if ($row['is_admin'] == 1) {
                    return true;
                }
            }            
        } 
        //else if ($this->is_admin == 1) {
        //    return true;
        //}
        else if (isset($_SESSION['user']) && isset($_SESSION['user']) != "") {
            try {
                $user = New User($_SESSION['user']);
            } catch (Exception $e) {
                $user = false;
            }

            if ($user->is_admin == 1) {
                return true;
            }
        }
        
        return false;
    }    
    
}
