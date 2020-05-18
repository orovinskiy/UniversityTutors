<?php

/**
 * Controller logic for viewing pages and using the site.
 * @author Keller Flint
 */

//including the mail.php file to enable emailing
require_once("mail.php");

class Controller
{
    private $_f3; //router
    private $_db;
    private $_val;

    /**
     * This function gets the info to display a correct navbar
     * @param array $link array of links to get to different pages
     * @param array $style array of stylesheet links to connect the correct one
     * @param string $title This will be the title of the page
     * @author Oleg
     */
    private function navBuilder($link, $style, $title)
    {
        $this->_f3->set('link', $link);
        $this->_f3->set('style', $style);
        $this->_f3->set('title', $title);
    }

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
     * @param array $param The parameters passed to the route
     * @author Keller Flint
     */
    function tutorsPage($param)
    {
        //checking to see if user is logged in. If not logged in, will redirect to login page
        //$this->isLoggedIn(); //comment to remove the login requirement

        //This is for building up a navbar
        $this->navBuilder(array('Admin Manager' => '../admin', 'Logout' => '../logout'),
            array('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
                'https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css',
                '../styles/tutorsStyle.css'), 'Tutors');

        // Get current year
        $currentYear = $this->_db->getCurrentYear();

        // Get tutor data for current year
        $tutorsData = $this->_db->getTutors($param["year"], $param["status"]);

        // Set values for select dropdowns
        $this->_f3->set("backgroundOptions", array("none" => "Not Done", "sent" => "Sent", "clear" => "Clear", "flag" => "Flag"));
        $this->_f3->set("referenceOptions", array("none" => "Not Done", "incomplete" => "In Progress", "clear" => "Clear", "flag" => "Flag"));
        $this->_f3->set("ADPOptions", array("none" => "Not Sent", "invited" => "Invited", "registered" => "Registered"));
        $this->_f3->set("i9Options", array("none" => "Not Sent", "tutor" => "Tutor Done", "admin" => "Admin Done"));
        $this->_f3->set("SPSOptions", array("none" => "Not Done", "tutor" => "Tutor Done", "admin" => "Admin Done"));
        $this->_f3->set("year", $param["year"]);
        $this->_f3->set("currentYear", $currentYear);
        $this->_f3->set("status", $param["status"]);

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
            // TODO create function to generate and send email to tutor DONE!!
            if ($this->_val->uniqueEmail($_POST["email"]) && $this->_val->validEmail($_POST["email"])) {
                //send email
                $success = $this->sendEmail($_POST["email"]);
                //checking to see if email was sent successfully
                if (!$success) {
                    echo "Sending of email was unsuccessful";
                } else {
                    echo "Email successfully sent to " . $_POST["email"];
                }
                $this->_db->addNewTutor($_POST["year"], $_POST["email"]);
            } else {
                echo "Invalid email address";
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
        //checking to see if user is logged in. If not logged in, will redirect to login page
        //$this->isLoggedIn(); //comment to remove the login requirement

        //this is for building up a navbar
        $this->navBuilder(array('Profile' => '../form/' . $param['userId'], 'Logout' => '../logout'), array('../styles/checklist.css')
            , 'Tutor Checklist');

        //get the current year
        $currentYear = $this->_db->getCurrentYear();

        $checkBoxes = $GLOBALS['db']->getTutorsChecklist($currentYear, $param['userId']);
        $checkBoxes = $checkBoxes[0];

        $checkBoxes['year_i9'] == 'none' ? $checkBoxes['year_i9'] = '0' : $checkBoxes['year_i9'] = '1';
        empty($checkBoxes['tutor_image']) ? $checkBoxes['tutor_image'] = 'formEmpty' : $checkBoxes['tutor_image'] = 'formFull';
        empty($checkBoxes['tutor_bio']) ? $checkBoxes['tutor_bio'] = 'formEmpty' : $checkBoxes['tutor_bio'] = 'formFull';
        $checkBoxes['year_SPS'] == 'none' ? $checkBoxes['year_SPS'] = '0' : $checkBoxes['year_SPS'] = '1';

        if ($checkBoxes['year_ADP'] == 'invited') {
            $checkBoxes['year_ADP'] = '0';
        }
        if ($checkBoxes['year_ADP'] == 'registered') {
            $checkBoxes['year_ADP'] = '1';
        }

        $this->_f3->set("currentYear", $this->_db->getCurrentYear());
        $this->_f3->set('yearID', $checkBoxes['year_id']);
        $this->_f3->set('userID', $param['userId']);
        $this->_f3->set('userName', $checkBoxes['tutor_first'] . " " . $checkBoxes['tutor_last']);
        $this->_f3->set('checkboxes', array("ADP Registration" => array("Value" => $checkBoxes['year_ADP'],
            "Column" => "year_ADP", "id" => "adp"),
            "Adult Sexual Misconduct" => array("Value" => $checkBoxes['year_sexual_misconduct'],
                "Column" => "year_sexual_misconduct", "id" => "sex-miscond"),
            "Affirmations and Disclosures" => array("Value" => $checkBoxes['year_affirmation_disclosures'],
                "Column" => "year_affirmation_disclosures", "id" => "affirm-disclose"),
            "Handbook Verification" => array("Value" => $checkBoxes['year_handbook_verification'],
                "Column" => "year_handbook_verification", "id" => "handbook-verify"),
            "I-9" => array("Value" => $checkBoxes['year_i9'], "Column" => "year_i9", "id" => "i9"),
            "Offer Letter" => array("Value" => $checkBoxes['year_offer_letter'],
                "Column" => "year_offer_letter", "id" => "offer-letter"),
            "Orientation RSVP" => array("Value" => $checkBoxes['year_orientation'],
                "Column" => "year_orientation", "id" => "orientation"),
            "W4" => array("Value" => $checkBoxes['year_w4'], "Column" => "year_w4", "id" => "w4"),
            "SPS" => array("Value" => $checkBoxes['year_SPS'], "Column" => "year_SPS", "id" => "sps"),
            "Bio" => array("Value" => $checkBoxes['tutor_bio'], "Column" => "tutor_bio"),
            "Image" => array("Value" => $checkBoxes['tutor_image'], "Column" => "tutor_image")));


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
        //checking to see if user is logged in. If not logged in, will redirect to login page
        //$this->isLoggedIn(); //comment to remove the login requirement


        //this is for building up a navbar
        $this->navBuilder(array('Checklist' => '../checklist/' . $param["id"], 'Logout' => '../logout')
            , array('../styles/formStyle.css'), 'Onboarding Form');

        global $dirName;
        //retrieving data form database
        $this->_f3->set("firstName", $this->_db->getTutorById($param["id"])["tutor_first"]);
        $this->_f3->set("lastName", $this->_db->getTutorById($param["id"])["tutor_last"]);
        $this->_f3->set("phone", $this->_db->getTutorById($param["id"])["tutor_phone"]);
        $this->_f3->set("databaseSsn", ($this->_db->getTutorById($param["id"])["tutor_ssn"]));
        $this->_f3->set("bioText", $this->_db->getTutorById($param["id"])["tutor_bio"]);
        $this->_f3->set("email", $this->_db->getUserById($param["id"])["user_email"]);
        //get the image form the database
        $this->_f3->set("image", $this->_db->getTutorById($param["id"])["tutor_image"]);
        //getting ssn decrypting and only showing last 4 digits and setting hive
        $ssn = $this->decryption($this->_db->getTutorByID($param["id"])['tutor_ssn']);
        $masked = $this->ssnMask($ssn);
        //echo $masked;
        $this->_f3->set("ssn", $masked);

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
            if ($this->_val->validForm($_FILES['fileToUpload'],
                $randomFileName, $param["id"], $_POST['bio'])) {
                //check if user input ssn for update if not pass the database value
                if (empty($_POST['ssn']) || substr($_POST['ssn'], 0, 3) == "XXX") {
                    $_POST['ssn'] = $this->decryption($this->_f3->get("databaseSsn"));
                }
                //check param id
                if ($param["id"] != 0) {
                    $this->_db->updateTutor($param["id"], trim($_POST['firstName']), trim($_POST['lastName']),
                        $_POST['phone'], $this->encryption($_POST['ssn']), trim($_POST['bio']));
                    $this->_db->updateEmail($param["id"], trim($_POST['email']));

                    //if file name  is not empty save  file to uploads dir and store it in database
                    if (!empty($_FILES['fileToUpload']['name'])) {
                        move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $dirName . $randomFileName);
                        $this->_db->uploadTutorImage($randomFileName, $param["id"]);
                    }
                    $this->_f3->reroute("/checklist/" . $param["id"]);
                }
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


    /**function to encrypt the user input SSN
     * @param string $ssn takes user input SSN
     * @return string encrypted string if successful otherwise false
     * @author  Laxmi
     */
    function encryption($ssn)
    {
        //store cipher method
        $ciphering = "AES-128-CTR";

        // Use OpenSSl Encryption method
        $options = 0;

        // Non-NULL Initialization Vector for encryption
        $encryption_iv = '1234567891011121';

        // Use openssl_encrypt() function to encrypt the data
        return openssl_encrypt($ssn, $ciphering,
            ENCRYPTION_KEY, $options, $encryption_iv);
    }


    /**
     * function to decrypt user input SSN
     * @param string $encryptedSsn takes encrypted string
     * @return string decrypted string if successful otherwise false
     */
    function decryption($encryptedSsn)
    {
        //store cipher method
        $ciphering = "AES-128-CTR";

        // Use OpenSSl Encryption method
        $options = 0;

        // Non-NULL Initialization Vector for decryption
        $decryption_iv = '1234567891011121';

        // Use openssl_decrypt() function to decrypt the data
        return openssl_decrypt($encryptedSsn, $ciphering,
            DECRYPTION_KEY, $options, $decryption_iv);
    }

    /**
     * Function that handles the login page
     * @author Dallas Sloan
     */
    function login()
    {
        //var_dump($_SESSION);

        //checking to see if user if already logged in if so redirects to appropriate page
        if (isset($_SESSION['user'])) {
            $this->redirects();
        }
        //when form is posted
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //var_dump($_POST);
            //attempt to grab user info from login credentials
            $userLogin = $this->_db->login($_POST['username'], md5($_POST['password']));
            //check to see if valid input was found
            if (!empty($userLogin)) {
                //instantiate new user object
                $user = new User($userLogin['user_id'], $userLogin['user_email'], $userLogin['user_is_admin']);
                //saving object to session
                $_SESSION['user'] = $user;
                //setting session login to true
                //$_SESSION['user'] = true;

                //call redirects method to redirect to correct page
                $this->redirects();

            } else {
                //login info was not valid set error message
                $this->_f3->set('loginError', "Invalid Username and/or Password");
            }

        }
        $view = new Template();
        echo $view->render("views/login.html");
    }

    /**
     * Function that handles the logout process, which resets the session and reroutes to login page
     * @author Dallas Sloan
     */
    function logout()
    {
        //destroy session
        $_SESSION = array();

        //redirect to login page
        $this->_f3->reroute('/login');
    }

    /**
     *private method used to correctly redirect user upon logging into login page
     * @author Dallas Sloan
     */
    private function redirects()
    {
        //checking to see if user is an admin or tutor and redirecting accordingly
        if ($_SESSION['user']->getUserIsAdmin() == 1) {
            //get current year
            $year = $this->_db->getCurrentYear();
            $this->_f3->reroute("/tutors/$year&all");


        } else {
            //checking to see if user has filled out their basic info, if not redirected to form
            $userInfo = $this->_db->getTutorById($_SESSION['user']->getUserID());
            if ($userInfo['tutor_last'] == null) {
                $this->_f3->reroute("/form/" . $_SESSION['user']->getUserID());
            } else { //form has been filled out redirect to checklist
                $this->_f3->reroute("/checklist/" . $_SESSION['user']->getUserID());
            }
        }
    }

    /**
     * A function to check whether or not a user object has been set for the current session. If it is set the user
     * can proceed to the page they were attempting to access. If it's not set, they are redirected to the login screen
     * @author Dallas Sloan
     */
    private function isLoggedIn()
    {
        if (!isset($_SESSION['user'])) {
            $this->_f3->reroute('/login');
        }
    }

    /**
     * Rendering and logic for admin management page
     * @author Keller Flint
     */
    function adminPage()
    {
        // TODO check if logged in user is admin

        $this->navBuilder(array('Tutors Info' => '../tutors/' . $this->_db->getCurrentYear() . '&all', 'Logout' => 'logout'),
            '', 'Admin Manager');

        // add user
        if (isset($_POST["email"])) {
            if ($this->_val->uniqueEmail($_POST["email"]) && $this->_val->validEmail($_POST["email"])) {
                $this->_db->addAdmin($_POST["email"]);
            } else {
                $this->_f3->set("emailError", "Please enter a valid email address.");
            }
        }

        $this->_f3->set("admins", $this->_db->getAdmins());

        $view = new Template();
        echo $view->render('views/admin.html');
    }

    /**
     * Page displaying additional information about a tutor
     *
     * @param array $param Param array containing the id of the tutor we want to load
     * @author Keller Flint
     */
    function tutorInfoPage($param)
    {

        //This is the navbar generating
        $this->navBuilder(array('Tutors Info' => '../tutors/' . $this->_db->getCurrentYear() . '&all',
            'Admin Manager' => '../admin', 'Logout' => '../logout'), '', 'Tutor');

        $tutor = $this->_db->getTutorById($param["id"]);

        $this->_f3->set("tutor", $tutor);
        $this->_f3->set("user", $this->_db->getUserById($param["id"]));

        $this->_f3->set("ssn", $this->decryption($tutor["tutor_ssn"]));

        $view = new Template();
        echo $view->render('views/tutorInfo.html');
    }

    /**
     * Function that creates and sends an email to specified recipient
     * @param String $to email address of email recipient
     * @return bool returns true if email was sent successfully false if not sent successfully
     * @throws phpmailerException
     * @author Dallas Sloan
     */
    function sendEmail($to)
    {
        //creating variables for input params for email
        $from = 'universitytutors@kold-tutors.greenriverdev.com';
        $fromName = "University Tutors Admin";
        $subject = "Welcome New Tutor!";
        $body = "We will need to get with Liz to know exactly what she wants to send in the email";
        $success = smtpmailer($to, $from, $fromName, $subject, $body);
        return $success;
    }

    /**
     * Ajax logic for admin page
     *
     * @author Keller Flint
     */
    function adminAjax()
    {
        if (isset($_POST["delete_id"])) {

            // Admins cannot delete themselves
            if ($_SESSION['user']->getUserID() != $_POST["delete_id"]) {
                $this->_db->deleteUser($_POST["delete_id"]);
            } else {
                echo "Admins cannot delete themselves";
            }
        }
    }

    /**
     * Function to mask SSN by hiding all characters but last 4 digits
     * @param String $ssn to be masked
     * @return string ssn that has been masked
     * @author Dallas Sloan
     */
    private function ssnMask($ssn)
    {
        $lastFour = substr($ssn, -4);
        $masked = "XXX-XX-" . $lastFour;
        return $masked;
    }

    /**
     * Page to update a user's password
     *
     * @param int $id The user id
     * @author Keller Flint
     */
    function passwordPage($id)
    {
        if ($_SESSION['user']->getUserID() != $id) {
            $this->_f3->reroute("/login");
        }

        if (isset($_POST["new"])) {
            if ($this->_db->confirmPassword($id, md5($_POST["current"]))) {
                if ($_POST["new"] == $_POST["confirm"]) {
                    $this->_db->updatePassword($id, md5($_POST["new"]));
                    $this->redirects();
                } else {
                    $this->_f3->set("confirmError", "Passwords do not match");
                }
            } else {
                $this->_f3->set("passwordError", "Invalid login credentials");
            }
        }

        $view = new Template();
        echo $view->render('views/password.html');
    }

}