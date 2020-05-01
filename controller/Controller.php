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
        $this->_db->updateYearData($_POST["column"], $_POST["value"], $_POST["yearId"]);
    }

    /**
     * Ajax logic for checklist page
     * @author Oleg
     */
    function checklistAjax(){
        $this->_db->updateYearData($_POST['column'],$_POST['value'],$_POST['year']);
    }

    /**
     * View of all the required forms. Lets the user check of or uncheck a form if it has been
     * completed
     * @param $param int is the id of the user
     * @author oleg
     */
    function checklist($param){
        //get the current year
        $currentYear = date('Y');

        $checkBoxes = $GLOBALS['db']->getTutorsChecklist($currentYear,$param['userId']);
        $checkBoxes = $checkBoxes[0];

        $checkBoxes['year_i9'] == 'none' ? $checkBoxes['year_i9'] = '0' : $checkBoxes['year_i9'] = '1';
        $checkBoxes['year_ADP'] == 'none' ? $checkBoxes['year_ADP'] = '0' : $checkBoxes['year_ADP'] = '1';

        $this->_f3->set('yearID',$checkBoxes['year_id']);
        $this->_f3->set('userName',$checkBoxes['tutor_first']." ".$checkBoxes['tutor_last']);
        $this->_f3->set('checkboxes',array("ADP Registration"=>array("Value"=>$checkBoxes['year_ADP'],"Column"=>"year_ADP"),
            "Adult Sexual Misconduct"=>array("Value"=>$checkBoxes['year_sexual_misconduct'],"Column"=>"year_sexual_misconduct"),
            "Affirmations and Disclosures"=>array("Value"=>$checkBoxes['year_affirmation_disclosures'],"Column"=>"year_affirmation_disclosures"),
            "Handbook Verification"=>array("Value"=>$checkBoxes['year_handbook_verification'],"Column"=>"year_handbook_verification"),
            "I-9"=>array("Value"=>$checkBoxes['year_i9'],"Column"=>"year_i9"),
            "Offer Letter"=>array("Value"=>$checkBoxes['year_offer_letter'],"Column"=>"year_offer_letter"),
            "Orientation RSVP"=>array("Value"=>$checkBoxes['year_orientation'],"Column"=>"year_orientation"),
            "W4"=>array("Value"=>$checkBoxes['year_w4'],"Column"=>"year_w4")));



        $view = new Template();
        echo $view->render("views/checklist.html");
    }
}