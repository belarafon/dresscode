<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'eqlutody_wp35' );

/** MySQL database username */
define( 'DB_USER', 'eqlutody_wp35' );

/** MySQL database password */
define( 'DB_PASSWORD', '1S4@p01vu!' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'xxli3gypdqb1iq88oidxcty5sujszfoua52aaq4vmxa63w2khz9ijrvrzuawock4' );
define( 'SECURE_AUTH_KEY',  'r9lnuqc5f2y2jsvtmb2xdgg5jag2qnfjw4qb6q25slrtaub2eo6tfjxvnnjjv0yg' );
define( 'LOGGED_IN_KEY',    'alrnxajek3x8c1iq8tmxt9bj4dp3zkegpuwwn3okbe5nmhghvm4yuwjxin8fzh5f' );
define( 'NONCE_KEY',        'wplbu6s24yit8zpcwfzyz4kjknicnmmigdde4fby3z0k4yqsixnzqvzniczcuqpb' );
define( 'AUTH_SALT',        'z7va57tkicwk2yvoydrottvusodc8qtpchl3wkh5ppilodkvkk2lmdohagitl27h' );
define( 'SECURE_AUTH_SALT', 'zaiphqhipn4krfhl3fa6exg9gnxsqsfqomu48cgpopss80corc5ndtxmczlmrpba' );
define( 'LOGGED_IN_SALT',   'kv6dsrwqvxfofy1yii1ahxfwypnyd7y2nrmkard8txv3a1oqhfyentnn9mi3kpot' );
define( 'NONCE_SALT',       'dc5rrc8wqeeeaodtjkvawb3k5qtkaskqxkqocgyl2esfzn0satuvzmv9jrfgfs4c' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpbk_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'AUTOSAVE_INTERVAL', 1000 ); // Seconds

define( 'WP_POST_REVISIONS', 3 );

define('WP_DEBUG', false);

define( 'WP_SITEURL', 'https://' . $_SERVER['SERVER_NAME'] . '/' );
define( 'WP_HOME', 'https://' . $_SERVER['SERVER_NAME'] . '/' );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
