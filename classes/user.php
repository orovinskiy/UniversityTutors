<?php

/**
 * User class for University Tutors Project
 * Creates a new user by using the userEmail obtained upon login
 * @author Dallas Sloan
 */

class User
{
    private $userIsAdmin;
    private $userID;
    private $_db;

    /**
     * User constructor.
     * @param $userEmail email of user logging into system
     * @param $_db database object
     * @author Dallas Sloan
     */
    public function __construct($userEmail, $_db)
    {
        $this->_db = $_db;
        $userInfo = $this->_db->getUserByEmail($userEmail);
        $this->userID = $userInfo['user_id'];
        $this->userIsAdmin = $userInfo['user_is_admin'];
        //var_dump($userInfo); //used for testing
    }

    /**
     * Getter for userIsAdmin field
     * @return int 1 if user is admin 0 if not
     * @author Dallas Sloan
     */
    public function getUserIsAdmin()
    {
        return $this->userIsAdmin;
    }

    /**
     * Getter for userID field
     * @return int user ID
     * @author Dallas Sloan
     */
    public function getUserID()
    {
        return $this->userID;
    }


}
