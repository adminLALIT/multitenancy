<?php
namespace local_brandconfig;

use html_writer;
use moodle_url;
use table_sql;
/**
 * team_list_form class to be put in team_list_form.php of root of Moodle installation.
 *  for defining some custom column names and proccessing.
 */

class brandlist extends table_sql
{
  /**
   * Constructor
   * @param int $uniqueid all tables have to have a unique id, this is used
   *      as a key when storing table properties like sort order in the session.
   */
  function __construct($uniqueid)
  {
    parent::__construct($uniqueid);
    // Define the list of columns to show.
    $columns = array('name', 'theme', 'brandlogo', 'maincolor', 'action');
    $this->define_columns($columns);

    // Define the titles of columns to show in header.
    $headers = array(get_string('company', 'local_brandconfig'), get_string('theme'), get_string('brandlogo', 'local_brandconfig'), get_string('maincolor', 'block_iomad_company_admin'), get_string('action'));
    $this->define_headers($headers);
  }
   /**
   * This function is called for each data row to allow processing of the
   * brandlogo value.
   *
   * @param object $values Contains object with all the values of record.
   */
  function col_maincolor($values)
  {
    global $CFG, $DB;
    return html_writer::span("","", ['style' => 'float: left;background:'.$values->maincolor.';width:15px;height:15px;']);
  }

  /**
   * This function is called for each data row to allow processing of the
   * brandlogo value.
   *
   * @param object $values Contains object with all the values of record.
   */
  function col_brandlogo($values)
  {
    global $CFG, $DB;
    $imageurl = get_logo_by_brandid($values->id);
    return html_writer::div("<img src='$imageurl' width='80' height='80'>");
  }

  /**
   * This function is called for each data row to allow processing of the
   * action value.
   *
   * @param object $values Contains object with all the values of record.
   */

  function col_action($values)
  {
    global $CFG, $DB, $OUTPUT;
    $baseurl = new moodle_url('/local/brandconfig/managebrand.php');
    $url = new moodle_url('addbrand.php', array('delete' => 1, 'id' => $values->id, 'returnurl' => $baseurl));
    $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/delete', 'Delete'));
    $url = new moodle_url('addbrand.php', array('id' => $values->id));
    $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/edit', 'Edit'));

    return implode(' ', $buttons);
  }
}
