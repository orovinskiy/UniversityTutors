<?php

/**
 * Database Class for University Tutors Project
 * @author Dallas Sloan
 * filename: database.php
 *
 * This is a class that creates a database object and queries the working database for the project
 * Class Database
 */
class Database
{
    //PDO object
    private $_dbh;

    /**
     * Constructor for database class. Creates a new database object
     */
    function __construct()
    {
        try{
            //Creates a new PDO connection
            $this ->_dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        }
        catch (PDOException $e){
            echo $e -> getMessage();
        }
    }
}
