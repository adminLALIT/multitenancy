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

 function local_brandconfig_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {

  if ($context->contextlevel != CONTEXT_SYSTEM) {
      send_file_not_found();
  }

  $fs = get_file_storage();
  $file = $fs->get_file($context->id, 'local_brandconfig', $filearea, $args[0], '/', $args[1]);

  send_stored_file($file);
}


function get_logo_by_brandid($id){
    global $CFG;
    $context = context_system::instance();
    $fs = get_file_storage();
    // get the image
    $files = $fs->get_area_files($context->id, 'local_brandconfig', 'brandlogo', $id, "timemodified", false);
      foreach ($files as $file) {
        $filename = $file->get_filename();
        $mimetype = $file->get_mimetype();
        $imageurl = file_encode_url($CFG->wwwroot . '/pluginfile.php', '/' . $context->id . '/local_brandconfig/brandlogo/' . $id . '/' . $filename);
      }
      if ($imageurl) {
        # code...
        return $imageurl;
      }
      else {
        return null;
      }
}

function get_login_fields(){
  return array('usernametext',
  'passwordtext',
  'forgetpasstext',
  'signuptext',
  'signintext',
  'footercopyrighttext',
  'loginbannertext',
  'logintitletext');
}

?>