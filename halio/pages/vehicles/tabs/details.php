<?php

$unit_setting = $wpdb->get_row("SELECT * FROM `" . $wpdb->prefix . "halio_settings` WHERE `key` = 'units';");
$units = halio_get_settings_row('units')->value == 'miles' ? __('Mile', 'halio') : 'KM';

?><h1 class="center"><?php
  printf(
    __('Edit %s', 'halio'),
    $vehicle->name
  );
?></h1>

<form method="post" class="form-horizontal halio-settings-form" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>">

  <input type="hidden" name="edit_vehicle[id]" value="<?= $vehicle->id; ?>">

  <div class="form-group">
    <label for="HalioEditVehicleName" class="col-sm-3 control-label"><?php
      _e('Vehicle Name', 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" class="form-control" id="HalioEditVehicleName" placeholder="<?php _e('Vehicle Name', 'halio'); ?>" name="edit_vehicle[name]" value="<?php echo $vehicle->name; ?>">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The name users see when they book a vehicle.', 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <label for="HalioEditVehiclePassengerSpace" class="col-sm-3 control-label"><?php
      _e('Passenger Space', 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="number" min="1" class="form-control" id="HalioEditVehiclePassengerSpace" name="edit_vehicle[passenger_space]" value="<?php echo $vehicle->passenger_space; ?>">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The maximum number of passengers this vehicle can hold.', 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <label for="HalioEditVehicleSuitcaseSpace" class="col-sm-3 control-label"><?php
      _e('Suitcase Space', 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="number" min="0" class="form-control" id="HalioEditVehicleSuitcaseSpace" name="edit_vehicle[suitcase_space]" value="<?php echo $vehicle->suitcase_space; ?>">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The maximum number of suitcases this vehicle can hold.', 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <label for="HalioEditVehicleNumberOwned" class="col-sm-3 control-label"><?php
      _e('Number Owned', 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="number" min="0" class="form-control" id="HalioEditVehicleNumberOwned" name="edit_vehicle[number_owned]" value="<?php echo $vehicle->number_owned; ?>">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The number of these vehicles you have in your fleet.', 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <label for="HalioEditVehicleStartingFare" class="col-sm-3 control-label"><?php
      _e('Starting Fare', 'halio');
    ?></label>
    <div class="col-sm-5">
      <div class="input-group">
        <div class="input-group-addon"><?= get_woocommerce_currency_symbol(); ?></div>
        <input type="number" min="0" step="any" class="form-control" id="HalioEditVehicleStartingFare" name="edit_vehicle[starting_fare]" value="<?php echo $vehicle->starting_fare; ?>">
      </div>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The initial price of the fare.', 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <label for="HalioEditVehiclePricerPerUnitDistance" class="col-sm-3 control-label"><?php
      printf(
        __('Price per %s', 'halio'),
        $units
      );
    ?></label>
    <div class="col-sm-5">
      <div class="input-group">
        <div class="input-group-addon"><?= get_woocommerce_currency_symbol(); ?></div>
        <input type="number" min="0" step="any" class="form-control" id="HalioEditVehiclePricerPerUnitDistance" name="edit_vehicle[price_per_unit_distance]" value="<?php echo $vehicle->price_per_unit_distance; ?>">
      </div>
    </div>
    <div class="col-sm-4 helper-text"></div>
  </div>

  <div class="form-group">
    <label for="HalioEditVehiclePricerPerMinute" class="col-sm-3 control-label"><?php
      _e('Price per Minute', 'halio');
    ?></label>
    <div class="col-sm-5">
      <div class="input-group">
        <div class="input-group-addon"><?= get_woocommerce_currency_symbol(); ?></div>
        <input type="number" min="0" step="any" class="form-control" id="HalioEditVehiclePricerPerMinute" name="edit_vehicle[price_per_minute]" value="<?php echo $vehicle->price_per_minute; ?>">
      </div>
    </div>
    <div class="col-sm-4 helper-text"></div>
  </div>

  <div class="form-group">
    <label for="HalioEditVehiclePricerPerOccupant" class="col-sm-3 control-label"><?php
      _e('Price per Occupant', 'halio');
    ?></label>
    <div class="col-sm-5">
      <div class="input-group">
        <div class="input-group-addon"><?= get_woocommerce_currency_symbol(); ?></div>
        <input type="number" min="0" step="any" class="form-control" id="HalioEditVehiclePricerPerOccupant" name="edit_vehicle[price_per_occupant]" value="<?php echo $vehicle->price_per_occupant; ?>">
      </div>
    </div>
    <div class="col-sm-4 helper-text"></div>
  </div>

  <div class="form-group">
    <div class="col-sm-offset-3 col-sm-5 center">
      <input type="submit" value="<?php _e('Update', 'halio'); ?>" class="btn btn-large btn-primary">
    </div>
  </div>
</form>
