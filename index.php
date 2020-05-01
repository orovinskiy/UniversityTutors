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


//create new Controller
$controller = new Controller($f3, $db);

// Define a default route
$f3->route('GET /', function () {
    //below is code to test the database functions
    //$result = $GLOBALS['db']->getTutors();
    //$result2 = $GLOBALS['db']->getTutor(2020,1);
    //$results = $GLOBALS['db']->testDatabase();
    //var_dump($result2);

    $view = new Template();
    echo $view->render("views/home.html");
});


/**
 * Route for checklist
 * @author oleg
 */
$f3->route('GET /checklist/@userId', function ($f3,$param) {
    //get the current year
    $currentYear = date('Y');
    var_dump($param);

    $checkBoxes = $GLOBALS['db']->getTutorsChecklist($currentYear,$param['userId']);
    $checkBoxes = $checkBoxes[0];

    $checkBoxes['year_i9'] == 'none' ? $checkBoxes['year_i9'] = '0' : $checkBoxes['year_i9'] = '1';
    $checkBoxes['year_ADP'] == 'none' ? $checkBoxes['year_ADP'] = '0' : $checkBoxes['year_ADP'] = '1';

    $f3->set('userName',$checkBoxes['tutor_first']." ".$checkBoxes['tutor_last']);
    $f3->set('checkboxes',array("ADP Registration"=>$checkBoxes['year_ADP'],
        "Adult Sexual Misconduct"=>$checkBoxes['year_sexual_misconduct'],
        "Affirmations and Disclosures"=>$checkBoxes['year_affirmation_disclosures'],
        "Handbook Verification"=>$checkBoxes['year_handbook_verification'],
        "I-9"=>$checkBoxes['year_i9'],
        "Offer Letter"=>$checkBoxes['year_offer_letter'],
        "Orientation RSVP"=>$checkBoxes['year_orientation'],
        "W4"=>$checkBoxes['year_w4']));


    var_dump($checkBoxes);

    $view = new Template();
    echo $view->render("views/checklist.html");
});

/**
 * reroute too file
 */
$f3->route('POST /makeBox', function () {
    $view = new Template();
    echo $view->render('views/form.html');
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
 * @author Keller Flint
 */
$f3->route('GET /tutors/@year', function ($f3, $param) {
    global $controller;
    $controller->tutorsPage($param['year']);
});

/**
 * Route for tutors page ajax functions
 * @author Keller Flint
 */
$f3->route('POST /tutorsAjax', function () {
    global $controller;
    $controller->tutorsAjax();
});

$f3->run();