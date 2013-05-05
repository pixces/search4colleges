<?php

/**
 * adminlib.php - Contains functions that only administrators will ever need to use
 *
 * @author Martin Dougiamas and many others
 * @version  $Id: adminlib.php,v 1.153.2.75 2010/04/10 15:11:50 iarenaza Exp $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

define('INSECURE_DATAROOT_WARNING', 1);
define('INSECURE_DATAROOT_ERROR', 2);

function upgrade_main_savepoint($result, $version) {
    global $CFG;

    if ($result) {
        if ($CFG->version >= $version) {
            // something really wrong is going on in main upgrade script
            error("Upgrade savepoint: Can not upgrade main version from $CFG->version to $version.");
        }
        set_config('version', $version);
    } else {
        notify ("Upgrade savepoint: Error during main upgrade to version $version");
    }
}

function upgrade_mod_savepoint($result, $version, $type) {
    //TODO
}

function upgrade_plugin_savepoint($result, $version, $type, $dir) {
    //TODO
}

function upgrade_backup_savepoint($result, $version) {
    //TODO
}

function upgrade_blocks_savepoint($result, $version, $type) {
    //TODO
}

/**
 * Upgrade plugins
 *
 * @uses $db
 * @uses $CFG
 * @param string $type The type of plugins that should be updated (e.g. 'enrol', 'qtype')
 * @param string $dir  The directory where the plugins are located (e.g. 'question/questiontypes')
 * @param string $return The url to prompt the user to continue to
 */
function upgrade_plugins($type, $dir, $return) {
    global $CFG, $db;

/// Let's know if the header has been printed, so the funcion is being called embedded in an outer page
    $embedded = defined('HEADER_PRINTED');

    $plugs = get_list_of_plugins($dir);
    $updated_plugins = false;
    $strpluginsetup  = get_string('pluginsetup');

    foreach ($plugs as $plug) {

        $fullplug = $CFG->dirroot .'/'.$dir.'/'. $plug;

        unset($plugin);

        if (is_readable($fullplug .'/version.php')) {
            include_once($fullplug .'/version.php');  // defines $plugin with version etc
        } else {
            continue;                              // Nothing to do.
        }

        $oldupgrade = false;
        $newupgrade = false;
        if (is_readable($fullplug . '/db/'. $CFG->dbtype . '.php')) {
            include_once($fullplug . '/db/'. $CFG->dbtype . '.php');  // defines old upgrading function
            $oldupgrade = true;
        }
        if (is_readable($fullplug . '/db/upgrade.php')) {
            include_once($fullplug . '/db/upgrade.php');  // defines new upgrading function
            $newupgrade = true;
        }

        if (!isset($plugin)) {
            continue;
        }

        if (!empty($plugin->requires)) {
            if ($plugin->requires > $CFG->version) {
                $info = new object();
                $info->pluginname = $plug;
                $info->pluginversion  = $plugin->version;
                $info->currentmoodle = $CFG->version;
                $info->requiremoodle = $plugin->requires;
                if (!$updated_plugins && !$embedded) {
                    print_header($strpluginsetup, $strpluginsetup,
                        build_navigation(array(array('name' => $strpluginsetup, 'link' => null, 'type' => 'misc'))), '',
                        upgrade_get_javascript(), false, '&nbsp;', '&nbsp;');
                }
                upgrade_log_start();
                notify(get_string('pluginrequirementsnotmet', 'error', $info));
                $updated_plugins = true;
                continue;
            }
        }

        $plugin->name = $plug;   // The name MUST match the directory

        $pluginversion = $type.'_'.$plug.'_version';

        if (!isset($CFG->$pluginversion)) {
            set_config($pluginversion, 0);
        }

        if ($CFG->$pluginversion == $plugin->version) {
            // do nothing
        } else if ($CFG->$pluginversion < $plugin->version) {
            if (!$updated_plugins && !$embedded) {
                print_header($strpluginsetup, $strpluginsetup,
                        build_navigation(array(array('name' => $strpluginsetup, 'link' => null, 'type' => 'misc'))), '',
                        upgrade_get_javascript(), false, '&nbsp;', '&nbsp;');
            }
            $updated_plugins = true;
            upgrade_log_start();
            print_heading($dir.'/'. $plugin->name .' plugin needs upgrading');
            $db->debug = true;
            @set_time_limit(0);  // To allow slow databases to complete the long SQL

            if ($CFG->$pluginversion == 0) {    // It's a new install of this plugin
            /// Both old .sql files and new install.xml are supported
            /// but we priorize install.xml (XMLDB) if present
                $status = false;
                if (file_exists($fullplug . '/db/install.xml')) {
                    $status = install_from_xmldb_file($fullplug . '/db/install.xml'); //New method
                } else if (file_exists($fullplug .'/db/'. $CFG->dbtype .'.sql')) {
                    $status = modify_database($fullplug .'/db/'. $CFG->dbtype .'.sql'); //Old method
                } else {
                    $status = true;
                }

                $db->debug = false;
            /// Continue with the instalation, roles and other stuff
                if ($status) {
                /// OK so far, now update the plugins record
                    set_config($pluginversion, $plugin->version);

                /// Install capabilities
                    if (!update_capabilities($type.'/'.$plug)) {
                        error('Could not set up the capabilities for '.$plugin->name.'!');
                    }
                /// Install events
                    events_update_definition($type.'/'.$plug);

                /// Run local install function if there is one
                    if (is_readable($fullplug .'/lib.php')) {
                        include_once($fullplug .'/lib.php');
                        $installfunction = $plugin->name.'_install';
                        if (function_exists($installfunction)) {
                            if (! $installfunction() ) {
                                notify('Encountered a problem running install function for '.$plugin->name.'!');
                            }
                        }
                    }

                    notify(get_string('modulesuccess', '', $plugin->name), 'notifysuccess');
                } else {
                    notify('Installing '. $plugin->name .' FAILED!');
                }
            } else {                            // Upgrade existing install
            /// Run de old and new upgrade functions for the module
                $oldupgrade_function = $type.'_'.$plugin->name .'_upgrade';
                $newupgrade_function = 'xmldb_' . $type.'_'.$plugin->name .'_upgrade';

            /// First, the old function if exists
                $oldupgrade_status = true;
                if ($oldupgrade && function_exists($oldupgrade_function)) {
                    $db->debug = true;
                    $oldupgrade_status = $oldupgrade_function($CFG->$pluginversion);
                } else if ($oldupgrade) {
                    notify ('Upgrade function ' . $oldupgrade_function . ' was not available in ' .
                             $fullplug . '/db/' . $CFG->dbtype . '.php');
                }

            /// Then, the new function if exists and the old one was ok
                $newupgrade_status = true;
                if ($newupgrade && function_exists($newupgrade_function) && $oldupgrade_status) {
                    $db->debug = true;
                    $newupgrade_status = $newupgrade_function($CFG->$pluginversion);
                } else if ($newupgrade) {
                    notify ('Upgrade function ' . $newupgrade_function . ' was not available in ' .
                             $fullplug . '/db/upgrade.php');
                }

                $db->debug=false;
            /// Now analyze upgrade results
                if ($oldupgrade_status && $newupgrade_status) {    // No upgrading failed
                    // OK so far, now update the plugins record
                    set_config($pluginversion, $plugin->version);
                    if (!update_capabilities($type.'/'.$plug)) {
                        error('Could not update '.$plugin->name.' capabilities!');
                    }
                    events_update_definition($type.'/'.$plug);
                    notify(get_string('modulesuccess', '', $plugin->name), 'notifysuccess');
                } else {
                    notify('Upgrading '. $plugin->name .' from '. $CFG->$pluginversion .' to '. $plugin->version .' FAILED!');
                }
            }
            echo '<hr />';
        } else {
            upgrade_log_start();
            error('Version mismatch: '. $plugin->name .' can\'t downgrade '. $CFG->$pluginversion .' -> '. $plugin->version .' !');
        }
    }

    upgrade_log_finish();

    if ($updated_plugins && !$embedded) {
        print_continue($return);
        print_footer('none');
        die;
    }
}

/**
 * Find and check all modules and load them up or upgrade them if necessary
 *
 * @uses $db
 * @uses $CFG
 * @param string $return The url to prompt the user to continue to
 * @todo Finish documenting this function
 */
