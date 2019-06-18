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
 * A report to display details about cohorts
 *
 * @package    report
 * @subpackage cohortdetail
 * @copyright   2019 Bruno Malaval <bruno.malaval@uha.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/formslib.php');
require_once(dirname(__FILE__) . '/view_form.php');

global $DB, $USER;

// Check capability / require login and 'moodle/cohort:view' in SYSTEM context.

//$context = context_system::instance();
require_login();
//require_capability('moodle/cohort:view', $context);

admin_externalpage_setup('reportcohortdetail');

$title = get_string('pluginname', 'report_cohortdetail');
$pagetitle = $title;
$url = new moodle_url("/report/cohortdetail/index.php");
$PAGE->set_url($url);

$navigationinfo = array(
        'name' => get_string('pluginname', 'report_cohortdetail'),
        'url' => new moodle_url('/report/cohortdetail/index.php')
    );
$PAGE->add_report_nodes($USER->id, $navigationinfo);

echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);

$mform = new cohortdetail_form($CFG->wwwroot.'/report/cohortdetail/index.php');

// Display form and results.

if ($dataform = $mform->get_data()) {

    $mform->display();

    $cohortid = $dataform->cohortid;

    // Construct and display cohort members table.

    if (isset($dataform->membersbutton)) {

        $sql = "SELECT u.username as un ,u.lastname as ul ,u.firstname as uf, u.idnumber as ui
                  FROM {user} u
                  JOIN {cohort_members} cm ON cm.userid = u.id
                  JOIN {cohort} c ON c.id = cm.cohortid
                 WHERE c.id = :cohortid AND c.visible = :visible AND c.contextid = :context
              ORDER BY ul";
        $params = array('cohortid' => $cohortid, 'visible' => 1, 'context' => 1);
        $users = $DB->get_records_sql($sql, $params);

        $utable = new html_table();
        $utable->head = array(get_string('username', 'report_cohortdetail'),
            get_string('idnumber', 'report_cohortdetail'),
            get_string('fullname', 'report_cohortdetail'));

        foreach ($users as $user) {
            $utable->data[] = array($user->un, $user->ui, "( ".$user->ul." ".$user->uf." )");
        }

        echo("<hr>");
        $membersct = count($users);
        echo html_writer::tag('h3', get_string('members', 'report_cohortdetail')." ( ".$membersct." )");
        echo html_writer::table($utable);

    }

    // Construct and display courses table.

    if (isset($dataform->coursesbutton)) {

        $sql = "SELECT c.id as ci, c.category as cc, c.fullname as cf
                  FROM {course} c
                  JOIN {enrol} e ON e.courseid = c.id
                 WHERE e.enrol like 'cohort' AND e.customint1 = :cohortid
              ORDER BY c.fullname";
        $params = array('cohortid' => $cohortid);
        $courseslist = $DB->get_records_sql($sql, $params);

        $ctable = new html_table();
        $ctable->head = array(get_string('coursename', 'report_cohortdetail'),
            get_string('categorypath', 'report_cohortdetail'));

        foreach ($courseslist as $course) {
            $context = context_course::instance($course->ci);
            $category = $DB->get_record('course_categories', array('id' => $course->cc));
            if (has_capability('enrol/cohort:config', $context)) {
                $cats = explode("/", $category->path);
                $countcats = count($cats);
                //unset($catpath);
                $catpath = '';
                for ($counter = 1; $counter < $countcats; $counter++) {
                    $catname = $DB->get_record("course_categories", array("id" => $cats[$counter]));
                    $catpath = $catpath.' / '.$catname->name;
                }
                $clink = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->ci.'">'.$course->cf.'</a>';
                $ctable->data[] = array($clink, $catpath);
            }
        }

        echo("<hr>");
        $selectcohort = $DB->get_record("cohort", array("id" => $cohortid));
        echo html_writer::tag('h3', get_string('courses', 'report_cohortdetail')." ( ".$selectcohort->name." )");
        echo html_writer::table($ctable);

    }

    // Construct and display "my courses" table.

    if (isset($dataform->mycoursesbutton)) {

        $sql = "SELECT c.id AS ci, c.fullname AS cf, c.category AS cc
                  FROM {course} c
                  JOIN {context} ctx ON c.id = ctx.instanceid
                  JOIN {role_assignments} ra ON ra.contextid = ctx.id
                  JOIN {user} u ON u.id = ra.userid
                 WHERE u.id = :userid";
        $params = array('userid' => $USER->id);
        $courseslist = $DB->get_records_sql($sql, $params);

        $ctable = new html_table();
        $ctable->head = array(get_string('coursename', 'report_cohortdetail'),
            get_string('cohorts', 'report_cohortdetail'));

        foreach ($courseslist as $course) {
            $context = context_course::instance($course->ci);
            $category = $DB->get_record('course_categories', array('id' => $course->cc));
            if (has_capability('enrol/cohort:config', $context)) {
                $cats = explode("/", $category->path);
                $countcats = count($cats);
                //unset($catpath);
                $catpath = '';
                for ($counter = 1; $counter < $countcats; $counter++) {
                    $catname = $DB->get_record("course_categories", array("id" => $cats[$counter]));
                    $catpath = $catpath.' / '.$catname->name;
                }
                $clink = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->ci.'">'.$course->cf.'</a>';
                $mycourse = $clink.'<br>'.$catpath;

                $sql = "SELECT c.name as cn
                          FROM {cohort} c
                          JOIN {enrol} e ON e.customint1 = c.id
                         WHERE e.enrol like 'cohort' AND e.courseid = :courseid
                      ORDER BY c.name";
                $params = array('courseid' => $course->ci);
                $cohortslist = $DB->get_records_sql($sql, $params);
                if (count($cohortslist) > 0) {
                    $cl = '';
                    foreach ($cohortslist as $cohort) {
                        $cl = $cl.'<br>'.$cohort->cn;
                    }
                    $ctable->data[] = array($mycourse, $cl);
                }
            }
        }

        echo("<hr>");
        echo html_writer::tag('h3', get_string('mycourses', 'report_cohortdetail'));
        echo html_writer::table($ctable);

    }

} else {

    $mform->display();

}

echo $OUTPUT->footer();
