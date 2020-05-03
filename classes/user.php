<?php

/**
 * User class for University Tutors Project
 * @author Dallas Sloan
 */

class User
{
    private $userEmail;
    private $userIsAdmin;
    private $userName;
    private $_db;

    /**
     * User constructor.
     * @param $userEmail
     * @param $userIsAdmin
     * @param $userName
     * @param $_db
     */
    public function __construct($userID, $userYear, $_db)
    {
        $this->_db = $_db;
        $userInfo = $this->_db->getTutor($userYear, $userID);
        $this->userEmail = $userInfo["user_email"];
        //$this->userName = $userInfo['tutor_first'] + userInfo['tutor_last'];
        //$this->userIsAdmin = $userInfo['']
        var_dump($userInfo);
        //echo $this->userName;
        //var_dump($userInfo["user_email"]);
    }


}