function upgrade_activity_modules($return) {

    global $CFG, $db;

    if (!$mods = get_list_of_plugins('mod') ) {
        error('No modules installed!');
    }

    $updated_modules = false;
    $strmodulesetup  = get_string('modulesetup');

    foreach ($mods as $mod) {

        if ($mod == 'NEWMODULE') {   // Someone has unzipped the template, ignore it
            continue;
        }

        $fullmod = $CFG->dirroot .'/mod/'. $mod;

        unset($module);

        if ( is_readable($fullmod .'/version.php')) {
            include_once($fullmod .'/version.php');  // defines $module with version etc
        } else {
            notify('Module '. $mod .': '. $fullmod .'/version.php was not readable');
            continue;
        }

        $oldupgrade = false;
        $newupgrade = false;
        if ( is_readable($fullmod .'/db/' . $CFG->dbtype . '.php')) {
            include_once($fullmod .'/db/' . $CFG->dbtype . '.php');  // defines old upgrading function
            $oldupgrade = true;
        }
        if ( is_readable($fullmod . '/db/upgrade.php')) {
            include_once($fullmod . '/db/upgrade.php');  // defines new upgrading function
            $newupgrade = true;
        }

        if (!isset($module)) {
            continue;
        }

        if (!empty($module->requires)) {
            if ($module->requires > $CFG->version) {
                $info = new object();
                $info->modulename = $mod;
                $info->moduleversion  = $module->version;
                $info->currentmoodle = $CFG->version;
                $info->requiremoodle = $module->requires;
                if (!$updated_modules) {
                    print_header($strmodulesetup, $strmodulesetup,
                            build_navigation(array(array('name' => $strmodulesetup, 'link' => null, 'type' => 'misc'))), '',
                            upgrade_get_javascript(), false, '&nbsp;', '&nbsp;');
                }
                upgrade_log_start();
                notify(get_string('modulerequirementsnotmet', 'error', $info));
                $updated_modules = true;
                continue;
            }
        }

        $module->name = $mod;   // The name MUST match the directory

        include_once($fullmod.'/lib.php');  // defines upgrading and/or installing functions

        if ($currmodule = get_record('modules', 'name', $module->name)) {
            if ($currmodule->version == $module->version) {
                // do nothing
            } else if ($currmodule->version < $module->version) {
            /// If versions say that we need to upgrade but no upgrade files are available, notify and continue
                if (!$oldupgrade && !$newupgrade) {
                    notify('Upgrade files ' . $mod . ': ' . $fullmod . '/db/' . $CFG->dbtype . '.php or ' .
                                                            $fullmod . '/db/upgrade.php were not readable');
                    continue;
                }
                if (!$updated_modules) {
                    print_header($strmodulesetup, $strmodulesetup,
                            build_navigation(array(array('name' => $strmodulesetup, 'link' => null, 'type' => 'misc'))), '',
                            upgrade_get_javascript(), false, '&nbsp;', '&nbsp;');
                }
                upgrade_log_start();
                print_heading($module->name .' module needs upgrading');

            /// Run de old and new upgrade functions for the module
                $oldupgrade_function = $module->name . '_upgrade';
                $newupgrade_function = 'xmldb_' . $module->name . '_upgrade';

            /// First, the old function if exists
                $oldupgrade_status = true;
                if ($oldupgrade && function_exists($oldupgrade_function)) {
                    $db->debug = true;
                    $oldupgrade_status = $oldupgrade_function($currmodule->version, $module);
                    if (!$oldupgrade_status) {
                        notify ('Upgrade function ' . $oldupgrade_function .
                                ' did not complete successfully.');
                    }
                } else if ($oldupgrade) {
                    notify ('Upgrade function ' . $oldupgrade_function . ' was not available in ' .
                             $mod . ': ' . $fullmod . '/db/' . $CFG->dbtype . '.php');
                }

            /// Then, the new function if exists and the old one was ok
                $newupgrade_status = true;
                if ($newupgrade && function_exists($newupgrade_function) && $oldupgrade_status) {
                    $db->debug = true;
                    $newupgrade_status = $newupgrade_function($currmodule->version, $module);
                } else if ($newupgrade && $oldupgrade_status) {
                    notify ('Upgrade function ' . $newupgrade_function . ' was not available in ' .
                             $mod . ': ' . $fullmod . '/db/upgrade.php');
                }

                $db->debug=false;
            /// Now analyze upgrade results
                if ($oldupgrade_status && $newupgrade_status) {    // No upgrading failed
                    // OK so far, now update the modules record
                    $module->id = $currmodule->id;
                    if (! update_record('modules', $module)) {
                        error('Could not update '. $module->name .' record in modules table!');
                    }
                    remove_dir($CFG->dataroot . '/cache', true); // flush cache
                    notify(get_string('modulesuccess', '', $module->name), 'notifysuccess');
                    echo '<hr />';
                } else {
                    notify('Upgrading '. $module->name .' from '. $currmodule->version .' to '. $module->version .' FAILED!');
                }

            /// Update the capabilities table?
                if (!update_capabilities('mod/'.$module->name)) {
                    error('Could not update '.$module->name.' capabilities!');
                }
                events_update_definition('mod/'.$module->name);

                $updated_modules = true;

            } else {
                upgrade_log_start();
                error('Version mismatch: '. $module->name .' can\'t downgrade '. $currmodule->version .' -> '. $module->version .' !');
            }

        } else {    // module not installed yet, so install it
            if (!$updated_modules) {
                print_header($strmodulesetup, $strmodulesetup,
                        build_navigation(array(array('name' => $strmodulesetup, 'link' => null, 'type' => 'misc'))), '',
                        upgrade_get_javascript(), false, '&nbsp;', '&nbsp;');
            }
            upgrade_log_start();
            print_heading($module->name);
            $updated_modules = true;
            $db->debug = true;
            @set_time_limit(0);  // To allow slow databases to complete the long SQL

        /// Both old .sql files and new install.xml are supported
        /// but we priorize install.xml (XMLDB) if present
            if (file_exists($fullmod . '/db/install.xml')) {
                $status = install_from_xmldb_file($fullmod . '/db/install.xml'); //New method
            } else {
                $status = modify_database($fullmod .'/db/'. $CFG->dbtype .'.sql'); //Old method
            }

            $db->debug = false;

        /// Continue with the installation, roles and other stuff
            if ($status) {
                if ($module->id = insert_record('modules', $module)) {

                /// Capabilities
                    if (!update_capabilities('mod/'.$module->name)) {
                        error('Could not set up the capabilities for '.$module->name.'!');
                    }

                /// Events
                    events_update_definition('mod/'.$module->name);

                /// Run local install function if there is one
                    $installfunction = $module->name.'_install';
                    if (function_exists($installfunction)) {
                        if (! $installfunction() ) {
                            notify('Encountered a problem running install function for '.$module->name.'!');
                        }
                    }

                    notify(get_string('modulesuccess', '', $module->name), 'notifysuccess');
                    echo '<hr />';
                } else {
                    error($module->name .' module could not be added to the module list!');
                }
            } else {
                error($module->name .' tables could NOT be set up successfully!');
            }
        }

    /// Check submodules of this module if necessary

        $submoduleupgrade = $module->name.'_upgrade_submodules';
        if (function_exists($submoduleupgrade)) {
            $submoduleupgrade();
        }


    /// Run any defaults or final code that is necessary for this module

        if ( is_readable($fullmod .'/defaults.php')) {
            // Insert default values for any important configuration variables
            unset($defaults);
            include($fullmod .'/defaults.php'); // include here means execute, not library include
            if (!empty($defaults)) {
                foreach ($defaults as $name => $value) {
                    if (!isset($CFG->$name)) {
                        set_config($name, $value);
                    }
                }
            }
        }
    }

    upgrade_log_finish(); // finish logging if started

    if ($updated_modules) {
        print_continue($return);
        print_footer('none');
        die;
    }
}

/**
 * Try to obtain or release the cron lock.
 *
 * @param string  $name  name of lock
 * @param int  $until timestamp when this lock considered stale, null means remove lock unconditionaly
 * @param bool $ignorecurrent ignore current lock state, usually entend previous lock
 * @return bool true if lock obtained
 */
function set_cron_lock($name, $until, $ignorecurrent=false) {
    if (empty($name)) {
        debugging("Tried to get a cron lock for a null fieldname");
        return false;
    }

    // remove lock by force == remove from config table
    if (is_null($until)) {
        set_config($name, null);
        return true;
    }

    if (!$ignorecurrent) {
        // read value from db - other processes might have changed it
        $value = get_field('config', 'value', 'name', $name);

        if ($value and $value > time()) {
            //lock active
            return false;
        }
    }

    set_config($name, $until);
    return true;
}

function print_progress($done, $total, $updatetime=5, $sleeptime=1, $donetext='') {
    static $thisbarid;
    static $starttime;
    static $lasttime;

    if ($total < 2) {   // No need to show anything
        return;
    }

    // Are we done?
    if ($done >= $total) {
        $done = $total;
        if (!empty($thisbarid)) {
            $donetext .= ' ('.$done.'/'.$total.') '.get_string('success');
            print_progress_redraw($thisbarid, $done, $total, 500, $donetext);
            $thisbarid = $starttime = $lasttime = NULL;
        }
        return;
    }

    if (empty($starttime)) {
        $starttime = $lasttime = time();
        $lasttime = $starttime - $updatetime;
        $thisbarid = uniqid();
        echo '<table width="500" cellpadding="0" cellspacing="0" align="center"><tr><td width="500">';
        echo '<div id="bar'.$thisbarid.'" style="border-style:solid;border-width:1px;width:500px;height:50px;">';
        echo '<div id="slider'.$thisbarid.'" style="border-style:solid;border-width:1px;height:48px;width:10px;background-color:green;"></div>';
        echo '</div>';
        echo '<div id="text'.$thisbarid.'" align="center" style="width:500px;"></div>';
        echo '</td></tr></table>';
        echo '</div>';
    }

    $now = time();

    if ($done && (($now - $lasttime) >= $updatetime)) {
        $elapsedtime = $now - $starttime;
        $projectedtime = (int)(((float)$total / (float)$done) * $elapsedtime) - $elapsedtime;
        $percentage = round((float)$done / (float)$total, 2);
        $width = (int)(500 * $percentage);

        if ($projectedtime > 10) {
            $projectedtext = '  Ending: '.format_time($projectedtime);
        } else {
            $projectedtext = '';
        }

        $donetext .= ' ('.$done.'/'.$total.') '.$projectedtext;
        print_progress_redraw($thisbarid, $done, $total, $width, $donetext);

        $lasttime = $now;
    }
}

// Don't call this function directly, it's called from print_progress.
function print_progress_redraw($thisbarid, $done, $total, $width, $donetext='') {
    if (empty($thisbarid)) {
        return;
    }
    echo '<script>';
    echo 'document.getElementById("text'.$thisbarid.'").innerHTML = "'.addslashes($donetext).'";'."\n";
    echo 'document.getElementById("slider'.$thisbarid.'").style.width = \''.$width.'px\';'."\n";
    echo '</script>';
}

function upgrade_get_javascript() {
    global $CFG;

    if (!empty($_SESSION['installautopilot'])) {
        $linktoscrolltoerrors = '<script type="text/javascript">var installautopilot = true;</script>'."\n";
    } else {
        $linktoscrolltoerrors = '<script type="text/javascript">var installautopilot = false;</script>'."\n";
    }
    $linktoscrolltoerrors .= '<script type="text/javascript" src="' . $CFG->wwwroot . '/lib/scroll_to_errors.js"></script>';

    return $linktoscrolltoerrors;
}

function create_admin_user() {
    global $CFG, $USER;

    if (empty($CFG->rolesactive)) {   // No admin user yet.

        $user = new object();
        $user->auth         = 'manual';
        $user->firstname    = get_string('admin');
        $user->lastname     = get_string('user');
        $user->username     = 'admin';
        $user->password     = hash_internal_user_password('admin');
        $user->email        = 'root@localhost';
        $user->confirmed    = 1;
        $user->mnethostid   = $CFG->mnet_localhost_id;
        $user->lang         = $CFG->lang;
        $user->maildisplay  = 1;
        $user->timemodified = time();

        if (!$user->id = insert_record('user', $user)) {
            error('SERIOUS ERROR: Could not create admin user record !!!');
        }

        if (!$user = get_record('user', 'id', $user->id)) {   // Double check.
            error('User ID was incorrect (can\'t find it)');
        }

        // Assign the default admin roles to the new user.
        if (!$adminroles = get_roles_with_capability('moodle/legacy:admin', CAP_ALLOW)) {
            error('No admin role could be found');
        }
        $sitecontext = get_context_instance(CONTEXT_SYSTEM);
        foreach ($adminroles as $adminrole) {
            role_assign($adminrole->id, $user->id, 0, $sitecontext->id);
        }

        set_config('rolesactive', 1);

        // Log the user in.
        $USER = get_complete_user_data('username', 'admin');
        $USER->newadminuser = 1;
        load_all_capabilities();

        redirect("$CFG->wwwroot/user/editadvanced.php?id=$user->id");  // Edit thyself
    } else {
        error('Can not create admin!');
    }
}

