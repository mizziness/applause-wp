<?php

@ini_set( 'max_execution_time' , 300 );
@ini_set( 'max_input_time', 300 );
@ini_set( 'upload_max_filesize', '512M');
@ini_set( 'post_max_size', '512M' );
@ini_set( 'memory_limit', '512M' );

require_once  ("vendor/autoload.php");

if (class_exists('Dotenv\Dotenv') && file_exists(__DIR__ . '/.env')) {
    Dotenv\Dotenv::create(__DIR__)->load();
}

$environment = getenv("ENVIRONMENT") ?? 'development';

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

//"mysql://root:root@127.0.0.1:3306/"

define( 'DB_NAME', $environment == 'development' ? 'applause-wp-stage' : 'av08830b' );

/** Database username */
define( 'DB_USER', $environment == 'development' ? 'root' : 'av08830' );

/** Database password */
define( 'DB_PASSWORD', $environment == 'development' ? 'root' : 'pT5UpkM.jJd2mWG6f' );

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

define('WP_DEBUG', 1 );
define('WP_DEBUG_DISPLAY', 0 );
define('WP_DEBUG_LOG', "wp-content/debug.log");
define('WP_MEMORY_LIMIT', '1G');

define('FORCE_SSL_ADMIN', true);
define('FORCE_SSL_LOGIN', true);

/* Turn HTTPS 'on' if HTTP_X_FORWARDED_PROTO matches 'https' */
if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
  $_SERVER['HTTPS'] = 'on';
  $_SERVER['SERVER_PORT'] = 443;
}

define('DISALLOW_FILE_MODS', true);
define('ALLOW_UNFILTERED_UPLOADS', true);

define('WP_CACHE', true );
define('ET_BUILDER_CACHE_MODULES', true);
define('ET_BUILDER_CACHE_ASSETS', true);

define('AS3CF_PROVIDER', "aws");
define('AS3CF_BUCKET', getenv('AWS_BUCKET'));
define('AS3CF_REGION', getenv('AWS_REGION'));
define( 'DBI_AWS_ACCESS_KEY_ID', getenv('AWS_ACCESS_KEY_ID') );
define( 'DBI_AWS_SECRET_ACCESS_KEY', getenv('AWS_SECRET_ACCESS_KEY') );

define('AS3CF_SETTINGS', serialize(
  array(
    'provider' => 'aws',
    'access-key-id' => getenv('AWS_ACCESS_KEY_ID'),
    'secret-access-key' => getenv('AWS_SECRET_ACCESS_KEY')
  )
));

define('AS3CF_AWS_ACCESS_KEY_ID', getenv('AWS_ACCESS_KEY_ID'));
define('AS3CF_AWS_SECRET_ACCESS_KEY', getenv('AWS_SECRET_ACCESS_KEY'));

if(function_exists('get_option')) {

  $localUrl = getenv("WP_SITEURL") ?? "https://applause-wp.local/";

  update_option('siteurl', $localUrl);
  update_option('home', $localUrl);
  define('WP_HOME', $localUrl);
  define('WP_SITEURL', $localUrl);
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
