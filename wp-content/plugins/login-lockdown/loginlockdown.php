<?php
/*
  Plugin Name: Login LockDown
  Plugin URI: https://wploginlockdown.com/
  Description: Protect login form by banning IP after multiple failed login attempts.
  Version: 1.83
  Author: WebFactory Ltd
  Author URI: https://www.webfactoryltd.com/
  License: GNU General Public License v3.0
  Text Domain: login-lockdown
  Requires at least: 4.0
  Tested up to: 6.0
  Requires PHP: 5.2

  Copyright 2022 - 2023  WebFactory Ltd  (email: support@webfactoryltd.com)
  Copyright 2007 - 2022  Michael VanDeMar

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$loginlockdown_db_version = "1.0";
$loginlockdownOptions = loginLockdown_get_options();

function loginLockdown_install()
{
    global $wpdb;
    global $loginlockdown_db_version;
    $table_name = $wpdb->prefix . "login_fails";

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE " . $table_name . " (
			`login_attempt_ID` bigint(20) NOT NULL AUTO_INCREMENT,
			`user_id` bigint(20) NOT NULL,
			`login_attempt_date` datetime NOT NULL default '0000-00-00 00:00:00',
			`login_attempt_IP` varchar(100) NOT NULL default '',
			PRIMARY KEY  (`login_attempt_ID`)
			);";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    $table_name = $wpdb->prefix . "lockdowns";

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE " . $table_name . " (
			`lockdown_ID` bigint(20) NOT NULL AUTO_INCREMENT,
			`user_id` bigint(20) NOT NULL,
			`lockdown_date` datetime NOT NULL default '0000-00-00 00:00:00',
			`release_date` datetime NOT NULL default '0000-00-00 00:00:00',
			`lockdown_IP` varchar(100) NOT NULL default '',
			PRIMARY KEY  (`lockdown_ID`)
			);";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    add_option("loginlockdown_db_version", "1.0", "", "no");
    // added in 1.6, cleanup from previously improperly set db versions
    delete_option("loginlockdown_db1_version");
    delete_option("loginlockdown_db2_version");
}

function loginLockdown_countFails($username = "")
{
    global $wpdb;
    global $loginlockdownOptions;
    $table_name = $wpdb->prefix . "login_fails";
    $subnet = loginLockdown_calc_subnet($_SERVER['REMOTE_ADDR']);

    $numFails = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(login_attempt_ID) FROM " . $table_name . " WHERE login_attempt_date + INTERVAL %d MINUTE > %s AND login_attempt_IP LIKE %s",
            array($loginlockdownOptions['retries_within'], current_time('mysql'), $subnet[1]  . "%")
        )
    );

    return $numFails;
}

function loginLockdown_incrementFails($username = "")
{
    global $wpdb;
    global $loginlockdownOptions;
    $table_name = $wpdb->prefix . "login_fails";
    $subnet = loginLockdown_calc_subnet($_SERVER['REMOTE_ADDR']);

    $username = sanitize_user($username);
    $user = get_user_by('login', $username);
    if ($user || "yes" == $loginlockdownOptions['lockout_invalid_usernames']) {
        if ($user === false) {
            $user_id = -1;
        } else {
            $user_id = $user->ID;
        }

        $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'login_attempt_date' => current_time('mysql'),
                'login_attempt_IP' => $subnet[0]
            )
        );
    }
}

function loginLockdown_lockDown($username = "")
{
    global $wpdb;
    global $loginlockdownOptions;
    $table_name = $wpdb->prefix . "lockdowns";
    $subnet = loginLockdown_calc_subnet($_SERVER['REMOTE_ADDR']);

    $username = sanitize_user($username);
    $user = get_user_by('login', $username);
    if ($user || "yes" == $loginlockdownOptions['lockout_invalid_usernames']) {
        if ($user === false) {
            $user_id = -1;
        } else {
            $user_id = $user->ID;
        }

        $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'lockdown_date' => current_time('mysql'),
                'release_date' => date('Y-m-d H:i:s', strtotime(current_time('mysql')) + $loginlockdownOptions['lockout_length'] * 60),
                'lockdown_IP' => $subnet[0]
            )
        );
    }
}

function loginLockdown_isLockedDown()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "lockdowns";
    $subnet = loginLockdown_calc_subnet($_SERVER['REMOTE_ADDR']);

    $stillLocked = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM " . $table_name . " WHERE release_date > %s AND lockdown_IP LIKE %s", array(current_time('mysql'), $subnet[1] . "%")));

    return $stillLocked;
}

function loginLockdown_listLockedDown()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "lockdowns";

    $listLocked = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT lockdown_ID, floor((UNIX_TIMESTAMP(release_date)-UNIX_TIMESTAMP(%s))/60) AS minutes_left, lockdown_IP FROM $table_name WHERE release_date > %s",
            array(current_time('mysql'), current_time('mysql'))
        ),
    ARRAY_A);

    return $listLocked;
}

function loginLockdown_get_options()
{
    $loginlockdownAdminOptions = array(
        'max_login_retries' => 3,
        'retries_within' => 5,
        'lockout_length' => 60,
        'lockout_invalid_usernames' => 'no',
        'mask_login_errors' => 'no',
        'show_credit_link' => 'no'
    );
    $loginlockdownOptions = get_option("loginlockdownAdminOptions");
    if (!empty($loginlockdownOptions)) {
        foreach ($loginlockdownOptions as $key => $option) {
            $loginlockdownAdminOptions[$key] = $option;
        }
    }
    update_option("loginlockdownAdminOptions", $loginlockdownAdminOptions);
    return $loginlockdownAdminOptions;
}

function loginLockdown_calc_subnet($ip)
{
    $subnet[0] = $ip;
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
        $ip = loginLockdown_expandipv6($ip);
        preg_match("/^([0-9abcdef]{1,4}:){4}/", $ip, $matches);
        $subnet[0] = $ip;
        $subnet[1] = $matches[0];
    } else {
        $subnet[1] = substr($ip, 0, strrpos($ip, ".") + 1);
    }
    return $subnet;
}

function loginLockdown_expandipv6($ip)
{
    $hex = unpack("H*hex", inet_pton($ip));
    $ip = substr(preg_replace("/([A-f0-9]{4})/", "$1:", $hex['hex']), 0, -1);

    return $ip;
}


function loginLockdown_print_admin_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "lockdowns";
    $loginlockdownAdminOptions = loginLockdown_get_options();

    if (isset($_POST['update_loginlockdownSettings'])) {

        //wp_nonce check
        check_admin_referer('login-lockdown_update-options');

        if (isset($_POST['ll_max_login_retries'])) {
            $loginlockdownAdminOptions['max_login_retries'] = sanitize_text_field($_POST['ll_max_login_retries']);
        }
        if (isset($_POST['ll_retries_within'])) {
            $loginlockdownAdminOptions['retries_within'] = sanitize_text_field($_POST['ll_retries_within']);
        }
        if (isset($_POST['ll_lockout_length'])) {
            $loginlockdownAdminOptions['lockout_length'] = sanitize_text_field($_POST['ll_lockout_length']);
        }
        if (isset($_POST['ll_lockout_invalid_usernames'])) {
            $loginlockdownAdminOptions['lockout_invalid_usernames'] = sanitize_text_field($_POST['ll_lockout_invalid_usernames']);
        }
        if (isset($_POST['ll_mask_login_errors'])) {
            $loginlockdownAdminOptions['mask_login_errors'] = sanitize_text_field($_POST['ll_mask_login_errors']);
        }
        if (isset($_POST['ll_show_credit_link'])) {
            $loginlockdownAdminOptions['show_credit_link'] = sanitize_text_field($_POST['ll_show_credit_link']);
        }
        update_option("loginlockdownAdminOptions", $loginlockdownAdminOptions);
        ?>
        <div class="updated">
            <p><strong><?php esc_html_e("Settings Updated.", "loginlockdown"); ?></strong></p>
        </div>
    <?php
    }
    if (isset($_POST['release_lockdowns'])) {

        //wp_nonce check
        check_admin_referer('login-lockdown_release-lockdowns');

        if (isset($_POST['releaseme'])) {
            $released = array_map( 'intval', $_POST['releaseme'] );

            foreach ($released as $release_id) {
                $wpdb->query(
                    $wpdb->prepare("UPDATE $table_name SET release_date = %s WHERE lockdown_ID = %d", array(current_time('mysql'), $release_id))
                );
            }
        }
        update_option("loginlockdownAdminOptions", $loginlockdownAdminOptions);
    ?>
        <div class="updated">
            <p><strong><?php esc_html_e("Lockdowns Released.", "loginlockdown"); ?></strong></p>
        </div>
    <?php
    }
    $dalist = loginLockdown_listLockedDown();
    ?>
    <div class="wrap">
        <?php

        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';

        ?>
        <h2><?php esc_html_e('Login LockDown Options', 'loginlockdown') ?></h2>

        <h2 class="nav-tab-wrapper">
            <a href="?page=loginlockdown.php&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Settings', 'loginlockdown') ?></a>
            <a href="?page=loginlockdown.php&tab=activity" class="nav-tab <?php echo $active_tab == 'activity' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Activity', 'loginlockdown') ?> (<?php echo count($dalist); ?>)</a>
        </h2>
        <?php if ($active_tab == 'settings') { ?>
            <form method="post" action="<?php echo esc_attr($_SERVER["REQUEST_URI"]); ?>">
                <?php
                if (function_exists('wp_nonce_field'))
                    wp_nonce_field('login-lockdown_update-options');
                ?>

                <h3><?php esc_html_e('Max Login Retries', 'loginlockdown') ?></h3>
                <p><?php esc_html_e('Number of failed login attempts within the "Retry Time Period Restriction" (defined below) needed to trigger a LockDown.', 'loginlockdown') ?></p>
                <p><input type="text" name="ll_max_login_retries" size="8" value="<?php echo esc_attr($loginlockdownAdminOptions['max_login_retries']); ?>"></p>
                <h3><?php esc_html_e('Retry Time Period Restriction (minutes)', 'loginlockdown') ?></h3>
                <p><?php esc_html_e('Amount of time that determines the rate at which failed login attempts are allowed before a LockDown occurs.', 'loginlockdown') ?></p>
                <p><input type="text" name="ll_retries_within" size="8" value="<?php echo esc_attr($loginlockdownAdminOptions['retries_within']); ?>"></p>
                <h3><?php esc_html_e('Lockout Length (minutes)', 'loginlockdown') ?></h3>
                <p><?php esc_html_e('How long a particular IP block will be locked out for once a LockDown has been triggered.', 'loginlockdown') ?></p>
                <p><input type="text" name="ll_lockout_length" size="8" value="<?php echo esc_attr($loginlockdownAdminOptions['lockout_length']); ?>"></p>
                <h3><?php esc_html_e('Lockout Invalid Usernames?', 'loginlockdown') ?></h3>
                <p><?php esc_html_e('By default Login LockDown will not trigger if an attempt is made to log in using a username that does not exist. You can override this behavior here.', 'loginlockdown') ?></p>
                <p><input type="radio" name="ll_lockout_invalid_usernames" value="yes" <?php if ($loginlockdownAdminOptions['lockout_invalid_usernames'] == "yes") echo "checked"; ?>>&nbsp;<?php esc_html_e('Yes', 'loginlockdown') ?>&nbsp;&nbsp;&nbsp;<input type="radio" name="ll_lockout_invalid_usernames" value="no" <?php if ($loginlockdownAdminOptions['lockout_invalid_usernames'] == "no") echo "checked"; ?>>&nbsp;<?php esc_html_e('No', 'loginlockdown') ?></p>
                <h3><?php esc_html_e('Mask Login Errors?', 'loginlockdown') ?></h3>
                <p><?php esc_html_e('WordPress will normally display distinct messages to the user depending on whether they try and log in with an invalid username, or with a valid username but the incorrect password. Toggling this option will hide why the login failed.', 'loginlockdown') ?></p>
                <p><input type="radio" name="ll_mask_login_errors" value="yes" <?php if ($loginlockdownAdminOptions['mask_login_errors'] == "yes") echo "checked"; ?>>&nbsp;<?php esc_html_e('Yes', 'loginlockdown') ?>&nbsp;&nbsp;&nbsp;<input type="radio" name="ll_mask_login_errors" value="no" <?php if ($loginlockdownAdminOptions['mask_login_errors'] == "no") echo "checked"; ?>>&nbsp;<?php esc_html_e('No', 'loginlockdown') ?></p>
                <h3><?php esc_html_e('Show Credit Link?', 'loginlockdown') ?></h3>
                <p><?php esc_html_e('If enabled, Login LockDown will display the following message on the login form', 'loginlockdown') ?>:<br />
                <blockquote><?php esc_html_e('Login form protected by', 'loginlockdown') ?> <a href='https://wploginlockdown.com/'>Login LockDown</a>.</blockquote>
                <?php esc_html_e('This helps others know about the plugin so they can protect their blogs as well if they like. You can enable or disable this message below', 'loginlockdown') ?>:</p>
                <input type="radio" name="ll_show_credit_link" value="yes" <?php if ($loginlockdownAdminOptions['show_credit_link'] == "yes" || $loginlockdownAdminOptions['show_credit_link'] == "") echo "checked"; ?>>&nbsp;<?php esc_html_e('Yes, display the credit link.', 'loginlockdown') ?><br />
                <input type="radio" name="ll_show_credit_link" value="shownofollow" <?php if ($loginlockdownAdminOptions['show_credit_link'] == "shownofollow") echo "checked"; ?>>&nbsp;<?php esc_html_e('Display the credit link, but add "rel=\'nofollow\'" (ie. do not pass any link juice).', 'loginlockdown') ?><br />
                <input type="radio" name="ll_show_credit_link" value="no" <?php if ($loginlockdownAdminOptions['show_credit_link'] == "no") echo "checked"; ?>>&nbsp;<?php esc_html_e('No, do not display the credit link.', 'loginlockdown') ?><br />
                <div class="submit">
                    <input type="submit" class="button button-primary" name="update_loginlockdownSettings" value="<?php esc_html_e('Update Settings', 'loginlockdown') ?>" />
                </div>
            </form>
        <?php } else { ?>
            <form method="post" action="<?php echo esc_attr($_SERVER["REQUEST_URI"]); ?>">
                <?php
                if (function_exists('wp_nonce_field'))
                    wp_nonce_field('login-lockdown_release-lockdowns');
                ?>
                <h3><?php
                    if (count($dalist) == 1) {
                        printf(esc_html__('There is currently %d locked out IP address.', 'loginlockdown'), count($dalist));
                    } else {
                        printf(esc_html__('There are currently %d locked out IP addresses.', 'loginlockdown'), count($dalist));
                    } ?></h3>

                <?php
                $num_lockedout = count($dalist);
                if (0 == $num_lockedout) {
                    echo "<p>No IP blocks currently locked out.</p>";
                } else {
                    foreach ($dalist as $key => $option) {
                ?>
                        <li><input type="checkbox" name="releaseme[]" value="<?php echo esc_attr($option['lockdown_ID']); ?>"> <?php echo esc_attr($option['lockdown_IP']); ?> (<?php echo esc_attr($option['minutes_left']); ?> <?php esc_html_e('minutes left', 'loginlockdown') ?>)</li>
                <?php
                    }
                }
                ?>
                <div class="submit">
                    <input type="submit" class="button button-primary" name="release_lockdowns" value="<?php esc_html_e('Release Selected', 'loginlockdown') ?>" />
                </div>
            </form>
        <?php } ?>
    </div>
<?php
} //End function loginLockdown_print_admin_page()

function loginLockdown_ap()
{
    if (function_exists('add_options_page')) {
        add_options_page('Login LockDown', 'Login LockDown', 'manage_options', basename(__FILE__), 'loginLockdown_print_admin_page');
    }
}

function loginLockdown_credit_link()
{
    global $loginlockdownOptions;
    $thispage = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    $homepage = get_option("home");
    $showcreditlink = $loginlockdownOptions['show_credit_link'];
    $relnofollow = true;
    if ($showcreditlink != "shownofollow" && ($thispage == $homepage || $thispage == $homepage . "/" || substr($_SERVER["REQUEST_URI"], strlen($_SERVER["REQUEST_URI"]) - 12) == "wp-login.php")) {
        $relnofollow = false;
    }
    if ($showcreditlink != "no") {
        echo "<p>";
        esc_html_e('Login form protected by', 'loginlockdown');
        echo " <a href='" . esc_url('https://wploginlockdown.com/') . "' " . ($relnofollow ? "rel='nofollow'" : "") . ">Login LockDown</a>.<br /><br /><br /></p>";
    }
}

//Actions and Filters
if (isset($loginlockdown_db_version)) {
    //Actions
    add_action('admin_menu', 'loginLockdown_ap');
    register_activation_hook(__FILE__, 'loginLockdown_install');
    add_action('login_form', 'loginLockdown_credit_link');

    remove_filter('authenticate', 'wp_authenticate_username_password', 20, 3);
    add_filter('authenticate', 'loginLockdown_wp_authenticate_username_password', 20, 3);
    //Filters

    //Functions
    function loginLockdown_wp_authenticate_username_password($user, $username, $password)
    {
        if (is_a($user, 'WP_User')) {
            return $user;
        }

        if (empty($username) || empty($password)) {
            $error = new WP_Error();

            if (empty($username))
                $error->add('empty_username', __('<strong>ERROR</strong>: The username field is empty.', 'loginlockdown'));

            if (empty($password))
                $error->add('empty_password', __('<strong>ERROR</strong>: The password field is empty.', 'loginlockdown'));

            return $error;
        }

        $userdata = get_user_by('login', $username);

        if (!$userdata) {
            return new WP_Error('invalid_username', sprintf(__('<strong>ERROR</strong>: Invalid username. <a href="%s" title="Password Lost and Found">Lost your password</a>?', 'loginlockdown'), site_url('wp-login.php?action=lostpassword', 'login')));
        }

        $userdata = apply_filters('wp_authenticate_user', $userdata, $password);
        if (is_wp_error($userdata)) {
            return $userdata;
        }

        if (!wp_check_password($password, $userdata->user_pass, $userdata->ID)) {
            return new WP_Error('incorrect_password', sprintf(__('<strong>ERROR</strong>: Incorrect password. <a href="%s" title="Password Lost and Found">Lost your password</a>?', 'loginlockdown'), site_url('wp-login.php?action=lostpassword', 'login')));
        }

        $user =  new WP_User($userdata->ID);
        return $user;
    }


    if (!function_exists('wp_authenticate')) {
        function wp_authenticate($username, $password)
        {
            global $wpdb, $error;
            global $loginlockdownOptions;

            $username = sanitize_user($username);
            $password = trim($password);

            if ("" != loginLockdown_isLockedDown()) {
                return new WP_Error('incorrect_password', __("<strong>ERROR</strong>: We're sorry, but this IP range has been blocked due to too many recent failed login attempts.<br /><br />Please try again later.", 'loginlockdown'));
            }

            $user = apply_filters('authenticate', null, $username, $password);

            if ($user == null) {
                // TODO what should the error message be? (Or would these even happen?)
                // Only needed if all authentication handlers fail to return anything.
                $user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.', 'loginlockdown'));
            }

            $ignore_codes = array('empty_username', 'empty_password');

            if (is_wp_error($user) && !in_array($user->get_error_code(), $ignore_codes)) {
                loginLockdown_incrementFails($username);

                if ($loginlockdownOptions['max_login_retries'] <= loginLockdown_countFails($username)) {
                    loginLockdown_lockDown($username);
                    return new WP_Error('incorrect_password', __("<strong>ERROR</strong>: We're sorry, but this IP range has been blocked due to too many recent failed login attempts.<br /><br />Please try again later.", 'loginlockdown'));
                }
                if ('yes' == $loginlockdownOptions['mask_login_errors']) {
                    return new WP_Error('authentication_failed', sprintf(__('<strong>ERROR</strong>: Invalid username or incorrect password. <a href="%s" title="Password Lost and Found">Lost your password</a>?', 'loginlockdown'), site_url('wp-login.php?action=lostpassword', 'login')));
                } else {
                    do_action('wp_login_failed', $username);
                }
            }

            return $user;
        }
    }

    // multisite network-wide activation
    register_activation_hook(__FILE__, 'loginLockdown_multisite_activate');
    function loginLockdown_multisite_activate($networkwide)
    {
        global $wpdb;

        if (function_exists('is_multisite') && is_multisite()) {
            // check if it is a network activation - if so, run the activation function for each blog id
            if ($networkwide) {
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    loginLockdown_install();
                }
                switch_to_blog($old_blog);
                return;
            }
        }
    }

    // multisite new site activation
    add_action('wpmu_new_blog', 'loginLockdown_multisite_newsite', 10, 6);
    function loginLockdown_multisite_newsite($blog_id, $user_id, $domain, $path, $site_id, $meta)
    {
        global $wpdb;

        if (is_plugin_active_for_network('loginlockdown/loginlockdown.php')) {
            $old_blog = $wpdb->blogid;
            switch_to_blog($blog_id);
            loginLockdown_install();
            switch_to_blog($old_blog);
        }
    }

    // multisite old sites check

    add_action('admin_init', 'loginLockdown_multisite_legacy');
    function loginLockdown_multisite_legacy()
    {
        $loginlockdownMSRunOnce = get_option("loginlockdownmsrunonce");
        if (empty($loginlockdownMSRunOnce)) {
            global $wpdb;

            if (function_exists('is_multisite') && is_multisite()) {

                $old_blog = $wpdb->blogid;

                // Get all blog ids
                $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                foreach ($blogids as $blog_id) {

                    // check if already exists
                    $bed_check = $wpdb->query($wpdb->prepare("SHOW TABLES LIKE %s", array($wpdb->base_prefix . $blog_id . '_login_fails')));
                    if (!$bed_check) {

                        switch_to_blog($blog_id);
                        loginLockdown_install();
                    }
                }
                switch_to_blog($old_blog);
            }
            add_option("loginlockdownmsrunonce", "done", "", "no");
            return;
        }
    }
}

add_action('plugins_loaded', 'loginLockdown_init', 10);

function loginLockdown_init()
{
    load_plugin_textdomain('loginlockdown', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
