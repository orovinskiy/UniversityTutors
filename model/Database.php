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
     * Returns all tutor data for the given year
     *
     * @param string $year The year to get tutor data for
     * @return array All tutor data used to build the tutor table
     * @author Keller Flint
     */
    function getTutors($year = "2020")
    {
        // Get all tutors data for the given year
        $sql = "SELECT tutorYear_id, User.user_id, user_email, tutor_first, tutor_last, tutor_bio, tutor_image FROM TutorYear 
                INNER JOIN Tutor ON TutorYear.user_id = Tutor.user_id
                INNER JOIN User ON TutorYear.user_id = User.user_id
                WHERE tutorYear_year = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$year]);

        $tutors = $statement->fetchAll(PDO::FETCH_ASSOC);

        $tableData = array();

        // Build associate arrays with additional item and data information for each tutor
        foreach ($tutors as $tutorInfo) {
            $tutorKey = $tutorInfo["tutorYear_id"];
            $tableData[$tutorKey]["info"] = $tutorInfo;

            $itemData = $this->getItemTutorYear($tutorKey);
            $tableData[$tutorKey]["items"] = $itemData;
        }
        return $tableData;
    }

    /**
     * Returns item and state data for the given tutorYear_id
     *
     * @param int $tutorYearId The tutorYear_id
     * @return array Item and state data
     * @author Keller Flint
     */
    function getItemTutorYear($tutorYearId)
    {
        $sql = "SELECT * FROM ItemTutorYear 
                INNER JOIN State ON ItemTutorYear.state_id = State.state_id
                INNER JOIN Item ON ItemTutorYear.item_id = Item.item_id
                WHERE tutorYear_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$tutorYearId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns state information for all states
     *
     * @return array State information for all states
     * @author Keller Flint
     */
    function getAllStates()
    {
        $sql = "SELECT * FROM State";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Updates the state of ItemYearData for selects
     *
     * @param int $itemId The id of the item to be updated
     * @param int $tutorYearId The id of the tutorYear to be updated
     * @param int $stateId The id of the state the item is being updated to
     * @author Keller Flint
     */
    function updateItemTutorYearSelect($itemId, $tutorYearId, $stateId)
    {
        $sql = "UPDATE ItemTutorYear SET state_id = ? WHERE item_id = ? AND tutorYear_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$stateId, $itemId, $tutorYearId]);

        return "true";
    }

    /**
     * Updates the state of ItemYearData for checkboxes
     *
     * @param int $itemId The id of the item to be updated
     * @param int $tutorYearId The id of the tutorYear to be updated
     * @param int $stateOrder The order of the state the item is being updated to
     * @author Keller Flint
     */
    function updateItemTutorYearCheck($itemId, $tutorYearId, $stateOrder)
    {
        // Get the state id by the passed order
        $stateId = $this->getStateByOrder($itemId, $stateOrder)["state_id"];

        // Update the state
        $sql = "UPDATE ItemTutorYear SET state_id = ? WHERE item_id = ? AND tutorYear_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$stateId, $itemId, $tutorYearId]);
    }

    /**
     * Inserts into User, Tutor and Year tables to create a new user.
     *
     * @param string $year The new user's starting year of employment.
     * @param string $email The new user's email
     * @param string $password The user's new password
     * @author Keller Flint
     */
    function addNewTutor($year, $email, $password)
    {
        //hash the password parameter
        $password = md5($password);
        // add new user
        $sql = "INSERT INTO User VALUES(default, ?, ?, 0)";

        $statement = $this->_dbh->prepare($sql);

        $statement->execute([$email, $password]);

        $userId = $this->_dbh->lastInsertId();

        // add new tutor
        $sql = "INSERT INTO Tutor (user_id) VALUES($userId)";

        $statement = $this->_dbh->prepare($sql);

        $statement->execute();

        // add new tutorYear data
        $sql = "INSERT INTO TutorYear VALUES(default, ?, ?)";

        $statement = $this->_dbh->prepare($sql);

        $statement->execute([$userId, $year]);

        $tutorYearId = $this->_dbh->lastInsertId();

        $this->populateDefaultData($tutorYearId);
    }

    /**
     * Adds default data to ItemTutorYear
     *
     * @param int $tutorYearId
     * @author Keller Flint
     */
    private function populateDefaultData($tutorYearId)
    {
        // get default state for each item
        $sql = "SELECT state_id, state_set_by, item_id FROM State WHERE state_set_by = 'default'";

        $statement = $this->_dbh->prepare($sql);

        $statement->execute();

        $defaults = $statement->fetchAll(PDO::FETCH_ASSOC);

        // set default state to each item for the ItemTutorYear
        $sql = "INSERT INTO ItemTutorYear VALUES (?, ?, ?, NULL)";
        $statement = $this->_dbh->prepare($sql);
        foreach ($defaults as $state) {
            $statement->execute([$state["item_id"], $tutorYearId, $state["state_id"]]);
        }

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
    function updateTutor($user_id, $firstName, $lastName, $phone, $ssn, $bio)
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

    /**This updates the tutorYearItem with the passed in file name
     * @param string $filename name of file
     * @param int $itemId id of item()
     * @param int $tutorYear id of tutorYear
     */
    function updateFileItem($filename, $itemId, $tutorYear)
    {
        $sql = 'UPDATE ItemTutorYear SET itemTutorYear_file = ? WHERE item_id = ? AND tutorYear_id = ?';

        $statement = $this->_dbh->prepare($sql);

        $statement->execute([$filename, $itemId, $tutorYear]);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
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
        $sql = "SELECT State.state_is_done, Item.item_file, State.state_order, State.state_id, State.state_text,State.state_set_by,
                Item.item_name, Item.item_id, ItemTutorYear.itemTutorYear_file, Item.item_is_upload, TutorYear.tutorYear_id FROM ItemTutorYear 
                inner join TutorYear on ItemTutorYear.tutorYear_id = TutorYear.tutorYear_id 
                inner join Item on ItemTutorYear.item_id = Item.item_id 
                inner join State on ItemTutorYear.state_id = State.state_id
                where TutorYear.user_id = ? and TutorYear.tutorYear_year = ?";

        $statement = $this->_dbh->prepare($sql);

        $statement->execute([$userID, $year]);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        $finalRes = array();
        foreach ($results as $array) {
            $allStates = $this->getStates($array['item_id']);
            $keepItem = false;

            //go through each state of a item to see if it has a tutor setby
            //if it does keep the item
            foreach ($allStates as $stateArray) {
                if ($stateArray['state_set_by'] === 'tutor') {
                    $keepItem = true;
                    break;
                }
            }

            if ($keepItem) {
                array_push($finalRes, $array);
            }
        }
        return $finalRes;
    }

    function getTutorBioImage($userID){
        $sql = "SELECT tutor_bio, tutor_image FROM Tutor WHERE user_id = ?";

        $statement = $this->_dbh->prepare($sql);

        $statement->execute([$userID]);

        $results = $statement->fetch(PDO::FETCH_ASSOC);

        return $results;
    }

    /**This function gets the name of a tutor based on the id
     * @param int $userID the id of a user
     * @return string The full name of a user
     * @author Oleg
     */
    function getTutorName($userID)
    {
        $sql = "SELECT tutor_first, tutor_last FROM Tutor WHERE user_id = ?";

        $statement = $this->_dbh->prepare($sql);

        $statement->execute([$userID]);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $results[0]['tutor_first'] . ' ' . $results[0]['tutor_last'];
    }

    /**Gets the next state of a item (WARNING: if its the last state of a item it will go to the first state
     * of a different item)
     * @param int $itemID
     * @param int $order
     * @return string returns the set by of a state
     * @author Oleg
     */
    function getNextState($itemID, $order)
    {

        $order++;

        $sql = "SELECT State.state_set_by FROM State WHERE State.item_id = ? AND state_order = ?";

        $statement = $this->_dbh->prepare($sql);

        $statement->execute([$itemID, $order]);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $results[0]['state_set_by'];
    }

    /**gets the state id
     * @param int $itemID
     * @param int $order what order
     * @return string state id
     * @author Oleg
     */
    function getNextStateID($itemID, $order)
    {

        $sql = "SELECT State.state_id FROM State WHERE State.item_id = ? AND State.state_order=?";

        $statement = $this->_dbh->prepare($sql);

        $statement->execute([$itemID, $order]);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $results[0]['state_id'];
    }

    /**This function gets the file of a tutors specific item
     * @param int $tutorID the id of tutorYear
     * @param int $itemID the id of the item
     * @return string mixed returns the file name if exists
     * @author  oleg
     */
    function getTutorFile($tutorID, $itemID){
        $sql = 'SELECT itemTutorYear_file FROM tutors.ItemTutorYear WHERE tutorYear_id = ? AND item_id = ?';

        $statement = $this->_dbh->prepare($sql);

        $statement->execute([$tutorID,$itemID]);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $results[0]["itemTutorYear_file"];
    }

    /**This gets the original file of a item
     * @param int $itemID id of the item
     * @return string mixed returns the file name if exists
     */
    function getOgFile($itemID){
        $sql = 'SELECT item_file FROM Item WHERE item_id = ?';

        $statement = $this->_dbh->prepare($sql);

        $statement->execute([$itemID]);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $results[0]["item_file"];
    }

    /**Gets the next state of a item (WARNING: if its the last state of a item it will go to the first state
     * of a different item)
     * @param int $stateID
     * @return string returns the text of a state
     * @author Oleg
     */
    function getNextStateText($stateID, $order)
    {
        $order = $order + 1;
        $sql = "SELECT State.state_text FROM State WHERE State.item_id = ? AND state_order = ?";

        $statement = $this->_dbh->prepare($sql);

        $statement->execute([$stateID, $order]);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $results[0]['state_text'];
    }

    /**Updates the state of a item for a tutor
     * @param int $state the new state to set
     * @param int $item the item that the state will be changed for
     * @param int $user the user that's associated with the item
     * @author Oleg
     */
    function updateStateOfTutor($state, $item, $user)
    {
        $sql = "UPDATE ItemTutorYear SET state_id = ? where item_id = ? and tutorYear_id = ?";

        $statement = $this->_dbh->prepare($sql);

        $statement->execute([$state, $item, $user]);
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
    function deleteUser($user_id)
    {
        // TODO delete associated files

        // get all associated tutorYears
        $sql = "SELECT tutorYear_id FROM TutorYear WHERE user_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$user_id]);
        $tutorYears = $statement->fetchAll(PDO::FETCH_ASSOC);

        // delete user's data from each itemTutorYear
        foreach ($tutorYears as $tutorYear) {
            $sql = "DELETE FROM ItemTutorYear WHERE tutorYear_id = ?";
            $statement = $this->_dbh->prepare($sql);
            $statement->execute([$tutorYear["tutorYear_id"]]);
        }

        // delete user data from tutorYear
        $sql = "DELETE FROM TutorYear WHERE user_id = ?";
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
    function getCurrentYear()
    {
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
    function setCurrentYear($year)
    {
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
    function importUser($user_id)
    {
        $year = $this->getCurrentYear();

        // Cannot add a user to the same year twice
        if ($this->userInYear($user_id, $year)) {
            return;
        }

        $sql = "insert into TutorYear values(default, ?, ?)";

        $statement = $this->_dbh->prepare($sql);

        $statement->execute([$user_id, $year]);

        $tutorYearId = $this->_dbh->lastInsertId();

        $this->populateDefaultData($tutorYearId);
    }

    /**
     * Get user by their email
     * @param int $user_email users email
     * @return array information about user
     * @author  Dallas Sloan
     */
    function getUserByEmail($user_email)
    {
        $sql = "SELECT * FROM User WHERE user_email =? ";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$user_email]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Function to validate whether a user has provided valid credentials upon attempting to login
     * @param string $username username input into login form
     * @param string $password password input into login form
     * @return array containing all columns from User table if user logged in is valid, if not valid returns null
     */
    function login($username, $password)
    {
        //sql statement
        $sql = "SELECT * FROM User 
                where user_email = ? and user_password = ?";

        //preparing statement
        $statement = $this->_dbh->prepare($sql);

        //execute statement
        $statement->execute([$username, $password]);

        //return user_ID that matches parameters or null if not found
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get id and email for all admins
     *
     * @return array admin id and email data
     * @author Keller Flint
     */
    function getAdmins()
    {
        $sql = "SELECT user_id, user_email FROM User WHERE user_is_admin = 1";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add a new admin
     *
     * @param string $email email of the new admin
     * @return int id of the new admin
     * @author Keller Flint
     */
    function addAdmin($email)
    {
        $sql = "INSERT INTO User VALUES (DEFAULT, ?, MD5('1234'), 1)";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$email]);
        return $this->_dbh->lastInsertId();
    }

    function userInYear($id, $year)
    {
        $sql = "SELECT user_id FROM Year WHERE user_id = ? AND year_start = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$id, $year]);

        return $statement->fetch(PDO::FETCH_ASSOC)["user_id"] != NULL;
    }

    /**
     * Returns true if password is correct for the given user
     *
     * @param int $userId The user's id
     * @param string $userPassword The user's password
     * @return bool True if password is corret for given user
     * @author Keller Flint
     */
    function confirmPassword($userId, $userPassword)
    {
        var_dump($userId);
        var_dump($userPassword);

        $sql = "SELECT user_id FROM User WHERE user_id = ? AND user_password = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$userId, $userPassword]);

        $result = $statement->fetch(PDO::FETCH_ASSOC)["user_id"];

        return $result == $userId;
    }

    /**
     * Updates the password for the given user
     *
     * @param int $userId The user's id
     * @param string $userPassword The new password
     * @author Keller Flint
     */
    function updatePassword($userId, $userPassword)
    {
        $sql = "UPDATE User SET user_password = ? WHERE user_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$userPassword, $userId]);
    }

    /* START OF EDIT FUNCTIONS */

    /**
     * Returns all data for the given item
     *
     * @param int $itemId The id of the item
     * @return array data for the given item
     * @author Keller Flint
     */
    function getItem($itemId)
    {
        $sql = "SELECT * FROM Item WHERE item_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$itemId]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Returns all state data for the given item
     *
     * @param int $itemId The id of the item
     * @return array All state data for the given item
     * @author
     */
    function getStates($itemId)
    {
        $sql = "SELECT * FROM State WHERE item_id = ? ORDER BY state_order ASC";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$itemId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns highest order of the states for an item
     *
     * @param int $itemId The id of the item
     * @return int The highest order of the states for an item
     * @author Keller Flint
     */
    function getMaxState($itemId)
    {
        $sql = "SELECT MAX(state_order) AS max FROM State WHERE item_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$itemId]);
        $result = $statement->fetch(PDO::FETCH_ASSOC)['max'];
        if (!$result) {
            $result = 0;
        }
        return $result;
    }

    /**
     * Updates data for the given item
     *
     * @param int $itemId Id of the item
     * @param string $itemName The item's name
     * @param string $itemType The item's type
     * @author Keller Flint
     */
    function updateItem($itemId, $itemName, $itemType)
    {
        $sql = "UPDATE Item SET item_name = ?, item_type = ? WHERE item_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$itemName, $itemType, $itemId]);
    }

    /**
     * Updates data for the given state
     *
     * @param int $stateId Id of the state
     * @param string $stateName The state's name
     * @param string $stateSetBy Who the state is set by
     * @param string $stateText The description text for the state
     * @param string $stateIsDone If this state counts as done for the item
     * @author Keller Flint
     */
    function updateState($stateId, $stateName, $stateSetBy, $stateText, $stateIsDone)
    {
        $sql = "UPDATE State SET state_name = ?, state_set_by= ?, state_text = ?, state_is_done = ? WHERE state_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$stateName, $stateSetBy, $stateText, $stateIsDone, $stateId]);
    }


    /**
     * Moves a state up or down relative to other states in the same item
     *
     * @param int $stateId The id of the state being moved
     * @param int $direction The direction the state is being moved (-1 for up, 1 for down)
     * @author Keller Flint
     */
    function updateStateOrder($stateId, $direction)
    {
        if ($direction != 1 && $direction != -1) {
            throw new InvalidArgumentException("direction must be either 1 or -1");
        }

        $thisState = $this->getState($stateId);
        $otherState = $this->getStateByOrder($thisState["item_id"], $thisState["state_order"] + $direction);

        $sql = "UPDATE State SET state_order = ? WHERE state_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$thisState["state_order"], $otherState["state_id"]]);
        $statement->execute([$otherState["state_order"], $thisState["state_id"]]);
    }

    /**
     * Get a state by it's order in an item
     *
     * @param int $itemId The id of the item the state is associated with
     * @param int $order The order of the state in the item
     * @return array The state's data
     * @author Keller Flint
     */
    function getStateByOrder($itemId, $order)
    {
        $sql = "SELECT * FROM State WHERE item_id = ? AND state_order = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$itemId, $order]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get a state by it's id
     *
     * @param int $stateId The id of the state
     * @return array The state's data
     * @author Keller Flint
     */
    function getState($stateId)
    {
        $sql = "SELECT * FROM State WHERE state_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$stateId]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Returns the item id for a given state
     *
     * @param int $stateId The id of the state
     * @return int The item's id
     * @author Keller Flint
     */
    function getItemByState($stateId)
    {
        $sql = "SELECT item_id FROM State WHERE state_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$stateId]);
        return $statement->fetch(PDO::FETCH_ASSOC)["item_id"];
    }

    /**
     * Returns the number of states for an item
     *
     * @param int $itemId The item's id
     * @param string $state The state to count
     * @return int The number of default states for an item
     * @author Keller Flint
     */
    function getStateCount($itemId, $state)
    {
        $sql = "";
        if ($state == "all") {
            $sql = "SELECT count(state_set_by) AS count FROM State WHERE item_id = ?";
            $statement = $this->_dbh->prepare($sql);
            $statement->execute([$itemId]);
            return $statement->fetch(PDO::FETCH_ASSOC)["count"];
        } else {
            $sql = "SELECT count(state_set_by) AS count FROM State WHERE item_id = ? AND state_set_by = ?";
            $statement = $this->_dbh->prepare($sql);
            $statement->execute([$itemId, $state]);
            return $statement->fetch(PDO::FETCH_ASSOC)["count"];
        }
    }

    /**
     * Adds a new state
     *
     * @param int $itemId The item the state is associated with
     * @param string $stateName The state's name
     * @param string $stateSetBy Who the state is set by
     * @param string $stateText The description text for the state
     * @param string $stateIsDone If this state counts as done for the item
     * @return int New state id
     * @author Keller Flint
     */
    function addState($itemId, $stateName, $stateSetBy, $stateText, $stateIsDone)
    {
        $order = $this->getMaxState($itemId) + 1;
        $sql = "INSERT INTO State VALUES(DEFAULT, ? , ?, ?, ?, $order , ?)";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$itemId, $stateName, $stateSetBy, $stateText, $stateIsDone]);
        return $this->_dbh->lastInsertId();
    }

    /**
     * Deletes a state and sets all of its associations to the item's default state
     *
     * @param int $stateId The id of the state to be deleted
     * @author Keller Flint
     */
    function deleteState($stateId)
    {
        // get the item id
        $itemId = $this->getItemByState($stateId);
        // get item's default state (that is not this state(for the case of this one being deleted))
        $defaultStateId = $this->getDefaultState($itemId, $stateId);
        // where item id = id and state item-id = state-id in ItemTutorYear, set state_id = default_state_id
        $sql = "UPDATE ItemTutorYear SET state_id = ? WHERE item_id = ? AND state_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$defaultStateId, $itemId, $stateId]);
        // delete the state
        $sql = "DELETE FROM State WHERE state_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$stateId]);
        // reorder the remaining states
        $this->orderStates($itemId);
    }

    /**
     * Private helper method for deleting states
     *
     * @param int $itemId The item to get the default state of
     * @param int $stateId The state id to exclude
     * @return int The state_id of the default state
     * @author Keller Flint
     */
    private
    function getDefaultState($itemId, $stateId)
    {
        $sql = "SELECT state_id FROM State WHERE item_id = ? AND state_id != ? AND state_set_by = 'default'";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$itemId, $stateId]);
        return $statement->fetch(PDO::FETCH_ASSOC)["state_id"];
    }

    /**
     * Private helper method for reordering states when one state gets deleted
     *
     * @param int $itemId The id of the item being reordered
     * @author Keller Flint
     */
    private
    function orderStates($itemId)
    {
        $sql = "SELECT state_id FROM State WHERE item_id = ? ORDER BY state_order ASC";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$itemId]);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        $x = 1;
        foreach ($results as $result) {
            $sql = "UPDATE State SET state_order = $x WHERE state_id = " . $result["state_id"];
            $x = $x + 1;
            $statement = $this->_dbh->prepare($sql);
            $statement->execute();
        }
    }

    /**
     * Returns all items and all state data for each item in an associative array
     *
     * @return array all item and item state data
     * @author Keller Flint
     */
    function getItems()
    {
        $sql = "SELECT * FROM Item";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute();

        $items = $statement->fetchAll(PDO::FETCH_ASSOC);

        $itemData = array();

        foreach ($items as $item) {
            $itemKey = $item["item_id"];
            $itemData[$itemKey] = $item;
            $itemData[$itemKey]["states"] = $this->getStates($itemKey);
        }

        return $itemData;
    }

    /**
     * Creates a new item in the database
     *
     * @param string $itemName The name of the item
     * @param string $itemType The type of the item
     * @return int The id of the new item
     * @author Keller Flint
     *
     */
    function addItem($itemName, $itemType)
    {
        // Create the new item
        $sql = "INSERT INTO Item VALUES (DEFAULT, ?, ?, 0, NULL)";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$itemName, $itemType]);
        $itemId = $this->_dbh->lastInsertId();

        // Create the default state
        $stateId = $this->addState($itemId, "default", "default", "Autogenerated default state", 0);

        // Create entry for each ItemTutorYear
        $tutorYears = $this->getTutorYears();
        $sql = "INSERT INTO ItemTutorYear VALUES (?, ?, ?, NULL)";
        $statement = $this->_dbh->prepare($sql);

        foreach ($tutorYears as $tutorYear) {
            $statement->execute([$itemId, $tutorYear["tutorYear_id"], $stateId]);
        }

        return $itemId;
    }

    /**
     * Gets all tutor year ids
     *
     * @return array All tutor year ids
     * @author Keller Flint
     */
    function getTutorYears()
    {
        $sql = "SELECT tutorYear_id FROM TutorYear";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Deletes the given item
     *
     * @param int $itemId The item id to be deleted
     * @author Keller Flint
     */
    function deleteItem($itemId)
    {
        $sql = "DELETE FROM ItemTutorYear WHERE item_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$itemId]);

        $sql = "DELETE FROM State WHERE item_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$itemId]);

        $sql = "DELETE FROM Item WHERE item_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$itemId]);
    }

    /**
     * Gets the year of tutor by user id
     * @param int $user_id user's id
     * @return array of tutor year of current year if exist
     * @author  laxmi kandel
     */
    function getTutorYear($user_id)
    {
        $currentYear = $this->getCurrentYear();
        $sql = "SELECT tutorYear_year from TutorYear where user_id = ? and tutorYear_year = $currentYear";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$user_id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check if the given user_id is admin
     * @param int $user_id user's id
     * @return array of user_is_admin if exist
     * @author laxmi kandel
     */
    function checkAdmin($user_id)
    {
        $sql = "SELECT user_is_admin from User where user_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$user_id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * update the database to store the path of attachments
     * @param String $filePath name of the file/attachments
     * @param int $itemId id of the item to where file paths are going to be stored
     * @author Laxmi
     */
    function updateItemTable($filePath, $itemId)
    {
        $sql = "UPDATE Item SET item_file =?  WHERE  item_id=?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$filePath, $itemId]);
    }

    /**
     * update the database if the file is upload
     * @param int $uploadRequired value of checkbox
     * @param int $itemId id of the item
     * @author  Laxmi
     */
    function updateItemIsUpload($uploadRequired, $itemId)
    {
        $sql = "UPDATE Item SET item_is_upload =$uploadRequired   WHERE  item_id=?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$itemId]);
    }

    /**
     * Remove the file from the database
     * @param int $itemId item's id to be removed
     * @author Keller and Laxmi
     */
    function removeFile($itemId)
    {
        //TODO delete file in server
        $sql = "UPDATE Item SET item_file= NULL WHERE item_id =?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$itemId]);
    }

    /**
     * Gets the tutor upload files
     * @param string $currentYear the current year
     * @param int $tutorId tutor year id
     * @return array Array of items uploaded by tutor in that specified year
     * @author Laxmi Kandel
     */
    function getItemTutor($currentYear, $tutorId)
    {
        $sql = "select itemTutorYear_file from tutors.ItemTutorYear
                inner join tutors.TutorYear on ItemTutorYear.tutorYear_id = TutorYear.tutorYear_id
                inner join tutors.Tutor on TutorYear.user_id = Tutor.user_id
                where ItemTutorYear.tutorYear_id = ?
                and Tutor.user_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$currentYear, $tutorId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get the yearId of the given tutor
     * @param int $userId tutor's id
     * @return array  Array of tutorYear_id of given user_id
     * @author Laxmi Kandel
     */
    function getYearId($userId)
    {
        $sql = "select tutorYear_id from tutors.TutorYear
                inner join tutors.Tutor on Tutor.user_id = TutorYear.user_id
                where Tutor.user_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$userId]);
        return $statement->fetch(PDO::FETCH_ASSOC)['tutorYear_id'];
    }

    /**
     * Delete the given tutor year data
     *
     * @param int $tutorYearId The tutorYearId of the tutor to be deleted
     * @author Keller Flint
     */
    function removeFromYear($tutorYearId)
    {
        // delete item data for tutorYear id
        $sql = "DELETE FROM ItemTutorYear WHERE tutorYear_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$tutorYearId]);

        // delete user data from tutorYear id
        $sql = "DELETE FROM TutorYear WHERE tutorYear_id = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$tutorYearId]);
    }

    /**
     * Get all files uploaded by tutor of specific year
     * @param int  $currentYear specified year
     * @return array Array of all files upload by tutors of specified year
     * @author Laxmi Kandel
     */
    function getAllTutorUploads($currentYear)
    {
        $sql = "SELECT ItemTutorYear.itemTutorYear_file FROM ItemTutorYear 
                inner join TutorYear on ItemTutorYear.tutorYear_id = TutorYear.tutorYear_id
                where tutorYear_year = ?";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$currentYear]);
        return ($statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /*----------------------------------------------------------------------------------*/
    /* Code for placement Project */

    function insertSchool($school){
        $sql = "INSERT INTO School VALUE(DEFAULT ,?);";

        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$school]);
    }

    function checkSchool($school){
        $sql = "SELECT * FROM School WHERE school_name = ?";

        $statement = $this->_dbh->prepare($sql);
        $statement->execute([$school]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    function getAllSchools(){
        $sql = "SELECT * FROM School ";

        $statement = $this->_dbh->prepare($sql);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    function getJobsForSchool($school){

    }


}
