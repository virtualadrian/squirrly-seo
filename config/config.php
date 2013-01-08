<?php
/**
 * The configuration file
 * 
 * Constants and ini_sets
 */
/* debug section */
define('_SQ_DEBUG_LOG_', false);
define('_SQ_DEBUG_SQL_', false);
/** current plugin version */

define('_SQ_SUPPORT_EMAIL_', 'support@squirrly.co');

$sq_showNote = array();
//$sq_showNote['1.0.1'] = __('This is the first version of Squirrly.', 'squirrly');

/* Improve PHP configuration to prevent issues */
@ini_set('upload_max_filesize', '50M');
@ini_set('default_charset', 'utf-8');
@ini_set('magic_quotes_runtime', 0);

define('_SQ_NONCE_ID_', 'sq_none');

/* No path file? error ...*/
require_once(dirname(__FILE__).'/paths.php');


if (!defined('PHP_VERSION_ID'))
{
	$version = explode('.', PHP_VERSION);
	define('PHP_VERSION_ID', ((int)@$version[0] * 1000 + (int)@$version[1] * 100 ));
}
if (!defined('WP_VERSION_ID'))
{
	$version = explode('.', $wp_version);
	define('WP_VERSION_ID', ((int)@$version[0] * 1000 + (int)@$version[1] * 100 ));
}
if (!defined('SQ_VERSION_ID'))
{
	$version = explode('.', SQ_VERSION);
	define('SQ_VERSION_ID', ((int)@$version[0] * 1000 + (int)@$version[1] + (int)@$version[2] * 100 ));
}
              
/* Define the record name in the Option and UserMeta tables */
define('SQ_OPTION', 'sq_options');
define('SQ_META', 'sq_plugin_flash');

if(WP_VERSION_ID >= 3000)
    define('SQ_URI', 'wp350');
else
    define('SQ_URI', 'wp2');
?>