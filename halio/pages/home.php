<div class="halio-settings-page settings"><?php

  if ( isset($flash) ) {
    ?><div class="alert alert-<?= $flash['type']; ?>" role="alert">
      <?= $flash['message']; ?>
    </div><?php
  }

  ?><h1 class="center"><?= __('Halio Settings', 'halio'); ?></h1>

  <div>

    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active">
        <a href="#halio-setup" aria-controls="home" role="tab" data-toggle="tab" class="halio-tab-link"><?php
          _e('Setup', 'halio');
        ?></a>
      </li>
      <li role="presentation">
        <a href="#halio-customisation" aria-controls="profile" role="tab" data-toggle="tab" class="halio-tab-link"><?php
          _e('Customisation', 'halio');
        ?></a>
      </li>
    </ul>


    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="halio-setup">
        <form class="form-horizontal halio-settings-form" method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>">

          <h4 class="col-sm-offset-3 col-sm-5"><?= __('Halio Setup', 'halio'); ?></h4><?php

          $api_key = halio_get_settings_row('api_key');
          ?><div class="form-group">
            <label for="HalioSettingApiKey" class="col-sm-3 control-label"><?php
              _e('Google Maps API Key', 'halio');
            ?></label>
            <div class="col-sm-5">
              <input type="text" class="form-control" id="HalioSettingApiKey" placeholder="<?php _e('API Key', 'halio'); ?>" value="<?= $api_key->value; ?>" name="setting[<?= $api_key->id; ?>][value]">
            </div>
            <div class="col-sm-4 helper-text"><?php
              $maps_api_link = 'https://console.developers.google.com/flows/enableapi?apiid=maps_backend&keyType=CLIENT_SIDE&reusekey=true';

              printf(
                __('It is highly recommended you get an API key to work with Google Maps. To create one, <a href="%s">click here</a>.', 'halio'),
                $maps_api_link
              );
            ?></div>
          </div><?php

          $units = halio_get_settings_row('units');
          ?><div class="form-group">
            <label for="HalioSettingUnits" class="col-sm-3 control-label"><?php
              _e('Units', 'halio');
            ?></label>
            <div class="col-sm-5">
              <select class="form-control" id="HalioSettingUnits" name="setting[<?= $units->id; ?>][value]"><?php
                if ( $units->value == 'kilometers' ) {
                  ?><option value="kilometers" selected><?php _e('kilometers', 'halio'); ?></option>
                  <option value="miles"><?php _e('miles', 'halio'); ?></option><?php
                } else {
                  ?><option value="kilometers"><?php _e('kilometers', 'halio'); ?></option>
                  <option value="miles" selected><?php _e('miles', 'halio'); ?></option><?php
                }
              ?></select>
            </div>
            <div class="col-sm-4 helper-text"><?php
              _e('The units customers will be quoted with.', 'halio');
            ?></div>
          </div><?php

          $map_starting_country = halio_get_settings_row('map_starting_country');
          ?><div class="form-group">
            <label for="HalioSettingMapStartingCountry" class="col-sm-3 control-label"><?php
              _e('Map Starting Country', 'halio');
            ?></label>
            <div class="col-sm-5">
              <select class="form-control" id="HalioSettingMapStartingCountry" name="setting[<?= $map_starting_country->id; ?>][value]"><?php
                echo html_list_of_countries();
              ?></select>
            </div>
            <div class="col-sm-4 helper-text"><?php
              _e('The country which the map is centered on when it first loads.', 'halio');
            ?></div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php _e('Currency', 'halio'); ?></label>
            <div class="col-sm-5">
              <p class="form-control-static"><?= get_woocommerce_currency_symbol(); ?></p>
            </div>
            <div class="col-sm-4 helper-text"><?php
              _e('The currency customers will be quoted with. This is taken from WooCommerce.', 'halio');
            ?></div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php _e('WooCommerce Product ID', 'halio'); ?></label>
            <div class="col-sm-5">
              <p class="form-control-static"><?php
                $wc_product_id = halio_get_settings_row('wc_product_id')->value;
                echo $wc_product_id;
              ?></p>
            </div>
            <div class="col-sm-4 helper-text"><?php
              printf(
                __('ID of the <a href="%s">WooCommerce product</a> used as Taxi Fare.', 'halio'),
                halio_wc_edit_product_path($wc_product_id)
              );
            ?></div>
          </div>

          <div class="form-group">
            <div class="col-sm-offset-3 col-sm-5 center">
              <input type="submit" value="<?php _e('Update', 'halio'); ?>" class="btn btn-large btn-primary">
            </div>
          </div>
        </form>
      </div>

      <div role="tabpanel" class="tab-pane" id="halio-customisation">
        <form class="form-horizontal halio-settings-form" method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>">

          <h4 class="col-sm-offset-3 col-sm-5"><?php
            _e('Halio Customisation', 'halio');
          ?></h4><?php

          $return_fare_multiplier = halio_get_settings_row('return_fare_multiplier');
          ?><div class="form-group">
            <label for="HalioSettingReturnFareMultiplier" class="col-sm-3 control-label"><?php
              _e('Return Fare Multiplier', 'halio');
            ?></label>
            <div class="col-sm-5">
              <input type="number" min="0" step="any" class="form-control" id="HalioSettingReturnFareMultiplier" value="<?= $return_fare_multiplier->value; ?>" name="setting[<?= $return_fare_multiplier->id; ?>][value]">
            </div>
            <div class="col-sm-4 helper-text"><?php
              _e('The value the fare is multiplied by when the user requests a return journey.', 'halio');
            ?></div>
          </div><?php

          $enforce_autocomplete_restriction = halio_get_settings_row('enforce_autocomplete_country_restriction');
          ?><div class="form-group">
            <label for="HalioSettingEnforceAutocompleteRestriction" class="col-sm-3 control-label"><?php
              _e('Enforce Autocomplete Country Restriction', 'halio');
            ?></label>
            <div class="col-sm-5">
              <select class="form-control halio__settings--enforce-autocomplete-restriction edit" id="HalioSettingEnforceAutocompleteRestriction" name="setting[<?= $enforce_autocomplete_restriction->id; ?>][value]">
                <option value="1" <?php if ($enforce_autocomplete_restriction->value) echo 'selected'; ?>><?php
                  _e('True', 'halio');
                ?></option>
                <option value="0" <?php if (!$enforce_autocomplete_restriction->value) echo 'selected'; ?>><?php
                  _e('False', 'halio');
                ?></option>
              </select>
            </div>
            <div class="col-sm-4 helper-text"><?php
              _e('When users are searching, do you want to limit the search to a specific country?', 'halio');
            ?></div>
          </div><?php

          $autocomplete_restriction = halio_get_settings_row('autocomplete_country');
          ?><div class="form-group">
            <label for="HalioSettingAutocompleteCountry" class="col-sm-3 control-label"><?php
              _e('Autocomplete Country', 'halio');
            ?></label>
            <div class="col-sm-5">
              <select class="form-control halio__settings--autocomplete-country edit" id="HalioSettingAutocompleteCountry" name="setting[<?= $autocomplete_restriction->id; ?>][value]" <?php if (!$enforce_autocomplete_restriction->value) echo 'disabled'; ?>>

              <option disabled <?php if ($autocomplete_restriction->value == '') echo 'selected'; ?>><?php
                _e('Please select a country...', 'halio');
              ?></option><?php

                $iso_3166_codes = iso_3166_country_codes();

                foreach ($iso_3166_codes as $iso_code => $country_name) {
                  ?><option value="<?= $iso_code; ?>" <?php if ($autocomplete_restriction->value == $iso_code) echo 'selected'; ?>><?php
                    echo $country_name;
                  ?></option><?php
                }
              ?></select>
            </div>
            <div class="col-sm-4 helper-text"><?php
              _e('What country should the autocomplete be limited to?', 'halio');
            ?></div>
          </div><?php

          $vehicle_unavailable = halio_get_settings_row('form_vehicle_unavailable_message');
          ?><div class="form-group">
            <label for="HalioFormSettingVehicleUnavailableMessage" class="col-sm-3 control-label"><?php
              _e('Vehicle Unavailable Message', 'halio');
            ?></label>
            <div class="col-sm-5">
              <textarea placeholder="<?php _e('Vehicle Unavailable Message', 'halio'); ?>" class="form-control halio__setting--form-vehicle-unavailable-message edit" id="HalioFormSettingVehicleUnavailableMessage" name="setting[<?= $vehicle_unavailable->id; ?>][value]" rows="3"><?php
                echo $vehicle_unavailable->value;
              ?></textarea>
            </div>
            <div class="col-sm-4 helper-text"><?php
              _e('The message that displays if the vehicle the user has chosen is not available at the selected time. This can be set in <strong>Vehicles > Edit > Edit Availability</strong>.', 'halio');
            ?></div>
          </div><?php

          $vehicle_fully_booked = halio_get_settings_row('form_vehicle_fully_booked_message');
          ?><div class="form-group">
            <label for="HalioFormSettingVehicleFullyBooked" class="col-sm-3 control-label"><?php
              _e('Vehicle Fully Booked Message', 'halio');
            ?></label>
            <div class="col-sm-5">
              <textarea type="text" placeholder="<?php _e('Vehicle Fully Booked Message', 'halio'); ?>" class="form-control halio__setting--form-vehicle-fully-booked-message edit" id="HalioFormSettingVehicleFullyBooked" name="setting[<?= $vehicle_fully_booked->id; ?>][value]" rows="3"><?php
                echo $vehicle_fully_booked->value;
              ?></textarea>
            </div>
            <div class="col-sm-4 helper-text"><?php
              _e('The message that displays if the user is selecting a time that is fully booked for the selected vehicle.', 'halio');
            ?></div>
          </div>

          <h4 class="col-sm-offset-3 col-sm-5 header"><?php
            _e('Min/Max Fare', 'halio');
          ?></h4><?php

          $enforce_minimum_fare = halio_get_settings_row('enforce_minimum_fare');
          ?><div class="form-group">
            <label for="HalioSettingEnforceMinimumFare" class="col-sm-3 control-label"><?php
              _e('Enforce Minimum Fare', 'halio');
            ?></label>
            <div class="col-sm-5">
              <select class="form-control halio__settings--enforce-minimum-fare edit" id="HalioSettingEnforceMinimumFare" name="setting[<?= $enforce_minimum_fare->id; ?>][value]">
                <option value="1" <?php if ($enforce_minimum_fare->value) echo 'selected'; ?>><?php
                  _e('True', 'halio');
                ?></option>
                <option value="0" <?php if (!$enforce_minimum_fare->value) echo 'selected'; ?>><?php
                  _e('False', 'halio');
                ?></option>
              </select>
            </div>
            <div class="col-sm-4 helper-text"><?php
              _e('Do you want to enforce a minimum fare? <strong>This will take priority over any pricing conditions/fixed prices/price per mile settings you have.</strong> If the users fare is below the minimum fare, it will be set to the minimum fare.', 'halio');
            ?></div>
          </div><?php

          $minimum_fare = halio_get_settings_row('minimum_fare');
          ?><div class="form-group">
            <label for="HalioSettingMinimumFare" class="col-sm-3 control-label"><?php
              _e('Minimum Fare Amount', 'halio');
            ?></label>
            <div class="col-sm-5">
              <div class="input-group">
                <div class="input-group-addon"><?= get_woocommerce_currency_symbol(); ?></div>
                <input type="number" min="0" step="any" class="form-control halio__settings--minimum-fare edit" id="HalioSettingMinimumFare" name="setting[<?= $minimum_fare->id; ?>][value]" value="<?= $minimum_fare->value; ?>" <?php if (!$enforce_minimum_fare->value) echo 'disabled="disabled"'; ?>>
              </div>
            </div>
            <div class="col-sm-4 helper-text"><?php
              _e('The minimum amount a customer can be charged, no matter the length of the journey.', 'halio');
            ?></div>
          </div><?php

          $enforce_maximum_fare = halio_get_settings_row('enforce_maximum_fare');
          ?><div class="form-group">
            <label for="HalioSettingEnforceMaximumFare" class="col-sm-3 control-label"><?php
              _e('Enforce Maximum Fare', 'halio');
            ?></label>
            <div class="col-sm-5">
              <select class="form-control halio__settings--enforce-maximum-fare edit" id="HalioSettingEnforceMaximumFare" name="setting[<?= $enforce_maximum_fare->id; ?>][value]">
                <option value="1" <?php if ($enforce_maximum_fare->value) echo 'selected'; ?>><?php
                  _e('True', 'halio');
                ?></option>
                <option value="0" <?php if (!$enforce_maximum_fare->value) echo 'selected'; ?>><?php
                  _e('False', 'halio');
                ?></option>
              </select>
            </div>
            <div class="col-sm-4 helper-text"><?php
              _e('Do you want to enforce a maximum fare? <strong>This will take priority over any pricing conditions/fixed prices/price per mile settings you have.</strong>', 'halio');
            ?></div>
          </div><?php

          $maximum_fare = halio_get_settings_row('maximum_fare');
          ?><div class="form-group">
            <label for="HalioSettingMaximumFare" class="col-sm-3 control-label"><?php
              _e('Maximum Fare Amount', 'halio');
            ?></label>
            <div class="col-sm-5">
              <div class="input-group">
                <div class="input-group-addon"><?= get_woocommerce_currency_symbol(); ?></div>
                <input type="number" min="0" step="any" class="form-control halio__settings--maximum-fare edit" id="HalioSettingMaximumFare" name="setting[<?= $maximum_fare->id; ?>][value]" value="<?= $maximum_fare->value; ?>" <?php if (!$enforce_maximum_fare->value) echo 'disabled="disabled"'; ?>>
              </div>
            </div>
            <div class="col-sm-4 helper-text"><?php
              _e('The maximum amount a customer can be charged, no matter the length of the journey. This should be used in conjunction with the Maximum Distance rules.', 'halio');
            ?></div>
          </div><?php

          ?><h4 class="col-sm-offset-3 col-sm-5 header"><?php
            _e('Min/Max Distances', 'halio');
          ?></h4><?php

          $enforce_minimum_distance = halio_get_settings_row('enforce_minimum_distance');
          ?><div class="form-group">
            <label for="HalioSettingEnforceMinimumDistance" class="col-sm-3 control-label"><?php
              _e('Enforce Minimum Distance', 'halio');
            ?></label>
            <div class="col-sm-5">
              <select class="form-control halio__setting--enforce-minimum-distance edit" id="HalioSettingEnforceMinimumDistance" name="setting[<?= $enforce_minimum_distance->id; ?>][value]">
                <option value="1" <?php if ($enforce_minimum_distance->value) echo 'selected'; ?>><?php
                  _e('True', 'halio');
                ?></option>
                <option value="0" <?php if (!$enforce_minimum_distance->value) echo 'selected'; ?>><?php
                  _e('False', 'halio');
                ?></option>
              </select>
            </div>
            <div class="col-sm-4 helper-text"><?php
              _e('Do you want there to be a minimum distance that all fares can be? If the customers trip is under the specified amount they will not be able to book a fare and the specified error message will display.', 'halio');
            ?></div>
          </div><?php

          $min_distance = halio_get_settings_row('minimum_distance');
          ?><div class="form-group">
            <label for="HalioSettingMinimumDistance" class="col-sm-3 control-label"><?php
              printf(
                __('Minimum Distance (%s)', 'halio'),
                halio_get_settings_row('units')->value
              );
            ?></label>
            <div class="col-sm-5">
              <input type="number" min="0" step="any" class="form-control halio__setting--minimum-distance edit" id="HalioSettingMinimumDistance" placeholder="<?php _e('Minimum Distance', 'halio'); ?>" value="<?= $min_distance->value; ?>" name="setting[<?= $min_distance->id; ?>][value]" <?php if (!$enforce_minimum_distance->value) echo 'disabled'; ?>>
            </div>
            <div class="col-sm-4 helper-text"><?php
              printf(
                __('The minimum distance a fare can be, this will only apply if you have chosen to enforce minimum distance. This is in %s. This can be changed in the Setup Tab.', 'halio'),
                halio_get_settings_row('units')->value
              );
            ?></div>
          </div><?php

          $minimum_distance_error = halio_get_settings_row('minimum_distance_error_message');
          ?><div class="form-group">
            <label for="HalioSettingMinimumDistanceErrorMessage" class="col-sm-3 control-label"><?php
              _e('Minimum Distance Error Message', 'halio');
            ?></label>
            <div class="col-sm-5">
              <textarea class="form-control halio__setting--minimum-distance-error-message edit" id="HalioSettingMinimumDistanceErrorMessage" placeholder="<?php _e('Minimum Distance Error Message', 'halio'); ?>" name="setting[<?= $minimum_distance_error->id; ?>][value]" <?php if (!$enforce_minimum_distance->value) echo 'disabled'; ?> rows="3"><?php
                echo $minimum_distance_error->value;
              ?></textarea>
            </div>
            <div class="col-sm-4 helper-text"><?php
              _e('The error message that will be displayed to the user if the trip is under the specified minimum distance.', 'halio');
            ?></div>
          </div><?php

          $enforce_maximum_distance = halio_get_settings_row('enforce_maximum_distance');
          ?><div class="form-group">
            <label for="HalioSettingEnforceMaximumDistance" class="col-sm-3 control-label"><?php
              _e('Enforce Maximum Distance', 'halio');
            ?></label>
            <div class="col-sm-5">
              <select class="form-control halio__setting--enforce-maximum-distance edit" id="HalioSettingEnforceMaximumDistance" name="setting[<?= $enforce_maximum_distance->id; ?>][value]">
                <option value="1" <?php if ($enforce_maximum_distance->value) echo 'selected'; ?>><?php
                  _e('True', 'halio');
                ?></option>
                <option value="0" <?php if (!$enforce_maximum_distance->value) echo 'selected'; ?>><?php
                  _e('False', 'halio');
                ?></option>
              </select>
            </div>
            <div class="col-sm-4 helper-text"><?php
              _e('Do you want there to be a maximum distance that all fares can be? If the customers trip is over the specified amount they will not be able to book a fare and the specified error message will display.', 'halio');
            ?></div>
          </div><?php

          $max_distance = halio_get_settings_row('maximum_distance');
          ?><div class="form-group">
            <label for="HalioSettingMaximumDistance" class="col-sm-3 control-label"><?php
              printf(
                __('Maximum Distance (%s)', 'halio'),
                halio_get_settings_row('units')->value
              );
            ?></label>
            <div class="col-sm-5">
              <input type="number" min="0" step="any" class="form-control halio__setting--maximum-distance edit" id="HalioSettingMaximumDistance" placeholder="<?php _e('Maximum Distance', 'halio'); ?>" value="<?= $max_distance->value; ?>" name="setting[<?= $max_distance->id; ?>][value]" <?php if (!$enforce_maximum_distance->value) echo 'disabled'; ?>>
            </div>
            <div class="col-sm-4 helper-text"><?php
              printf(
                __('The maximum distance a fare can be, this will only apply if you have chosen to enforce maximum distance. This is in %s. This can be changed in the Setup Tab.', 'halio'),
                halio_get_settings_row('units')->value
              );
            ?></div>
          </div><?php

          $maximum_distance_error = halio_get_settings_row('maximum_distance_error_message');
          ?><div class="form-group">
            <label for="HalioSettingMaximumDistanceErrorMessage" class="col-sm-3 control-label"><?php
              _e('Maximum Distance Error Message', 'halio');
            ?></label>
            <div class="col-sm-5">
              <textarea class="form-control halio__setting--maximum-distance-error-message edit" id="HalioSettingMaximumDistanceErrorMessage" placeholder="<?php _e('Maximum Distance Error Message', 'halio'); ?>" name="setting[<?= $maximum_distance_error->id; ?>][value]" <?php if (!$enforce_maximum_distance->value) echo 'disabled'; ?> rows="3"><?php
                echo $maximum_distance_error->value;
              ?></textarea>
            </div>
            <div class="col-sm-4 helper-text"><?php
              _e('The error message that will be displayed to the user if the trip is over the specified maximum distance.', 'halio');
            ?></div>
          </div><?php

          $booking_buffer_time = halio_get_settings_row('form_booking_buffer_time_minutes');

          $time_units = array(
            1 => __('Minutes', 'halio'),
            60 => __('Hours', 'halio'),
            1440 => __('Days', 'halio'),
            10080 => __('Weeks', 'halio')
          );

          $buffer_units = 0;
          $buffer_value = 0;

          $time_units_keys = array_keys($time_units);

          if ( !empty($booking_buffer_time->value) ) {
            for ($i = count($time_units) - 1; $i >= 0; $i--) {
              $buffer_units = $time_units_keys[$i];
              $buffer_value = intval($booking_buffer_time->value) / floatval($time_units_keys[$i]);

              if ( intval($buffer_value) == $buffer_value ) {
                break;
              }
            }
          }

          ?><div class="form-group">
            <label for="HalioSettingBookingBufferTime" class="col-sm-3 control-label"><?php
              _e('Booking Buffer Time', 'halio');
            ?></label>
            <div class="col-sm-2">
              <input type="text" class="form-control halio__setting--booking-buffer-time-value edit" placeholder="<?php _e('Booking Buffer Time', 'halio'); ?>" name="setting[<?= $booking_buffer_time->id; ?>][value]" value="<?= $buffer_value; ?>">
            </div>
            <div class="col-sm-3">
              <select id="HalioSettingBookingBufferTime" class="form-control halio__setting--booking-buffer-time-unit" name="setting[<?= $booking_buffer_time->id; ?>][multiplier]">
                <option disabled <?php if ($buffer_units == 0) echo 'selected'; ?>>Please choose a unit...</option><?php

                foreach ($time_units as $multiplier => $unit) {
                  ?><option value="<?= $multiplier; ?>" <?php if ($multiplier == $buffer_units) echo 'selected'; ?>><?= $unit; ?></option><?php
                }
              ?></select>
            </div>
            <div class="col-sm-4 helper-text"><?php
              _e('The amount of time a user must book in advance. Only integers will work in the value field, for more accuracy reduce the unit.', 'halio');
            ?></div>
          </div><?php

          ?><h4 class="col-sm-offset-3 col-sm-5 header"><?php
            _e('Acceptance Region', 'halio');
          ?></h4><?php

          $enforce_acceptance_region = halio_get_settings_row('enforce_acceptance_region');
          ?><div class="form-group">
            <label for="HalioSettingEnforceAcceptanceRegion" class="col-sm-3 control-label"><?php
              _e('Enforce Acceptance Region', 'halio');
            ?></label>
            <div class="col-sm-5">
              <select class="form-control halio__setting--enforce-rejection-region edit" id="HalioSettingEnforceAcceptanceRegion" name="setting[<?= $enforce_acceptance_region->id; ?>][value]">
                <option value="1" <?php if ($enforce_acceptance_region->value) echo 'selected'; ?>><?php
                  _e('True', 'halio');
                ?></option>
                <option value="0" <?php if (!$enforce_acceptance_region->value) echo 'selected'; ?>><?php
                  _e('False', 'halio');
                ?></option>
              </select>
            </div>
            <div class="col-sm-4 helper-text"><?php
              _e('Do you want to only allow fares to be booked if they are collected within the specified acceptance region?', 'halio');
            ?></div>
          </div><?php

          $acceptance_region_message = halio_get_settings_row('form_not_in_acceptance_region_message');
          ?><div class="form-group">
            <label for="HalioSettingAcceptanceRegionErrorMessage" class="col-sm-3 control-label"><?php
              _e('Acceptance Region Error Message', 'halio');
            ?></label>
            <div class="col-sm-5">
              <textarea type="text" class="form-control halio__setting--acceptance-region-error-message edit" id="HalioSettingAcceptanceRegionErrorMessage" placeholder="<?php _e('Maximum Distance Error Message', 'halio'); ?>" name="setting[<?= $acceptance_region_message->id; ?>][value]" <?php if (!$enforce_acceptance_region->value) echo 'disabled'; ?> rows="3"><?php
                echo $acceptance_region_message->value;
              ?></textarea>
            </div>
            <div class="col-sm-4 helper-text"><?php
              _e('The error message that will display if the user books a job where the pick up location is outside the acceptance region.', 'halio');
            ?></div>
          </div><?php

          $acceptance_region_coordinates = halio_get_settings_row('acceptance_region_coordinates');
          ?><div class="form-group acceptance-region-map" <?php if (!$enforce_acceptance_region->value) echo 'style="display: none;"'; ?>>
            <label for="HalioSettingAcceptanceRegion" class="col-sm-3 control-label"><?php
              _e('Acceptance Region', 'halio');
            ?></label>
            <input type="hidden" id="HalioMapStartingCountry" value="<?= halio_get_settings_row('map_starting_country')->value; ?>">
            <input type="hidden" name="setting[<?= $acceptance_region_coordinates->id; ?>][value]" id="HalioSettingAcceptanceRegion" class="halio__setting--acceptance-region new" value="<?= $acceptance_region_coordinates->value; ?>">
            <div class="col-sm-5 acceptance-region-map-container">
              <div class="acceptance-region-map-actions">
                <a href="#" class="btn btn-primary" id="acceptance-region-delete-shape"><?php
                  _e('Delete Shape', 'halio');
                ?></a>
              </div>

              <div id="acceptance_region_map"></div>
            </div>
            <div class="col-sm-4 helper-text"><?php
              _e("If 'Enforce Acceptance Region' is true, customers will only be able to book fares that have a starting address within the bounds of the shape drawn on this map.", 'halio');
            ?><br><br><?php
              _e("To delete a shape, click the shape on the map, then press 'Delete Shape'.", 'halio');
            ?></div>
          </div>

          <div class="form-group">
            <div class="col-sm-offset-3 col-sm-5 center">
              <input type="submit" value="<?php _e('Update', 'halio'); ?>" class="btn btn-large btn-primary">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
