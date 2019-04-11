<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Form for entering parameters / viewing cohort detail
 *
 * @package report_cohortdetail
 * @copyright   2019 Bruno Malaval <bruno.malaval@uha.fr>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class cohortdetail_form extends moodleform {

    private $cohortid;

    public function __construct($cohortid) {
        $this->cohortid = $cohortid;
        parent::__construct();
    }

    public function definition() {
        global $CFG;        
        global $DB;

        $mform = $this->_form; 

        if (isset($_POST['cohortid'])) {
            $cohortid = $_POST['cohortid'];
        } else {
            $cohortid = 0;
        }

        $sql = "SELECT c.id as ci, c.name as cn, c.description as cd
                  FROM {cohort} c
                 WHERE c.visible = :visible AND c.contextid = :context
              ORDER BY cn";
        $params = array('visible' => 1, 'context' => 1);
        $results = $DB->get_records_sql($sql, $params);

        foreach ($results as $cohort) {
            $cohorts[$cohort->ci] = $cohort->cn.' ('.$cohort->cd.' )';
        }

        $options = array(
            'multiple' => false,
            'showsuggestions' => true ,
            'placeholder' => 'Saisir le nom de la cohorte',
        );
        $mform->addElement('autocomplete', 'cohortid', get_string('cohort', 'report_cohortdetail'), $cohorts, $options);

        $this->add_action_buttons($cancel = false, $submitlabel = get_string('display', 'report_cohortdetail'));

    }
    //Custom validation should be added here.
    public function validation($data, $files) {
        return array();
    }
}
