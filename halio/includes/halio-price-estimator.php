<?php

class HalioPriceEstimator {

  public function new_ajax_request() {
    echo json_encode($this->estimate_price($_POST));
    wp_die();
  }

  /**
   * [estimate_price description]
   * @param  array    $options
   * @param  integer  $options['vehicle_id']
   * @param  integer  $options['distance_in_meters']
   * @param  string   $options['occupants']
   * @param  integer  $options['duration']           in seconds
   * @param  string   $options['pick_up_time']       in datetime format
   * @param  string   $options['starting_coords']['lat']
   * @param  string   $options['starting_coords']['long']
   * @param  string   $options['destination_coords']['lat']
   * @param  string   $options['destination_coords']['long']
   * @return JSON Object
   */
  public function estimate_price($options = array()) {
    global $wpdb;
    $vehicle_table = $wpdb->prefix . 'halio_vehicles';
    $settings_table = $wpdb->prefix . 'halio_settings';
    $pricing_conditions_table = $wpdb->prefix . 'halio_polygon_pricing_conditions';
    $polygon_coordinates_table = $wpdb->prefix . 'halio_polygon_coordinates';

    // If user can't edit vehicle type, use the default one
    if ( !halio_get_settings_row('form_can_edit_vehicle_type')->value ) {
      $options['vehicle_id'] = halio_get_settings_row('form_default_vehicle_id')->value;
    }

    // If user can't edit direction, set to one way
    if ( !halio_get_settings_row('form_can_edit_direction')->value ) {
      $options['journey_direction'] = 'one_way';
    }

    // If user can't edit occupants, set to 0
    if ( !halio_get_settings_row('form_can_edit_occupants')->value ) {
      $options['occupants'] = 0;
    }

    $vehicle = $wpdb->get_row("SELECT * FROM $vehicle_table WHERE id = " . esc_sql($options['vehicle_id']) . ";");
    $return_fare_multiplier = $wpdb->get_row("SELECT * FROM `$settings_table` WHERE `key` = 'return_fare_multiplier';");
    $units = $wpdb->get_row("SELECT * FROM `$settings_table` WHERE `key` = 'units';");
    $can_book = true;
    $return_message = '';

    $times = array(
      'pick_up_time' => $options['pick_up_time']
    );

    $distance_in_meters = intval($options['distance_in_meters']);

    if ( $units->value == 'miles' ) {
      $distance = $distance_in_meters * 0.00062137;
    } elseif ( $units->value == 'kilometers' ) {
      $distance = $distance_in_meters * 0.001;
    }

    $starting_x = $options['starting_coords']['long'];
    $starting_y = $options['starting_coords']['lat'];
    $ending_x = $options['destination_coords']['long'];
    $ending_y = $options['destination_coords']['lat'];

    if ( !empty($options['return_pick_up_time']) ) {
      $times['return_pick_up_time'] = $options['return_pick_up_time'];
    }

    if ( !$this->any_available_vehicles($vehicle, $times, $options['duration']) ) {
      return array(
        'price' => 0,
        'message' => halio_get_settings_row('form_vehicle_unavailable_message')->value,
        'can_book' => false
      );
    }

    if ( !$this->any_unbooked_vehicles($vehicle, $times, $options['duration']) ) {
      return array(
        'price' => 0,
        'message' => halio_get_settings_row('form_vehicle_fully_booked_message')->value,
        'can_book' => false
      );
    }

    // Check that fare is between max and min distance if it applies
    if (
      halio_get_settings_row('enforce_minimum_distance')->value &&
      $distance < floatval(halio_get_settings_row('minimum_distance')->value)
    ) {
      return array(
        'price' => 0,
        'message' => halio_get_settings_row('minimum_distance_error_message')->value,
        'can_book' => false
      );
    } elseif (
      halio_get_settings_row('enforce_maximum_distance')->value &&
      $distance > floatval(halio_get_settings_row('maximum_distance')->value)
    ) {
      return array(
        'price' => 0,
        'message' => halio_get_settings_row('maximum_distance_error_message')->value,
        'can_book' => false
      );
    }

    if ( halio_get_settings_row('enforce_acceptance_region')->value ) {
      $ar_vertices = explode('|', halio_get_settings_row('acceptance_region_coordinates')->value);
      $ar_x_coords = array();
      $ar_y_coords = array();

      foreach ($ar_vertices as $vertex) {
        $coord = explode(',', $vertex);
        array_push($ar_x_coords, $coord[1]);
        array_push($ar_y_coords, $coord[0]);
      }

      if ( !is_point_in_polygon(count($ar_vertices), $ar_x_coords, $ar_y_coords, $starting_x, $starting_y) ) {
        return array(
          'price' => 0,
          'message' => halio_get_settings_row('form_not_in_acceptance_region_message')->value,
          'can_book' => false
        );
      }
    }

    // Starting fare
    $total_fare = $vehicle->starting_fare;

    // Cost per occupant
    if ( isset($options['occupants']) ) {
      $total_fare += $options['occupants'] * $vehicle->price_per_occupant;
    }

    // Cost per minute
    $duration = $options['duration'] / 60;
    $total_fare += $duration * $vehicle->price_per_minute;

    // Cost per unit distance (mile or kilometer)
    $total_fare += $distance * $vehicle->price_per_unit_distance;

    $time_pricing_conditions = $wpdb->get_results(
      "SELECT * FROM `" . $wpdb->prefix . "halio_time_pricing_conditions`
      WHERE
        `is_active` = 1 AND
        (`vehicle_id` = " . esc_sql($options['vehicle_id']) . " OR `vehicle_id` = 0);"
    );

    $increase_already_applied = array();
    $return_increase_already_applied = array();

    if ( !empty($time_pricing_conditions) ) {
      $ending_conditions = array();
      $return_ending_conditions = array();

      foreach ($time_pricing_conditions as $time_pricing_condition) {

        if ( $this->time_in_time_pricing_condition($time_pricing_condition, $options['pick_up_time'], $options['duration']) ) {

          if (
            $time_pricing_condition->ending_time == '23:59:00' ||
            $time_pricing_condition->ending_time == '23:59:59'
          ) {
            $details = array(
              'increase_amount' => $time_pricing_condition->increase_amount,
              'increase_multiplier' => $time_pricing_condition->increase_multiplier
            );

            $ending_conditions[$time_pricing_condition->id] = $details;
          }
        }
      }

      foreach ($time_pricing_conditions as $time_pricing_condition) {

        if ( $this->time_in_time_pricing_condition($time_pricing_condition, $options['pick_up_time'], $options['duration']) ) {

          if ( $time_pricing_condition->starting_time == '00:00:00' ) {
            foreach ($ending_conditions as $condition_id => $details) {

              if (
                $time_pricing_condition->increase_amount == $details['increase_amount'] ||
                $time_pricing_condition->increase_multiplier == $details['increase_multiplier']
              ) {
                array_push($increase_already_applied, $condition_id);
              }
            }
          }
        }
      }

      foreach ($time_pricing_conditions as $time_pricing_condition) {
        if (
          !in_array($time_pricing_condition->id, $increase_already_applied) &&
          $this->time_in_time_pricing_condition($time_pricing_condition, $options['pick_up_time'], $options['duration'])
        ) {
          $total_fare += $time_pricing_condition->increase_amount;
          $total_fare *= $time_pricing_condition->increase_multiplier;
        }
      }

      if ( !empty($options['return_pick_up_time']) ) {

        foreach ($time_pricing_conditions as $time_pricing_condition) {

          if (
            $this->time_in_time_pricing_condition($time_pricing_condition, $options['return_pick_up_time'], $options['duration'])
          ) {

            if (
              $time_pricing_condition->ending_time == '23:59:00' ||
              $time_pricing_condition->ending_time == '23:59:59'
            ) {
              $details = array(
                'increase_amount' => $time_pricing_condition->increase_amount,
                'increase_multiplier' => $time_pricing_condition->increase_multiplier
              );

              $return_ending_conditions[$time_pricing_condition->id] = $details;
            }
          }
        }

        foreach ($time_pricing_conditions as $time_pricing_condition) {

          if (
            $this->time_in_time_pricing_condition($time_pricing_condition, $options['return_pick_up_time'], $options['duration'])
          ) {

            if ( $time_pricing_condition->starting_time == '00:00:00' ) {
              foreach ($return_ending_conditions as $condition_id => $details) {

                if (
                  $time_pricing_condition->increase_amount == $details['increase_amount'] ||
                  $time_pricing_condition->increase_multiplier == $details['increase_multiplier']
                ) {
                  array_push($return_increase_already_applied, $condition_id);
                }
              }
            }
          }
        }

        foreach ($time_pricing_conditions as $time_pricing_condition) {
          if (
            !in_array($time_pricing_condition->id, $return_increase_already_applied) &&
            $this->time_in_time_pricing_condition($time_pricing_condition, $options['return_pick_up_time'], $options['duration'])
          ) {
            $total_fare += $time_pricing_condition->increase_amount;
            $total_fare *= $time_pricing_condition->increase_multiplier;
          }
        }
      }
    }

    $polygon_pricing_conditions = $wpdb->get_results(
      "SELECT * FROM `" . $pricing_conditions_table . "`
      WHERE
        `is_active` = 1 AND
        (`vehicle_id` = " . esc_sql($options['vehicle_id']) . " OR `vehicle_id` = 0);"
    );

    // This needs to be run 2nd to last as it holds the rules with fixed prices
    // The one-way/return check is run last
    if ( !empty($polygon_pricing_conditions) ) {
      foreach ($polygon_pricing_conditions as $polygon_pricing_condition) {
        $pu_x_coords = $pu_y_coords = $do_x_coords = $do_y_coords = array();

        $pu_polygon_coordinates = $wpdb->get_results("SELECT * FROM `" . $polygon_coordinates_table . "` WHERE `polygon_pricing_condition_id` = '" . $polygon_pricing_condition->id . "' AND `type` = 'pick_up';");
        $do_polygon_coordinates = $wpdb->get_results("SELECT * FROM `" . $polygon_coordinates_table . "` WHERE `polygon_pricing_condition_id` = '" . $polygon_pricing_condition->id . "' AND `type` = 'drop_off';");

        foreach ($pu_polygon_coordinates as $polygon_coordinate) {
          array_push($pu_x_coords, $polygon_coordinate->longitude);
          array_push($pu_y_coords, $polygon_coordinate->latitude);
        }

        foreach ($do_polygon_coordinates as $polygon_coordinate) {
          array_push($do_x_coords, $polygon_coordinate->longitude);
          array_push($do_y_coords, $polygon_coordinate->latitude);
        }

        $pu_polygon_sides = $polygon_pricing_condition->pick_up_polygon_nos;
        $do_polygon_sides = $polygon_pricing_condition->drop_off_polygon_nos;

        // Condition only applies in one direction
        if ($polygon_pricing_condition->one_way_or_both == 'one_way') {

          // Customer can be picked up anywhere OR is picked up within defined area for condition
          // AND
          // Customer can be dropped off anywhere OR is dropped off within defined area for condition
          if (
            ($polygon_pricing_condition->pick_up_location == 'anywhere' ||
              ($polygon_pricing_condition->pick_up_location == 'specific_area' &&
                is_point_in_polygon($pu_polygon_sides, $pu_x_coords, $pu_y_coords, $starting_x, $starting_y))) &&
            ($polygon_pricing_condition->drop_off_location == 'anywhere' ||
              ($polygon_pricing_condition->drop_off_location == 'specific_area' &&
                is_point_in_polygon($do_polygon_sides, $do_x_coords, $do_y_coords, $ending_x, $ending_y)))
          ) {
            if ($polygon_pricing_condition->increase_or_fixed == 'fixed') {
              $total_fare = $polygon_pricing_condition->fixed_amount;
            } elseif ($polygon_pricing_condition->increase_or_fixed == 'increase') {
              $total_fare += $polygon_pricing_condition->increase_amount;
              $total_fare *= $polygon_pricing_condition->increase_multiplier;
            }
          }
        } elseif ($polygon_pricing_condition->one_way_or_both == 'each_way') {

          //   Customer can be picked up anywhere OR is picked up within defined pickup area for condition
          //   AND
          //   Customer can be dropped off anywhere OR is dropped off within defined dropoff area for condition
          // OR
          //   Customer can be picked up anywhere OR is picked up within defined dropoff area for condition
          //   AND
          //   Customer can be dropped off anywhere OR is dropped off within defined pickup area for condition
          if (
            (
              ($polygon_pricing_condition->pick_up_location == 'anywhere' ||
                ($polygon_pricing_condition->pick_up_location == 'specific_area' &&
                  is_point_in_polygon($pu_polygon_sides, $pu_x_coords, $pu_y_coords, $starting_x, $starting_y))) &&
              ($polygon_pricing_condition->drop_off_location == 'anywhere' ||
                ($polygon_pricing_condition->drop_off_location == 'specific_area' &&
                  is_point_in_polygon($do_polygon_sides, $do_x_coords, $do_y_coords, $ending_x, $ending_y)))
            ) || (
              ($polygon_pricing_condition->pick_up_location == 'anywhere' ||
                ($polygon_pricing_condition->pick_up_location == 'specific_area' &&
                  is_point_in_polygon($do_polygon_sides, $do_x_coords, $do_y_coords, $starting_x, $starting_y))) &&
              ($polygon_pricing_condition->drop_off_location == 'anywhere' ||
                ($polygon_pricing_condition->drop_off_location == 'specific_area' &&
                  is_point_in_polygon($pu_polygon_sides, $pu_x_coords, $pu_y_coords, $ending_x, $ending_y)))
            )
          ) {
            if ($polygon_pricing_condition->increase_or_fixed == 'fixed') {
              $total_fare = $polygon_pricing_condition->fixed_amount;
            } elseif ($polygon_pricing_condition->increase_or_fixed == 'increase') {
              $total_fare += $polygon_pricing_condition->increase_amount;
              $total_fare *= $polygon_pricing_condition->increase_multiplier;
            }
          }
        }
      }
    }

    if ( $options['journey_direction'] == 'return' ) {
      if ( $return_fare_multiplier->value ) {
        $direction_multiplier = floatval($return_fare_multiplier->value);
      } else {
        $direction_multiplier = 2;
      }
    } else {
      $direction_multiplier = 1;
    }

    $total_fare *= $direction_multiplier;

// Check max fare
if (
  halio_get_settings_row('enforce_maximum_fare')->value &&
  $total_fare > floatval(halio_get_settings_row('maximum_fare')->value)
) {
  $maximum_fare = floatval(halio_get_settings_row('maximum_fare')->value);
  $formatter = new NumberFormatter(
      'nl_NL', // Locale instellen op Nederlands (Nederland) - pas dit aan indien nodig
      NumberFormatter::CURRENCY
  );
  $total_fare = $formatter->formatCurrency($maximum_fare, 'EUR'); // Gebruik EUR als valuta - pas dit aan indien nodig
}

// Check minimum fare
if (
  halio_get_settings_row('enforce_minimum_fare')->value &&
  $total_fare < floatval(halio_get_settings_row('minimum_fare')->value)
) {
  $minimum_fare = floatval(halio_get_settings_row('minimum_fare')->value);
  $formatter = new NumberFormatter(
      'nl_NL', // Locale instellen op Nederlands (Nederland) - pas dit aan indien nodig
      NumberFormatter::CURRENCY
  );
  $total_fare = $formatter->formatCurrency($minimum_fare, 'EUR'); // Gebruik EUR als valuta - pas dit aan indien nodig
}

    return array(
      'price' => $total_fare,
      'message' => $return_message,
      'can_book' => $can_book
    );
  }

