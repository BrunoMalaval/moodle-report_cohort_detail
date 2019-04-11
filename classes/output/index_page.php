<?php
// Standard GPL and phpdocs
namespace report_cohortdetail\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

class index_page implements renderable, templatable {

    /** @var string $sometext Some text to show how to pass data to a template. */

    var $sometext = null;

    public function __construct($sometext) {

        $this->sometext = $sometext;

    }

    public function export_for_template(renderer_base $output) {

        $data = new stdClass();

        $data->sometext = $this->sometext;

        return $data;

    }
}
