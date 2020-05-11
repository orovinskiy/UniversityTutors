<?php

/**
 * Validating onboarding form
 * @author Laxmi kandel
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
     * @return array
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
        $firstName = trim($firstName);
        return ctype_alpha($firstName);
    }

    /**
     * Validating Last Name
     * @param string $last tutor's last name
     * @return bool true if last name only has alphabets
     */
    function validLastName($last)
    {
        $last = trim($last);
        return ctype_alpha($last);
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
        $regexSsn = "/^\d{3}-\d{2}-\d{4}$/";
        $ssnResult = false;
        $ssn = trim($ssn);
        if (preg_match($regexSsn, $ssn)) {
            $ssnResult = true;
        }
        return $ssnResult;
    }

    /**Validating all the required fields name, phone, email, ssn, image
     * @param bool $checkBox checkbox status
     * @param string $file user's selected file for image
     * @param string $newName name for file
     * @param int $param user id
     * @param string $bio user's bio
     * @return bool true/false if all the required fields are valid/not valid
     * @author  Laxmi
     */
    function validForm($checkBox, $file, $newName, $param, $bio)
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
                if (!$this->validateFileUpload($file, $newName)) {
                    $isValid = false;
                }
            }
        }

        //check to see if check box is checked/unchecked
        if ($checkBox == "on") {
            if (empty(trim($bio))) {
                $isValid = false;
                $f3->set("errors['check']", "Please enter bio");
                return $isValid;
            }
            if (strlen(trim($bio)) < 100) {
                $isValid = false;
                $f3->set("errors['check']", "Must be more than 100 characters");
            }
        }
        return $isValid;
    }

    /**
     * Validate image
     * @param string $file image file
     * @param string $newName image name
     * @return bool true/false if file is valid/not valid
     */

    function validateFileUpload($file, $newName)
    {
        global $dirName;
        global $f3;

        $isValid = true;
        //defining the valid file type
        $validateType = array('image/gif', 'image/jpeg', 'image/jpg', 'image/png');

        //checking the file size 2MB-maximum
        if ($_SERVER['CONTENT_LENGTH'] > 3000000) {
            $f3->set("errors['largeImg", "Sorry! file size too large Maximum file size is 3 MB ");
            $isValid = false;
        } //check the file type
        elseif (in_array($file['type'], $validateType)) {
            if ($file['error'] > 0) {
                $f3->set("errors['returnCode']", "Sorry! file could not be uploaded Try again");
                $isValid = false;
            }

            //checking for duplicate
            if (file_exists($dirName . $newName)) {
                $f3->set("errors['duplicatedImage']", "Sorry! This image is already exist choose another one");
                $isValid = false;
            } else {
                $f3->set("success['uploadSuccessfully']", "Updated successfully");
            }
        } else {
            $f3->set("errors['wrongFileType']", "Sorry! Only supports .jpeg, .jpg, .gif and .png images");
            $isValid = false;
        }
        return $isValid;
    }
}