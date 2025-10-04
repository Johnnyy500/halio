<?php
if (!defined('ABSPATH')) { exit; }


global $flash;
global $wpdb;
global $woocommerce;

$vehicles = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "halio_vehicles` WHERE `is_active` = 1");
$verticle_class = '';
$half_width_column = 'col-md-4';
$quarter_width_column = 'col-md-3';
$map_column_width = 'col-md-8';

if ( isset($options['vertical']) ) {
  $options['vertical'] = $options['vertical'];
}

if ($options['verticle'] == 'true') {
  $half_width_column = 'col-md-12';
  $quarter_width_column = 'col-md-6';
  $verticle_class = 'verticle';
  $map_column_width = 'col-md-12';
}

if ( class_exists('WooCommerce') ) {
  ?><div class="row halio-form-container <?= $verticle_class; ?>"><?php
    if ( isset($flash) ) {
      ?><div class="alert alert-<?= $flash['type']; ?>" role="alert"><?=
        $flash['message'];
      ?></div><?php
    }

    ?><div class="halio-form-header">
      <div class="halio-company-information center"><?php
        if ( halio_get_settings_row('form_show_title_or_image')->value == 'title' ) {
          echo halio_get_settings_row('form_title')->value;
        } else {
          ?><img class="halio-form-image" src="<?= halio_get_settings_row('form_title_image')->value; ?>"><?php
        }
      ?></div>
    </div>

    <div class="<?= $half_width_column; ?> halio-left-container">
      <form class="halio-form" method="post" action="">
        <input type="hidden" id="HalioDistance" name="halio_distance_in_meters">
        <input type="hidden" id="HalioPrettyDistance" name="halio_distance">
        <input type="hidden" id="HalioDuration" name="halio_duration_in_seconds">
        <input type="hidden" id="HalioPrettyDuration" name="halio_duration">
        <input type="hidden" id="HalioStartingLat" name="halio_starting_coords_lat">
        <input type="hidden" id="HalioStartingLong" name="halio_starting_coords_long">
        <input type="hidden" id="HalioDestinationLat" name="halio_destination_coords_lat">
        <input type="hidden" id="HalioDestinationLong" name="halio_destination_coords_long">
        <input type="hidden" id="HalioMapStartingCountry" value="<?= halio_get_settings_row('map_starting_country')->value; ?>">
        <input type="hidden" id="HalioUnitSystem" value="<?= halio_get_settings_row('units')->value; ?>">
        <input type="hidden" id="HalioPrice" name="halio_price">
        <input type="hidden" id="HalioMinuteBuffer" name="halio_minute_buffer" value="<?= halio_get_settings_row('form_booking_buffer_time_minutes')->value; ?>"><?php
        if ( halio_get_settings_row('enforce_autocomplete_country_restriction')->value ) {
          ?><input type="hidden" id="HalioAutocompleteRestriction" value="<?= halio_get_settings_row('autocomplete_country')->value; ?>"><?php
        }

        ?><div class="halio-input-overlay-group">
          <div class="halio-overlay">
            <span class="letter">
              <i class="fa fa-map-marker"></i>
            </span>
          </div><?php
          if ( halio_get_settings_row('use_fixed_addresses_for_origin')->value ) {
            $starting_addresses = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "halio_fixed_addresses` WHERE `is_active` = 1 AND `origin_or_destination` = 'origin'");

            ?><select id="HalioStartingAddressSelect" name="halio_starting_address">
              <option><?= halio_get_settings_row('form_starting_address_select_text')->value; ?></option><?php
              foreach ($starting_addresses as $starting_address) {
                ?><option value="<?= $starting_address->address; ?>"><?= $starting_address->pretty_address; ?></option><?php
              }
            ?></select><?php
          } else {
            ?>
              <input type="text" id="HalioStartingAddress" aria-describedby="starting-address-status" name="halio_starting_address" placeholder="<?= halio_get_settings_row('form_starting_address_label')->value; ?>" autofocus><?php
          }
        ?></div>

        <hr class="halio-address-separator">

        <div class="halio-input-overlay-group">
          <div class="halio-overlay">
            <span class="letter">
              <i class="fa fa-map-marker"></i>
            </span>
          </div><?php
          if ( halio_get_settings_row('use_fixed_addresses_for_destination')->value ) {
            $destination_addresses = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "halio_fixed_addresses` WHERE `is_active` = 1 AND `origin_or_destination` = 'destination'");

            ?><select id="HalioDestinationAddressSelect" name="halio_destination_address">
              <option><?= halio_get_settings_row('form_destination_address_select_text')->value; ?></option><?php
              foreach ($destination_addresses as $destination_address) {
                ?><option value="<?= $destination_address->address; ?>"><?= $destination_address->pretty_address; ?></option><?php
              }
            ?></select><?php
          } else {
            ?><input type="text" id="HalioDestinationAddress" aria-describedby="destination-address-status" name="halio_destination_address" placeholder="<?= halio_get_settings_row('form_destination_address_label')->value; ?>"><?php
          }
        ?></div><?php

        if (
          !halio_get_settings_row('use_fixed_addresses_for_origin')->value ||
          !halio_get_settings_row('use_fixed_addresses_for_destination')->value
        ) {
          ?><div class="halio-marker-helper-container" style="display: none;">
            <span class="halio-marker-helper"><?php
              if (
                !halio_get_settings_row('use_fixed_addresses_for_origin')->value &&
                !halio_get_settings_row('use_fixed_addresses_for_destination')->value
              ) {
                _e('Drag the markers to change location', 'halio');
              } else if ( !halio_get_settings_row('use_fixed_addresses_for_destination')->value ) {
                _e('Drag the destination marker to change location', 'halio');
              } else if ( !halio_get_settings_row('use_fixed_addresses_for_origin')->value ) {
                _e('Drag the origin marker to change location', 'halio');
              }
            ?></span>
          </div><?php
        }

        ?><div class="halio-padding-container">
          <ul class="halio-input-group"><?php

            if ( halio_get_settings_row('form_can_edit_vehicle_type')->value ) {
              ?><li class="halio-input-container">
                <label for="HalioVehicleType"><?= halio_get_settings_row('form_vehicle_type_label')->value; ?></label>

                <div class="input-right">
                  <select class="form-control" id="HalioVehicleType" name="halio_vehicle_id">
                    <option disabled selected><?= halio_get_settings_row('form_vehicle_type_label')->value; ?>...</option><?php
                    foreach ($vehicles as $vehicle) {
                      ?><option value="<?= $vehicle->id; ?>" data-max-occupants="<?= $vehicle->passenger_space; ?>"><?=
                        $vehicle->name;
                      ?></option><?php
                    }
                  ?></select>
                </div>
              </li><?php
            }

            if ( halio_get_settings_row('form_can_edit_occupants')->value ) {
              ?><li class="halio-input-container">
                <label for="HalioNoOfOccupants"><?= halio_get_settings_row('form_number_of_occupants_label')->value; ?></label>

                <div class="input-right">
                  <select class="form-control" id="HalioNoOfOccupants" name="halio_occupants">
                    <option selected disabled><?= halio_get_settings_row('form_number_of_occupants_label')->value; ?>...</option><?php
                    $occupant_options = array( 1, 2, 3, 4 );
                    foreach ($occupant_options as $option) {
                      ?><option value="<?= $option; ?>"><?=
                        $option;
                      ?></option><?php
                    }
                  ?></select>
                </div>
              </li><?php
            }

            if ( halio_get_settings_row('form_can_edit_direction')->value ) {
              ?><li class="halio-input-container">
                <label for="HalioDirection"><?= halio_get_settings_row('form_direction_label')->value; ?></label>

                <div class="input-right">
                  <select class="form-control" id="HalioDirection"  name="halio_direction">
                    <option selected disabled><?= halio_get_settings_row('form_direction_label')->value; ?>...</option><?php
                    $direction_options = array(
                      'one_way' => halio_get_settings_row('checkout_one_way_label')->value,
                      'return' => halio_get_settings_row('checkout_return_label')->value
                    );

                    foreach ($direction_options as $key => $value) {
                      ?><option value="<?= $key; ?>"><?=
                        $value;
                      ?></option><?php
                    }
                  ?></select>
                </div>
              </li><?php
            }

            ?><li class="halio-input-container">
              <label for="HalioPickupTime"><?= halio_get_settings_row('form_pick_up_time_label')->value; ?></label>

              <div class="input-right" id="HalioPickupTimeContainer">
                <input type="text" class="form-control" id="HalioPickupTime" name="halio_pick_up_time" placeholder="<?= halio_get_settings_row('form_pick_up_time_label')->value; ?>">
              </div>
            </li>

            <li class="halio-input-container return-pick-up-time" style="display: none;">
              <label for="HalioReturnPickupTime"><?= halio_get_settings_row('form_return_pick_up_time_label')->value; ?></label>

              <div class="input-right" id="HalioReturnPickupTimeContainer">
                <input type="text" class="form-control" id="HalioReturnPickupTime" name="halio_return_pick_up_time" placeholder="<?= halio_get_settings_row('form_return_pick_up_time_label')->value; ?>">
              </div>
            </li>
          </ul>
        </div>

        <div class="row center action-buttons-container">
          <a href="#" class="btn btn-primary estimate-cost" data-disable-with="<i class='fa fa-refresh fa-spin'></i>" data-original-text="<?= halio_get_settings_row('form_estimate_cost_label')->value; ?>" data-estimating-text="<?= halio_get_settings_row('form_estimating_cost_label')->value; ?>"><?=
            halio_get_settings_row('form_estimate_cost_label')->value;
          ?></a>
        </div>

        <div class="alert alert-warning halio-form-feedback" role="alert" style="display: none;"></div>

        <div class="estimate-container" style="display: none;">
          <div class="halio-price-container center">
            <span class="currency"><?= get_woocommerce_currency_symbol(); ?></span>
            <span class="price">0.00</span>
          </div>

          <ul class="journey-stats center"><?php
            if (halio_get_settings_row('form_show_duration_in_estimate')->value) {
              ?><li class="stat">
                <span class="fa fa-clock-o"></span>
                <span class="duration"></span>
              </li><?php
            }

            if (halio_get_settings_row('form_show_distance_in_estimate')->value) {
              ?><li class="stat">
                <span class="fa fa-road"></span>
                <span class="distance"></span>
              </li><?php
            }
          ?></ul>
        </div>

        <div class="center halio-booking-button-container" style="display: none;">
          <input type="submit" value="<?= halio_get_settings_row('form_book_button_text')->value; ?>" class="btn btn-success booking-button">
        </div>
      </form>
    </div><?php

    if ($options['verticle'] == 'true') {
      ?><div class="<?= $map_column_width; ?> halio-right-container halio-verticle-map-container">
        <div id="map"></div>
      </div><?php
    } else {
      ?><div class="<?= $map_column_width; ?> halio-right-container">
      <div id="map"></div>
    </div><?php
    }

  ?></div><?php
} else {
  ?>You need to install WooCommerce. Without it Halio will not work.<?php
}
