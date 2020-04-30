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
     * @author Dallas Sloan
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
            $sql = "SELECT Tutor.tutor_first, Tutor.tutor_last, User.user_email, Year.year_packet_sent, Year.year_background,
                    Year.year_reference, Year.year_offer_letter, Year.year_affirmation_disclosures, Year.year_sexual_misconduct,
                    Year.year_w4, Year.year_handbook_verification, Year.year_ADP, Year.year_i9, Year.year_orientation,
                    Year.year_placement from Year
                    JOIN Tutor on Year.user_id = Tutor.user_id
                    JOIN User on Year.user_id = User.user_id
                    where Year.year_start = :year";

            //Preparing statement
            $statement = $this->_dbh->prepare($sql);

            //Binding Parameters
            $statement->bindParam(':year', $year);

            //Execute Statement
            $statement->execute();

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
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        //echo "$results";

        return $results;

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

    /** This fetches all the information to be displayed for the tutors view.
     * Only important data is chosen.
     * @param int $year the year that is required
     * @param int $userID the database id of the tutor
     * @return array returns all the required checkboxes as well as the name
     */
    function getTutorsChecklist($year, $userID){
        $sql = "SELECT tutor_first, tutor_last, year_offer_letter, year_affirmation_disclosures,
        year_sexual_misconduct, year_w4, year_handbook_verification, year_ADP, year_i9, year_orientation FROM
        Year INNER JOIN Tutor ON Tutor.user_id = Year.user_id WHERE Tutor.user_id = ? AND year_start = ?";

        $statement = $this->_dbh->prepare($sql);

        $statement->execute([$userID,$year]);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }

}
