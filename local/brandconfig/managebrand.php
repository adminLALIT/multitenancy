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
require "$CFG->libdir/tablelib.php";
require('lib.php');
require_login();
$tab = optional_param('t', 1, PARAM_INT);
// $systemcontext = context_system::instance();
// $usercontext = context_user::instance($USER->id);
// var_dump($systemcontext->id);
// var_dump($usercontext->id);
// die();

$context = context_system::instance();

$companyid = iomad::get_my_companyid($context);

if (!has_capability('local/brandconfig:manage', $context)) {
    throw new moodle_exception(get_string('nopermission', 'local_brandconfig'), 'core');
}

$PAGE->set_url('/local/brandconfig/managebrand.php');
$PAGE->set_context($context);
$PAGE->set_title(get_string('managetheme', 'local_brandconfig'));
$PAGE->set_heading(get_string('managetheme', 'local_brandconfig'));
$PAGE->navbar->add(get_string('myhome'), new moodle_url('/my/index.php'));
$PAGE->navbar->add(get_string('managetheme', 'local_brandconfig'), '');
$table = new \local_brandconfig\brandlist('uniqueid');
$logintable = new \local_brandconfig\loginconfiglist('uniqueid');    // Login configuration table.

$tabs = [];
$tabs[] = new tabobject(1, new moodle_url($PAGE->url, ['t' => 1]), get_string('themebrand', 'local_brandconfig'));
$tabs[] = new tabobject(2, new moodle_url($PAGE->url, ['t' => 2]), get_string('loginpage', 'local_brandconfig'));
$tabs[] = new tabobject(3, new moodle_url($PAGE->url, ['t' => 3]), get_string('landingpage', 'local_brandconfig'));

echo $OUTPUT->header();
echo $OUTPUT->tabtree($tabs, $tab);

// For theme brand tab.
if ($tab == 1) {
    $where = 'bd.companyid = ' .$companyid;
    $field = 'bd.*, c.name';
    $from = '{brandetails} bd JOIN {company} c ON c.id = bd.companyid';
    // Work out the sql for the table.
    $table->set_sql($field, $from, $where);
    $table->define_baseurl("$CFG->wwwroot/local/brandconfig/managebrand.php");
    $table->no_sorting('action');
    $table->no_sorting('brandlogo');
    echo html_writer::link('addbrand.php', get_string('addbrand', 'local_brandconfig'), ['style' => 'float:right;', 'class' => 'btn btn-primary']);
    echo "<br>";
    echo "<br>";
    $table->out(10, true);
}

if ($tab == 2) {
    echo html_writer::link('loginsetup.php', get_string('loginsetup', 'local_brandconfig'), ['style' => 'float:right;', 'class' => 'btn btn-primary']);
    echo "<br>";
    echo "<br>";
    $where = 'lc.companyid = ' .$companyid;
    $field = 'lc.*, c.name';
    $from = '{loginconfig} lc JOIN {company} c ON c.id = lc.companyid';
    // Work out the sql for the table.
    $logintable->set_sql($field, $from, $where);
    $logintable->define_baseurl("$CFG->wwwroot/local/brandconfig/managebrand.php?t=2");
    $logintable->no_sorting('action');
    $logintable->no_sorting('brandlogo');
    $logintable->out(10, true);

}

echo $OUTPUT->footer();
