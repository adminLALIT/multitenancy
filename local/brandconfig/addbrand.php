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
 * Run the code checker from the web.
 *
 * @package    local_brandconfig
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Include theme_form.php.
require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/iomad_company_admin/includes/colourpicker.php');

require_login();
\MoodleQuickForm::registerElementType('iomad_colourpicker',
    $CFG->dirroot . '/blocks/iomad_company_admin/includes/colourpicker.php', 'MoodleQuickForm_iomad_colourpicker');

global $CFG, $PAGE;
$id = optional_param('id', 0, PARAM_INT);
$return = new moodle_url('/local/brandconfig/manage.php');
$delete = optional_param('delete', 0, PARAM_BOOL);
$confirm = optional_param('confirm', 0, PARAM_BOOL);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

$context = context_system::instance();

if (!has_capability('local/brandconfig:manage', $context)) {
throw new moodle_exception(get_string('nopermission', 'local_brandconfig'), 'core');
}
$companyid = iomad::get_my_companyid($context);

$PAGE->set_url('/local/brandconfig/addbrand.php');
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->navbar->add(get_string('myhome'), new moodle_url('/my/index.php'));
$PAGE->navbar->add(get_string('managetheme', 'local_brandconfig'), new moodle_url('/local/brandconfig/managebrand.php'));
if ($id) {
    $PAGE->navbar->add(get_string('addbrand', 'local_brandconfig'));
} else {
    $PAGE->navbar->add(get_string('addbrand', 'local_brandconfig'));
}
$PAGE->set_title(get_string('addbrand', 'local_brandconfig'));
$PAGE->set_heading(get_string('addbrand', 'local_brandconfig'));
$companyrecord = new stdClass;
// Instantiate simplehtml_form.
$editoroptions = array(
  'maxfiles' => 1,
  'maxbytes' => 262144, 'subdirs' => 0, 'context' => $context, 'accepted_types' => array('web_image')
);
if ($id) {
    $instance = $DB->get_record('brandetails', array(
    'id' => $id
    ), '*', MUST_EXIST);
    $instance = file_prepare_standard_filemanager(
        $instance,
        'brandlogo',
        $editoroptions,
        context_system::instance(),
        'local_brandconfig',
        'brandlogo',
        $instance->id
        );

    if ($delete && $instance->id) {

        if ($confirm && confirm_sesskey()) {
            // Delete existing files first.
            $fs = get_file_storage();
            $fs->delete_area_files(context_system::instance()->id, 'local_brandconfig', 'image', $instance->id);
            $DB->delete_records('brandetails', ['id' => $instance->id]);
            redirect($returnurl);
        }
        $strheading = get_string('deletebrand', 'local_brandconfig');
        $PAGE->navbar->add($strheading);
        $PAGE->set_title($strheading);
        echo $OUTPUT->header();
        echo $OUTPUT->heading($strheading);
        $yesurl = new moodle_url('/local/brandconfig/addbrand.php', array(
          'id' => $instance->id, 'delete' => 1,
          'confirm' => 1, 'sesskey' => sesskey(), 'returnurl' => $returnurl
        ));
        $message = get_string('deletebrandconfirm', 'local_brandconfig');
        echo $OUTPUT->confirm($message, $yesurl, $returnurl);
        echo $OUTPUT->footer();
        die;
    }
} else {
    $instance = new stdClass();
    $instance->id = null;
    // $instance = file_prepare_standard_filemanager(
    // $instance,
    // 'image',
    // $editoroptions,
    // context_system::instance(),
    // 'local_brandconfig',
    // 'image',
    // null
    // );
}

$mform = new \local_brandconfig\addbrand($PAGE->url, array(
  'editoroptions' => $editoroptions, 'companyid' => $companyid, $instance
));
// Form processing and displaying is done here.
if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . '/local/brandconfig/managebrand.php');
    // Handle form cancel operation, if cancel button is present on form.
} else if ($fromform = $mform->get_data()) {
    $fromform->timemodified = time();
  
    if ($fromform->brandid) {  // If we edit the theme.
        $fromform->id = $fromform->brandid;
        $ins = file_postupdate_standard_filemanager(
            $fromform,
            'brandlogo',
            $editoroptions,
            context_system::instance(),
            'local_brandconfig',
            'brandlogo',
            $fromform->id
          );

        $ins->id = $fromform->brandid;
        $updated = $DB->update_record('brandetails', $ins);
        if ($updated) {
            redirect($CFG->wwwroot . '/local/brandconfig/managebrand.php', get_string('recordupdated', 'local_brandconfig'), null, \core\output\notification::NOTIFY_INFO);
        }
    } else {  // Create a new theme.

        $fromform->creatorid = $USER->id;
        $fromform->timecreated = time();
        $brandid = $DB->insert_record('brandetails', $fromform, $returnid = true, $bulk = false);
        $editoroptions['context'] = $context;
        $ins = file_postupdate_standard_filemanager(
            $fromform,
            'brandlogo',
            $editoroptions,
            context_system::instance(),
            'local_brandconfig',
            'brandlogo',
            $brandid
          );
        $ins->id = $brandid;
        $ins->id = $DB->update_record('brandetails', $ins);
    }
    // In this case you process validated data. $mform->get_data() returns data posted in form.
    if ($ins->id) {
        redirect($CFG->wwwroot . '/local/brandconfig/managebrand.php', get_string('recordcreated', 'local_brandconfig'), null, \core\output\notification::NOTIFY_INFO);
    }
}
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