////////////////////////////////////////////////
/// upgrade logging functions
////////////////////////////////////////////////

$upgradeloghandle = false;
$upgradelogbuffer = '';
// I did not find out how to use static variable in callback function,
// the problem was that I could not flush the static buffer :-(
global $upgradeloghandle, $upgradelogbuffer;

/**
 * Check if upgrade is already running.
 *
 * If anything goes wrong due to missing call to upgrade_log_finish()
 * just restart the browser.
 *
 * @param string warning message indicating upgrade is already running
 * @param int page reload timeout
 */
function upgrade_check_running($message, $timeout) {
    if (!empty($_SESSION['upgraderunning'])) {
        print_header();
        redirect(me(), $message, $timeout);
    }
}

/**
 * Start logging of output into file (if not disabled) and
 * prevent aborting and concurrent execution of upgrade script.
 *
 * Please note that you can not write into session variables after calling this function!
 *
 * This function may be called repeatedly.
 */
function upgrade_log_start() {
    global $CFG, $upgradeloghandle;

    if (!empty($_SESSION['upgraderunning'])) {
        return; // logging already started
    }

    @ignore_user_abort(true);            // ignore if user stops or otherwise aborts page loading
    $_SESSION['upgraderunning'] = 1;     // set upgrade indicator
    if (empty($CFG->dbsessions)) {       // workaround for bug in adodb, db session can not be restarted
        session_write_close();           // from now on user can reload page - will be displayed warning
    }
    make_upload_directory('upgradelogs');
    ob_start('upgrade_log_callback', 2); // function for logging to disk; flush each line of text ASAP
    register_shutdown_function('upgrade_log_finish'); // in case somebody forgets to stop logging
}

/**
 * Terminate logging of output, flush all data, allow script aborting
 * and reopen session for writing. Function error() does terminate the logging too.
 *
 * Please make sure that each upgrade_log_start() is properly terminated by
 * this function or error().
 *
 * This function may be called repeatedly.
 */
function upgrade_log_finish() {
    global $CFG, $upgradeloghandle, $upgradelogbuffer;

    if (empty($_SESSION['upgraderunning'])) {
        return; // logging already terminated
    }

    @ob_end_flush();
    if ($upgradelogbuffer !== '') {
        @fwrite($upgradeloghandle, $upgradelogbuffer);
        $upgradelogbuffer = '';
    }
    if ($upgradeloghandle and ($upgradeloghandle !== 'error')) {
        @fclose($upgradeloghandle);
        $upgradeloghandle = false;
    }
    if (empty($CFG->dbsessions)) {
        @session_start();                // ignore header errors, we only need to reopen session
    }
    $_SESSION['upgraderunning'] = 0; // clear upgrade indicator
    if (connection_aborted()) {
        die;
    }
    @ignore_user_abort(false);
}

/**
 * Callback function for logging into files. Not more than one file is created per minute,
 * upgrade session (terminated by upgrade_log_finish()) is always stored in one file.
 *
 * This function must not output any characters or throw warnigns and errors!
 */
function upgrade_log_callback($string) {
    global $CFG, $upgradeloghandle, $upgradelogbuffer;

    if (empty($CFG->disableupgradelogging) and ($string != '') and ($upgradeloghandle !== 'error')) {
        if ($upgradeloghandle or ($upgradeloghandle = @fopen($CFG->dataroot.'/upgradelogs/upg_'.date('Ymd-Hi').'.html', 'a'))) {
            $upgradelogbuffer .= $string;
            if (strlen($upgradelogbuffer) > 2048) { // 2kB write buffer
                @fwrite($upgradeloghandle, $upgradelogbuffer);
                $upgradelogbuffer = '';
            }
        } else {
            $upgradeloghandle = 'error';
        }
    }
    return $string;
}

/**
 * Test if and critical warnings are present
 * @return bool
 */
function admin_critical_warnings_present() {
    global $SESSION;

    if (!has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
        return 0;
    }

    if (!isset($SESSION->admin_critical_warning)) {
        $SESSION->admin_critical_warning = 0;
        if (ini_get_bool('register_globals')) {
            $SESSION->admin_critical_warning = 1;
        } else if (is_dataroot_insecure(true) === INSECURE_DATAROOT_ERROR) {
            $SESSION->admin_critical_warning = 1;
        }
    }

    return $SESSION->admin_critical_warning;
}

/**
 * Detects if float support at least 10 deciman digits
 * and also if float-->string conversion works as expected.
 * @return bool true if problem found
 */
function is_float_problem() {
    $num1 = 2009010200.01;
    $num2 = 2009010200.02;

    return ((string)$num1 === (string)$num2 or $num1 === $num2 or $num2 <= (string)$num1);
}

/**
 * Try to verify that dataroot is not accessible from web.
 * It is not 100% correct but might help to reduce number of vulnerable sites.
 *
 * Protection from httpd.conf and .htaccess is not detected properly.
 * @param bool $fetchtest try to test public access by fetching file
 * @return mixed empty means secure, INSECURE_DATAROOT_ERROR found a critical problem, INSECURE_DATAROOT_WARNING migth be problematic
 */
function is_dataroot_insecure($fetchtest=false) {
    global $CFG;

    $siteroot = str_replace('\\', '/', strrev($CFG->dirroot.'/')); // win32 backslash workaround

    $rp = preg_replace('|https?://[^/]+|i', '', $CFG->wwwroot, 1);
    $rp = strrev(trim($rp, '/'));
    $rp = explode('/', $rp);
    foreach($rp as $r) {
        if (strpos($siteroot, '/'.$r.'/') === 0) {
            $siteroot = substr($siteroot, strlen($r)+1); // moodle web in subdirectory
        } else {
            break; // probably alias root
        }
    }

    $siteroot = strrev($siteroot);
    $dataroot = str_replace('\\', '/', $CFG->dataroot.'/');

    if (strpos($dataroot, $siteroot) !== 0) {
        return false;
    }

    if (!$fetchtest) {
        return INSECURE_DATAROOT_WARNING;
    }

    // now try all methods to fetch a test file using http protocol

    $httpdocroot = str_replace('\\', '/', strrev($CFG->dirroot.'/'));
    preg_match('|(https?://[^/]+)|i', $CFG->wwwroot, $matches);
    $httpdocroot = $matches[1];
    $datarooturl = $httpdocroot.'/'. substr($dataroot, strlen($siteroot));
    if (make_upload_directory('diag', false) === false) {
        return INSECURE_DATAROOT_WARNING;
    }
    $testfile = $CFG->dataroot.'/diag/public.txt';
    if (!file_exists($testfile)) {
        file_put_contents($testfile, 'test file, do not delete');
    }
    $teststr = trim(file_get_contents($testfile));
    if (empty($teststr)) {
        // hmm, strange
        return INSECURE_DATAROOT_WARNING;
    }

    $testurl = $datarooturl.'/diag/public.txt';
    if (extension_loaded('curl') and
        !(stripos(ini_get('disable_functions'), 'curl_init') !== FALSE) and
        !(stripos(ini_get('disable_functions'), 'curl_setop') !== FALSE) and
        ($ch = @curl_init($testurl)) !== false) {
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $data = curl_exec($ch);
        if (!curl_errno($ch)) {
            $data = trim($data);
            if ($data === $teststr) {
                curl_close($ch);
                return INSECURE_DATAROOT_ERROR;
            }
        }
        curl_close($ch);
    }

    if ($data = @file_get_contents($testurl)) {
        $data = trim($data);
        if ($data === $teststr) {
            return INSECURE_DATAROOT_ERROR;
        }
    }

    preg_match('|https?://([^/]+)|i', $testurl, $matches);
    $sitename = $matches[1];
    $error = 0;
    if ($fp = @fsockopen($sitename, 80, $error)) {
        preg_match('|https?://[^/]+(.*)|i', $testurl, $matches);
        $localurl = $matches[1];
        $out = "GET $localurl HTTP/1.1\r\n";
        $out .= "Host: $sitename\r\n";
        $out .= "Connection: Close\r\n\r\n";
        fwrite($fp, $out);
        $data = '';
        $incoming = false;
        while (!feof($fp)) {
            if ($incoming) {
                $data .= fgets($fp, 1024);
            } else if (@fgets($fp, 1024) === "\r\n") {
                $incoming = true;
            }
        }
        fclose($fp);
        $data = trim($data);
        if ($data === $teststr) {
            return INSECURE_DATAROOT_ERROR;
        }
    }

    return INSECURE_DATAROOT_WARNING;
}

/// =============================================================================================================
/// administration tree classes and functions


// n.b. documentation is still in progress for this code

/// INTRODUCTION

/// This file performs the following tasks:
///  -it defines the necessary objects and interfaces to build the Moodle
///   admin hierarchy
///  -it defines the admin_externalpage_setup(), admin_externalpage_print_header(),
///   and admin_externalpage_print_footer() functions used on admin pages

/// ADMIN_SETTING OBJECTS

/// Moodle settings are represented by objects that inherit from the admin_setting
/// class. These objects encapsulate how to read a setting, how to write a new value
/// to a setting, and how to appropriately display the HTML to modify the setting.

/// ADMIN_SETTINGPAGE OBJECTS

/// The admin_setting objects are then grouped into admin_settingpages. The latter
/// appear in the Moodle admin tree block. All interaction with admin_settingpage
/// objects is handled by the admin/settings.php file.

/// ADMIN_EXTERNALPAGE OBJECTS

/// There are some settings in Moodle that are too complex to (efficiently) handle
/// with admin_settingpages. (Consider, for example, user management and displaying
/// lists of users.) In this case, we use the admin_externalpage object. This object
/// places a link to an external PHP file in the admin tree block.

/// If you're using an admin_externalpage object for some settings, you can take
/// advantage of the admin_externalpage_* functions. For example, suppose you wanted
/// to add a foo.php file into admin. First off, you add the following line to
/// admin/settings/first.php (at the end of the file) or to some other file in
/// admin/settings:

///    $ADMIN->add('userinterface', new admin_externalpage('foo', get_string('foo'),
///        $CFG->wwwdir . '/' . '$CFG->admin . '/foo.php', 'some_role_permission'));

/// Next, in foo.php, your file structure would resemble the following:

///        require_once('.../config.php');
///        require_once($CFG->libdir.'/adminlib.php');
///        admin_externalpage_setup('foo');
///        // functionality like processing form submissions goes here
///        admin_externalpage_print_header();
///        // your HTML goes here
///        admin_externalpage_print_footer();

/// The admin_externalpage_setup() function call ensures the user is logged in,
/// and makes sure that they have the proper role permission to access the page.

