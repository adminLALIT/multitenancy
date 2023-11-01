<?php

function xmldb_local_brandconfig_upgrade($oldversion): bool
{
    global $CFG, $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2023030915) {

        // Perform the upgrade from version 2023051103 to the next version.

        $table = new xmldb_table('brandetails');
        if (!$dbman->table_exists($table)) {
            $dbman->install_one_table_from_xmldb_file(__DIR__ . '/install.xml', 'brandetails');
        }

        $table = new xmldb_table('loginconfig');
        if (!$dbman->table_exists($table)) {
            $dbman->install_one_table_from_xmldb_file(__DIR__ . '/install.xml', 'loginconfig');
        }

        $table = new xmldb_table('role_based_blocks');
        if (!$dbman->table_exists($table)) {
            $dbman->install_one_table_from_xmldb_file(__DIR__ . '/install.xml', 'role_based_blocks');
        }
       
    }

    if ($oldversion < 2023051103) {
        // Perform the upgrade from version 2023051103 to the next version.

        // The content of this section should be generated using the XMLDB Editor.
    }

    // Everything has succeeded to here. Return true.
    return true;
}
