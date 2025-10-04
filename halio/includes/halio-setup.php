<?php
if (!defined('ABSPATH')) { exit; }


class HalioSetup {

  public static function load_scripts() {
    wp_enqueue_script('jquery', array(), false, true);
    wp_enqueue_script('jquery-ui-core', array(), false, true);
    wp_enqueue_script('moment', halio_plugin_url('includes/js/moment.js'), array(), HALIO_VERSION, true);
    wp_enqueue_script('fullcalendar', halio_plugin_url('includes/js/fullcalendar.min.js'), array(), HALIO_VERSION, true);
    wp_enqueue_script('qTip', halio_plugin_url('includes/js/qTip.min.js'), array('jquery'), HALIO_VERSION, true);
    wp_enqueue_script('bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js', array(), NULL, true);
    wp_enqueue_script('bootstrap-datepicker', halio_plugin_url('includes/js/bootstrap-datepicker.js'), array(), HALIO_VERSION, true);
    wp_enqueue_script('halio', halio_plugin_url('includes/js/script.js'), array(), HALIO_VERSION, true);
    wp_localize_script('halio', 'ajax_object', array( 'ajax_url' => admin_url('admin-ajax.php') ));

    $api_key = halio_get_settings_row('api_key')->value;

    if ( is_admin() ) {
      // Include drawing library if admin
      wp_enqueue_script('maps', '//maps.googleapis.com/maps/api/js?key=' . $api_key . '&callback=initMap&libraries=places,drawing', array(), false, true);
    } else {
      wp_enqueue_script('maps', '//maps.googleapis.com/maps/api/js?key=' . $api_key . '&callback=initMap&libraries=places', array(), false, true);
    }
  }

  public static function load_styles() {
    wp_enqueue_style('halio-bootstrap', halio_plugin_url('includes/css/bootstrap.css'), array(), HALIO_VERSION);
    wp_enqueue_style('bootstrap-datepicker', halio_plugin_url('includes/css/bootstrap-datepicker.css'), array('halio-bootstrap'), HALIO_VERSION);
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
    wp_enqueue_style('fullcalendar', halio_plugin_url('includes/css/fullcalendar.min.css'), array('halio-bootstrap'), HALIO_VERSION);
    wp_enqueue_style('qTip', halio_plugin_url('includes/css/qTip.min.css'), array(), HALIO_VERSION);
    wp_enqueue_style('halio', halio_plugin_url('includes/css/style.css'), array('halio-bootstrap', 'bootstrap-datepicker'), HALIO_VERSION);
  }

  public static function add_admin_sidebar() {
    // Halio main menu
    add_menu_page(
      'Halio',
      'Halio',
      'install_plugins',
      'halio-menu',
      array(new HalioPageRender(), 'render_home_page'),
      'http://i.imgur.com/BSKwym0.png'
    );

    // Settings sub-menu
    add_submenu_page(
      'halio-menu',
      __('Settings', 'halio'),
      __('Settings', 'halio'),
      'install_plugins',
      'halio-menu',
      array(new HalioPageRender(), 'render_home_page')
    );

    // Orders sub-menu
    // add_submenu_page(
    //   'halio-menu',
    //   'Orders',
    //   'Orders',
    //   'install_plugins',
    //   'halio-orders',
    //   array('HalioPageRender', 'render_orders_page')
    // );


    // Vehicles sub-menu
    add_submenu_page(
      'halio-menu',
      __('Vehicles', 'halio'),
      __('Vehicles', 'halio'),
      'install_plugins',
      'halio-vehicles',
      array(new HalioPageRender(), 'render_vehicles_page')
    );

    // Pricing conditions sub-menu
    add_submenu_page(
      'halio-menu',
      __('Pricing Conditions', 'halio'),
      __('Pricing Conditions', 'halio'),
      'install_plugins',
      'halio-pricing-conditions',
      array(new HalioPageRender(), 'render_pricing_conditions_page')
    );

    // Pricing conditions sub-menu
    add_submenu_page(
      'halio-menu',
      __('Form Design', 'halio'),
      __('Form Design', 'halio'),
      'install_plugins',
      'halio-form-design',
      array(new HalioPageRender(), 'render_form_design_page')
    );

    // Fixed addresses sub-menu
    add_submenu_page(
      'halio-menu',
      __('Fixed Addresses', 'halio'),
      __('Fixed Addresses', 'halio'),
      'install_plugins',
      'halio-fixed-addresses',
      array(new HalioPageRender(), 'render_fixed_addresses_page')
    );

    add_submenu_page(
      'halio-menu',
      __('Calendar', 'halio'),
      __('Calendar', 'halio'),
      'install_plugins',
      'halio-calendar',
      array(new HalioPageRender(), 'render_calendar_page')
    );
  }

  public static function load_translations() {
    load_plugin_textdomain('halio', false, dirname(dirname(plugin_basename(__FILE__))) . '/languages');
  }
}
