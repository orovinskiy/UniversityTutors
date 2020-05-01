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

    function validForm1()
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
            $valid = false;
            $f3->set("errors['email']", "Please enter valid email address ");
        }
        //SSN
        if (!$this->validSsn($f3->get('ssn'))) {
            $isValid = false;
            $f3->set("errors['ssn']", "Please enter valid 9 digit SSN ");
        }
        return $isValid;
    }


//    function validForm($firstName, $lastName, $email, $phone, $ssn)
//    {
//        global $f3;
//        $isValid = true;//flag
//        $errors = [];
//
//        // first name  validation
//        if (empty(trim($firstName))) {
//            $isValid = false;
//            array_push($errors, ["val-empty-error" => "Cannot be empty."]);
//        }
//        if (strlen($firstName) > 255) {
//            $isValid = false;
//            array_push($errors, ["val-lessThan255-error" => "Cannot be longer than 255 characters."]);
//        }
//        if(!$this->validFirstName($firstName)){
//            $$isValid = false;
//            array_push($errors,["val-empty-error"=> "Please enter only alphabets"]);
//        }
//        $f3->set("errors['firstName']", $errors);
//        $errors = [];
//
//        //last name validation
//        if (empty(trim($lastName))) {
//            $isValid = false;
//            array_push($errors, ["val-empty-error" => "Cannot be empty."]);
//        }
//        if (strlen($lastName) > 255) {
//            $isValid = false;
//            array_push($errors, ["val-lessThan5000-error" => "Cannot be longer than 255 characters."]);
//        }
//        if(!$this->validLastName($lastName)){
//            $isValid = false;
//            array_push($errors,["val-error"=> "Please enter only alphabets"]);
//        }
//        $f3->set("errors['lastName']", $errors);
//        $errors = [];
//
//        //valid email
//        if(!$this->validEmail($email)){
//            $isValid = false;
//            array_push($errors,["val-error"=> "Please enter valid email"]);
//        }
//        $f3->set("errors['email']", $errors);
//        $errors = [];
//
//
//        //valid phone
//        if(empty(trim($phone))){
//            $isValid = false;
//            array_push($errors, ["val-empty-error" => "Cannot be empty."]);
//        }
//        if(!$this->validPhone($phone)){
//            $isValid = false;
//            array_push($errors,["val-error"=> "Please enter valid phone number"]);
//        }
//
//        //valid ssn
//        if(empty(trim($ssn))){
//            $isValid = false;
//            array_push($errors, ["val-empty-error" => "Cannot be empty."]);
//        }
//        if(!$this->validSsn($ssn)){
//            $isValid = false;
//            array_push($errors,["val-error"=> "Please enter 9 digit ssn"]);
//        }
//        return $isValid;
//
//    }
}