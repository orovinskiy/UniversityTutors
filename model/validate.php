<?php

/**
 * Validating onboarding form
 * @author Laxmi kandel
 * @author Keller Flint
 */
class Validate
{
    //stores an array of error messages
    private $_errors;
    private $_db;


    /**
     * Validator constructor.
     */
    public function __construct($db)
    {
        $this->_errors = array();
        $this->_db = $db;
    }

    /**
     * Returns the errors associative array
     *
     * @return array The array of errors
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Validating First Name
     * @param string $firstName tutor's first name
     * @return bool true if first name only has alphabets
     */
    function validFirstName($firstName)
    {
        return preg_match_all("/^[A-Za-z][A-Za-z\'\-]+([\ A-Za-z][A-Za-z\'\-]+)*/",
            $firstName,
            $out, PREG_PATTERN_ORDER);
    }

    /**
     * Validating Last Name
     * @param string $last tutor's last name
     * @return bool true if last name only has alphabets
     */
    function validLastName($last)
    {
        return preg_match_all("/^[A-Za-z][A-Za-z\'\-]+([\ A-Za-z][A-Za-z\'\-]+)*/",
            $last,
            $out, PREG_PATTERN_ORDER);
    }

    /**
     * Validating email
     * @param string $email tutor's email
     * @return bool true if email is valid
     */
    function validEmail($email)
    {
        $emailResult = false;
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailResult = true;
        }
        return $emailResult;
    }

    /**
     * Check to see if email is unique base on database
     * @param string $email user input email
     * @return bool true if email does not exist in database otherwise false
     */
    function uniqueEmail($email)
    {
        global $db;
        $uniqueEmail = false;
        if (empty($db->getEmail($email))) {
            $uniqueEmail = true;
        }
        return $uniqueEmail;
    }

    /**
     * Validating Phone Number
     * @param string $phone tutor's phone
     * @return bool true if phone is valid
     */
    function validPhone($phone)
    {
        $regex = "/\(\d{3}\) \d{3}-\d{4}/";
        $phoneResult = false;
        $phone = trim($phone);
        if (preg_match($regex, $phone)) {
            $phoneResult = true;
        }
        return $phoneResult;
    }

    /**
     * Validating SSN
     * @param string ssn tutor's ssn
     * @return bool true if ssn is valid
     */
    function validSsn($ssn)
    {
        global $f3;
        $regexSsn = "/^\d{3}-\d{2}-\d{4}$/";
        $ssnResult = false;
        $ssn = trim($ssn);
        //checking to see if SSN is masked, if so marking as valid
        if (substr($ssn, 0, 3) == "XXX") {
            return true;
        }

        if (preg_match($regexSsn, $ssn)) {
            $ssnResult = true;
        }
        if (!empty($f3->get("databaseSsn") and preg_match($regexSsn, $ssn))) {
            $ssnResult = true;
        }
        if (!empty($f3->get("databaseSsn")) and empty($ssn)) {
            $ssnResult = true;
        }
        return $ssnResult;
    }

    /**Validating all the required fields name, phone, email, ssn, image
     * @param string $file user's selected file for image
     * @param int $param user id
     * @param string $bio user's bio
     * @return bool true/false if all the required fields are valid/not valid
     * @author  Laxmi
     */
    function validForm($file, $param, $bio)
    {
        global $f3;
        global $db;
        $isValid = true;//flag


        //FIRST  NAME
        if (!$this->validFirstName($f3->get('firstName'))) {
            $isValid = false;
            $f3->set("errors['firstName']", "Please enter first name ");
        }
        //LAST NAME
        if (!$this->validLastName($f3->get('lastName'))) {
            $isValid = false;
            $f3->set("errors['lastName']", "Please enter last name ");
        }
        //PHONE
        if (!$this->validPhone($f3->get('phone'))) {
            $isValid = false;
            $f3->set("errors['phone']", "Please enter valid phone number ");
        }
        //EMAIL
        if (!$this->validEmail($f3->get('email'))) {
            $isValid = false;
            $f3->set("errors['email']", "Please enter valid email address ");
        }

        //UNIQUE EMAIL
        if ($param != $db->getEmail($f3->get('email'))['user_id']) {
            if (!$this->uniqueEmail($f3->get('email'))) {
                $isValid = false;
                $f3->set("errors['email']", "This email has been already taken, please choose another ");
            }
        }

        //SSN
        if (!$this->validSsn($f3->get('ssn'))) {
            $isValid = false;
            $f3->set("errors['ssn']", "Please enter valid  SSN ");
        }
        //image file
        if (isset($file)) {
            if (!empty($file["name"])) {
                if (!$this->validateFileUpload($file)) {
                    $isValid = false;
                }
            }
        }
        //removing BIO validation, no longer need this field to be validated.
        /*//bio validation if it is not empty
        if (!empty(trim($bio)) && strlen(trim($bio)) < 100) {
            $isValid = false;
            $f3->set("errors['check']", "Must be more than 100 characters");
        }
        */
        return $isValid;
    }

    /**
     * Validate image
     * @param string $file image file
     * @return bool true/false if file is valid/not valid
     */

    function validateFileUpload($file)
    {
        global $f3;

        $isValid = true;
        //defining the valid file type
        $validateType = array('image/gif', 'image/jpeg', 'image/jpg', 'image/png');

        //checking the file size 2MB-maximum
        if ($_SERVER['CONTENT_LENGTH'] > 3000000) {
            $f3->set("errors['largeImg", "Sorry! file size too large Maximum file size is 3 MB ");
            $isValid = false;
            //check the file type
        } elseif (in_array($file['type'], $validateType)) {
            if ($file['error'] > 0) {
                $f3->set("errors['returnCode']", "Sorry! file could not be uploaded Try again");
                $isValid = false;
            }

            //since we have the used the tutor's id and name as image file name to store in our db
            // we know file name is going to be unique so no need check for duplicates
//            if (file_exists($dirName . $newName)) {
//                echo $dirName.$newName;
//                $f3->set("errors['duplicatedImage']", "Sorry! This image is already exist choose another one");
//                $isValid = false;
//            }
            else {
                $f3->set("success['uploadSuccessfully']", "Updated successfully");
            }
        } else {
            $f3->set("errors['wrongFileType']", "Sorry! Only supports .jpeg, .jpg, .gif and .png images");
            $isValid = false;
        }
        return $isValid;
    }

    /**File validator for tutors upload
     * @param object $file a File object
     * @return boolean bool returns true if passed the validation false for not passing
     * @Oleg
     */
    function validateFileUploadTut($file)
    {
        global $f3;

        $isValid = true;
        //defining the valid file type
        $validateType = array('application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        //checking the file size 2MB-maximum
        if ($_SERVER['CONTENT_LENGTH'] > 500000) {
            $f3->set("errors", "Sorry! file size too large Maximum file size is 500 KB/false");
            $isValid = false;
            //check the file type
        } elseif (in_array($file['type'], $validateType)) {
            if ($file['error'] > 0) {
                $f3->set("errors", "Sorry! file could not be uploaded Try again/false");
                $isValid = false;
            }

            //since we have the used the tutor's id and name as image file name to store in our db
            // we know file name is going to be unique so no need check for duplicates
//            if (file_exists($dirName . $newName)) {
//                echo $dirName.$newName;
//                $f3->set("errors['duplicatedImage']", "Sorry! This image is already exist choose another one");
//                $isValid = false;
//            }
            else {
                $f3->set("success", "Your file was uploaded successfully/true");
            }
        } else {
            $f3->set("errors", "Sorry! Only supports .docx, and .pdf files/false");
            $isValid = false;
        }
        return $isValid;
    }

    /**
     * Returns true if the item data is valid
     *
     * @param string $itemName The name of the item
     * @return bool Returns true if the item data is valid
     * @author Keller Flint
     */
    function validateItem($itemName)
    {
        $isValid = true;
        if (empty(trim($itemName))) {
            $isValid = false;
            $this->_errors["itemName"] = "Item name may not be empty";
        }
        if (strlen($itemName) > 255) {
            $isValid = false;
            $this->_errors["itemName"] = "Item name may not be greater than 255 characters";
        }
        return $isValid;
    }

    /**
     * Returns true if the state data is valid
     *
     * @param int $stateId The id of the state
     * @param string $stateName The name of the state
     * @param string $stateText The description of the state
     * @return bool Returns true if the state data is valid
     * @author Keller Flint
     */
    function validateState($stateId, $stateName, $stateText)
    {
        $isValid = true;
        if (empty(trim($stateName))) {
            $this->_errors["stateName" . $stateId] = "Name may not be empty";
            $isValid = false;
        }
        if (strlen($stateName) > 255) {
            $isValid = false;
            $this->_errors["stateName" . $stateId] = "Name may not be greater than 255 characters";
        }

        if (strlen($stateText) > 5000) {
            $isValid = false;
            $this->_errors["stateText" . $stateId] = "Description may not be greater than 5000 characters";
        }
        return $isValid;
    }
}