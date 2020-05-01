<?php

/**
 * Fat free instantiation and routing.
 *
 * @author Keller Flint
 * @author Laxmi Kandel
 */

//require the autoload file
require_once('vendor/autoload.php');
require_once("model/config.php");

//Turn on error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

//create an instance of the base class
$f3 = Base::instance();

//create new Database object
$db = new Database();

//instantiate Validate class
 $val = new Validate();

//define variables
$f3->set("tutorForms", array("ADP Registration", "Adult Sexual Misconduct",
    "Affirmations and Disclosures", "Handbook Verification", "I-9", "Offer Letter",
    "Orientation RSVP", "W4"));

// Define a default route
$f3->route('GET /', function () {
    //below is code to test the database functions
//    $result = $GLOBALS['db']->getTutors();
//    $result2 = $GLOBALS['db']->getTutor(2020, 1);
//    $results = $GLOBALS['db']->testDatabase();
    $view = new Template();
    echo $view->render("views/home.html");
});


/**
 * Route for checklist
 * @author oleg
 */
$f3->route('GET /checklist', function () {
    $view = new Template();
    echo $view->render("views/checklist.html");
});


/**
 * Route for onboarding-form
 * @author Laxmi
 */
$f3->route('GET|POST /form', function ($f3) {
    global $val;
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $f3->set('firstName', $_POST['firstName']);
        $f3->set('lastName', $_POST['lastName']);
        $f3->set('email', $_POST['email']);
        $f3->set('phone', $_POST['phone']);
        $f3->set('ssn', $_POST['ssn']);
        //if the user input in form is valid
        if($val->ValidForm1()){
            echo"Hi I am valid";
            $GLOBALS['db']->insertMember(4, $_POST['firstName'], $_POST['lastName'], $_POST['phone'], $_POST['ssn']);

        }
        //user_id is hardcoded here needs to be changed after the login page is up
    }
    $view = new Template();
    echo $view->render('views/form.html');
});

/**
 * Route for admin viewing and management of tutors.
 * @author Keller
 */
$f3->route('GET /tutors', function () {
    $view = new Template();
    echo $view->render("views/tutors.html");
});

$f3->run();