/// The admin_externalpage_print_header() function prints the header (it figures
/// out what category and subcategories the page is classified under) and ensures
/// that you're using the admin pagelib (which provides the admin tree block and
/// the admin bookmarks block).

/// The admin_externalpage_print_footer() function properly closes the tables
/// opened up by the admin_externalpage_print_header() function and prints the
/// standard Moodle footer.

/// ADMIN_CATEGORY OBJECTS

/// Above and beyond all this, we have admin_category objects. These objects
/// appear as folders in the admin tree block. They contain admin_settingpage's,
/// admin_externalpage's, and other admin_category's.

/// OTHER NOTES

/// admin_settingpage's, admin_externalpage's, and admin_category's all inherit
/// from part_of_admin_tree (a pseudointerface). This interface insists that
/// a class has a check_access method for access permissions, a locate method
/// used to find a specific node in the admin tree and find parent path.

/// admin_category's inherit from parentable_part_of_admin_tree. This pseudo-
/// interface ensures that the class implements a recursive add function which
/// accepts a part_of_admin_tree object and searches for the proper place to
/// put it. parentable_part_of_admin_tree implies part_of_admin_tree.

/// Please note that the $this->name field of any part_of_admin_tree must be
/// UNIQUE throughout the ENTIRE admin tree.

/// The $this->name field of an admin_setting object (which is *not* part_of_
/// admin_tree) must be unique on the respective admin_settingpage where it is
/// used.


/// CLASS DEFINITIONS /////////////////////////////////////////////////////////

/**
 * Pseudointerface for anything appearing in the admin tree
 *
 * The pseudointerface that is implemented by anything that appears in the admin tree
 * block. It forces inheriting classes to define a method for checking user permissions
 * and methods for finding something in the admin tree.
 *
 * @author Vincenzo K. Marcovecchio
 * @package admin
 */
class part_of_admin_tree {

    /**
     * Finds a named part_of_admin_tree.
     *
     * Used to find a part_of_admin_tree. If a class only inherits part_of_admin_tree
     * and not parentable_part_of_admin_tree, then this function should only check if
     * $this->name matches $name. If it does, it should return a reference to $this,
     * otherwise, it should return a reference to NULL.
     *
     * If a class inherits parentable_part_of_admin_tree, this method should be called
     * recursively on all child objects (assuming, of course, the parent object's name
     * doesn't match the search criterion).
     *
     * @param string $name The internal name of the part_of_admin_tree we're searching for.
     * @return mixed An object reference or a NULL reference.
     */
    function &locate($name) {
        trigger_error('Admin class does not implement method <strong>locate()</strong>', E_USER_WARNING);
        return;
    }

    /**
     * Removes named part_of_admin_tree.
     *
     * @param string $name The internal name of the part_of_admin_tree we want to remove.
     * @return bool success.
     */
    function prune($name) {
        trigger_error('Admin class does not implement method <strong>prune()</strong>', E_USER_WARNING);
        return;
    }

    /**
     * Search using query
     * @param strin query
     * @return mixed array-object structure of found settings and pages
     */
    function search($query) {
        trigger_error('Admin class does not implement method <strong>search()</strong>', E_USER_WARNING);
        return;
    }

    /**
     * Verifies current user's access to this part_of_admin_tree.
     *
     * Used to check if the current user has access to this part of the admin tree or
     * not. If a class only inherits part_of_admin_tree and not parentable_part_of_admin_tree,
     * then this method is usually just a call to has_capability() in the site context.
     *
     * If a class inherits parentable_part_of_admin_tree, this method should return the
     * logical OR of the return of check_access() on all child objects.
     *
     * @return bool True if the user has access, false if she doesn't.
     */
    function check_access() {
        trigger_error('Admin class does not implement method <strong>check_access()</strong>', E_USER_WARNING);
        return;
    }

    /**
     * Mostly usefull for removing of some parts of the tree in admin tree block.
     *
     * @return True is hidden from normal list view
     */
    function is_hidden() {
        trigger_error('Admin class does not implement method <strong>is_hidden()</strong>', E_USER_WARNING);
        return;
    }
}

/**
 * Pseudointerface implemented by any part_of_admin_tree that has children.
 *
 * The pseudointerface implemented by any part_of_admin_tree that can be a parent
 * to other part_of_admin_tree's. (For now, this only includes admin_category.) Apart
 * from ensuring part_of_admin_tree compliancy, it also ensures inheriting methods
 * include an add method for adding other part_of_admin_tree objects as children.
 *
 * @author Vincenzo K. Marcovecchio
 * @package admin
 */
class parentable_part_of_admin_tree extends part_of_admin_tree {

    /**
     * Adds a part_of_admin_tree object to the admin tree.
     *
     * Used to add a part_of_admin_tree object to this object or a child of this
     * object. $something should only be added if $destinationname matches
     * $this->name. If it doesn't, add should be called on child objects that are
     * also parentable_part_of_admin_tree's.
     *
     * @param string $destinationname The internal name of the new parent for $something.
     * @param part_of_admin_tree &$something The object to be added.
     * @return bool True on success, false on failure.
     */
    function add($destinationname, $something) {
        trigger_error('Admin class does not implement method <strong>add()</strong>', E_USER_WARNING);
        return;
    }

}

/**
 * The object used to represent folders (a.k.a. categories) in the admin tree block.
 *
 * Each admin_category object contains a number of part_of_admin_tree objects.
 *
 * @author Vincenzo K. Marcovecchio
 * @package admin
 */
class admin_category extends parentable_part_of_admin_tree {

    /**
     * @var mixed An array of part_of_admin_tree objects that are this object's children
     */
    var $children;

    /**
     * @var string An internal name for this category. Must be unique amongst ALL part_of_admin_tree objects
     */
    var $name;

    /**
     * @var string The displayed name for this category. Usually obtained through get_string()
     */
    var $visiblename;

    /**
     * @var bool Should this category be hidden in admin tree block?
     */
    var $hidden;

    /**
     * paths
     */
    var $path;
    var $visiblepath;

    /**
     * Constructor for an empty admin category
     *
     * @param string $name The internal name for this category. Must be unique amongst ALL part_of_admin_tree objects
     * @param string $visiblename The displayed named for this category. Usually obtained through get_string()
     * @param bool $hidden hide category in admin tree block
     */
    function admin_category($name, $visiblename, $hidden=false) {
        $this->children    = array();
        $this->name        = $name;
        $this->visiblename = $visiblename;
        $this->hidden      = $hidden;
    }

    /**
     * Returns a reference to the part_of_admin_tree object with internal name $name.
     *
     * @param string $name The internal name of the object we want.
     * @param bool $findpath initialize path and visiblepath arrays
     * @return mixed A reference to the object with internal name $name if found, otherwise a reference to NULL.
     */
    function &locate($name, $findpath=false) {
        if ($this->name == $name) {
            if ($findpath) {
                $this->visiblepath[] = $this->visiblename;
                $this->path[]        = $this->name;
            }
            return $this;
        }

        $return = NULL;
        foreach($this->children as $childid=>$unused) {
            if ($return =& $this->children[$childid]->locate($name, $findpath)) {
                break;
            }
        }

        if (!is_null($return) and $findpath) {
            $return->visiblepath[] = $this->visiblename;
            $return->path[]        = $this->name;
        }

        return $return;
    }

    /**
     * Search using query
     * @param strin query
     * @return mixed array-object structure of found settings and pages
     */
    function search($query) {
        $result = array();
        foreach ($this->children as $child) {
            $subsearch = $child->search($query);
            if (!is_array($subsearch)) {
                debugging('Incorrect search result from '.$child->name);
                continue;
            }
            $result = array_merge($result, $subsearch);
        }
        return $result;
    }

