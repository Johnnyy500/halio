<?php
if (!defined('ABSPATH')) { exit; }


require_once HALIO_PLUGIN_DIR . '/includes/halio-setup.php';
require_once HALIO_PLUGIN_DIR . '/includes/halio-html-form.php';
require_once HALIO_PLUGIN_DIR . '/includes/halio-shortcodes.php';
require_once HALIO_PLUGIN_DIR . '/includes/halio-install.php';
require_once HALIO_PLUGIN_DIR . '/includes/halio-page-render.php';
require_once HALIO_PLUGIN_DIR . '/includes/halio-price-estimator.php';
require_once HALIO_PLUGIN_DIR . '/includes/halio-new-order.php';
require_once HALIO_PLUGIN_DIR . '/includes/halio-calendar.php';
require_once HALIO_PLUGIN_DIR . '/includes/functions.php';

// Load deps
add_action('wp_enqueue_scripts', array('HalioSetup', 'load_styles'));
add_action('wp_enqueue_scripts', array('HalioSetup', 'load_scripts'));
add_action('admin_enqueue_scripts', array('HalioSetup', 'load_styles'));
add_action('admin_enqueue_scripts', array('HalioSetup', 'load_scripts'));

// Load translations
add_action('plugins_loaded', array('HalioSetup', 'load_translations'));

// Add menu to admin area
add_action('admin_menu', array('HalioSetup', 'add_admin_sidebar'));

// Listen for shortcode
add_shortcode('halio', array('HalioShortcodes', 'shortcode_found'));

// Price Estiamte AJAX
add_action('wp_ajax_halio_estimate_price', array(new HalioPriceEstimator(), 'new_ajax_request'));
add_action('wp_ajax_nopriv_halio_estimate_price', array(new HalioPriceEstimator(), 'new_ajax_request'));

// Calendar Events AJAX
add_action('wp_ajax_halio_get_calendar_events', array(new HalioCalendar(), 'get_events'));
add_action('wp_ajax_nopriv_halio_get_calendar_events', array(new HalioCalendar(), 'get_events'));

// On every page load check if user has submitted Halio form
add_action('wp_loaded', array(new HalioNewOrder(), 'check_for_new_order'));

// Add custom fields etc. to WooCommerce
add_filter('woocommerce_checkout_fields', array(new HalioNewOrder(), 'customise_checkout_fields'));
add_filter('woocommerce_after_order_notes', array(new HalioNewOrder(), 'add_custom_checkout_fields'));
add_action('woocommerce_checkout_update_order_meta', array(new HalioNewOrder(), 'halio_update_order_meta'));
add_action('woocommerce_before_calculate_totals', array(new HalioNewOrder(), 'calculate_price'));
add_action('woocommerce_thankyou', array(new HalioNewOrder(), 'woocommerce_user_page_order_info'), 1);
add_action('woocommerce_view_order', array(new HalioNewOrder(), 'woocommerce_user_page_order_info'), 1);
add_action('woocommerce_admin_order_data_after_shipping_address', array(New HalioNewOrder(), 'order_details'), 10, 1);
add_action('woocommerce_email_before_order_table', array(new HalioNewOrder(), 'email_content'), 10, 2);
