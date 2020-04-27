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