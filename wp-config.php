<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'WebsiteKecamatanBintanTimur' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

if ( !defined('WP_CLI') ) {
    define( 'WP_SITEURL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] );
    define( 'WP_HOME',    $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] );
}



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
define( 'AUTH_KEY',         'brD32zDNpy0BaKUiAnlb6j7mmlToCJecxoNCYZI6FHZemvLs9e9T3KS8R2fr9No1' );
define( 'SECURE_AUTH_KEY',  'e8uSJ0l0qewEuZsHYTPn4VyXU4xFClomrCCJWl9gd7bL8ATp7l4MbkOQ6iDeAtkn' );
define( 'LOGGED_IN_KEY',    'VQeQFYVYO2byyCdBXxG9f3Whr9O1bwyOp21FaqfZJc9rSvwWOgbYozujmoBxNsDc' );
define( 'NONCE_KEY',        'zJDLc9EA9asQR1JTuLYwjhJKP5jKiv4xXWc5ZzaAfXyxdEhlAlFGGrtqn5W5kJyo' );
define( 'AUTH_SALT',        'Jysfj5PHmbiKcJudYgBMrgJY7Ljthp9cQuDmhoKDAot2a1yTtXpxqM9eCAOiNbU4' );
define( 'SECURE_AUTH_SALT', 'ctOJrmw4nodtSPCKO4zWCgZjtS8LFaCAUuAFJTww71LH1y3zCW1pfuZdaEr6c8vu' );
define( 'LOGGED_IN_SALT',   'DyP0b1lbkWJBMGuFbrZJEoFMGR0sCJrez1lsNs6hBfyOqlE9P5XMaO0Ruir5VjJh' );
define( 'NONCE_SALT',       'l7UcwsMdLoN7DwtcCuoR1oDXo6dmD8gqWTWTpto88I14B20MtPJshpvG1O2aO2qJ' );

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
