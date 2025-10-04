<?php

class HalioInstall {

  private $database_version = '1.0';

  public function install() {

    global $wpdb;
    global $charset_collate;

    $vehicle_table_name                    = $wpdb->prefix . 'halio_vehicles';
    $settings_table_name                   = $wpdb->prefix . 'halio_settings';
    $polygon_pricing_conditions_table_name = $wpdb->prefix . 'halio_polygon_pricing_conditions';
    $time_pricing_conditions_table_name    = $wpdb->prefix . 'halio_time_pricing_conditions';
    $polygon_coordinates_table_name        = $wpdb->prefix . 'halio_polygon_coordinates';
    $fixed_address_table_name              = $wpdb->prefix . 'halio_fixed_addresses';
    $custom_checkout_fields_table_name     = $wpdb->prefix . 'halio_custom_checkout_fields';
    $vat_table_name                        = $wpdb->prefix . 'halio_vehicle_availability_time';
    $charset_collate                       = $wpdb->get_charset_collate();

    // Create Vehicles Table
    $sql_create_vehicles_table = "CREATE TABLE IF NOT EXISTS `$vehicle_table_name` (
      `id` int(11) unsigned NOT NULL auto_increment,
      `name` varchar(255) NOT NULL,
      `is_active` boolean NOT NULL DEFAULT true,
      `passenger_space` int(11) default 1,
      `suitcase_space` int(11) default 0,
      `number_owned` int(11) default 1,
      `starting_fare` numeric(15, 2) NOT NULL default 0.00,
      `price_per_unit_distance` numeric(15, 2) NOT NULL default 0.00,
      `price_per_minute` numeric(15, 2) NOT NULL default 0.00,
      `price_per_occupant` numeric(15, 2) NOT NULL default 0.00,
      `price_per_toll_road` numeric(15, 2) NOT NULL default 0.00,
      `active_monday` boolean NOT NULL DEFAULT true,
      `active_tuesday` boolean NOT NULL DEFAULT true,
      `active_wednesday` boolean NOT NULL DEFAULT true,
      `active_thursday` boolean NOT NULL DEFAULT true,
      `active_friday` boolean NOT NULL DEFAULT true,
      `active_saturday` boolean NOT NULL DEFAULT true,
      `active_sunday` boolean NOT NULL DEFAULT true,
      PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql_create_vehicles_table);

