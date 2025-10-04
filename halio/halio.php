<?php

/**
 * Plugin Name: Halio
 * Plugin URI: http://halio.timgreen.biz
 * Author: Tim Green
 * Author URI: http://timgreen.biz
 * Text Domain: halio
 * Domain Path: /languages
 * Version: 1.5.1
 * License: GPLv2
 * Description: Powerful pricing estimates for driving services.
 */

define('HALIO_VERSION', '1.5.1');
define('HALIO_PLUGIN', __FILE__);
define('HALIO_PLUGIN_DIR', untrailingslashit(dirname(HALIO_PLUGIN)));

require_once HALIO_PLUGIN_DIR . '/settings.php';

// Plugin activation
register_activation_hook(__FILE__, array(new HalioInstall(), 'install'));


