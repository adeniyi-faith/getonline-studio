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
define( 'DB_NAME', 'worldin6_wp240' );

/** Database username */
define( 'DB_USER', 'worldin6_wp240' );

/** Database password */
define( 'DB_PASSWORD', '4se5-!2Slp' );

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
define( 'AUTH_KEY',         'lski8wk8yiekjh7ezrf8rwnuz0qewlhekjovems0ryop3cbjz7bbvpptkiolqc2o' );
define( 'SECURE_AUTH_KEY',  'tkalzh5creg9axqndiuxzoak8g8kuam9of2gjer0ih3mtxeidbnqxyth1ymya9dn' );
define( 'LOGGED_IN_KEY',    'wyq2qwyv4itk44e5tsg9vql8mkj8fkup2gfavlcc3vfnhnfaxfzspwj6euuozyvy' );
define( 'NONCE_KEY',        'jeudkrhttvqcn58gknttoa6xjmbmvvyzaye0rsat6zcigu0xbehbuxhbfozsrgkc' );
define( 'AUTH_SALT',        '7yqroz48zazivpysfcxxoqa1ixwkbth9sqcihu1my5jkem7y9lsvzsugnh6ydku8' );
define( 'SECURE_AUTH_SALT', 'k96rbk3v26d0sjmk4rvrzr6oqoj6orrwalfodcvdzhrnev1rsibjvvrnfyiddwkk' );
define( 'LOGGED_IN_SALT',   '1o3wpkvdrr0nl8w9jkzltcx3biabqeru15zezfp17aw4ulrv6twmhu6rxhu9mdga' );
define( 'NONCE_SALT',       'irfytj56h8fzaozaiayzhmigpfmrlkwlu88nbc822jcmuxbrwycrvzrwm8lphhc1' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wpo6_';

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
