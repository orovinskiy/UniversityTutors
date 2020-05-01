<?php

/**
 * Controller logic for viewing pages and using the site.
 *
 * @author Keller Flint
 */

class Controller
{
    private $_f3; //router
    private $_db;
    private $_val;

    /**
     * Controller constructor
     * @param $f3 Object The fat free instance
     */
    function __construct($f3, $db)
    {
        //$this->_val = new Validation();
        $this->_f3 = $f3;
        $this->_db = $db;
    }

    /**
     * Logic and rendering for tutors page
     * @param string $year The year to load tutors data for
     * @author Keller Flint
     */
    function tutorsPage($year)
    {

        // Get tutor data for current year
        $tutorsData = $this->_db->getTutors($year);

        // Set values for select dropdowns
        $this->_f3->set("backgroundOptions", array("none" => "Not Done", "sent" => "Sent", "clear" => "Clear", "flag" => "Flag"));
        $this->_f3->set("referenceOptions", array("none" => "Not Done", "incomplete" => "In Progress", "clear" => "Clear", "flag" => "Flag"));
        $this->_f3->set("ADPOptions", array("none" => "Not Sent", "invited" => "Invited", "registered" => "Registered"));
        $this->_f3->set("i9Options", array("none" => "Not Sent", "tutor" => "Tutor Done", "admin" => "Admin Done"));
        $this->_f3->set("year", $year);

        // Store tutor data is hive
        $this->_f3->set("tutorsData", $tutorsData);

        $view = new Template();
        echo $view->render("views/tutors.html");
    }

    /**
     * Ajax logic for tutors page
     * @author Keller Flint
     */
    function tutorsAjax()
    {
        if (isset($_POST["yearId"])) {
            $this->_db->updateYearData($_POST["column"], $_POST["value"], $_POST["yearId"]);
        } else if (isset($_POST["year"])) {
            // TODO create function to generate and send email to tutor
            echo $this->_db->addNewTutor($_POST["year"], $_POST["email"]);
        }

    }
}