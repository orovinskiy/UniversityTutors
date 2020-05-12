<?php

/**
 * Database Class for University Tutors Project
 * @author Dallas Sloan
 * filename: Database.php
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
     * @author Dallas Sloan
     */
    function __construct()
    {
        try {
            //Creates a new PDO connection
            $this->_dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * function to retrieve all data within Year table to show all information from Year table, joining with the User,
     * and Tutor table to show all information needed within admin datatable
     * @param string $year the year you would like to see information for. Default parameter is 2020
     * @return array Returns array of all rows within Year table
     * @author Dallas Sloan
     */
    function getTutors($year = '2020')
    {
        //defining query

        $sql = "SELECT Year.year_id, Tutor.user_id,Tutor.tutor_first, Tutor.tutor_last, User.user_email, Year.year_packet_sent, Year.year_background,
                    Year.year_reference, Year.year_offer_letter, Year.year_affirmation_disclosures, Year.year_sexual_misconduct,
                    Year.year_w4, Year.year_handbook_verification, Year.year_ADP, Year.year_i9, Year.year_orientation,
                    Year.year_placement from Year
                    JOIN Tutor on Year.user_id = Tutor.user_id
                    JOIN User on Year.user_id = User.user_id
                    where Year.year_start = ?";

        //Preparing statement
        $statement = $this->_dbh->prepare($sql);

        //Execute Statement
        $statement->execute([$year]);

        //Get Results
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        //echo var_dump($results);
        return $results;


    }

    /**
     * Function to retrieve information for a specific tutor to be shown within the new hire screen
     * @param int $year parameter to know what year to grab information for
     * @param int $userID parameter to select the specific tutor
     * @return array returns the row with the data for the specific tutor for the specific year
     */
    function getTutor($year, $userID)
    {
        //defining query
        $sql = "SELECT Tutor.tutor_first, Tutor.tutor_last, User.user_email, Year.year_packet_sent, Year.year_background,
                    Year.year_reference, Year.year_offer_letter, Year.year_affirmation_disclosures, Year.year_sexual_misconduct,
                    Year.year_w4, Year.year_handbook_verification, Year.year_ADP, Year.year_i9, Year.year_orientation,
                    Year.year_placement from Year
                    JOIN Tutor on Year.user_id = Tutor.user_id
                    JOIN User on Year.user_id = User.user_id
                    where User.user_id = ?
                    and Year.year_start = ?";

        //Preparing statement
        $statement = $this->_dbh->prepare($sql);

        //Execute Statement and binding parameter
        $statement->execute([$userID, $year]);

        //Get Results
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        //echo "$results";

        return $results;

    }

    /**
     * Updates data is the Year table given a column, value and yearId
     *
     * @param string $column The name of the column in the database being updated
     * @param mixed $value The value to set the column to
     * @param int $yearId The year_id for the year data being updated
     * @author Keller Flint
     */
    function updateYearData($column, $value, $yearId)
    {

        // TODO validate $column against php array of column names used in db to prevent sql injection
        if ($value == '0' || $value == '1') {
            $sql = "UPDATE Year SET $column = b? WHERE year_id = ?";
        } else {
            $sql = "UPDATE Year SET $column = ? WHERE year_id = ?";
        }


        $statement = $this->_dbh->prepare($sql);

        $statement->execute([$value, $yearId]);
    }

    /**
     * Inserts into User, Tutor and Year tables to create a new user.
     *
     * @param string $year The new user's starting year of employment.
     * @param string $email The new user's email
     * @return int The new user's id
     * @author Keller Flint
     */
    function addNewTutor($year, $email)
    {
        // add new user
        $sql = "insert into User values(default, ?, '1234', b'0')";

        $statement = $this->_dbh->prepare($sql);

        $statement->execute([$email]);

        $id = $this->_dbh->lastInsertId();

        // add new tutor
        $sql = "insert into Tutor (user_id) values($id)";

        $statement = $this->_dbh->prepare($sql);

        $statement->execute();

        // add new year data
        $sql = "insert into Year values(default, $id, ?,b'0','none','none',b'0', b'0',b'0',b'0',b'0','none', 'none', b'0', NULL)";

        $statement = $this->_dbh->prepare($sql);

        $statement->execute([$year]);

        return $id;
    }

    /**
     * Function to test the database connection
     * @return array returns all rows from Tutor table
     * @author Dallas Sloan
     */
    function testDatabase()
    {
        $sql = "SELECT * from Tutor";

        //Preparing statement
        $statement = $this->_dbh->prepare($sql);

        //Execute Statement and binding parameter
        $statement->execute();

        //Get Results
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        //echo var_dump($results);

        return $results;

    }

    /**
     * update tutor table in given user's id
     * @param int $user_id given user's id
     * @param string $firstName tutor's first name
     * @param string $lastName tutor's last name
     * @param string $phone tutor's phone
     * @param string $ssn tutor's ssn
     * @param string $bio tutor's bio
     * @author laxmi
     */
    function updateTutor($user_id, $firstName, $lastName, $phone, $ssn,$bio)
    {
        $sql = "UPDATE Tutor SET  tutor_first= ?, tutor_last=?,tutor_phone=?,tutor_ssn=?,tutor_bio =? WHERE user_id=?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$firstName, $lastName, $phone, $ssn, $bio, $user_id]);
    }

    /**
     * Get the tutor by their user_id
     * @param int $user_id given user's id
     * @return array  a row from Tutor table
     * @author laxmi
     */
    function getTutorById($user_id)
    {
        $sql = "SELECT * FROM Tutor WHERE user_id=?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$user_id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get user by it's id
     * @param int $user_id given user's id
     * @return array a rows from Tutor table
     * @author  laxmi
     */
    function getUserById($user_id)
    {
        $sql = "SELECT * FROM User WHERE user_id =? ";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$user_id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update email
     * @param int $user_id given user's id
     * @param string $email tutor's email
     * @author laxmi
     */
    function updateEmail($user_id, $email)
    {
        $sql = "UPDATE User SET user_email=? WHERE user_id =?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$email, $user_id]);
    }

    /**
     * Upload tutor's image
     * @param string $filePath path of image
     * @param int $user_id given user's id
     * @laxmi
     */
    function uploadTutorImage($filePath, $user_id)
    {
        $sql = "UPDATE Tutor SET tutor_image =?  WHERE  user_id=?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$filePath, $user_id]);
    }

    /** This fetches all the information to be displayed for the tutors view.
     * Only important data is chosen.
     * @param int $year the year that is required
     * @param int $userID the database id of the tutor
     * @return array returns all the required checkboxes as well as the name
     * @author oleg
     */
    function getTutorsChecklist($year, $userID)
    {
        $sql = "SELECT tutor_first, tutor_last, year_offer_letter, year_affirmation_disclosures,
        year_sexual_misconduct, year_id, year_w4, year_handbook_verification, year_ADP, year_i9, year_orientation FROM
        Year INNER JOIN Tutor ON Tutor.user_id = Year.user_id WHERE Tutor.user_id = ? AND year_start = ?";

        $statement = $this->_dbh->prepare($sql);

        $statement->execute([$userID, $year]);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }

    /**Get email form the user table
     * @param string $email user's email
     * @return array of a row with the specific user email
     * @author laxmi
     */
    function getEmail($email)
    {
        $sql = "SELECT * FROM User WHERE user_email=?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$email]);
        return $statement->fetch(PDO::FETCH_ASSOC);

    }

    /**
     * Deletes a user and all associated data from database by id
     *
     * @param int $user_id The id of the user to be deleted
     * @author Keller Flint
     */
    function deleteUser($user_id) {
        // delete user data from year
        $sql = "DELETE FROM Year WHERE user_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$user_id]);

        // delete user data from tutor
        $sql = "DELETE FROM Tutor WHERE user_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$user_id]);

        // delete user
        $sql = "DELETE FROM User WHERE user_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$user_id]);
    }

    /**
     * Returns the current year
     *
     * @return string The current year
     * @author Keller Flint
     */
    function getCurrentYear() {
        $sql = "SELECT DISTINCT info_current_year FROM Info";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC)["info_current_year"];
    }

    /**
     * Sets current year to the given year
     *
     * @param string $year The year to set current year to
     * @author Keller Flint
     */
    function setCurrentYear($year) {
        $sql = "UPDATE Info SET info_current_year = ? WHERE info_id = 1";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$year]);
    }

    /**
     * Import user to the current year by adding a new Year entry to the database with the user's old id.
     *
     * @param int $user_id The id of the user we are importing
     * @author Keller Flint
     */
    function importUser($user_id) {
        $year = $this->getCurrentYear();

        $sql = "insert into Year values(default, ?, ?,b'0','none','none',b'0', b'0',b'0',b'0',b'0','none', 'none', b'0', NULL)";

        $statement = $this->_dbh->prepare($sql);

        $statement->execute([$user_id, $year]);
    }

}
