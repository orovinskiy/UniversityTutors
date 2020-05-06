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
        $this->_f3 = $f3;
        $this->_db = $db;
        $this->_val = new Validate($db);
    }

    /**
     * Logic and rendering for tutors page
     * @param string $year The year to load tutors data for
     * @author Keller Flint
     */
    function tutorsPage($year)
    {

        // Get current year
        $currentYear = $this->_db->getCurrentYear();

        // Get tutor data for current year
        $tutorsData = $this->_db->getTutors($year);

        // Set values for select dropdowns
        $this->_f3->set("backgroundOptions", array("none" => "Not Done", "sent" => "Sent", "clear" => "Clear", "flag" => "Flag"));
        $this->_f3->set("referenceOptions", array("none" => "Not Done", "incomplete" => "In Progress", "clear" => "Clear", "flag" => "Flag"));
        $this->_f3->set("ADPOptions", array("none" => "Not Sent", "invited" => "Invited", "registered" => "Registered"));
        $this->_f3->set("i9Options", array("none" => "Not Sent", "tutor" => "Tutor Done", "admin" => "Admin Done"));
        $this->_f3->set("year", $year);
        $this->_f3->set("currentYear", $currentYear);

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
        if (isset($_POST["yearId"])) {
            $this->_db->updateYearData($_POST["column"], $_POST["value"], $_POST["yearId"]);
        } else if (isset($_POST["email"])) {
            // TODO create function to generate and send email to tutor
            if ($this->_val->uniqueEmail($_POST["email"])) {
                echo $this->_db->addNewTutor($_POST["year"], $_POST["email"]);
            } else {
                echo "ERROR: Email already exists";
            }
        } else if (isset($_POST["delete"])) {
            $this->_db->deleteUser($_POST["user_id"]);
        } else if (isset($_POST["current_year"])) {
            $this->_db->setCurrentYear($_POST["current_year"]);
        } else if (isset($_POST["user_id"])) {
            $this->_db->importUser($_POST["user_id"]);
        }

    }

    /**
     * Ajax logic for checklist page
     * @author Oleg
     */
    function checklistAjax()
    {
        $this->_db->updateYearData($_POST['column'], $_POST['value'], $_POST['year']);
    }

    /**
     * View of all the required forms. Lets the user check of or uncheck a form if it has been
     * completed
     * @param int $param is the id of the user
     * @author oleg
     */
    function checklist($param)
    {
        //get the current year
        $currentYear = date('Y');

        $checkBoxes = $GLOBALS['db']->getTutorsChecklist($currentYear, $param['userId']);
        $checkBoxes = $checkBoxes[0];

        $checkBoxes['year_i9'] == 'none' ? $checkBoxes['year_i9'] = '0' : $checkBoxes['year_i9'] = '1';

        if($checkBoxes['year_ADP'] == 'invited'){
            $checkBoxes['year_ADP'] = '0';
        }
        if($checkBoxes['year_ADP'] == 'registered'){
            $checkBoxes['year_ADP'] = '1';
        }

        $this->_f3->set('yearID', $checkBoxes['year_id']);
        $this->_f3->set('userName', $checkBoxes['tutor_first'] . " " . $checkBoxes['tutor_last']);
        $this->_f3->set('checkboxes', array("ADP Registration" => array("Value" => $checkBoxes['year_ADP'],
            "Column" => "year_ADP", "id"=>"adp"),
            "Adult Sexual Misconduct" => array("Value" => $checkBoxes['year_sexual_misconduct'],
                "Column" => "year_sexual_misconduct", "id"=>"sex-miscond"),
            "Affirmations and Disclosures" => array("Value" => $checkBoxes['year_affirmation_disclosures'],
                "Column" => "year_affirmation_disclosures", "id"=>"affirm-disclose"),
            "Handbook Verification" => array("Value" => $checkBoxes['year_handbook_verification'],
                "Column" => "year_handbook_verification", "id"=>"handbook-verify"),
            "I-9" => array("Value" => $checkBoxes['year_i9'], "Column" => "year_i9", "id"=>"i9"),
            "Offer Letter" => array("Value" => $checkBoxes['year_offer_letter'],
                "Column" => "year_offer_letter", "id"=>"offer-letter"),
            "Orientation RSVP" => array("Value" => $checkBoxes['year_orientation'],
                "Column" => "year_orientation", "id"=>"orientation"),
            "W4" => array("Value" => $checkBoxes['year_w4'], "Column" => "year_w4", "id"=>"w4")));


        $view = new Template();
        echo $view->render("views/checklist.html");
    }

    /**
     * Render page for form
     * @param int $param user's id
     * @author laxmi
     */

    function formPage($param)
    {
        global $dirName;
        //retrieving data form database
        $this->_f3->set("firstName", $this->_db->getTutorById($param["id"])["tutor_first"]);
        $this->_f3->set("lastName", $this->_db->getTutorById($param["id"])["tutor_last"]);
        $this->_f3->set("phone", $this->_db->getTutorById($param["id"])["tutor_phone"]);
        $this->_f3->set("ssn", $this->_db->getTutorById($param["id"])["tutor_ssn"]);
        $this->_f3->set("bioText", $this->_db->getTutorById($param["id"])["tutor_bio"]);
        $this->_f3->set("email", $this->_db->getUserById($param["id"])["user_email"]);
        //get the image form the database
        $this->_f3->set("image", $this->_db->getTutorById($param["id"])["tutor_image"]);

        //when request is sent
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            //Storing data in hive variables
            $this->_f3->set('firstName', $_POST['firstName']);
            $this->_f3->set('lastName', $_POST['lastName']);
            $this->_f3->set('email', $_POST['email']);
            $this->_f3->set('phone', $_POST['phone']);
            $this->_f3->set('ssn', $_POST['ssn']);
            $this->_f3->set('bioText', $_POST['bio']);

            //store randomly generated string for user input image
            $randomFileName = $this->generateRandomString() . "." . explode("/", $_FILES['fileToUpload']['type'])[1];

            //if the user input in form is valid
            if ($this->_val->validForm($_FILES['fileToUpload'], $randomFileName, $param["id"])) {
                //check param id
                if ($param["id"] != 0) {
                    $this->_db->updateTutor($param["id"], trim($_POST['firstName']), trim($_POST['lastName']),
                        $_POST['phone'], $_POST['ssn'],trim($_POST['bio']));
                    $this->_db->updateEmail($param["id"], trim($_POST['email']));

                    //if file name  is not empty save  file to uploads dir and store it in database
                    if (!empty($_FILES['fileToUpload']['name'])) {
                        move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $dirName . $randomFileName);
                        $this->_db->uploadTutorImage($randomFileName, $param["id"]);
                    }
                }
                $this->_f3->reroute("/checklist/" . $param["id"]);
            }
        }
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