<?php

/**
 * Fat free instantiation and routing.
 *
 * @author Keller Flint
 * @author Laxmi Kandel
 */

//require the autoload file
require_once('vendor/autoload.php');

//Turn on error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

//create an instance of the base class
$f3 = Base::instance();

//define variables
$f3->set("tutorForms",array("ADP Registration","Adult Sexual Misconduct",
    "Affirmations and Disclosures","Handbook Verification","I-9","Offer Letter",
    "Orientation RSVP","W4"));

// Define a default route
$f3->route('GET /', function () {
    $view = new Template();
    echo $view->render("views/home.html");
});

// Define a default route
$f3->route('GET /test', function () {
    $view = new Template();
    echo $view->render("views/home.html");
});

// Define a route too tutors checklist
$f3->route('GET /checklist', function () {
    $view = new Template();
    echo $view->render("views/checklist.html");
});


$f3->run();