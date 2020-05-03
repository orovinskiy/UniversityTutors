<?php

/**
 * Validating onboarding form
 * @author Laxmi kandel
 */
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
     * @param $firstName tutor's first name
     * @return bool
     */
    function validFirstName($firstName)
    {
        $firstName = trim($firstName);
        return ctype_alpha($firstName);
    }

    /**
     * Validating Last Name
     * @param $last tutor's last name
     * @return bool
     */
    function validLastName($last)
    {
        $last = trim($last);
        return ctype_alpha($last);
    }

    /**
     * Validating email
     * @param $email tutor's email
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
     * @param $phone tutor's phone
     * @return bool
     */
    function validPhone($phone)
    {
        $regex =  "/\(\d{3}\) \d{3}-\d{4}/";
        $phoneResult = false;
        $phone = trim($phone);
        if (strlen($phone) == 14 && preg_match($regex,$phone)){
            $phoneResult = true;
        }
        return $phoneResult;
    }

    /**
     * Validating SSN if provided
     * @param ssn tutor's ssn
     * @return bool
     */
    function validSsn($ssn)
    {
        $regexSsn ="/^\d{3}-\d{2}-\d{4}$/";
        $ssnResult = false;
        $ssn = trim($ssn);
        if(!empty($ssn)){
            if (strlen($ssn) == 11 && preg_match($regexSsn,$ssn)) {
                $ssnResult = true;
            }
        }else{
            $ssnResult =true;
        }
        return $ssnResult;
    }

    /**Validating all the required fields name, phone, email, ssn, image
     * @param $file user's selected file for image
     * @param $newName name for file
     * @return bool
     * @author  Laxmi
     */
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
            $f3->set("errors['phone']", "Please enter valid phone number ");
        }
        //EMAIL
        if (!$this->validEmail($f3->get('email'))) {
            $isValid = false;
            $f3->set("errors['email']", "Please enter valid email address ");
        }
        //SSN
        if (!$this->validSsn($f3->get('ssn'))) {
            $isValid = false;
            $f3->set("errors['ssn']", "Please enter valid  SSN ");
        }
        //image file
        if (isset($file)) {
            if(!empty($file["name"])){
                if (!$this->validateFileUpload($file, $newName)) {
                    $isValid = false;
                }
            }
        }
        return $isValid;
    }

    /**
     * Validate image
     * @param $file image file
     * @param $newName image name
     * @return bool
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