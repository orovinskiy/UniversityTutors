<?php

/**
 * User class for University Tutors Project
 * Creates a new user by using the userEmail obtained upon login
 * @author Dallas Sloan
 */

class User
{
    private $userID;
    private $userEmail;
    private $userIsAdmin;

    /**
     * User constructor.
     * @param string $userID ID of user
     * @param string $userEmail email of user logging into system
     * @param string $userIsAdmin whether or not user is admin. If if admin 0 if not admin
     * @author Dallas Sloan
     */
    public function __construct($userID, $userEmail, $userIsAdmin)
    {
        $this->userID = $userID;
        $this->userEmail = $userEmail;
        $this->userIsAdmin = $userIsAdmin;
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
