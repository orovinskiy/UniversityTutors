<?php

/**
 * Controller logic for viewing pages and using the site.
 *
 * @author Keller Flint
 */

class Controller
{
    private $_f3; //router
    private $_val;

    /**
     * Controller constructor
     * @param $f3 Object The fat free instance
     */
    function __construct($f3)
    {
        //$this->_val = new Validation();
        $this->_f3 = $f3;
    }

    /**
     * Logic and rendering for tutors page
     * @param string $year The year to load tutors data for
     * @author Keller Flint
     */
    function tutorsPage($year)
    {
        global $db;

        // Get tutor data for current year
        $tutorsData = $db->getTutors($year);
        
        // Set values for select dropdowns
        $this->_f3->set("backgroundOptions", array("none" => "Not Done", "sent" => "Sent", "clear" => "Clear", "flag" => "Flag"));
        $this->_f3->set("referenceOptions", array("none" => "Not Done", "incomplete" => "In Progress", "clear" => "Clear", "flag" => "Flag"));
        $this->_f3->set("ADPOptions", array("none" => "Not Sent", "invited" => "Invited", "registered" => "Registered"));
        $this->_f3->set("i9Options", array("none" => "Not Sent", "tutor" => "Tutor Done", "admin" => "Admin Done"));

        // Store tutor data is hive
        $this->_f3->set("tutorsData", $tutorsData);

        $view = new Template();
        echo $view->render("views/tutors.html");
    }

    /**
     * Ajax logic for tutors page
     * @author Keller Flint
     */
    function tutorsAjax() {
        global $db;

        $db->updateYearData($_POST["column"], $_POST["value"], $_POST["yearId"]);
    }
}