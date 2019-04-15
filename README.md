# moodle-report_cohortdetail #

This plugin is under development, but works as it stands

Provides a report on cohorts members and use of them as enrolment method

For a selected cohort, search and display :
- cohort's member list
- courses list which use this cohort as enrolment method
  Show only courses where users have capacity enrol/cohort:config

Without take care of selected cohort
- courses list with cohorts use as enrolment method
  course must have at least one cohort enrolment method
  Show only courses where users have capacity enrol/cohort:config

Users need to have capacity moodle/cohort:view in SYSTEM context to access report

## Installation ##

Unzip the report in the /report directory of your Moodle installation.

Rename the folder as "cohortdetail"

## License ##

2019 Bruno Malaval <bruno.malaval@uha.fr>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.
