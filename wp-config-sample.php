<?php
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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'av08830b' );

/** Database username */
define( 'DB_USER', 'av08830' );

/** Database password */
define( 'DB_PASSWORD', 'pT5UpkM.jJd2mWG6f' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define('AUTH_KEY',         'BOc;-Mz4|.I #W%MsC$T+k6nRyKfw5Bf$b1|^nQ-2-b#B7E>|.MH;8s-6z;/X@Qu');
define('SECURE_AUTH_KEY',  'R;tx;<J*tFS^;C=0-Sr$L?Ck@;-Ea`DM]nLh<y^y1>g%gc:`wp{f^qZ(FdXTBp[j');
define('LOGGED_IN_KEY',    '+|+k${#.<j|;JO3p1~eR}dNVar2,Gi+D}O$;6|;A[~[P tyCq%FeycvS.@y<6z?|');
define('NONCE_KEY',        '*L<g{(-dO% K%+5t3Lol`[;4>ADGMmVnQ+09I1>aI$q $)45*CICIiFJX8w]xj$>');
define('AUTH_SALT',        'CB D2uq NSTbl6AeBa!Z8No~j-zOQKq^VVY&Fx(|#~VA)vZwa]3M9SuBF-U>Hvwj');
define('SECURE_AUTH_SALT', 'SyblV&zf~mA&nAx7baut{ H6U(H[B>CQuAxMu{Ucp60m7yePnZp-T`w&r-=sM)l?');
define('LOGGED_IN_SALT',   '6*4/IGXDWJW7_:_Oxsn|z`!*i^*VbH%G^=F[1TC%n+e7}T/Q|-rvCqBX|^E i?pD');
define('NONCE_SALT',       '[|~Qo( jdce`IZy(<+P-FzV_Tw]&pJX~K/Y/0_gMXY%muu&-aPo(,Y{<H)BEM[dK');

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */

/* Add any custom values between this line and the "stop editing" line. */

define('WP_MEMORY_LIMIT', '1G');
define('WP_CACHE',       true );

define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', false);
define('WP_DEBUG_LOG', "/storage/av08830/logs/debug.log");

define('FORCE_SSL_ADMIN', true);
define('FORCE_SSL_LOGIN', true);

define('DISALLOW_FILE_MODS', true);

define('ET_BUILDER_CACHE_MODULES', true);
define('ET_BUILDER_CACHE_ASSETS', true);

define('ALLOW_UNFILTERED_UPLOADS', true);

if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
	$_SERVER['HTTPS'] = 'on';
	$_SERVER['SERVER_PORT'] = 443;
}

update_option('siteurl',    "https://stage-website.devcloud.applause.com");
update_option('home',       "https://stage-website.devcloud.applause.com");
define('WP_HOME',           "https://stage-website.devcloud.applause.com");
define('WP_SITEURL',        "https://stage-website.devcloud.applause.com");




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
