<?php

/**
 * Controller logic for viewing pages and using the site.
 * @author Keller Flint
 */

class Controller
{
    private $_f3; //router
    private $_db;
    private $_val;

    /**
     * Controller constructor
     * @param $f3 Object The fat free instance
     */
    function __construct($f3, $db)
    {
        $this->_val = new Validate();
        $this->_f3 = $f3;
        $this->_db = $db;
    }

    /**
     * Logic and rendering for tutors page
     * @param string $year The year to load tutors data for
     * @author Keller Flint
     */
    function tutorsPage($year)
    {

        // Get tutor data for current year
        $tutorsData = $this->_db->getTutors($year);

        // Set values for select dropdowns
        $this->_f3->set("backgroundOptions", array("none" => "Not Done", "sent" => "Sent", "clear" => "Clear", "flag" => "Flag"));
        $this->_f3->set("referenceOptions", array("none" => "Not Done", "incomplete" => "In Progress", "clear" => "Clear", "flag" => "Flag"));
        $this->_f3->set("ADPOptions", array("none" => "Not Sent", "invited" => "Invited", "registered" => "Registered"));
        $this->_f3->set("i9Options", array("none" => "Not Sent", "tutor" => "Tutor Done", "admin" => "Admin Done"));

        // Store tutor data is hive
        $this->_f3->set("tutorsData", $tutorsData);

        $view = new Template();
        echo $view->render("views/tutors.html");
    }

    /**
     * Ajax logic for tutors page
     * @author Keller Flint
     */
    function tutorsAjax()
    {
        $this->_db->updateYearData($_POST["column"], $_POST["value"], $_POST["yearId"]);
    }

    /**
     * Render page for form
     * @param $param user's id
     * @author laxmi
     */

    function formPage($param)
    {
        global $dirName;

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            //Storing data in hive variables
            $this->_f3->set('firstName', $_POST['firstName']);
            $this->_f3->set('lastName', $_POST['lastName']);
            $this->_f3->set('email', $_POST['email']);
            $this->_f3->set('phone', $_POST['phone']);
            $this->_f3->set('ssn', $_POST['ssn']);

            //store randomly generated string for user input image
            $randomFileName = $this->generateRandomString() . "." . explode("/", $_FILES['fileToUpload']['type'])[1];

            //if the user input in form is valid
            if ($this->_val->validForm($_FILES['fileToUpload'], $randomFileName)) {
                //check param id
                if ($param["id"] != 0) {
                    $this->_db->updateTutor($param["id"], $_POST['firstName'], $_POST['lastName'], $_POST['phone'], $_POST['ssn']);
                    $this->_db->updateEmail($param["id"], $_POST['email']);

                    //if file name  is not empty save  file to uploads dir and store it in database
                    if (!empty($_FILES['fileToUpload']['name'])) {
                        move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $dirName . $randomFileName);
                        $this->_db->uploadTutorImage($randomFileName, $param["id"]);
                    }
                }
            }
        }
        //retrieving data form database
        $this->_f3->set("tutor", $this->_db->getTutorById($param["id"]));
        $this->_f3->set("user", $this->_db->getUserById($param["id"]));
        $view = new Template();
        echo $view->render('views/form.html');
    }

    /**
     * function to generate random string for file name
     * @return string randomly generated string
     * @author laxmi
     */
    function generateRandomString()
    {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}