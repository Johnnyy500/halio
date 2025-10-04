<?php
if (!defined('ABSPATH')) { exit; }


class HalioPageRender {

  public static function render_home_page() {
    if ( !empty($_POST) ) {
      global $wpdb;
      $settings_table  = $wpdb->prefix . 'halio_settings';
      $buffer_time_id = halio_get_settings_row('form_booking_buffer_time_minutes')->id;

      foreach ($_POST['setting'] as $setting_id => $properties) {
        if ( $setting_id == $buffer_time_id ) {
          $properties['value'] = (int) ($properties['value'] * $properties['multiplier']);
        }

        $wpdb->update(
          $settings_table,
          array( 'value' => esc_sql($properties['value']) ),
          array( 'id' => $setting_id )
        );
      }

      $flash = array(
        'message' => __('Settings Updated!', 'halio'),
        'type' => 'success'
      );
    }

    require_once HALIO_PLUGIN_DIR . "/pages/home.php";
  }

  public static function render_vehicles_page() {
    if ( !empty($_POST) ) {
      global $wpdb;
      $vehicles_table = $wpdb->prefix . 'halio_vehicles';
      $vat_table = $wpdb->prefix . 'halio_vehicle_availability_time';

      if ( isset($_POST['edit_vehicle']) ) {
        // User has submitted edit form

        $update_query = $wpdb->update(
          $vehicles_table,
          array(
            'name' => esc_sql($_POST['edit_vehicle']['name']),
            'passenger_space' => esc_sql($_POST['edit_vehicle']['passenger_space']),
            'suitcase_space'  => esc_sql($_POST['edit_vehicle']['suitcase_space']),
            'number_owned' => esc_sql($_POST['edit_vehicle']['number_owned']),
            'starting_fare' => esc_sql($_POST['edit_vehicle']['starting_fare']),
            'price_per_unit_distance' => esc_sql($_POST['edit_vehicle']['price_per_unit_distance']),
            'price_per_minute' => esc_sql($_POST['edit_vehicle']['price_per_minute']),
            'price_per_occupant' => esc_sql($_POST['edit_vehicle']['price_per_occupant'])
          ),
          array( 'id' => esc_sql($_POST['edit_vehicle']['id']) )
        );

        if ($update_query) {
          $flash = array(
            'message' => __('Vehicle updated!', 'halio'),
            'type' => 'success'
          );
        } else {
          $flash = array(
            'message' => __('Error updating vehicle', 'halio'),
            'type' => 'warning'
          );
        }
      } elseif ( isset($_POST['delete_vehicle']) ) {
        $deleted = $wpdb->delete($vehicles_table, array( 'id' => esc_sql($_POST['delete_vehicle']['id']) ));

        if ( $deleted == false ) {
          $flash = array(
            'message' => __('Error deleting vehicle', 'halio'),
            'type' => 'danger'
          );
        } else {
          $flash = array(
            'message' => __('Vehicle deleted', 'halio'),
            'type' => 'success'
          );
        }
      } elseif ( isset($_POST['new_vehicle']) ) {
        // User submitted create form

        $insert_query = $wpdb->insert(
          $vehicles_table,
          array(
            'name' => esc_sql($_POST['new_vehicle']['name']),
            'passenger_space' => esc_sql($_POST['new_vehicle']['passenger_space']),
            'suitcase_space' => esc_sql($_POST['new_vehicle']['suitcase_space']),
            'number_owned' => esc_sql($_POST['new_vehicle']['number_owned']),
            'starting_fare' => esc_sql($_POST['new_vehicle']['starting_fare']),
            'price_per_unit_distance' => esc_sql($_POST['new_vehicle']['price_per_unit_distance']),
            'price_per_minute' => esc_sql($_POST['new_vehicle']['price_per_minute']),
            'price_per_occupant' => esc_sql($_POST['new_vehicle']['price_per_occupant'])
          )
        );

        if ($insert_query) {
          $flash = array(
            'message' => __('Vehicle Created', 'halio'),
            'type' => 'success'
          );
        } else {
          $flash = array(
            'message' => sprintf(
              __('Error creating vehicle %s', 'halio'),
              $wpdb->print_error()
            ),
            'type' => 'warning'
          );
        }
      } elseif ( isset($_POST['change_vehicle']) ) {
        // Activating/Deactivating Fixed Address
        $wpdb->update(
          $vehicles_table,
          array(
            'is_active' => $_POST['change_vehicle']['action'] === 'activate'
          ),
          array( 'id' => esc_sql($_POST['change_vehicle']['id']) )
        );
      } elseif ( isset($_POST['new_vehicle_availability_time']) ) {
        if (
          empty($_POST['new_vehicle_availability_time']['starting_time']) ||
          empty($_POST['new_vehicle_availability_time']['ending_time'])
        ) {
          $flash = array(
            'message' => __('You need to specify a start and end time', 'halio'),
            'type' => 'warning'
          );
        } else {

          $insert_query = $wpdb->insert(
            $vat_table,
            array(
              'vehicle_id' => esc_sql($_GET['vehicle_id']),
              'starting_time' => esc_sql($_POST['new_vehicle_availability_time']['starting_time']),
              'ending_time' => esc_sql($_POST['new_vehicle_availability_time']['ending_time']),
              'day' => esc_sql($_POST['new_vehicle_availability_time']['day'])
            )
          );

          if ($insert_query) {
            $flash = array(
              'message' => __('Vehicle Availability Time Created', 'halio'),
              'type' => 'success'
            );
          } else {
            $flash = array(
              'message' => sprintf(
                __('Error creating Vehicle Availability Time %s', 'halio'),
                $wpdb->print_error()
              ),
              'type' => 'warning'
            );
          }
        }
      } elseif ( isset($_POST['delete_vat']) ) {
        $wpdb->delete($vat_table, array( 'id' => esc_sql($_POST['delete_vat']['id']) ));

        $flash = array(
          'message' => __('Vehicle Availability Time deleted', 'halio'),
          'type' => 'success'
        );
      } elseif ( isset($_POST['change_vat']) ) {
        $days = array(
          'sunday' => __('Sunday', 'halio'),
          'monday' => __('Monday', 'halio'),
          'tuesday' => __('Tuesday', 'halio'),
          'wednesday' => __('Wednesday', 'halio'),
          'thursday' => __('Thursday', 'halio'),
          'friday' => __('Friday', 'halio'),
          'saturday' => __('Saturday', 'halio')
        );

        $day_keys = array_keys($days);

        $wpdb->update(
          $vehicles_table,
          array(
            'active_' . $day_keys[$_POST['change_vat']['day']] => $_POST['change_vat']['action'] === 'activate'
          ),
          array( 'id' => esc_sql($_POST['change_vat']['id']) )
        );

        $flash = array(
          'message' => sprintf(
            __('Vehicle status changed for %s', 'halio'),
            $days[$day_keys[$_POST['change_vat']['day']]]
          ),
          'type' => 'success'
        );
      }
    }

    if ( isset($_GET['vehicle_id']) ) {
      $file = 'edit';
    } else {
      $file = 'index';
    }

    require_once HALIO_PLUGIN_DIR . "/pages/vehicles/" . $file . ".php";
  }

