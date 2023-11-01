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

namespace local_brandconfig;
// moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class loginconfig extends \moodleform
{

    // Add elements to form
    public function definition()
    {
        global $CFG, $user;

        $mform = $this->_form; // Don't forget the underscore! 
        $editoroptions = $this->_customdata['editoroptions'];
        list($instance) = $this->_customdata;
        $companyid = $this->_customdata['companyid'];
        $loginfields = $this->_customdata['loginfields'];
       
        $mform->addElement('hidden', 'id', $instance->id);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'companyid', $companyid);
        $mform->setType('companyid', PARAM_INT);

        $mform->addElement('filemanager', 'loginlogo_filemanager', get_string('uploadlogo', 'local_brandconfig'), null, $editoroptions);
        $mform->addRule('loginlogo_filemanager', get_string('required'), 'required');
       
        $mform->addElement('filemanager', 'backlogo_filemanager', get_string('uploadbackground', 'local_brandconfig'), null, $editoroptions);
        $mform->addRule('backlogo_filemanager', get_string('required'), 'required');
      
        $mform->addElement('iomad_colourpicker', 'color', get_string('selectcolor', 'local_brandconfig'), 'size="20"');
        $mform->addRule('color', get_string('required'), 'required');
        $mform->setType('color', PARAM_CLEAN);
        
        for ($i=0; $i < count($loginfields); $i++) { 
            $mform->addElement('text', $loginfields[$i], get_string($loginfields[$i], 'local_brandconfig'));
            $mform->addRule($loginfields[$i], get_string('required'), 'required');
        }
    
        $this->add_action_buttons();
        $this->set_data($instance);
    }
    // Custom validation should be added here.
    function validation($data, $files)
    {
        global $CFG, $DB, $USER;

        $validated = array();
        $data = (object)$data;
     
        return $validated;
    }
}