    // Create Settings Table
    $sql_create_settings_table = "CREATE TABLE IF NOT EXISTS `$settings_table_name` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `key` varchar(150) NOT NULL,
      `value` text,
      PRIMARY KEY (`id`),
      UNIQUE (`key`)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql_create_settings_table);

    $this->add_settings_seed_data();

    // Create Polygon Pricing Conditions Table
    $sql_create_polygon_pricing_conditions_table = "CREATE TABLE IF NOT EXISTS `$polygon_pricing_conditions_table_name` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `is_active` boolean NOT NULL DEFAULT true,
      `vehicle_id` int(11) NOT NULL,
      `increase_amount` numeric(15, 2) NOT NULL default 0.00,
      `increase_multiplier` float(10) NOT NULL default 1,
      `fixed_amount` numeric(15, 2) NOT NULL default 0.00,
      `pick_up_polygon_nos` int(11) NOT NULL default 1,
      `drop_off_polygon_nos` int(11) NOT NULL default 1,
      `increase_or_fixed` varchar(255) NOT NULL,
      `one_way_or_both` varchar(255) NOT NULL,
      `pick_up_location` varchar(255) NOT NULL,
      `drop_off_location` varchar(255) NOT NULL,
      PRIMARY KEY (`id`)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql_create_polygon_pricing_conditions_table);

    // Create Polygon Coordinates Table
    $sql_create_polygon_coordinates_table = "CREATE TABLE IF NOT EXISTS `$polygon_coordinates_table_name` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `polygon_pricing_condition_id` int(11) NOT NULL,
      `longitude` float(20) NOT NULL,
      `latitude` float(20) NOT NULL,
      `type` varchar(255) NOT NULL,
      PRIMARY KEY (`id`)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql_create_polygon_coordinates_table);

    // Create Fixed Address Table
    $sql_create_fixed_address_table = "CREATE TABLE IF NOT EXISTS `$fixed_address_table_name` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `address` varchar(255) NOT NULL,
      `pretty_address` varchar(255) NOT NULL,
      `is_active` boolean NOT NULL default true,
      `origin_or_destination` varchar(50) NOT NULL,
      PRIMARY KEY (`id`)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql_create_fixed_address_table);

    // Create Time Pricing Condition Table
    $sql_create_time_pricing_condition = "CREATE TABLE IF NOT EXISTS `$time_pricing_conditions_table_name` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `starting_time` time NOT NULL,
      `ending_time` time NOT NULL,
      `vehicle_id` int(11) NOT NULL,
      `increase_amount` numeric(15, 2) NOT NULL default 0.00,
      `increase_multiplier` float(10) NOT NULL default 1,
      `is_active` boolean NOT NULL default true,
      `monday` boolean NOT NULL default false,
      `tuesday` boolean NOT NULL default false,
      `wednesday` boolean NOT NULL default false,
      `thursday` boolean NOT NULL default false,
      `friday` boolean NOT NULL default false,
      `saturday` boolean NOT NULL default false,
      `sunday` boolean NOT NULL default false,
      PRIMARY KEY (`id`)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql_create_time_pricing_condition);

    // Create Custom Checkout Fields Table
    $sql_create_custom_co_fields = "CREATE TABLE IF NOT EXISTS `$custom_checkout_fields_table_name` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `label` varchar(255) NOT NULL,
      `key` varchar(255) NOT NULL,
      `placeholder` varchar(255) NOT NULL,
      `is_required` boolean NOT NULL default false,
      `is_active` boolean NOT NULL default true,
      PRIMARY KEY (`id`)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql_create_custom_co_fields);

    // Create Vehicle Availability Time Table
    $vat_sql = "CREATE TABLE IF NOT EXISTS `$vat_table_name` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `vehicle_id` int(11) NOT NULL,
      `starting_time` time NOT NULL,
      `ending_time` time NOT NULL,
      `day` int(3) NOT NULL,
      PRIMARY KEY (`id`)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($vat_sql);

    $this->check_db_version();

    add_option('halio_db_version', $this->database_version);
  }

  public function add_settings_seed_data() {
    global $wpdb;

    $settings_table = $wpdb->prefix . 'halio_settings';
    $settings_query = "INSERT INTO $settings_table (`key`, `value`) VALUES ";

    // Seed Data
    $settings = array(
      'currency' => '£',
      'units' => 'miles',
      'api_key' => '',
      'return_fare_multiplier' => 2,
      'map_starting_country' => 'United Kingdom',
      'use_fixed_addresses_for_origin' => '0',
      'use_fixed_addresses_for_destination' => '0',
      'maximum_fare' => '',
      'enforce_maximum_fare' => '0',
      'minimum_fare' => '',
      'enforce_minimum_fare' => '0',
      'enforce_minimum_distance' => '0',
      'minimum_distance' => '10',
      'minimum_distance_error_message' => 'Sorry, this job is too short, we do not accept jobs of this distance.',
      'enforce_maximum_distance' => '0',
      'maximum_distance' => '100',
      'maximum_distance_error_message' => 'Sorry, this job is too long, we do not accept jobs of this distance.',
      'wc_product_id' => halio_create_wc_product(),
      'enforce_acceptance_region' => '0',
      'acceptance_region_coordinates' => '',
      'enforce_autocomplete_country_restriction' => '0',
      'autocomplete_country' => '',

      'form_can_edit_occupants' => '1',
      'form_can_edit_vehicle_type' => '1',
      'form_can_edit_direction' => '1',
      'form_default_vehicle_id' => '',
      'form_estimate_cost_label' => 'Estimate Cost',
      'form_estimating_cost_label' => 'Estimating...',
      'form_starting_address_label' => 'Starting Address',
      'form_destination_address_label' => 'Destination Address',
      'form_vehicle_type_label' => 'Vehicle Type',
      'form_number_of_occupants_label' => 'No. of Occupants',
      'form_direction_label' => 'Direction',
      'form_pick_up_time_label' => 'Pick up Time',
      'form_return_pick_up_time_label' => 'Return Pick up Time',
      'form_show_duration_in_estimate' => '1',
      'form_show_distance_in_estimate' => '1',
      'form_show_title_or_image' => 'image',
      'form_title' => 'Company Name',
      'form_title_image' => 'http://i.imgur.com/fPgk1Lw.png',
      'form_starting_address_select_text' => 'Please select a starting address...',
      'form_destination_address_select_text' => 'Please select a destination address...',
      'form_book_button_text' => 'Book!',
      'form_booking_buffer_time_minutes' => '0',
      'form_vehicle_unavailable_message' => 'This vehicle is not available for the given time. Please change the dates or select another vehicle.',
      'form_vehicle_fully_booked_message' => 'Sorry, this vehicle is fully booked at this time, please select another time or choose another vehicle.',
      'form_not_in_acceptance_region_message' => 'Sorry, we do not accept fares from that location, please chose another pick-up location.',

      'checkout_starting_address_label' => 'Starting Address',
      'checkout_destination_address_label' => 'Destination Address',
      'checkout_vehicle_label' => 'Vehicle',
      'checkout_occupants_label' => 'Occupants',
      'checkout_pick_up_time_label' => 'Pick Up Time',
      'checkout_return_pick_up_time_label' => 'Return Pick Up Time',
      'checkout_direction_label' => 'Direction',
      'checkout_duration_label' => 'Estimated Duration',
      'checkout_distance_label' => 'Distance',
      'checkout_one_way_label' => 'One Way',
      'checkout_return_label' => 'Return'
    );

    foreach ($settings as $key => $value) {
      $new_settings_query = $settings_query .  " ('$key', '$value');";
      $wpdb->query($new_settings_query);
    }
  }

  private function check_db_version() {
    if ( get_option('halio_db_version') !== $this->database_version ) {
      // update database
    }
  }
}
