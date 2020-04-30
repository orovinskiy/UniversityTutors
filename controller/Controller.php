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
     * Controller constructor.
     * @param $f3 Object The fat free instance
     */
    function __construct($f3)
    {
        //$this->_val = new Validation();
        $this->_f3 = $f3;
    }

    function tutorsPage()
    {
        global $db;

        $tutorsData = $db->getTutors("2020");

        $this->_f3->set("backgroundOptions", array("none" => "Not Done", "sent" => "Sent", "clear" => "Clear", "flag" => "Flag"));
        $this->_f3->set("referenceOptions", array("none" => "Not Done", "incomplete" => "In Progress", "clear" => "Clear", "flag" => "Flag"));
        $this->_f3->set("ADPOptions", array("none" => "Not Sent", "invited" => "Invited", "registered" => "Registered"));
        $this->_f3->set("i9Options", array("none" => "Not Sent", "tutor" => "Tutor Done", "admin" => "Admin Done"));

        $this->_f3->set("tutorsData", $tutorsData);

        var_dump($tutorsData);

        $view = new Template();
        echo $view->render("views/tutors.html");
    }
}