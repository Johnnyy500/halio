<?php
if (!defined('ABSPATH')) { exit; }


/**
 * Plugin Name: Halio
 * Plugin URI: http://halio.timgreen.biz
 * Author: Tim Green
 * Author URI: http://timgreen.biz
 * Text Domain: halio
 * Domain Path: /languages
 * Version: 1.6.0
 * License: GPLv2
 * Requires at least: 6.0
Requires PHP: 8.0
Tested up to: 6.6
Description: Powerful pricing estimates for driving services.
 */

define('HALIO_VERSION', '1.6.0');
define('HALIO_PLUGIN', __FILE__);
define('HALIO_PLUGIN_DIR', untrailingslashit(dirname(HALIO_PLUGIN)));

require_once HALIO_PLUGIN_DIR . '/settings.php';

// Plugin activation
register_activation_hook(__FILE__, array(new HalioInstall(), 'install'));


