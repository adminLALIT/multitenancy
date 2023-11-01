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

class addbrand extends \moodleform
{

    // Add elements to form
    public function definition()
    {
        global $CFG, $user;

        $mform = $this->_form; // Don't forget the underscore! 
        $editoroptions = $this->_customdata['editoroptions'];
        list($instance) = $this->_customdata;
        $companyid = $this->_customdata['companyid'];
       
        $mform->addElement('hidden', 'brandid', $instance->id);
        $mform->setType('brandid', PARAM_INT);
        $mform->addElement('hidden', 'companyid', $companyid);
        $mform->setType('companyid', PARAM_INT);

        $themes = get_plugin_list('theme');
        $themeselectarray = array();
        foreach ($themes as $themename => $themedir) {

            // Load the theme config.
            try {
                $theme = \theme_config::load($themename);
            } catch (Exception $e) {
                // Bad theme, just skip it for now.
                continue;
            }
            if ($themename !== $theme->name) {
                // Obsoleted or broken theme, just skip for now.
                continue;
            }
            if (!$CFG->themedesignermode && $theme->hidefromselector) {
                // The theme doesn't want to be shown in the theme selector and as theme
                // designer mode is switched off we will respect that decision.
                continue;
            }

            // Build the theme selection list.
            $themeselectarray[$themename] = get_string('pluginname', 'theme_' . $themename);
        }

        $mform->addElement(
            'select',
            'theme',
            get_string('selectatheme', 'block_iomad_company_admin'),
            $themeselectarray
        );
        $mform->getElement('theme')->setSelected('iomadboost');


        $mform->addElement('filemanager', 'brandlogo_filemanager', get_string('uploadlogo', 'local_brandconfig'), null, $editoroptions);
        if (!$instance->id) {
            $mform->addRule('brandlogo_filemanager', get_string('required'), 'required');
        }
        $mform->addElement('iomad_colourpicker', 'maincolor', get_string('maincolor', 'block_iomad_company_admin'), 'size="20"');
        $mform->addRule('maincolor', get_string('required'), 'required');
        $mform->setType('maincolor', PARAM_CLEAN);

        $mform->addElement('textarea', 'customcss', get_string("customcss", "local_brandconfig"), 'wrap="virtual" rows="8" cols="60"');
        $mform->addElement('textarea', 'footertext', get_string("footertext", "local_brandconfig"), 'wrap="virtual" rows="8" cols="60"');

        $this->add_action_buttons();
        $this->set_data($instance);
    }
    // Custom validation should be added here.
    function validation($data, $files)
    {
        global $CFG, $DB, $USER;

        $validated = array();
        $data = (object)$data;
        if (empty($data->brandid)) {
            if ($DB->record_exists('brandetails', ['companyid' => $data->companyid])) {
               $validated['theme'] = get_string('recordexist', 'local_brandconfig');
            }
        }
        return $validated;
    }
}
