<?php

// BEGIN iThemes Security - Do not modify or remove this line
// iThemes Security Config Details: 2
define( 'DISALLOW_FILE_EDIT', true ); // Disable File Editor - Security > Settings > WordPress Tweaks > File Editor
// END iThemes Security - Do not modify or remove this line

define( 'ITSEC_ENCRYPTION_KEY', 'KXAlfGc/OVMtbi5oMT8pTz41LXI1MyNWMEg9eDheR2AjUXBkQiVXfXpLUk9jNU5zRyhfNmQ+emUhNl42SzpOcQ==' );

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'plantasdb' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'R(E;oS t ezaUGT,>7?T]`fkARst(VRCuo?eg-b6!,*/Sg>#][/Mg])]2K`$A52Y' );
define( 'SECURE_AUTH_KEY',  'Y3G:Slw@*-h{,c_P41h3E!RZJs8j!|a}xaGt7Td~yp9<r]Og>oy`P!V+an8,X|xl' );
define( 'LOGGED_IN_KEY',    '4f8kVp/Fm=:(]Tl)QiTPztseV^|dAz3r%e;|//Ec+=y.!}Pj[BklB)M@pGsUz*mW' );
define( 'NONCE_KEY',        'J9VB4feH6[Cv0f]c-b6=J SDu]=A5(0A1uzC5T9ZIQ@tWKLLkso~!yAbs%Za9Cf`' );
define( 'AUTH_SALT',        '@Y58~w?4rY-y)kCrbIC B3CFYk;5>, 8YM0WIK<PUI`ioRKDuXb2|YEgzN_&mWQ]' );
define( 'SECURE_AUTH_SALT', '>Uwu5w%8WC<x*I$B]a9RumJ2P7*YvxP]8bs,&xlgS|(=s8)}_S}Z}_V+C=mVYHeg' );
define( 'LOGGED_IN_SALT',   '16y7lL&iz.P3 AHv{hP#azFH01m0_Twk5p8= O<hQeW_xCmOcOl+#O5na 5)jJJ<' );
define( 'NONCE_SALT',       '|zB_?~~K9S7w`@(, ^s/45}{;#9Gr)mL uqgnS<2L>W$l)X<kSI*g%db /^nc]m_' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
