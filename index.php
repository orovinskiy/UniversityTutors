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
 * @author Oleg
 */
$f3->route('GET /checklist/@userId', function ($f3,$param) {
    $GLOBALS['controller']->checklist($param);
});

/**
 * route to checklist ajax function
 * @author Oleg
 */
$f3->route('POST /makeBox', function () {
    $GLOBALS['controller']->checklistAjax();
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