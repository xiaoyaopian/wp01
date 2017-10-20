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
define('DB_NAME', 'dbwp');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'rellik-mysql');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '56@[l![1-2J6$2%*)XC,QQIjZ*98fIJICk38R{E1R1b:fScvc#rpH9$bu$RybN$+');
define('SECURE_AUTH_KEY',  'dFvh$2,-)48>Qo)#dI51 >ojtk^O5 ~Fqs:aAw>Y[fFgTzC5?4-]iIXZ[dgIZ7:o');
define('LOGGED_IN_KEY',    't}9M^?@`Q$W!HVq%ZbQuuY{V>.WZvs[<K T^Vg!(?u3@fb3p-@d5<ls=9?@Wv$Y,');
define('NONCE_KEY',        'oWBpPS6&o:I&/Ks&d=2aAu69L:i5B@ETQ)8!MsAx,pY;ZO+E;:XK&.,.y%:LE =D');
define('AUTH_SALT',        '1fM=O}w/T<qcFe;._B+|}2o}W1zE7AhdEW`8[$vyTK^_MsS7785$,,l3S7p)d-J.');
define('SECURE_AUTH_SALT', '}k1f6>,`uMhpw.L9Pzzdh@[m!a&_7xi*T!%{IHixx:q[:)f5t]r49yVTIRV!P*W+');
define('LOGGED_IN_SALT',   'ul4:(-HTHKT=[$tR~4)r;R$8/?<Z$lnOL])e7)n,qJJnX55|]l56dzbn6``6v(Nm');
define('NONCE_SALT',       '^7-ZF1*Z0u6+A%B l?}]e:tfi.ulK!I.09*L3c77=9+<}yigU|S2WP#XVN2bAi){');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp01_';

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
define('WP_DEBUG', ture);
ini_set('display_errors','Off');

/* For language  */
define('WPLANG', 'zh_CN');

/* For ???*/
define(‘FS_METHOD’, "direct");

define(‘FS_CHMOD_DIR’, 0777);

define(‘FS_CHMOD_FILE’, 0777);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