  public static function render_pricing_conditions_page() {
    if ( !empty($_POST) ) {
      global $wpdb;
      $ppc_table  = $wpdb->prefix . 'halio_polygon_pricing_conditions';
      $pc_table = $wpdb->prefix . 'halio_polygon_coordinates';
      $tpc_table = $wpdb->prefix . 'halio_time_pricing_conditions';

      if ( isset($_POST['poly_pricing_condition']) ) {
        // Creating Polygon Pricing Condition
        $pick_up_coordinates = array();
        $drop_off_coordinates = array();

        if (empty($_POST['poly_pricing_condition']['pick_up_location'])) {
          $flash = array(
            'message' => __("You need to select either 'Specific Area' or 'Anywhere' for the pick up location.", 'halio'),
            'type' => 'warning'
          );
        } elseif (empty($_POST['poly_pricing_condition']['drop_off_location'])) {
          $flash = array(
            'message' => __("You need to select either 'Specific Area' or 'Anywhere' for the drop off location.", 'halio'),
            'type' => 'warning'
          );
        } elseif ( $_POST['poly_pricing_condition']['pick_up_location'] == 'specific_area' && empty($_POST['poly_pricing_condition']['pick_up_coordinates']) ) {
          $flash = array(
            'message' => __('You need to draw the geographical area the rule applies to for pick ups', 'halio'),
            'type' => 'warning'
          );
        } elseif ( $_POST['poly_pricing_condition']['drop_off_location'] == 'specific_area' && empty($_POST['poly_pricing_condition']['drop_off_coordinates']) ) {
          $flash = array(
            'message' => __('You need to draw the geographical area the rule applies to for drop offs', 'halio'),
            'type' => 'warning'
          );
        } elseif ( empty($_POST['poly_pricing_condition']['name']) ) {
          $flash = array(
            'message' => __('You need to provide a name for this rule', 'halio'),
            'type' => 'warning'
          );
        } else {

          $increase_multiplier = isset($_POST['poly_pricing_condition']['increase_multiplier']) ? $_POST['poly_pricing_condition']['increase_multiplier'] : 1;
          $increase_amount = isset($_POST['poly_pricing_condition']['increase_amount']) ? $_POST['poly_pricing_condition']['increase_amount'] : 0;
          $fixed_amount = isset($_POST['poly_pricing_condition']['fixed_amount']) ? $_POST['poly_pricing_condition']['fixed_amount'] : 0;

          $sub_pu_coords = explode('|', $_POST['poly_pricing_condition']['pick_up_coordinates']);
          $sub_do_coords = explode('|', $_POST['poly_pricing_condition']['drop_off_coordinates']);

          foreach ($sub_pu_coords as $pu_coord) {
            $parts = explode(',', $pu_coord);
            $coords = array(
              'lat' => $parts[0],
              'long' => $parts[1]
            );

            array_push($pick_up_coordinates, $coords);
          }

          foreach ($sub_do_coords as $do_coord) {
            $parts = explode(',', $do_coord);
            $coords = array(
              'lat' => $parts[0],
              'long' => $parts[1]
            );

            array_push($drop_off_coordinates, $coords);
          }

          $insert_query = $wpdb->insert(
            $ppc_table,
            array(
              'name' => esc_sql($_POST['poly_pricing_condition']['name']),
              'vehicle_id' => esc_sql($_POST['poly_pricing_condition']['vehicle_id']),
              'increase_amount' => esc_sql($increase_amount),
              'increase_multiplier' => esc_sql($increase_multiplier),
              'fixed_amount' => esc_sql($fixed_amount),
              'pick_up_polygon_nos' => halio_safe_count($pick_up_coordinates),
              'drop_off_polygon_nos' => halio_safe_count($drop_off_coordinates),
              'pick_up_location' => esc_sql($_POST['poly_pricing_condition']['pick_up_location']),
              'drop_off_location' => esc_sql($_POST['poly_pricing_condition']['drop_off_location']),
              'increase_or_fixed' => esc_sql($_POST['poly_pricing_condition']['increase_or_fixed']),
              'one_way_or_both' => esc_sql($_POST['poly_pricing_condition']['one_way_or_both'])
            )
          );

          if ($insert_query) {
            $pricing_condition_id = $wpdb->insert_id;

            foreach ($pick_up_coordinates as $pu_coordinate) {
              $wpdb->insert(
                $pc_table,
                array(
                  'polygon_pricing_condition_id' => $pricing_condition_id,
                  'latitude'  => $pu_coordinate['lat'],
                  'longitude' => $pu_coordinate['long'],
                  'type' => 'pick_up'
                )
              );
            }

            foreach ($drop_off_coordinates as $do_coordinate) {
              $wpdb->insert(
                $pc_table,
                array(
                  'polygon_pricing_condition_id' => $pricing_condition_id,
                  'latitude'  => $do_coordinate['lat'],
                  'longitude' => $do_coordinate['long'],
                  'type' => 'drop_off'
                )
              );
            }

            $flash = array(
              'message' => __('Pricing condition created', 'halio'),
              'type' => 'success'
            );
          } else {
            $flash = array(
              'message' => sprintf(
                __('Error creating pricing condition: %s', 'halio'),
                $wpdb->print_error()
              ),
              'type' => 'warning'
            );
          }
        }

      } elseif ( isset($_POST['edit_poly_pricing_condition']) ) {
        // Editing Polygon Pricing Condition
        $pick_up_coordinates = array();
        $drop_off_coordinates = array();

        if ( $_POST['edit_poly_pricing_condition']['pick_up_location'] == 'specific_area' && empty($_POST['edit_poly_pricing_condition']['pick_up_coordinates']) ) {
          $flash = array(
            'message' => __('You need to draw the geographical area the rule applies to for pick ups', 'halio'),
            'type' => 'warning'
          );
        } elseif ( $_POST['edit_poly_pricing_condition']['drop_off_location'] == 'specific_area' && empty($_POST['edit_poly_pricing_condition']['drop_off_coordinates']) ) {
          $flash = array(
            'message' => __('You need to draw the geographical area the rule applies to for drop offs', 'halio'),
            'type' => 'warning'
          );
        } else {
          // No errors

          $increase_multiplier = isset($_POST['edit_poly_pricing_condition']['increase_multiplier']) ? $_POST['edit_poly_pricing_condition']['increase_multiplier'] : 1;
          $increase_amount = isset($_POST['edit_poly_pricing_condition']['increase_amount']) ? $_POST['edit_poly_pricing_condition']['increase_amount'] : 0;
          $fixed_amount = isset($_POST['edit_poly_pricing_condition']['fixed_amount']) ? $_POST['edit_poly_pricing_condition']['fixed_amount'] : 0;

          $sub_pu_coords = explode('|', $_POST['edit_poly_pricing_condition']['pick_up_coordinates']);
          $sub_do_coords = explode('|', $_POST['edit_poly_pricing_condition']['drop_off_coordinates']);

          foreach ($sub_pu_coords as $pu_coord) {
            $parts = explode(',', $pu_coord);
            $coords = array(
              'lat' => $parts[0],
              'long' => $parts[1]
            );

            array_push($pick_up_coordinates, $coords);
          }

          foreach ($sub_do_coords as $do_coord) {
            $parts = explode(',', $do_coord);
            $coords = array(
              'lat' => $parts[0],
              'long' => $parts[1]
            );

            array_push($drop_off_coordinates, $coords);
          }

          $update_query = $wpdb->update(
            $ppc_table,
            array(
              'name' => esc_sql($_POST['edit_poly_pricing_condition']['name']),
              'is_active' => esc_sql($_POST['edit_poly_pricing_condition']['is_active']),
              'vehicle_id' => esc_sql($_POST['edit_poly_pricing_condition']['vehicle_id']),
              'increase_amount' => esc_sql($increase_amount),
              'increase_multiplier' => esc_sql($increase_multiplier),
              'fixed_amount' => esc_sql($fixed_amount),
              'pick_up_polygon_nos' => halio_safe_count($pick_up_coordinates) - 1,
              'drop_off_polygon_nos' => halio_safe_count($drop_off_coordinates) - 1,
              'pick_up_location' => esc_sql($_POST['edit_poly_pricing_condition']['pick_up_location']),
              'drop_off_location' => esc_sql($_POST['edit_poly_pricing_condition']['drop_off_location']),
              'increase_or_fixed' => esc_sql($_POST['edit_poly_pricing_condition']['increase_or_fixed']),
              'one_way_or_both' => esc_sql($_POST['edit_poly_pricing_condition']['one_way_or_both'])
            ),
            array( 'id' => esc_sql($_POST['edit_poly_pricing_condition']['id']) )
          );

          $wpdb->delete(
            $pc_table,
            array( 'polygon_pricing_condition_id' => esc_sql($_POST['edit_poly_pricing_condition']['id']) )
          );

          foreach ($pick_up_coordinates as $coordinate) {
            $wpdb->insert(
              $pc_table,
              array(
                'polygon_pricing_condition_id' => esc_sql($_POST['edit_poly_pricing_condition']['id']),
                'latitude'  => $coordinate['lat'],
                'longitude' => $coordinate['long'],
                'type' => 'pick_up'
              )
            );
          }

          foreach ($drop_off_coordinates as $coordinate) {
            $wpdb->insert(
              $pc_table,
              array(
                'polygon_pricing_condition_id' => esc_sql($_POST['edit_poly_pricing_condition']['id']),
                'latitude'  => $coordinate['lat'],
                'longitude' => $coordinate['long'],
                'type' => 'drop_off'
              )
            );
          }

          $flash = array(
            'message' => __('Pricing condition created', 'halio'),
            'type' => 'success'
          );
        }
      } elseif ( isset($_POST['delete_poly_pricing_condition']) ) {
        // Deleting Polygon Pricing Condition
        $wpdb->delete(
          $ppc_table,
          array( 'id' => esc_sql($_POST['delete_poly_pricing_condition']['id']) )
        );

        $wpdb->delete(
          $pc_table,
          array( 'polygon_pricing_condition_id' => esc_sql($_POST['delete_poly_pricing_condition']['id']) )
        );

        $flash = array(
          'message' => __('Pricing condition deleted', 'halio'),
          'type' => 'success'
        );
      } elseif ( isset($_POST['change_poly_pricing_condition']) ) {
        // Activating/Deactivating Fixed Address
        $wpdb->update(
          $ppc_table,
          array(
            'is_active' => $_POST['change_poly_pricing_condition']['action'] === 'activate'
          ),
          array( 'id' => esc_sql($_POST['change_poly_pricing_condition']['id']) )
        );
      } elseif ( isset($_POST['time_pricing_condition']) ) {

        if (
          strtotime($_POST['time_pricing_condition']['starting_time']) > strtotime($_POST['time_pricing_condition']['ending_time'])
        ) {
          $flash = array(
            'message' => __('Times must be on the same day and the starting time must be before the ending time.', 'halio'),
            'type' => 'warning'
          );
        } else {

          $insert_query = $wpdb->insert(
            $tpc_table,
            array(
              'name' => esc_sql($_POST['time_pricing_condition']['name']),
              'starting_time' => esc_sql($_POST['time_pricing_condition']['starting_time']),
              'ending_time' => esc_sql($_POST['time_pricing_condition']['ending_time']),
              'vehicle_id' => esc_sql($_POST['time_pricing_condition']['vehicle_id']),
              'increase_amount' => esc_sql($_POST['time_pricing_condition']['increase_amount']),
              'increase_multiplier' => esc_sql($_POST['time_pricing_condition']['increase_multiplier']),
              'monday' => esc_sql($_POST['time_pricing_condition']['monday']),
              'tuesday' => esc_sql($_POST['time_pricing_condition']['tuesday']),
              'wednesday' => esc_sql($_POST['time_pricing_condition']['wednesday']),
              'thursday' => esc_sql($_POST['time_pricing_condition']['thursday']),
              'friday' => esc_sql($_POST['time_pricing_condition']['friday']),
              'saturday' => esc_sql($_POST['time_pricing_condition']['saturday']),
              'sunday' => esc_sql($_POST['time_pricing_condition']['sunday'])
            )
          );

          if ($insert_query) {
            $flash = array(
              'message' => __('Time Pricing Condition created!', 'halio'),
              'type' => 'success'
            );
          } else {
            $flash = array(
              'message' => __('Error creating Time Pricing Condition', 'halio'),
              'type' => 'warning'
            );
          }
        }
      } elseif ( isset($_POST['edit_time_pricing_condition']) ) {

        if (
          strtotime($_POST['edit_time_pricing_condition']['starting_time']) > strtotime($_POST['edit_time_pricing_condition']['ending_time'])
        ) {
          $flash = array(
            'message' => __('Times must be on the same day and the starting time must be before the ending time.', 'halio'),
            'type' => 'warning'
          );
        } else {

          $update_query = $wpdb->update(
            $tpc_table,
            array(
              'name' => esc_sql($_POST['edit_time_pricing_condition']['name']),
              'starting_time' => esc_sql($_POST['edit_time_pricing_condition']['starting_time']),
              'ending_time' => esc_sql($_POST['edit_time_pricing_condition']['ending_time']),
              'vehicle_id' => esc_sql($_POST['edit_time_pricing_condition']['vehicle_id']),
              'increase_amount' => esc_sql($_POST['edit_time_pricing_condition']['increase_amount']),
              'increase_multiplier' => esc_sql($_POST['edit_time_pricing_condition']['increase_multiplier']),
              'monday' => esc_sql($_POST['edit_time_pricing_condition']['monday']),
              'tuesday' => esc_sql($_POST['edit_time_pricing_condition']['tuesday']),
              'wednesday' => esc_sql($_POST['edit_time_pricing_condition']['wednesday']),
              'thursday' => esc_sql($_POST['edit_time_pricing_condition']['thursday']),
              'friday' => esc_sql($_POST['edit_time_pricing_condition']['friday']),
              'saturday' => esc_sql($_POST['edit_time_pricing_condition']['saturday']),
              'sunday' => esc_sql($_POST['edit_time_pricing_condition']['sunday']),
              'is_active' => esc_sql($_POST['edit_time_pricing_condition']['is_active'])
            ),
            array( 'id' => esc_sql($_POST['edit_time_pricing_condition']['id']) )
          );

          if ($update_query) {
            $flash = array(
              'message' => __('Time Pricing Condition updated!', 'halio'),
              'type' => 'success'
            );
          } else {
            $flash = array(
              'message' => __('Error updating Time Pricing Condition', 'halio'),
              'type' => 'warning'
            );
          }
        }
      } elseif ( isset($_POST['change_time_pricing_condition']) ) {
        // Activating/Deactivating Fixed Address
        $wpdb->update(
          $tpc_table,
          array(
            'is_active' => $_POST['change_time_pricing_condition']['action'] === 'activate'
          ),
          array( 'id' => esc_sql($_POST['change_time_pricing_condition']['id']) )
        );
      } elseif ( isset($_POST['delete_time_pricing_condition']) ) {
        // Deleting Polygon Pricing Condition
        $wpdb->delete(
          $tpc_table,
          array( 'id' => esc_sql($_POST['delete_time_pricing_condition']['id']) )
        );

        $flash = array(
          'message' => __('Pricing condition deleted', 'halio'),
          'type' => 'success'
        );
      } elseif ( isset($_POST['copy_polygon_pricing_condition']) ) {
        $original_ppc = $wpdb->get_row("SELECT * FROM `" . $wpdb->prefix . "halio_polygon_pricing_conditions` WHERE `id` = '" . $_POST['copy_polygon_pricing_condition']['id'] . "';");

        $copy_query = $wpdb->insert(
          $ppc_table,
          array(
            'name' => $original_ppc->name . " (copy)",
            'vehicle_id' => $original_ppc->vehicle_id,
            'increase_amount' => $original_ppc->increase_amount,
            'increase_multiplier' => $original_ppc->increase_multiplier,
            'fixed_amount' => $original_ppc->fixed_amount,
            'pick_up_polygon_nos' => $original_ppc->pick_up_polygon_nos,
            'drop_off_polygon_nos' => $original_ppc->drop_off_polygon_nos,
            'pick_up_location' => $original_ppc->pick_up_location,
            'drop_off_location' => $original_ppc->drop_off_location,
            'increase_or_fixed' => $original_ppc->increase_or_fixed,
            'one_way_or_both' => $original_ppc->one_way_or_both
          )
        );

        if ( $copy_query ) {

          $new_ppc_id = $wpdb->insert_id;

          $pick_up_coordinates = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "halio_polygon_coordinates` WHERE `polygon_pricing_condition_id` = '" . $original_ppc->id . "' AND `type` = 'pick_up' ORDER BY `id` ASC;");

          $drop_off_coordinates = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "halio_polygon_coordinates` WHERE `polygon_pricing_condition_id` = '" . $original_ppc->id . "' AND `type` = 'drop_off' ORDER BY `id` ASC;");

          foreach ($pick_up_coordinates as $pu_coordinate) {
            $wpdb->insert(
              $pc_table,
              array(
                'polygon_pricing_condition_id' => $new_ppc_id,
                'latitude'  => $pu_coordinate->latitude,
                'longitude' => $pu_coordinate->longitude,
                'type' => 'pick_up'
              )
            );
          }

          foreach ($drop_off_coordinates as $do_coordinate) {
            $wpdb->insert(
              $pc_table,
              array(
                'polygon_pricing_condition_id' => $new_ppc_id,
                'latitude'  => $do_coordinate->latitude,
                'longitude' => $do_coordinate->longitude,
                'type' => 'drop_off'
              )
            );
          }

          $flash = array(
            'message' => __('Pricing condition copied', 'halio'),
            'type' => 'success'
          );
        }
      }
    }

    if ( isset($_GET['polygon_pricing_condition_id']) ) {
      $file = "/pages/pricing-conditions/polygon/edit.php";
    } elseif ( isset($_GET['time_pricing_condition_id']) ) {
      $file = "/pages/pricing-conditions/time/edit.php";
    } else {
      $file = "/pages/pricing-conditions/index.php";
    }

    require_once HALIO_PLUGIN_DIR . $file;
  }

  public static function render_form_design_page() {

    if ( !empty($_POST) ) {
      global $wpdb;
      $checkout_table  = $wpdb->prefix . 'halio_custom_checkout_fields';


      if ( isset($_POST['setting']) ) {
        // Updating Setting
        $settings_table  = $wpdb->prefix . 'halio_settings';

        foreach ($_POST['setting'] as $setting_id => $properties) {
          $wpdb->update(
            $settings_table,
            array(
              'value' => esc_sql($properties['value'])
            ),
            array( 'id' => esc_sql($setting_id) )
          );
        }

        $flash = array(
          'message' => __('Settings Updated!', 'halio'),
          'type' => 'success'
        );
      } elseif ( isset($_POST['new_checkout_field']) ) {
        $key = uniqid('halio_');
        $result = $wpdb->get_results("SELECT id FROM `" . $checkout_table . "` WHERE `key` = '" . $key . "';");

        while (halio_safe_count($result) > 0) {
          $key = uniqid('halio_');
          $result = $wpdb->get_results("SELECT id FROM `" . $checkout_table . "` WHERE `key` = '" . $key . "';");
        }

        $insert_query = $wpdb->insert(
          $checkout_table,
          array(
            'label' => esc_sql($_POST['new_checkout_field']['label']),
            'key' => $key,
            'placeholder' => esc_sql($_POST['new_checkout_field']['placeholder']),
            'is_required' => esc_sql($_POST['new_checkout_field']['is_required']),
            'is_active' => esc_sql($_POST['new_checkout_field']['is_active'])
          )
        );

        if ($insert_query) {
          $flash = array(
            'message' => __('New Custom Checkout Field created!', 'halio'),
            'type' => 'success'
          );
        } else {
          $flash = array(
            'message' => __('Error creating Custom Checkout Field', 'halio'),
            'type' => 'warning'
          );
        }
      } elseif ( isset($_POST['edit_checkout_field']) ) {
        $update_query = $wpdb->update(
          $checkout_table,
          array(
            'label' => esc_sql($_POST['edit_checkout_field']['label']),
            'placeholder' => esc_sql($_POST['edit_checkout_field']['placeholder']),
            'is_required' => esc_sql($_POST['edit_checkout_field']['is_required']),
            'is_active' => esc_sql($_POST['edit_checkout_field']['is_active'])
          ),
          array( 'id' => esc_sql($_POST['edit_checkout_field']['id']) )
        );

        if ($update_query) {
          $flash = array(
            'message' => __('Checkout Field updated!', 'halio'),
            'type' => 'success'
          );
        } else {
          $flash = array(
            'message' => __('Error updating Checkout Field', 'halio'),
            'type' => 'warning'
          );
        }
      } elseif ( isset($_POST['change_checkout_field']) ) {
        // Activating/Deactivating Fixed Address
        $wpdb->update(
          $checkout_table,
          array(
            'is_active' => $_POST['change_checkout_field']['action'] === 'activate'
          ),
          array( 'id' => esc_sql($_POST['change_checkout_field']['id']) )
        );
      } elseif ( isset($_POST['delete_checkout_field']) ) {
        // Deleting Polygon Pricing Condition
        $wpdb->delete(
          $checkout_table,
          array( 'id' => esc_sql($_POST['delete_checkout_field']['id']) )
        );

        $flash = array(
          'message' => __('Checkout Field deleted', 'halio'),
          'type' => 'success'
        );
      }
    }

    if ( isset($_GET['checkout_field_id']) ) {
      require_once HALIO_PLUGIN_DIR . "/pages/form-design/checkout-fields/edit.php";
    } else {
      require_once HALIO_PLUGIN_DIR . "/pages/form-design/index.php";
    }
  }

  public static function render_fixed_addresses_page() {
    if ( !empty($_POST) ) {
      global $wpdb;

      if ( isset($_POST['new_fixed_address']) ) {
        // Creating Fixed Address
        $addresses_table  = $wpdb->prefix . 'halio_fixed_addresses';

        if ( empty($_POST['new_fixed_address']['address']) ) {
          $flash = array(
            'message' => __('You need to specify the address', 'halio'),
            'type' => 'warning'
          );
        } else if ( empty($_POST['new_fixed_address']['origin_or_destination']) ) {
          $flash = array(
            'message' => __('You need to specify whether this address is a starting or destination address.', 'halio'),
            'type' => 'warning'
          );
        } else {
          $pretty_address = empty($_POST['new_fixed_address']['pretty_address']) ? $_POST['new_fixed_address']['address'] : $_POST['new_fixed_address']['pretty_address'];

          $wpdb->insert(
            $addresses_table,
            array(
              'address' => esc_sql($_POST['new_fixed_address']['address']),
              'pretty_address' => esc_sql($pretty_address),
              'origin_or_destination'  => esc_sql($_POST['new_fixed_address']['origin_or_destination']),
              'is_active' => esc_sql($_POST['new_fixed_address']['is_active'])
            )
          );

          $flash = array(
            'message' => __('Fixed Address created!', 'halio'),
            'type' => 'success'
          );
        }
      } elseif ( isset($_POST['edit_fixed_address']) ) {
        $addresses_table  = $wpdb->prefix . 'halio_fixed_addresses';

        if ( !empty($_POST['edit_fixed_address']['address']) ) {

          $wpdb->update(
            $addresses_table,
            array(
              'address' => esc_sql($_POST['edit_fixed_address']['address']),
              'pretty_address' => esc_sql($_POST['edit_fixed_address']['pretty_address']),
              'origin_or_destination'  => esc_sql($_POST['edit_fixed_address']['origin_or_destination']),
              'is_active' => esc_sql($_POST['edit_fixed_address']['is_active'])
            ),
            array( 'id' => esc_sql($_POST['edit_fixed_address']['id']) )
          );

          $flash = array(
            'message' => __('Fixed Address updated!', 'halio'),
            'type' => 'success'
          );
        } else {
          $flash = array(
            'message' => __('You need to specify the address', 'halio'),
            'type' => 'warning'
          );
        }
      } elseif ( isset($_POST['setting']) ) {
        // Updating Setting
        $settings_table  = $wpdb->prefix . 'halio_settings';

        foreach ($_POST['setting'] as $setting_id => $properties) {
          $wpdb->update(
            $settings_table,
            array(
              'value' => esc_sql($properties['value'])
            ),
            array( 'id' => $setting_id )
          );
        }

        $flash = array(
          'message' => __('Settings Updated!', 'halio'),
          'type' => 'success'
        );
      } elseif ( isset($_POST['delete_fixed_address']) ) {
        // Deleting Fixed Address
        $address_table  = $wpdb->prefix . 'halio_fixed_addresses';

        $wpdb->delete($address_table, array( 'id' => esc_sql($_POST['delete_fixed_address']['id']) ));

        $flash = array(
          'message' => __('Fixed Address deleted', 'halio'),
          'type' => 'success'
        );
      } elseif ( isset($_POST['change_fixed_address']) ) {
        // Activating/Deactivating Fixed Address
        $wpdb->update(
          $wpdb->prefix . 'halio_fixed_addresses',
          array(
            'is_active' => $_POST['change_fixed_address']['action'] === 'activate'
          ),
          array( 'id' => esc_sql($_POST['change_fixed_address']['id']) )
        );
      }
    }

    if ( isset($_GET['fixed_address_id']) ) {
      $file = "/pages/fixed-addresses/edit.php";
    } else {
      $file = "/pages/fixed-addresses/index.php";
    }

    require_once HALIO_PLUGIN_DIR . $file;
  }

  public static function render_orders_page() {
    if ( !empty($_POST) ) {
      global $wpdb;
    }

    if ( isset($_GET['order_id']) ) {
      $file = "/pages/orders/view.php";
    } else {
      $file = "/pages/orders/index.php";
    }

    require_once HALIO_PLUGIN_DIR . $file;
  }

  public static function render_calendar_page() {
    $file = "/pages/calendar/index.php";

    require_once HALIO_PLUGIN_DIR . $file;
  }
}
