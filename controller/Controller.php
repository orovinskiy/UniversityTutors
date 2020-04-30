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

    function tutorsPage() {
        global $db;
        $view = new Template();
        echo $view->render("views/tutors.html");
    }
}