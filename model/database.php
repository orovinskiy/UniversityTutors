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

    function getTutors($year = '2020', $tutor = "all")
    {
        //checking to see if default $tutor parameter is used
        if ($tutor == "all"){
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
            echo var_dump($results);
            return $results;
        }

        //if default $tutor parameter is not used
        //defining query
        $sql = "SELECT Tutor.tutor_first, Tutor.tutor_last, User.user_email, Year.year_packet_sent, Year.year_background,
                    Year.year_reference, Year.year_offer_letter, Year.year_affirmation_disclosures, Year.year_sexual_misconduct,
                    Year.year_w4, Year.year_handbook_verification, Year.year_ADP, Year.year_i9, Year.year_orientation,
                    Year.year_placement from Year
                    JOIN Tutor on Year.user_id = Tutor.user_id
                    JOIN User on Year.user_id = User.user_id
                    where User.user_email = ?
                    and Year.year_start = ?";

        //Preparing statement
        $statement = $this->_dbh->prepare($sql);

        //Execute Statement and binding parameter
        $statement->execute([$tutor, $year]);

        //Get Results
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        echo "$results";

        return $results;

    }

    function testDatabase()
    {
        $sql = "SELECT * from Tutor";

        //Preparing statement
        $statement = $this->_dbh->prepare($sql);

        //Execute Statement and binding parameter
        $statement->execute();

        //Get Results
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        echo var_dump($results);

        return $results;

    }

}