    /**
     * Removes part_of_admin_tree object with internal name $name.
     *
     * @param string $name The internal name of the object we want to remove.
     * @return bool success
     */
    function prune($name) {

        if ($this->name == $name) {
            return false;  //can not remove itself
        }

        foreach($this->children as $precedence => $child) {
            if ($child->name == $name) {
                // found it!
                unset($this->children[$precedence]);
                return true;
            }
            if ($this->children[$precedence]->prune($name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Adds a part_of_admin_tree to a child or grandchild (or great-grandchild, and so forth) of this object.
     *
     * @param string $destinationame The internal name of the immediate parent that we want for $something.
     * @param mixed $something A part_of_admin_tree or setting instanceto be added.
     * @return bool True if successfully added, false if $something can not be added.
     */
    function add($parentname, $something) {
        $parent =& $this->locate($parentname);
        if (is_null($parent)) {
            debugging('parent does not exist!');
            return false;
        }

        if (is_a($something, 'part_of_admin_tree')) {
            if (!is_a($parent, 'parentable_part_of_admin_tree')) {
                debugging('error - parts of tree can be inserted only into parentable parts');
                return false;
            }
            $parent->children[] = $something;
            return true;

        } else {
            debugging('error - can not add this element');
            return false;
        }

    }

    /**
     * Checks if the user has access to anything in this category.
     *
     * @return bool True if the user has access to atleast one child in this category, false otherwise.
     */
    function check_access() {
        foreach ($this->children as $child) {
            if ($child->check_access()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is this category hidden in admin tree block?
     *
     * @return bool True if hidden
     */
    function is_hidden() {
        return $this->hidden;
    }
}

class admin_root extends admin_category {
    /**
     * list of errors
     */
    var $errors;

    /**
     * search query
     */
    var $search;

    /**
     * full tree flag - true means all settings required, false onlypages required
     */
    var $fulltree;


    function admin_root() {
        parent::admin_category('root', get_string('administration'), false);
        $this->errors   = array();
        $this->search   = '';
        $this->fulltree = true;
    }
}

/**
 * Links external PHP pages into the admin tree.
 *
 * See detailed usage example at the top of this document (adminlib.php)
 *
 * @author Vincenzo K. Marcovecchio
 * @package admin
 */
class admin_externalpage extends part_of_admin_tree {

    /**
     * @var string An internal name for this external page. Must be unique amongst ALL part_of_admin_tree objects
     */
    var $name;

    /**
     * @var string The displayed name for this external page. Usually obtained through get_string().
     */
    var $visiblename;

    /**
     * @var string The external URL that we should link to when someone requests this external page.
     */
    var $url;

    /**
     * @var string The role capability/permission a user must have to access this external page.
     */
    var $req_capability;

    /**
     * @var object The context in which capability/permission should be checked, default is site context.
     */
    var $context;

    /**
     * @var bool hidden in admin tree block.
     */
    var $hidden;

    /**
     * visible path
     */
    var $path;
    var $visiblepath;

    /**
     * Constructor for adding an external page into the admin tree.
     *
     * @param string $name The internal name for this external page. Must be unique amongst ALL part_of_admin_tree objects.
     * @param string $visiblename The displayed name for this external page. Usually obtained through get_string().
     * @param string $url The external URL that we should link to when someone requests this external page.
     * @param mixed $req_capability The role capability/permission a user must have to access this external page. Defaults to 'moodle/site:config'.
     * @param boolean $hidden Is this external page hidden in admin tree block? Default false.
     * @param context $context The context the page relates to. Not sure what happens
     *      if you specify something other than system or front page. Defaults to system.
     */
    function admin_externalpage($name, $visiblename, $url, $req_capability='moodle/site:config', $hidden=false, $context=NULL) {
        $this->name        = $name;
        $this->visiblename = $visiblename;
        $this->url         = $url;
        if (is_array($req_capability)) {
            $this->req_capability = $req_capability;
        } else {
            $this->req_capability = array($req_capability);
        }
        $this->hidden = $hidden;
        $this->context = $context;
    }

    /**
     * Returns a reference to the part_of_admin_tree object with internal name $name.
     *
     * @param string $name The internal name of the object we want.
     * @return mixed A reference to the object with internal name $name if found, otherwise a reference to NULL.
     */
    function &locate($name, $findpath=false) {
        if ($this->name == $name) {
            if ($findpath) {
                $this->visiblepath = array($this->visiblename);
                $this->path        = array($this->name);
            }
            return $this;
        } else {
            $return = NULL;
            return $return;
        }
    }

    function prune($name) {
        return false;
    }

    /**
     * Search using query
     * @param strin query
     * @return mixed array-object structure of found settings and pages
     */
    function search($query) {
        $textlib = textlib_get_instance();

        $found = false;
        if (strpos(strtolower($this->name), $query) !== false) {
            $found = true;
        } else if (strpos($textlib->strtolower($this->visiblename), $query) !== false) {
            $found = true;
        }
        if ($found) {
            $result = new object();
            $result->page     = $this;
            $result->settings = array();
            return array($this->name => $result);
        } else {
            return array();
        }
    }

    /**
     * Determines if the current user has access to this external page based on $this->req_capability.
     * @return bool True if user has access, false otherwise.
     */
    function check_access() {
        if (!get_site()) {
            return true; // no access check before site is fully set up
        }
        $context = empty($this->context) ? get_context_instance(CONTEXT_SYSTEM) : $this->context;
        foreach($this->req_capability as $cap) {
            if (is_valid_capability($cap) and has_capability($cap, $context)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is this external page hidden in admin tree block?
     *
     * @return bool True if hidden
     */
    function is_hidden() {
        return $this->hidden;
    }

}

/**
 * Used to group a number of admin_setting objects into a page and add them to the admin tree.
 *
 * @author Vincenzo K. Marcovecchio
 * @package admin
 */
class admin_settingpage extends part_of_admin_tree {

    /**
     * @var string An internal name for this external page. Must be unique amongst ALL part_of_admin_tree objects
     */
    var $name;

    /**
     * @var string The displayed name for this external page. Usually obtained through get_string().
     */
    var $visiblename;
    /**
     * @var mixed An array of admin_setting objects that are part of this setting page.
     */
    var $settings;

    /**
     * @var string The role capability/permission a user must have to access this external page.
     */
    var $req_capability;

    /**
     * @var object The context in which capability/permission should be checked, default is site context.
     */
    var $context;

    /**
     * @var bool hidden in admin tree block.
     */
    var $hidden;

    /**
     * paths
     */
    var $path;
    var $visiblepath;

    // see admin_externalpage
    function admin_settingpage($name, $visiblename, $req_capability='moodle/site:config', $hidden=false, $context=NULL) {
        $this->settings    = new object();
        $this->name        = $name;
        $this->visiblename = $visiblename;
        if (is_array($req_capability)) {
            $this->req_capability = $req_capability;
        } else {
            $this->req_capability = array($req_capability);
        }
        $this->hidden      = $hidden;
        $this->context     = $context;
    }

    // see admin_category
    function &locate($name, $findpath=false) {
        if ($this->name == $name) {
            if ($findpath) {
                $this->visiblepath = array($this->visiblename);
                $this->path        = array($this->name);
            }
            return $this;
        } else {
            $return = NULL;
            return $return;
        }
    }

    function search($query) {
        $found = array();

        foreach ($this->settings as $setting) {
            if ($setting->is_related($query)) {
                $found[] = $setting;
            }
        }

        if ($found) {
            $result = new object();
            $result->page     = $this;
            $result->settings = $found;
            return array($this->name => $result);
        }

        $textlib = textlib_get_instance();

        $found = false;
        if (strpos(strtolower($this->name), $query) !== false) {
            $found = true;
        } else if (strpos($textlib->strtolower($this->visiblename), $query) !== false) {
            $found = true;
        }
        if ($found) {
            $result = new object();
            $result->page     = $this;
            $result->settings = array();
            return array($this->name => $result);
        } else {
            return array();
        }
    }

    function prune($name) {
        return false;
    }

    /**
     * not the same as add for admin_category. adds an admin_setting to this admin_settingpage. settings appear (on the settingpage) in the order in which they're added
     * n.b. each admin_setting in an admin_settingpage must have a unique internal name
     * @param object $setting is the admin_setting object you want to add
     * @return true if successful, false if not
     */
    function add($setting) {
        if (!is_a($setting, 'admin_setting')) {
            debugging('error - not a setting instance');
            return false;
        }

        $this->settings->{$setting->name} = $setting;
        return true;
    }

    // see admin_externalpage
    function check_access() {
        if (!get_site()) {
            return true; // no access check before site is fully set up
        }
        $context = empty($this->context) ? get_context_instance(CONTEXT_SYSTEM) : $this->context;
        foreach($this->req_capability as $cap) {
            if (is_valid_capability($cap) and has_capability($cap, $context)) {
                return true;
            }
        }
        return false;
    }

    /**
     * outputs this page as html in a table (suitable for inclusion in an admin pagetype)
     * returns a string of the html
     */
    function output_html() {
        $adminroot =& admin_get_root();
        $return = '<fieldset>'."\n".'<div class="clearer"><!-- --></div>'."\n";
        foreach($this->settings as $setting) {
            $fullname = $setting->get_full_name();
            if (array_key_exists($fullname, $adminroot->errors)) {
                $data = $adminroot->errors[$fullname]->data;
            } else {
                $data = $setting->get_setting();
                if (is_null($data)) {
                    $data = $setting->get_defaultsetting();
                }
            }
            $return .= $setting->output_html($data);
        }
        $return .= '</fieldset>';
        return $return;
    }

    /**
     * Is this settigns page hidden in admin tree block?
     *
     * @return bool True if hidden
     */
    function is_hidden() {
        return $this->hidden;
    }

}


/**
 * Admin settings class. Only exists on setting pages.
 * Read & write happens at this level; no authentication.
 */
class admin_setting {

    var $name;
    var $visiblename;
    var $description;
    var $defaultsetting;
    var $updatedcallback;
    var $plugin; // null means main config table

    /**
     * Constructor
     * @param $name string unique ascii name
     * @param $visiblename string localised name
     * @param strin $description localised long description
     * @param mixed $defaultsetting string or array depending on implementation
     */
    function admin_setting($name, $visiblename, $description, $defaultsetting) {
        $this->parse_setting_name($name);
        $this->visiblename    = $visiblename;
        $this->description    = $description;
        $this->defaultsetting = $defaultsetting;
    }

    /**
     * Set up $this->name and possibly $this->plugin based on whether $name looks
     * like 'settingname' or 'plugin/settingname'. Also, do some sanity checking
     * on the names, that is, output a developer debug warning if the name
     * contains anything other than [a-zA-Z0-9_]+.
     *
     * @param string $name the setting name passed in to the constructor.
     */
    function parse_setting_name($name) {
        $bits = explode('/', $name);
        if (count($bits) > 2) {
            print_error('invalidadminsettingname', '', '', $name);
        }
        $this->name = array_pop($bits);
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->name)) {
            print_error('invalidadminsettingname', '', '', $name);
        }
        if (!empty($bits)) {
            $this->plugin = array_pop($bits);
            if ($this->plugin === 'moodle') {
                $this->plugin = null;
            } else if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->plugin)) {
                print_error('invalidadminsettingname', '', '', $name);
            }
        }
    }

    function get_full_name() {
        return 's_'.$this->plugin.'_'.$this->name;
    }

    function get_id() {
        return 'id_s_'.$this->plugin.'_'.$this->name;
    }

    function config_read($name) {
        global $CFG;
        if ($this->plugin === 'backup') {
            require_once($CFG->dirroot.'/backup/lib.php');
            $backupconfig = backup_get_config();
            if (isset($backupconfig->$name)) {
                return $backupconfig->$name;
            } else {
                return NULL;
            }

        } else if (!empty($this->plugin)) {
            $value = get_config($this->plugin, $name);
            return $value === false ? NULL : $value;

        } else {
            if (isset($CFG->$name)) {
                return $CFG->$name;
            } else {
                return NULL;
            }
        }
    }

    function config_write($name, $value) {
        global $CFG;
        if ($this->plugin === 'backup') {
            require_once($CFG->dirroot.'/backup/lib.php');
            return (boolean)backup_set_config($name, $value);
        } else {
            return (boolean)set_config($name, $value, $this->plugin);
        }
    }

    /**
     * Returns current value of this setting
     * @return mixed array or string depending on instance, NULL means not set yet
     */
    function get_setting() {
        // has to be overridden
        return NULL;
    }

    /**
     * Returns default setting if exists
     * @return mixed array or string depending on instance; NULL means no default, user must supply
     */
    function get_defaultsetting() {
        return $this->defaultsetting;
    }

    /**
     * Store new setting
     * @param mixed string or array, must not be NULL
     * @return '' if ok, string error message otherwise
     */
    function write_setting($data) {
        // should be overridden
        return '';
    }

    /**
     * Return part of form with setting
     * @param mixed data array or string depending on setting
     * @return string
     */
    function output_html($data, $query='') {
        // should be overridden
        return;
    }

    /**
     * function called if setting updated - cleanup, cache reset, etc.
     */
    function set_updatedcallback($functionname) {
        $this->updatedcallback = $functionname;
    }

    /**
     * Is setting related to query text - used when searching
     * @param string $query
     * @return bool
     */
    function is_related($query) {
        if (strpos(strtolower($this->name), $query) !== false) {
            return true;
        }
        $textlib = textlib_get_instance();
        if (strpos($textlib->strtolower($this->visiblename), $query) !== false) {
            return true;
        }
        if (strpos($textlib->strtolower($this->description), $query) !== false) {
            return true;
        }
        $current = $this->get_setting();
        if (!is_null($current)) {
            if (is_string($current)) {
                if (strpos($textlib->strtolower($current), $query) !== false) {
                    return true;
                }
            }
        }
        $default = $this->get_defaultsetting();
        if (!is_null($default)) {
            if (is_string($default)) {
                if (strpos($textlib->strtolower($default), $query) !== false) {
                    return true;
                }
            }
        }
        return false;
    }
}

/**
 * No setting - just heading and text.
 */
class admin_setting_heading extends admin_setting {
    /**
     * not a setting, just text
     * @param string $name of setting
     * @param string $heading heading
     * @param string $information text in box
     */
    function admin_setting_heading($name, $heading, $information) {
        parent::admin_setting($name, $heading, $information, '');
    }

    function get_setting() {
        return true;
    }

    function get_defaultsetting() {
        return true;
    }

    function write_setting($data) {
        // do not write any setting
        return '';
    }

    function output_html($data, $query='') {
        $return = '';
        if ($this->visiblename != '') {
            $return .= print_heading('<a name="'.$this->name.'">'.highlightfast($query, $this->visiblename).'</a>', '', 3, 'main', true);
        }
        if ($this->description != '') {
            $return .= print_box(highlight($query, $this->description), 'generalbox formsettingheading', '', true);
        }
        return $return;
    }
}

/**
 * The most flexibly setting, user is typing text
 */
class admin_setting_configtext extends admin_setting {

    var $paramtype;
    var $size;

    /**
     * config text contructor
     * @param string $name of setting
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     * @param mixed $paramtype int means PARAM_XXX type, string is a allowed format in regex
     * @param int $size default field size
     */
    function admin_setting_configtext($name, $visiblename, $description, $defaultsetting, $paramtype=PARAM_RAW, $size=null) {
        $this->paramtype = $paramtype;
        if (!is_null($size)) {
            $this->size  = $size;
        } else {
            $this->size  = ($paramtype == PARAM_INT) ? 5 : 30;
        }
        parent::admin_setting($name, $visiblename, $description, $defaultsetting);
    }

    function get_setting() {
        return $this->config_read($this->name);
    }

    function write_setting($data) {
        if ($this->paramtype === PARAM_INT and $data === '') {
            // do not complain if '' used instead of 0
            $data = 0;
        }
        // $data is a string
        $validated = $this->validate($data); 
        if ($validated !== true) {
            return $validated;
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Validate data before storage
     * @param string data
     * @return mixed true if ok string if error found
     */
    function validate($data) {
        if (is_string($this->paramtype)) {
            if (preg_match($this->paramtype, $data)) {
                return true;
            } else {
                return get_string('validateerror', 'admin');
            }

        } else if ($this->paramtype === PARAM_RAW) {
            return true;

        } else {
            $cleaned = stripslashes(clean_param(addslashes($data), $this->paramtype));
            if ("$data" == "$cleaned") { // implicit conversion to string is needed to do exact comparison
                return true;
            } else {
                return get_string('validateerror', 'admin');
            }
        }
    }

    function output_html($data, $query='') {
        $default = $this->get_defaultsetting();

        return format_admin_setting($this, $this->visiblename,
                '<div class="form-text defaultsnext"><input type="text" size="'.$this->size.'" id="'.$this->get_id().'" name="'.$this->get_full_name().'" value="'.s($data).'" /></div>',
                $this->description, true, '', $default, $query);
    }
}

/**
 * General text area without html editor.
 */
class admin_setting_configtextarea extends admin_setting_configtext {
    var $rows;
    var $cols;

    function admin_setting_configtextarea($name, $visiblename, $description, $defaultsetting, $paramtype=PARAM_RAW, $cols='60', $rows='8') {
        $this->rows = $rows;
        $this->cols = $cols;
        parent::admin_setting_configtext($name, $visiblename, $description, $defaultsetting, $paramtype);
    }

    function output_html($data, $query='') {
        $default = $this->get_defaultsetting();

        $defaultinfo = $default;
        if (!is_null($default) and $default !== '') {
            $defaultinfo = "\n".$default;
        } 

        return format_admin_setting($this, $this->visiblename,
                '<div class="form-textarea" ><textarea rows="'.$this->rows.'" cols="'.$this->cols.'" id="'.$this->get_id().'" name="'.$this->get_full_name().'">'.s($data).'</textarea></div>',
                $this->description, true, '', $defaultinfo, $query);
    }
}

/**
 * General text area with html editor.
 */
class admin_setting_confightmltextarea extends admin_setting_configtext {

    function admin_setting_confightmltextarea($name, $visiblename, $description, $defaultsetting, $paramtype=PARAM_RAW) {
        parent::admin_setting_configtext($name, $visiblename, $description, $defaultsetting, $paramtype);
    }

    function output_html($data, $query='') {
        global $CFG;

        $CFG->adminusehtmleditor = can_use_html_editor();
        $return = '<div class="form-htmlarea">'.print_textarea($CFG->adminusehtmleditor, 15, 60, 0, 0, $this->get_full_name(), $data, 0, true).'</div>';

        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', NULL, $query);
    }
}

/**
 * Password field, allows unmasking of password
 */
class admin_setting_configpasswordunmask extends admin_setting_configtext {
    /**
     * Constructor
     * @param string $name of setting
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting default password
     */
    function admin_setting_configpasswordunmask($name, $visiblename, $description, $defaultsetting) {
        parent::admin_setting_configtext($name, $visiblename, $description, $defaultsetting, PARAM_RAW, 30);
    }

    function output_html($data, $query='') {
        $id = $this->get_id();
        $unmask = get_string('unmaskpassword', 'form');
        $unmaskjs = '<script type="text/javascript">
//<![CDATA[
document.write(\'<span class="unmask"><input id="'.$id.'unmask" value="1" type="checkbox" onclick="unmaskPassword(\\\''.$id.'\\\')"/><label for="'.$id.'unmask">'.addslashes_js($unmask).'<\/label><\/span>\');
document.getElementById("'.$this->get_id().'").setAttribute("autocomplete", "off");
//]]>
</script>';
        return format_admin_setting($this, $this->visiblename,
                '<div class="form-password"><input type="password" size="'.$this->size.'" id="'.$this->get_id().'" name="'.$this->get_full_name().'" value="'.s($data).'" />'.$unmaskjs.'</div>',
                $this->description, true, '', NULL, $query);
    }
}

/**
 * Path to directory
 */
class admin_setting_configfile extends admin_setting_configtext {
    /**
     * Constructor
     * @param string $name of setting
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultdirectory default directory location
     */
    function admin_setting_configfile($name, $visiblename, $description, $defaultdirectory) {
        parent::admin_setting_configtext($name, $visiblename, $description, $defaultdirectory, PARAM_RAW, 50);
    }

    function output_html($data, $query='') {
        $default = $this->get_defaultsetting();

        if ($data) {
            if (file_exists($data)) {
                $executable = '<span class="pathok">&#x2714;</span>';
            } else {
                $executable = '<span class="patherror">&#x2718;</span>';
            }
        } else {
            $executable = '';
        }

        return format_admin_setting($this, $this->visiblename,
                '<div class="form-file defaultsnext"><input type="text" size="'.$this->size.'" id="'.$this->get_id().'" name="'.$this->get_full_name().'" value="'.s($data).'" />'.$executable.'</div>',
                $this->description, true, '', $default, $query);
    }
}

/**
 * Path to executable file
 */
class admin_setting_configexecutable extends admin_setting_configfile {

    function output_html($data, $query='') {
        $default = $this->get_defaultsetting();

        if ($data) {
            if (file_exists($data) and is_executable($data)) {
                $executable = '<span class="pathok">&#x2714;</span>';
            } else {
                $executable = '<span class="patherror">&#x2718;</span>';
            }
        } else {
            $executable = '';
        }

        return format_admin_setting($this, $this->visiblename,
                '<div class="form-file defaultsnext"><input type="text" size="'.$this->size.'" id="'.$this->get_id().'" name="'.$this->get_full_name().'" value="'.s($data).'" />'.$executable.'</div>',
                $this->description, true, '', $default, $query);
    }
}

/**
 * Path to directory
 */
class admin_setting_configdirectory extends admin_setting_configfile {
    function output_html($data, $query='') {
        $default = $this->get_defaultsetting();

        if ($data) {
            if (file_exists($data) and is_dir($data)) {
                $executable = '<span class="pathok">&#x2714;</span>';
            } else {
                $executable = '<span class="patherror">&#x2718;</span>';
            }
        } else {
            $executable = '';
        }

        return format_admin_setting($this, $this->visiblename,
                '<div class="form-file defaultsnext"><input type="text" size="'.$this->size.'" id="'.$this->get_id().'" name="'.$this->get_full_name().'" value="'.s($data).'" />'.$executable.'</div>',
                $this->description, true, '', $default, $query);
    }
}

/**
 * Checkbox
 */
class admin_setting_configcheckbox extends admin_setting {
    var $yes;
    var $no;

    /**
     * Constructor
     * @param string $name of setting
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     * @param string $yes value used when checked
     * @param string $no value used when not checked
     */
    function admin_setting_configcheckbox($name, $visiblename, $description, $defaultsetting, $yes='1', $no='0') {
        parent::admin_setting($name, $visiblename, $description, $defaultsetting);
        $this->yes = (string)$yes;
        $this->no  = (string)$no;
    }

    function get_setting() {
        return $this->config_read($this->name);
    }

    function write_setting($data) {
        if ((string)$data === $this->yes) { // convert to strings before comparison
            $data = $this->yes;
        } else {
            $data = $this->no;
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

    function output_html($data, $query='') {
        $default = $this->get_defaultsetting();

        if (!is_null($default)) {
            if ((string)$default === $this->yes) {
                $defaultinfo = get_string('checkboxyes', 'admin');
            } else {
                $defaultinfo = get_string('checkboxno', 'admin');
            }
        } else {
            $defaultinfo = NULL;
        }

        if ((string)$data === $this->yes) { // convert to strings before comparison
            $checked = 'checked="checked"';
        } else {
            $checked = '';
        }

        return format_admin_setting($this, $this->visiblename,
                '<div class="form-checkbox defaultsnext" ><input type="hidden" name="'.$this->get_full_name().'" value="'.s($this->no).'" /> '
                .'<input type="checkbox" id="'.$this->get_id().'" name="'.$this->get_full_name().'" value="'.s($this->yes).'" '.$checked.' /></div>',
                $this->description, true, '', $defaultinfo, $query);
    }
}

/**
 * Multiple checkboxes, each represents different value, stored in csv format
 */
class admin_setting_configmulticheckbox extends admin_setting {
    var $choices;

    /**
     * Constructor
     * @param string $name of setting
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting array of selected
     * @param array $choices array of $value=>$label for each checkbox
     */
    function admin_setting_configmulticheckbox($name, $visiblename, $description, $defaultsetting, $choices) {
        $this->choices = $choices;
        parent::admin_setting($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * This function may be used in ancestors for lazy loading of choices
     * @return true if loaded, false if error
     */
    function load_choices() {
        /*
        if (is_array($this->choices)) {
            return true;
        }
        .... load choices here
        */
        return true;
    }

    /**
     * Is setting related to query text - used when searching
     * @param string $query
     * @return bool
     */
    function is_related($query) {
        if (!$this->load_choices() or empty($this->choices)) {
            return false;
        }
        if (parent::is_related($query)) {
            return true;
        }

        $textlib = textlib_get_instance();
        foreach ($this->choices as $desc) {
            if (strpos($textlib->strtolower($desc), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    function get_setting() {
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return NULL;
        }
        if ($result === '') {
            return array();
        }
        return explode(',', $result);
    }

    function write_setting($data) {
        if (!is_array($data)) {
            return ''; // ignore it
        }
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }
        unset($data['xxxxx']);
        $result = array();
        foreach ($data as $key => $value) {
            if ($value and array_key_exists($key, $this->choices)) {
                $result[] = $key;
            }
        }
        return $this->config_write($this->name, implode(',', $result)) ? '' : get_string('errorsetting', 'admin');
    }

    function output_html($data, $query='') {
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }
        $default = $this->get_defaultsetting();
        if (is_null($default)) {
            $default = array();
        }
        if (is_null($data)) {
            foreach ($default as $key=>$value) {
                if ($value) {
                    $current[] = $value;
                }
            }
        }

        $options = array();
        $defaults = array();
        foreach($this->choices as $key=>$description) {
            if (in_array($key, $data)) {
                $checked = 'checked="checked"';
            } else {
                $checked = '';
            }
            if (!empty($default[$key])) {
                $defaults[] = $description;
            }

            $options[] = '<input type="checkbox" id="'.$this->get_id().'_'.$key.'" name="'.$this->get_full_name().'['.$key.']" value="1" '.$checked.' />'
                         .'<label for="'.$this->get_id().'_'.$key.'">'.highlightfast($query, $description).'</label>';
        }

        if (is_null($default)) {
            $defaultinfo = NULL;
        } else if (!empty($defaults)) {
            $defaultinfo = implode(', ', $defaults);
        } else {
            $defaultinfo = get_string('none');
        }

        $return = '<div class="form-multicheckbox">';
        $return .= '<input type="hidden" name="'.$this->get_full_name().'[xxxxx]" value="1" />'; // something must be submitted even if nothing selected
        if ($options) {
            $return .= '<ul>';
            foreach ($options as $option) {
                $return .= '<li>'.$option.'</li>';
            }
            $return .= '</ul>';
        }
        $return .= '</div>';

        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', $defaultinfo, $query);
        
    }
}

/**
 * Multiple checkboxes 2, value stored as string 00101011
 */
class admin_setting_configmulticheckbox2 extends admin_setting_configmulticheckbox {
    function get_setting() {
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return NULL;
        }
        if (!$this->load_choices()) {
            return NULL;
        }
        $result = str_pad($result, count($this->choices), '0');
        $result = preg_split('//', $result, -1, PREG_SPLIT_NO_EMPTY);
        $setting = array();
        foreach ($this->choices as $key=>$unused) {
            $value = array_shift($result);
            if ($value) {
                $setting[] = $key;
            }
        }
        return $setting;
    }

    function write_setting($data) {
        if (!is_array($data)) {
            return ''; // ignore it
        }
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }
        $result = '';
        foreach ($this->choices as $key=>$unused) {
            if (!empty($data[$key])) {
                $result .= '1';
            } else {
                $result .= '0';
            }
        }
        return $this->config_write($this->name, $result) ? '' : get_string('errorsetting', 'admin');
    }
}

/**
 * Select one value from list
 */
class admin_setting_configselect extends admin_setting {
    var $choices;

    /**
     * Constructor
     * @param string $name of setting
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     * @param array $choices array of $value=>$label for each selection
     */
    function admin_setting_configselect($name, $visiblename, $description, $defaultsetting, $choices) {
        $this->choices = $choices;
        parent::admin_setting($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * This function may be used in ancestors for lazy loading of choices
     * @return true if loaded, false if error
     */
    function load_choices() {
        /*
        if (is_array($this->choices)) {
            return true;
        }
        .... load choices here
        */
        return true;
    }

    function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }
        if (!$this->load_choices()) {
            return false;
        }
        $textlib = textlib_get_instance();
        foreach ($this->choices as $key=>$value) {
            if (strpos($textlib->strtolower($key), $query) !== false) {
                return true;
            }
            if (strpos($textlib->strtolower($value), $query) !== false) {
                return true;
            }
        }         
        return false;
    }

    function get_setting() {
        return $this->config_read($this->name);
    }

    function write_setting($data) {
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }
        if (!array_key_exists($data, $this->choices)) {
            return ''; // ignore it
        }

        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

    function output_html($data, $query='') {
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }
        $default = $this->get_defaultsetting();

        if (!is_null($default) and array_key_exists($default, $this->choices)) {
            $defaultinfo = $this->choices[$default];
        } else {
            $defaultinfo = NULL;
        }

        $current = $this->get_setting();
        $warning = '';
        if (is_null($current)) {
            //first run
        } else if (empty($current) and (array_key_exists('', $this->choices) or array_key_exists(0, $this->choices))) {
            // no warning
        } else if (!array_key_exists($current, $this->choices)) {
            $warning = get_string('warningcurrentsetting', 'admin', s($current));
            if (!is_null($default) and $data==$current) {
                $data = $default; // use default instead of first value when showing the form
            }
        }

        $return = '<div class="form-select defaultsnext"><select id="'.$this->get_id().'" name="'.$this->get_full_name().'">';
        foreach ($this->choices as $key => $value) {
            // the string cast is needed because key may be integer - 0 is equal to most strings!
            $return .= '<option value="'.$key.'"'.((string)$key==$data ? ' selected="selected"' : '').'>'.$value.'</option>';
        }
        $return .= '</select></div>';

        return format_admin_setting($this, $this->visiblename, $return, $this->description, true, $warning, $defaultinfo, $query);
    }

}

/**
 * Select multiple items from list
 */
class admin_setting_configmultiselect extends admin_setting_configselect {
    /**
     * Constructor
     * @param string $name of setting
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting array of selected items
     * @param array $choices array of $value=>$label for each list item
     */
    function admin_setting_configmultiselect($name, $visiblename, $description, $defaultsetting, $choices) {
        parent::admin_setting_configselect($name, $visiblename, $description, $defaultsetting, $choices);
    }

    function get_setting() {
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return NULL;
        }
        if ($result === '') {
            return array();
        }
        return explode(',', $result);
    }

    function write_setting($data) {
        if (!is_array($data)) {
            return ''; //ignore it
        }
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }

        unset($data['xxxxx']);

        $save = array();
        foreach ($data as $value) {
            if (!array_key_exists($value, $this->choices)) {
                continue; // ignore it
            }
            $save[] = $value;
        }

        return ($this->config_write($this->name, implode(',', $save)) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Is setting related to query text - used when searching
     * @param string $query
     * @return bool
     */
    function is_related($query) {
        if (!$this->load_choices() or empty($this->choices)) {
            return false;
        }
        if (parent::is_related($query)) {
            return true;
        }

        $textlib = textlib_get_instance();
        foreach ($this->choices as $desc) {
            if (strpos($textlib->strtolower($desc), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    function output_html($data, $query='') {
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }
        $choices = $this->choices;
        $default = $this->get_defaultsetting();
        if (is_null($default)) {
            $default = array();
        }
        if (is_null($data)) {
            $data = array();
        }

        $defaults = array();
        $size = min(10, count($this->choices));
        $return = '<div class="form-select"><input type="hidden" name="'.$this->get_full_name().'[xxxxx]" value="1" />'; // something must be submitted even if nothing selected
        $return .= '<select id="'.$this->get_id().'" name="'.$this->get_full_name().'[]" size="'.$size.'" multiple="multiple">';
        foreach ($this->choices as $key => $description) {
            if (in_array($key, $data)) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            if (in_array($key, $default)) {
                $defaults[] = $description;
            }

            $return .= '<option value="'.s($key).'" '.$selected.'>'.$description.'</option>';
        }

        if (is_null($default)) {
            $defaultinfo = NULL;
        } if (!empty($defaults)) {
            $defaultinfo = implode(', ', $defaults);
        } else {
            $defaultinfo = get_string('none');
        }

        $return .= '</select></div>';
        return format_admin_setting($this, $this->visiblename, $return, $this->description, true, '', $defaultinfo, $query);
    }
}

/**
 * Time selector
 * this is a liiitle bit messy. we're using two selects, but we're returning
 * them as an array named after $name (so we only use $name2 internally for the setting)
 */
class admin_setting_configtime extends admin_setting {
    var $name2;

    /**
     * Constructor
     * @param string $hoursname setting for hours
     * @param string $minutesname setting for hours
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting array representing default time 'h'=>hours, 'm'=>minutes
     */
    function admin_setting_configtime($hoursname, $minutesname, $visiblename, $description, $defaultsetting) {
        $this->name2 = $minutesname;
        parent::admin_setting($hoursname, $visiblename, $description, $defaultsetting);
    }

    function get_setting() {
        $result1 = $this->config_read($this->name);
        $result2 = $this->config_read($this->name2);
        if (is_null($result1) or is_null($result2)) {
            return NULL;
        }

        return array('h' => $result1, 'm' => $result2);
    }

    function write_setting($data) {
        if (!is_array($data)) {
            return '';
        }

        $result = $this->config_write($this->name, (int)$data['h']) && $this->config_write($this->name2, (int)$data['m']);
        return ($result ? '' : get_string('errorsetting', 'admin'));
    }

    function output_html($data, $query='') {
        $default = $this->get_defaultsetting();

        if (is_array($default)) {
            $defaultinfo = $default['h'].':'.$default['m'];
        } else {
            $defaultinfo = NULL;
        }

        $return = '<div class="form-time defaultsnext">'.
                  '<select id="'.$this->get_id().'h" name="'.$this->get_full_name().'[h]">';
        for ($i = 0; $i < 24; $i++) {
            $return .= '<option value="'.$i.'"'.($i == $data['h'] ? ' selected="selected"' : '').'>'.$i.'</option>';
        }
        $return .= '</select>:<select id="'.$this->get_id().'m" name="'.$this->get_full_name().'[m]">';
        for ($i = 0; $i < 60; $i += 5) {
            $return .= '<option value="'.$i.'"'.($i == $data['m'] ? ' selected="selected"' : '').'>'.$i.'</option>';
        }
        $return .= '</select></div>';
        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', $defaultinfo, $query);
    }

}

/**
 * An admin setting for selecting one or more users, who have a particular capability
 * in the system context. Warning, make sure the list will never be too long. There is
 * no paging or searching of this list.
 *
 * To correctly get a list of users from this config setting, you need to call the
 * get_users_from_config($CFG->mysetting, $capability); function in moodlelib.php.
 */
class admin_setting_users_with_capability extends admin_setting_configmultiselect {
    var $capability;

    /**
     * Constructor.
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised name
     * @param string $description localised long description
     * @param array $defaultsetting array of usernames
     * @param string $capability string capability name.
     */
    function admin_setting_users_with_capability($name, $visiblename, $description, $defaultsetting, $capability) {
        $this->capability = $capability;
        parent::admin_setting_configmultiselect($name, $visiblename, $description, $defaultsetting, NULL);
    }

    function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $users = get_users_by_capability(get_context_instance(CONTEXT_SYSTEM),
                $this->capability, 'u.id,u.username,u.firstname,u.lastname', 'u.lastname,u.firstname');
        $this->choices = array(
            '$@NONE@$' => get_string('nobody'),
            '$@ALL@$' => get_string('everyonewhocan', 'admin', get_capability_string($this->capability)),
        );
        foreach ($users as $user) {
            $this->choices[$user->username] = fullname($user);
        }
        return true;
    }

    function get_defaultsetting() {
        $this->load_choices();
        if (empty($this->defaultsetting)) {
            return array('$@NONE@$');
        } else if (array_key_exists($this->defaultsetting, $this->choices)) {
            return $this->defaultsetting;
        } else {
            return '';
        }
    }

    function get_setting() {
        $result = parent::get_setting();
        if (empty($result)) {
            $result = array('$@NONE@$');
        }
        return $result;
    }

    function write_setting($data) {
        // If all is selected, remove any explicit options.
        if (in_array('$@ALL@$', $data)) {
            $data = array('$@ALL@$');
        }
        // None never needs to be writted to the DB.
        if (in_array('$@NONE@$', $data)) {
            unset($data[array_search('$@NONE@$', $data)]);
        }
        return parent::write_setting($data);
    }
}

/**
 * Special checkbox for calendar - resets SESSION vars.
 */
class admin_setting_special_adminseesall extends admin_setting_configcheckbox {
    function admin_setting_special_adminseesall() {
        parent::admin_setting_configcheckbox('calendar_adminseesall', get_string('adminseesall', 'admin'),
                                             get_string('helpadminseesall', 'admin'), '0');
    }

    function write_setting($data) {
        global $SESSION;
        unset($SESSION->cal_courses_shown);
        return parent::write_setting($data);
    }
}

/**
 * Special select for settings that are altered in setup.php and can not be altered on the fly
 */
class admin_setting_special_selectsetup extends admin_setting_configselect {
    function get_setting() {
        // read directly from db!
        return get_config(NULL, $this->name);
    }

    function write_setting($data) {
        global $CFG;
        // do not change active CFG setting!
        $current = $CFG->{$this->name};
        $result = parent::write_setting($data);
        $CFG->{$this->name} = $current;
        return $result;
    }
}

/**
 * Special select for frontpage - stores data in course table
 */
class admin_setting_sitesetselect extends admin_setting_configselect {
    function get_setting() {
        $site = get_site();
        return $site->{$this->name};
    }

    function write_setting($data) {
        global $SITE;
        if (!in_array($data, array_keys($this->choices))) {
            return get_string('errorsetting', 'admin');
        }
        $record = new stdClass();
        $record->id           = SITEID;
        $temp                 = $this->name;
        $record->$temp        = $data;
        $record->timemodified = time();
        // update $SITE
        $SITE->{$this->name} = $data;
        return (update_record('course', $record) ? '' : get_string('errorsetting', 'admin'));
    }
}

/**
 * Special select - lists on the frontpage - hacky
 */
class admin_setting_courselist_frontpage extends admin_setting {
    var $choices;

    function admin_setting_courselist_frontpage($loggedin) {
        global $CFG;
        require_once($CFG->dirroot.'/course/lib.php');
        $name        = 'frontpage'.($loggedin ? 'loggedin' : '');
        $visiblename = get_string('frontpage'.($loggedin ? 'loggedin' : ''),'admin');
        $description = get_string('configfrontpage'.($loggedin ? 'loggedin' : ''),'admin');
        $defaults    = array(FRONTPAGECOURSELIST);
        parent::admin_setting($name, $visiblename, $description, $defaults);
    }

    function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array(FRONTPAGENEWS          => get_string('frontpagenews'),
                               FRONTPAGECOURSELIST    => get_string('frontpagecourselist'),
                               FRONTPAGECATEGORYNAMES => get_string('frontpagecategorynames'),
                               FRONTPAGECATEGORYCOMBO => get_string('frontpagecategorycombo'),
                               'none'                 => get_string('none'));
        if ($this->name == 'frontpage' and count_records('course') > FRONTPAGECOURSELIMIT) {
            unset($this->choices[FRONTPAGECOURSELIST]);
        }
        return true;
    }
    function get_setting() {
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return NULL;
        }
        if ($result === '') {
            return array();
        }
        return explode(',', $result);
    }

    function write_setting($data) {
        if (!is_array($data)) {
            return '';
        }
        $this->load_choices();
        $save = array();
        foreach($data as $datum) {
            if ($datum == 'none' or !array_key_exists($datum, $this->choices)) {
                continue;
            }
            $save[$datum] = $datum; // no duplicates
        }
        return ($this->config_write($this->name, implode(',', $save)) ? '' : get_string('errorsetting', 'admin'));
    }

    function output_html($data, $query='') {
        $this->load_choices();
        $currentsetting = array();
        foreach ($data as $key) {
            if ($key != 'none' and array_key_exists($key, $this->choices)) {
                $currentsetting[] = $key; // already selected first
            }
        }

        $return = '<div class="form-group">';
        for ($i = 0; $i < count($this->choices) - 1; $i++) {
            if (!array_key_exists($i, $currentsetting)) {
                $currentsetting[$i] = 'none'; //none
            }
            $return .='<select class="form-select" id="'.$this->get_id().$i.'" name="'.$this->get_full_name().'[]">';
            foreach ($this->choices as $key => $value) {
                $return .= '<option value="'.$key.'"'.("$key" == $currentsetting[$i] ? ' selected="selected"' : '').'>'.$value.'</option>';
            }
            $return .= '</select>';
            if ($i !== count($this->choices) - 2) {
                $return .= '<br />';
            }
        }
        $return .= '</div>';

        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', NULL, $query);
    }
}

/**
 * Special checkbox for frontpage - stores data in course table
 */
class admin_setting_sitesetcheckbox extends admin_setting_configcheckbox {
    function get_setting() {
        $site = get_site();
        return $site->{$this->name};
    }

    function write_setting($data) {
        global $SITE;
        $record = new object();
        $record->id            = SITEID;
        $record->{$this->name} = ($data == '1' ? 1 : 0);
        $record->timemodified  = time();
        // update $SITE
        $SITE->{$this->name} = $data;
        return (update_record('course', $record) ? '' : get_string('errorsetting', 'admin'));
    }
}

/**
 * Special text for frontpage - stores data in course table.
 * Empty string means not set here. Manual setting is required.
 */
class admin_setting_sitesettext extends admin_setting_configtext {
    function get_setting() {
        $site = get_site();
        return $site->{$this->name} != '' ? $site->{$this->name} : NULL;
    }

    function validate($data) {
        $cleaned = stripslashes(clean_param(addslashes($data), PARAM_MULTILANG));
        if ($cleaned === '') {
            return get_string('required');
        }
        if ("$data" == "$cleaned") { // implicit conversion to string is needed to do exact comparison
            return true;
        } else {
            return get_string('validateerror', 'admin');
        }
    }

    function write_setting($data) {
        global $SITE;
        $data = trim($data);
        $validated = $this->validate($data); 
        if ($validated !== true) {
            return $validated;
        }

        $record = new object();
        $record->id            = SITEID;
        $record->{$this->name} = addslashes($data);
        $record->timemodified  = time();
        // update $SITE
        $SITE->{$this->name} = $data;
        return (update_record('course', $record) ? '' : get_string('dbupdatefailed', 'error'));
    }
}

/**
 * Special text editor for site description.
 */
class admin_setting_special_frontpagedesc extends admin_setting {
    function admin_setting_special_frontpagedesc() {
        parent::admin_setting('summary', get_string('frontpagedescription'), get_string('frontpagedescriptionhelp'), NULL);
    }

    function get_setting() {
        $site = get_site();
        return $site->{$this->name};
    }

    function write_setting($data) {
        global $SITE;
        $record = new object();
        $record->id            = SITEID;
        $record->{$this->name} = addslashes($data);
        $record->timemodified  = time();
        $SITE->{$this->name} = $data;
        return (update_record('course', $record) ? '' : get_string('errorsetting', 'admin'));
    }

    function output_html($data, $query='') {
        global $CFG;

        $CFG->adminusehtmleditor = can_use_html_editor();
        $return = '<div class="form-htmlarea">'.print_textarea($CFG->adminusehtmleditor, 15, 60, 0, 0, $this->get_full_name(), $data, 0, true).'</div>';

        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', NULL, $query);
    }
}

class admin_setting_special_editorfontlist extends admin_setting {

    var $items;

    function admin_setting_special_editorfontlist() {
        global $CFG;
        $name = 'editorfontlist';
        $visiblename = get_string('editorfontlist', 'admin');
        $description = get_string('configeditorfontlist', 'admin');
        $defaults = array('k0' => 'Trebuchet',
                          'v0' => 'Trebuchet MS,Verdana,Arial,Helvetica,sans-serif',
                          'k1' => 'Arial',
                          'v1' => 'arial,helvetica,sans-serif',
                          'k2' => 'Courier New',
                          'v2' => 'courier new,courier,monospace',
                          'k3' => 'Georgia',
                          'v3' => 'georgia,times new roman,times,serif',
                          'k4' => 'Tahoma',
                          'v4' => 'tahoma,arial,helvetica,sans-serif',
                          'k5' => 'Times New Roman',
                          'v5' => 'times new roman,times,serif',
                          'k6' => 'Verdana',
                          'v6' => 'verdana,arial,helvetica,sans-serif',
                          'k7' => 'Impact',
                          'v7' => 'impact',
                          'k8' => 'Wingdings',
                          'v8' => 'wingdings');
   