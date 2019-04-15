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

function report_cohortdetail_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    global $USER;

    if (isguestuser() or !isloggedin()) {
        return;
    }

    if (\core\session\manager::is_loggedinas() or $USER->id != $user->id) {
        // No peeking at somebody else's sessions!
        return;
    }

    $context = context_system::instance();
    // $context = context_user::instance($USER->id);
    if (has_capability('moodle/cohort:view', $context)) {
        $node = new core_user\output\myprofile\node('reports', 'cohortdetail',
                'Detail des cohortes', null, new moodle_url('/report/cohortdetail/index.php'));
        $tree->add_node($node);
    }
    return true;
}
