<?php

/**
 * Fat free instantiation and routing.
 * @author Keller Flint
 * @author Laxmi Kandel
 */

//require the autoload file
require_once('vendor/autoload.php');
// ON SERVER: require_once("../config.php");
require_once("model/config.php");
require_once("model/validate.php");

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

//default place to uploads image files
// ON SERVER: $dirName = '../uploads/';
$dirName = 'uploads/';
$directoryName = 'attachments/';

// Define a default route
$f3->route('GET /', function () {
    //below is code to test the database functions
//    $result = $GLOBALS['db']->getTutors();
    //$result2 = $GLOBALS['db']->getTutor(2020, 1);
//    $results = $GLOBALS['db']->testDatabase();
    //$newUser = new User("Andy@mail.com", $GLOBALS['db']); //used to test user object
    //$userAdmin = $newUser->getUserIsAdmin(); //used to test user object
    //$userID = $newUser->getUserID(); //used to test user object
    //echo $userAdmin. " and ". $userID; //used to test user object
    $view = new Template();
    echo $view->render("views/home.html");
});


/**
 * Route for checklist
 * @author Oleg
 */
$f3->route('GET /checklist/@userId', function ($f3, $param) {
    $GLOBALS['controller']->checklist($param);
});

$f3->route('POST /tutFile',function(){
    echo $GLOBALS['controller']->uploadTutFile();
});

$f3->route('POST /sessSett',function(){
    $_SESSION['item'] = $_POST['item'];
    $_SESSION['file'] = $_POST['fileName'];
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
$f3->route('GET|POST /form/@id', function ($f3, $param) {
    global $controller;
    $controller->formPage($param);
});

/**
 * Route for admin viewing and management of tutors.
 * @author Keller Flint
 */
$f3->route('GET /tutors/@year', function ($f3, $param) {
    global $controller;
    $controller->tutorsPage($param);
});

/**
 * Route for tutors page ajax functions
 * @author Keller Flint
 */
$f3->route('POST /tutorsAjax', function () {
    global $controller;
    $controller->tutorsAjax();
});

/**
 * Route for login page
 * @author Dallas Sloan
 */
$f3->route('GET|POST /login', function () {
    global $controller;
    $controller->login();
});

/**
 * Route for Logout page.
 * @author Dallas Sloan
 */
$f3->route('GET /logout', function () {
    global $controller;
    $controller->logout();
});
/**
 * Route for admin management
 * @author Keller Flint
 */
$f3->route('GET|POST /admin', function () {
    global $controller;
    $controller->adminPage();
});

/**
 * Route for tutor info page
 * @author Keller Flint
 */
$f3->route('GET /tutor/@id', function ($f3, $param) {
    global $controller;
    $controller->tutorInfoPage($param);
});

/**
 * Route for adminAjax
 * @author Keller Flint
 */
$f3->route('POST /adminAjax', function () {
    global $controller;
    $controller->adminAjax();
});

/**
 * Route for resetting password
 * @author Keller Flint
 */
$f3->route('GET|POST /reset/@id', function ($f3, $param) {
    global $controller;
    $controller->passwordPage($param["id"]);
});

/**
 * Route for editing items
 * @author Keller Flint
 */
$f3->route('GET|POST /edit/@itemId', function($f3, $param) {
    global $controller;
    $controller->editPage($param["itemId"]);
});

$f3->run();
