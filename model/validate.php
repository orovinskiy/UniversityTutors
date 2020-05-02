<?php

/**
 * Validating onboarding form
 * */
class Validate
{
    /**
     * @var array
     */
    private $_errors;

    /**
     * Validator constructor.
     */
    public function __construct()
    {
        $this->_errors = array();
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
     * @param $firstName
     * @return bool
     */
    function validFirstName($firstName)
    {
        $firstName = trim($firstName);
        return ctype_alpha($firstName);
    }

    /**
     * Validating Last Name
     * @param $last
     * @return bool
     */
    function validLastName($last)
    {
        $last = trim($last);
        return ctype_alpha($last);
    }

    /**
     * Validating email
     * @param $email
     * @return bool
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
     * Validating Phone Number
     * @param $phone
     * @return bool
     */
    function validPhone($phone)
    {
        $phoneResult = false;
        $phone = trim($phone);
        if (strlen($phone) == 10 && ctype_digit($phone)) {
            $phoneResult = true;
        }
        return $phoneResult;
    }

    /**
     * Validating Phone Number
     * @param ssn
     * @return bool
     */
    function validSsn($ssn)
    {
        $ssnResult = false;
        $ssn = trim($ssn);
        if (strlen($ssn) == 9 && ctype_digit($ssn)) {
            $ssnResult = true;
        }
        return $ssnResult;
    }

    function validForm($file, $newName)
    {
        global $f3;
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
            $f3->set("errors['phone']", "Please enter valid 10 digit phone number ");
        }
        //EMAIL
        if (!$this->validEmail($f3->get('email'))) {
            $isValid = false;
            $f3->set("errors['email']", "Please enter valid email address ");
        }
        //SSN
        if (!$this->validSsn($f3->get('ssn'))) {
            $isValid = false;
            $f3->set("errors['ssn']", "Please enter valid 9 digit SSN ");
        }
        //image file
        echo "above file";
        if (isset($file)) {
                if (!$this->validateFileUpload($file, $newName)) {
                    $isValid = false;
                    $f3->set("errors['image']", "please upload image");
            }
        }
        return $isValid;
    }

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
            }
        } else {
            $f3->set("errors['wrongFileType']", "Sorry! Only supports .jpeg, .jpg, .gif and .png images");
            $isValid = false;
        }
        return $isValid;
    }

}