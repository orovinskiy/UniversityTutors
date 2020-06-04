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
    private $_mail;

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
     * @param $db Object The database object
     */
    function __construct($f3, $db)
    {
        $this->_f3 = $f3;
        $this->_db = $db;
        $this->_val = new Validate($db);
        //instantiate new mail class
        $this->_mail = new Mail();

    }

    /**
     * Logic and rendering for tutors page
     * @param array $param The parameters passed to the route
     * @author Keller Flint
     */
    function tutorsPage($param)
    {
        //checking to see if user is logged in. If not logged in, will redirect to login page
        $this->isLoggedIn($_SESSION['user_id']);
        //if non admin tires to access tutorsPage it redirects them to their log in page
        if ($this->_db->checkAdmin($_SESSION['user_id'])['user_is_admin'] == 0) {
            $this->redirects();
        }
        //This is for building up a navbar
        $this->navBuilder(array('Admin Manager' => '../admin', 'Logout' => '../logout'),
            array('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
                'https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css',
                '../styles/tutorsStyle.css'), 'Tutors');

        // Get current year
        $currentYear = $this->_db->getCurrentYear();

        // Get tutor data for current year
        $tableData = $this->_db->getTutors($param["year"]);

        // Get headers
        $items = $this->_db->getItems();

        $this->_f3->set("year", $param["year"]);
        $this->_f3->set("currentYear", $currentYear);

        // Store tutor data is hive
        $this->_f3->set("tableData", $tableData);
        $this->_f3->set("items", $items);

        //Store Default email data into hive
        $this->_f3->set("subject", $this->_mail->getSubject());
        $this->_f3->set("body", $this->_mail->getBody());
        $this->_f3->set("attachment", $this->_mail->getDefaultAttachments());
        $view = new Template();
        echo $view->render("views/tutors.html");
    }

    /**
     * Ajax logic for tutors page
     * @throws phpmailerException
     * @author Keller Flint
     */
    function tutorsAjax()
    {
        if (isset($_POST["stateId"])) {
            $this->_db->updateItemTutorYearSelect($_POST["itemId"], $_POST["tutorYearId"], $_POST["stateId"]);
        } else if (isset($_POST["stateOrder"])) {
            $this->_db->updateItemTutorYearCheck($_POST["itemId"], $_POST["tutorYearId"], $_POST["stateOrder"]);
        } else if (isset($_POST["email"])) {
            if ($this->_val->uniqueEmail($_POST["email"]) && $this->_val->validEmail($_POST["email"])) {
                //creating temp password and add new tutor
                $tempPassword = $this->generateRandomString();
                $this->_db->addNewTutor($_POST["year"], $_POST["email"], $tempPassword);
                //send email
                $success = $this->sendEmail($_POST["email"], $tempPassword);
                //checking to see if email was sent successfully
                if (!$success) {
                    echo "Sending of email was unsuccessful";
                } else {
                    echo "Email successfully sent to " . $_POST["email"];
                }
            } else {
                //checking for duplicate email
                if (!$this->_val->uniqueEmail($_POST["email"])) {
                    echo "Duplicate email address";
                } else {
                    echo "Invalid email address";
                }
            }
        } else if (isset($_POST["delete"])) {
            $this->_db->deleteUser($_POST["user_id"]);
        } else if (isset($_POST["current_year"])) {
            $this->_db->setCurrentYear($_POST["current_year"]);
        } else if (isset($_POST["user_id"])) {
            $this->_db->importUser($_POST["user_id"]);
        } else if (isset($_POST['subject']) && isset($_POST['body'])) { //updating default email info
            $this->_mail->setSubject($_POST['subject']);
            $this->_mail->setBody($_POST['body']);
        } else if (isset($_FILES['file'])) {
            // file name
            $filename = $_FILES['file']['name'];
            // Location
            $location = 'uploads/' . $filename;
            // Upload file
            move_uploaded_file($_FILES['file']['tmp_name'], $location);
            $response = $this->_mail->setDefaultAttachments($location);
            echo $response;
        } else if (isset($_POST['fileToDelete'])) {
            $test = $this->_mail->deleteDefaultAttachment($_POST['fileToDelete']);
            //echo var_dump($test);
        }
    }

    /**
     * Ajax logic for checklist page
     * @author Oleg
     */
    function checklistAjax()
    {
        var_dump($_POST);
        $stateID = $this->_db->getNextStateID($_POST['item'], $_POST['value'], $_POST['prev']);
        $this->_db->updateStateOfTutor($stateID, $_POST['item'], $_POST['user']);
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
        $this->isLoggedIn($param['userId']);
        if ($this->_db->checkAdmin($_SESSION['user_id'])['user_is_admin'] == 1) {
            $this->redirects();
        }

        //this is for building up a navbar
        $this->navBuilder(array('Profile' => '../form/' . $param['userId'], 'Logout' => '../logout'), array('../styles/checklist.css')
            , 'Tutor Checklist');

        //get the current year
        $currentYear = $this->_db->getCurrentYear();

        $checkBoxes = $GLOBALS['db']->getTutorsChecklist($currentYear, $param['userId']);


        $this->_f3->set("currentYear", $this->_db->getCurrentYear());
        $this->_f3->set('yearID', $checkBoxes['year_id']);
        $this->_f3->set('userID', $param['userId']);
        $this->_f3->set('checklist', $checkBoxes);
        $this->_f3->set('db', $this->_db);
        $this->_f3->set('userName', $this->_db->getTutorName($param['userId']));


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
        $this->isLoggedIn($param['id']);
        if ($this->_db->checkAdmin($_SESSION['user_id'])['user_is_admin'] == 1) {
            $this->redirects();
        }

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
            $imageFileName = $this->nameForImage($_POST['firstName'], $param["id"]) . "." . explode("/", $_FILES['fileToUpload']['type'])[1];
            //if the user input in form is valid
            if ($this->_val->validForm($_FILES['fileToUpload'],
                $param["id"], $_POST['bio'])) {
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
                        move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $dirName . $imageFileName);
                        $this->_db->uploadTutorImage($imageFileName, $param["id"]);
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
                //check if user in the currently running year or if it is admin
                if ($this->_db->getCurrentYear() == $this->_db->getTutorYear($userLogin['user_id'])['tutorYear_year']
                    || $this->_db->checkAdmin($userLogin['user_id'])['user_is_admin'] == 1) {
                    //instantiate new user object
                    $user = new User($userLogin['user_id'], $userLogin['user_email'], $userLogin['user_is_admin']);
                    //saving object to session
                    $_SESSION['user'] = $user;
                    //setting session login to true
                    //$_SESSION['user'] = true;

                    //call redirects method to redirect to correct page
                    $this->redirects();
                } else {
                    //User is not in current year list
                    $this->_f3->set('loginError', "Please Contact admin you are not enrolled as tutor for current year");
                }
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
            $this->_f3->reroute("/tutors/$year");


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
     * @param int $param
     * @author Dallas Sloan
     */
    private function isLoggedIn($param)
    {
        if (!isset($_SESSION['user'])) {
            $this->_f3->reroute('/login');
        }
        $_SESSION['user_id'] = $_SESSION['user']->getUserID();

        if ($_SESSION['user_id'] != $param) {
            $this->_f3->reroute('/login');
        }
    }

    /**
     * Rendering and logic for admin management page
     * @author Keller Flint
     */
    function adminPage()
    {
        // TODO check if logged in user is admin DONE
        //checking to see if user is logged in. If not logged in, will redirect to login page
        $this->isLoggedIn($_SESSION['user_id']); //comment to remove the login requirement
        //if non admin tries to excess admin page info it will redirects them to their login page
        if ($this->_db->checkAdmin($_SESSION['user_id'])['user_is_admin'] == 0) {
            $this->redirects();
        }
        $this->navBuilder(array('Tutors Info' => '../tutors/' . $this->_db->getCurrentYear(), 'Logout' => 'logout'),
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
        //checking to see if user is logged in. If not logged in, will redirect to login page
        $this->isLoggedIn($_SESSION['user_id']);


        //This is the navbar generating
        $this->navBuilder(array('Tutors Info' => '../tutors/' . $this->_db->getCurrentYear(),
            'Admin Manager' => '../admin', 'Logout' => '../logout'), '', 'Tutor');

        $tutor = $this->_db->getTutorById($param["id"]);

        $this->_f3->set("tutor", $tutor);
        $this->_f3->set("user", $this->_db->getUserById($param["id"]));

        $this->_f3->set("ssn", $this->decryption($tutor["tutor_ssn"]));

        //let admin download the tutor's image
        if (isset($_GET['download'])) {
            $tutorImage = $this->_db->getTutorById($param["id"])["tutor_image"];//gets the image name from db
//            echo $tutorImage;
            $filePath = 'uploads/' . $tutorImage;
//            echo $filePath; //gets the file path
            if (file_exists($filePath)) {
                //description of file/content
                header('Content-Description: File Transfer');

                //pull all types of file
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . basename($filePath));
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filePath));
                readfile($filePath);
                exit;
            }

        }

        $view = new Template();
        echo $view->render('views/tutorInfo.html');
    }

    /**
     * Function that creates and sends an email to specified recipient
     * @param String $to email address of email recipient
     * @param String $tempPassword randomly generated password
     * @return bool returns true if email was sent successfully false if not sent successfully
     * @throws phpmailerException
     * @author Dallas Sloan
     */
    function sendEmail($to, $tempPassword)
    {
        $loginLink = "<a href='http://kold-tutors.greenriverdev.com/UniversityTutors/login'>Login Here</a>";
        //creating variables for input params for email
        $from = 'universitytutors@kold-tutors.greenriverdev.com';
        $fromName = "University Tutors Admin";
        $loginBody = "<p>Login
                Information:</p>" . "<p>Username: " . $to . "</p>" . "<p>Temporary Password: " . $tempPassword . "</p>" .
            "<p>$loginLink</p>";
        $success = $this->_mail->smtpmailer($to, $from, $fromName, $loginBody);
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
        if ($ssn == '') {
            return $ssn;
        }
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
        //checking to see if user is logged in. If not logged in, will redirect to login page
        $this->isLoggedIn($id);

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

    /**
     * Generate the image file name to be tutor's name and tutor's id
     * @param String $tutor_name the tutor name
     * @param int $user_id the tutor id
     * @return string name for upload image
     * @author  laxmi
     */
    function nameForImage($tutor_name, $user_id)
    {
        return $tutor_name . "-" . $user_id;
    }

    /** Page to edit table items
     *
     * @param int $itemId The id of the item being edited
     * @author Keller Flint
     */
    function editPage($itemId)
    {

        // Nav builder
        $this->navBuilder(array('Tutors Info' => '../tutors/' . $this->_db->getCurrentYear(),
            'Admin Manager' => '../admin', 'Logout' => '../logout'), '', 'Column Edit');
        global $dirName;

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            if (isset($_POST['remove'])) {
                //remove the file
                $this->_db->removeFile($itemId);
            }
            // Save Item
            if (isset($_POST["itemSave"])) {
                $uploadRequired = 0;
                if (isset($_POST['uploadRequired'])) {
                    $uploadRequired = 1;
                }
                $this->_db->updateItemIsUpload($uploadRequired, $itemId);

                // var_dump($_FILES);
                if (isset($_FILES['fileToUpload'])) {
                    if (!empty($_FILES['fileToUpload']['name'])) {
                        $fileName = $this->nameForFile($_POST['itemName'], $itemId) . "." . explode("/", $_FILES['fileToUpload']['type'])[1];
                        move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $dirName . $fileName);
                        $this->_db->updateItemTable($fileName, $itemId);
                    }
                }
                if ($this->_val->validateItem($_POST["itemName"])) {
                    $this->_db->updateItem($_POST["itemId"], $_POST["itemName"], $_POST["itemType"]);
                } else {
                    $this->_f3->set("errors", $this->_val->getErrors());
                }

                // Creating a new item
                if ($itemId == 0) {
                    if ($this->_val->validateItem($_POST["itemName"])) {
                        $itemId = $this->_db->addItem($_POST["itemName"], $_POST["itemType"]);
                        $this->_f3->reroute("edit/$itemId");
                    } else {
                        $this->_f3->set("errors", $this->_val->getErrors());
                    }
                }
            }
        }


        // Save State
        if (isset($_POST["stateSave"])) {

            // Changing isDone to an int
            if (!isset($_POST["stateIsDone"])) {
                $_POST["stateIsDone"] = 0;
            } else {
                $_POST["stateIsDone"] = 1;
            }

            // Update existing state
            if ($_POST["stateId"] != 0) {
                if ($this->_val->validateState($_POST["stateId"], $_POST["stateName"], $_POST["stateText"])) {
                    $this->_db->updateState($_POST["stateId"], $_POST["stateName"], $_POST["stateSetBy"], $_POST["stateText"], $_POST["stateIsDone"]);
                } else {
                    $this->_f3->set("errors", $this->_val->getErrors());
                }
                // Add new state
            } else {
                if ($this->_val->validateState($_POST["stateId"], $_POST["stateName"], $_POST["stateText"])) {
                    $this->_db->addState($itemId, $_POST["stateName"], $_POST["stateSetBy"], $_POST["stateText"], $_POST["stateIsDone"]);
                } else {
                    $this->_f3->set("errors", $this->_val->getErrors());
                    $this->_f3->set("stateNameNew", $_POST["stateName"]);
                    $this->_f3->set("stateSetByNew", $_POST["stateSetBy"]);
                    $this->_f3->set("stateTextNew", $_POST["stateText"]);
                    $this->_f3->set("stateIsDoneNew", $_POST["stateIsDone"]);
                }
            }

        }

        // Move Up
        if (isset($_POST["moveUp"])) {
            $this->_db->updateStateOrder($_POST["stateId"], -1);
        }

        // Move Down
        if (isset($_POST["moveDown"])) {
            $this->_db->updateStateOrder($_POST["stateId"], 1);
        }

        // Check for default state warnings
        $defaults = $this->_db->getStateCount($itemId, "default");
        if ($defaults > 1) {
            $this->_f3->set("defaultWarning", "You have more than one default state set! Please have only one default state for this item. Having more than one default states can result in errors displaying the item.");
        } else if ($defaults < 1) {
            $this->_f3->set("defaultWarning", "You do not have a default state set! Please have exactly one default state for this item. Having no default states can result in errors displaying the item.");
        }

        // Showing error messages for delete state
        if (isset($_POST["stateDelete"])) {
            if ($defaults < 1) {
                // There must be at least one default state set
                $this->_f3->set("defaultError", "You must have a default state set before deleting this state!");
            } else if ($defaults == 1 && $_POST["stateSetBy"] == "default") {
                // Error for if the user is trying to delete the only default state
                $this->_f3->set("defaultError", "You may not delete the only default state! Please add another default state before deleting this one.");
            } else if ($defaults == 2 && $_POST["stateSetBy"] != "default") {
                // Error for if there are two default states set and it is ambiguous which one should be set as the new default
                $this->_f3->set("defaultError", "You must have only one default state set before deleting this state!");
            } else if ($defaults > 2) {
                // Error message for 3 or more default states which always makes it ambiguous which one should be set as the new default
                $this->_f3->set("defaultError", "You may not have more than 2 default states set before deleting a state!");
            } else {
                // Delete the state
                $this->_db->deleteState($_POST["stateId"]);
            }
        }

        if (isset($_POST["itemDelete"])) {
            $this->_db->deleteItem($itemId);
            $currentYear = $this->_db->getCurrentYear();
            $this->_f3->reroute("tutors/$currentYear");
        }


        $this->_f3->set("item", $this->_db->getItem($itemId));
        $this->_f3->set("stateData", $this->_db->getStates($itemId));
        $this->_f3->set("maxState", $this->_db->getMaxState($itemId));

        $view = new Template();
        echo $view->render('views/itemEdit.html');
    }

    /**
     * file name for avoiding naming convention
     * @param string $itemName name of the item
     * @param int $itemId id of the item
     * @return string name for the file
     * @author laxmi
     */
    function nameForFile($itemName, $itemId)
    {
        return $itemName . "-" . $itemId;
    }
}


