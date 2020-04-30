<?php

/**
 * Fat free instantiation and routing.
 *
 * @author Keller Flint
 * @author Laxmi Kandel
 */

//require the autoload file
require_once('vendor/autoload.php');
require_once ("model/config.php");

//Turn on error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

//create an instance of the base class
$f3 = Base::instance();

//create new Database object
$db = new Database();

//define variables
$f3->set("tutorForms",array("ADP Registration","Adult Sexual Misconduct",
    "Affirmations and Disclosures","Handbook Verification","I-9","Offer Letter",
    "Orientation RSVP","W4"));

// Define a default route
$f3->route('GET /', function () {
    //below is code to test the database functions
    //$result = $GLOBALS['db']->getTutors();
    //$result2 = $GLOBALS['db']->getTutor(2020,1)
    //$results = $GLOBALS['db']->testDatabase();
    //var_dump($result);

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
$f3->route('GET /form', function () {
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