  private function time_in_time_pricing_condition($condition, $pick_up, $duration) {
    $days = array( 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday' );

    $pick_up_datetime = DateTime::createFromFormat("d/m/Y H:i", $pick_up);
    $customer_start = $pick_up_datetime->getTimestamp();
    $customer_end = $customer_start + $duration;
    $start_day = intval(date('w', $pick_up_datetime->getTimestamp()));

    // How many days the journey spans over
    $journey_day_span = intval(date('w', ($customer_end))) - $start_day + 1;

    if ( $journey_day_span == 1 ) {
      if ( $condition->{$days[date('w', $customer_start)]} ) {
        // Seconds since midnight
        $condition_start = strtotime($condition->starting_time) % 86400;
        $condition_end = strtotime($condition->ending_time) % 86400;

        $customer_start = $pick_up_datetime->getTimestamp() % 86400;
        $customer_end = $customer_start + $duration;

        // Do the 2 ranges overlap?
        return $condition_start <= $customer_end && $condition_end >= $customer_start;
      }
    } else {
      $dow = date('w', $pick_up_datetime->getTimestamp());

      for ($i = 0; $i < $journey_day_span; $i++) {
        $is_first_day = $i == 0;
        $is_last_day = $i == $journey_day_span - 1;
        $is_middle_day = !$is_first_day && !$is_last_day;

        // Make $dow loop round to 0, not go above 6
        if ( $dow > 6 ) {
          $dow = ($dow % 6) - 1;
        }

        // If condition applies to current day
        if ( $condition->{$days[$dow]} ) {
          // Seconds since midnight
          $condition_start = strtotime($condition->starting_time) % 86400;
          $condition_end = strtotime($condition->ending_time) % 86400;

          // seconds since midnight
          if ( $is_first_day ) {
            $customer_start = $pick_up_datetime->getTimestamp() % 86400;
            $customer_end = 86400;
          } elseif ( $is_middle_day ) {
            $customer_start = 0;
            $customer_end = 86400;
          } elseif ( $is_last_day ) {
            $customer_start = 0;
            $customer_end = (($pick_up_datetime->getTimestamp() + $duration) % 86400);
          }

          if ($condition_start <= $customer_end && $condition_end >= $customer_start) {
            return true;
          }
        }

        $dow++;
      }
    }

    return false;
  }

  private function any_available_vehicles($vehicle, $times, $duration) {
    if ( isset($times['return_pick_up_time']) ) {
      return (
        halio_is_vehicle_available($vehicle, $times['pick_up_time'], $duration) &&
        halio_is_vehicle_available($vehicle, $times['return_pick_up_time'], $duration)
      );
    } else {
      return ( halio_is_vehicle_available($vehicle, $times['pick_up_time'], $duration) );
    }
  }

  private function any_unbooked_vehicles($vehicle, $times, $duration) {
    if ( isset($times['return_pick_up_time']) ) {
      return (
        halio_any_unbooked_vehicles($vehicle, $times['pick_up_time'], $duration) &&
        halio_any_unbooked_vehicles($vehicle, $times['return_pick_up_time'], $duration)
      );
    } else {
      return ( halio_any_unbooked_vehicles($vehicle, $times['pick_up_time'], $duration) );
    }
  }
}
