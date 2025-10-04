<?php

$ppc = $wpdb->get_results('SELECT * FROM `' . $wpdb->prefix . 'halio_polygon_pricing_conditions`');

?><h2 class="header center"><?php
  _e('Geolocation-based conditions', 'halio');
?></h2>

<table class="table table-bordered table-hover">
  <thead>
    <tr>
      <th><?php _e('Name', 'halio'); ?></th>
      <th><?php _e('Vehicle', 'halio'); ?></th>
      <th><?php _e('Fixed Price / Increase Amount', 'halio'); ?></th>
      <th><?php _e('Fixed Price', 'halio'); ?></th>
      <th><?php _e('Increase Amount', 'halio'); ?></th>
      <th><?php _e('Increase Multiplier', 'halio'); ?></th>
      <th><?php _e('One Way / Both Ways', 'halio'); ?></th>
      <th><?php _e('Active?', 'halio'); ?></th>
      <th><?php _e('Actions', 'halio'); ?></th>
    </tr>
  </thead>
  <tbody><?php
    if ( !empty($ppc) ) {
      foreach($ppc as $poly_pricing_condition) {
        ?><tr>
          <td>
            <a href="<?= halio_edit_polygon_pricing_condition_path($poly_pricing_condition->id); ?>"><?= $poly_pricing_condition->name; ?></a>
          </td>
          <td><?php
            if ($poly_pricing_condition->vehicle_id == 0) {
              echo 'All Vehicles';
            } else {
              $vehicle = $wpdb->get_row("SELECT * FROM `" . $wpdb->prefix . "halio_vehicles` WHERE `id` = '" . $poly_pricing_condition->vehicle_id . "';");
              echo '<a href="' . halio_edit_vehicle_path($vehicle->id) . '">' . $vehicle->name . '</a>';
            }
          ?></td>
          <td><?php
            switch ($poly_pricing_condition->increase_or_fixed) {
              case 'increase':
                _e('Increase', 'halio');
                break;
              case 'fixed':
                _e('Fixed Price', 'halio');
                break;
            }
          ?></td>
          <td><?php
            if ($poly_pricing_condition->increase_or_fixed == 'increase') {
              _e('N/A', 'halio');
            } else {
              echo get_woocommerce_currency_symbol() . $poly_pricing_condition->fixed_amount;
            }
          ?></td>
          <td><?php
            if ($poly_pricing_condition->increase_or_fixed == 'fixed') {
              _e('N/A', 'halio');
            } else {
              echo get_woocommerce_currency_symbol() . $poly_pricing_condition->increase_amount;
            }
          ?></td>
          <td><?php
            if ($poly_pricing_condition->increase_or_fixed == 'fixed') {
              _e('N/A', 'halio');
            } else {
              echo $poly_pricing_condition->increase_multiplier;
            }
          ?></td>
          <td><?php
            if ($poly_pricing_condition->one_way_or_both == 'one_way') {
              _e('One Way', 'halio');
            } else {
              _e('Each Way', 'halio');
            }
          ?></td>
          <td>
            <div class="halio-is-active-icon-container">
              <?= $poly_pricing_condition->is_active == 1 ? '<i class="fa fa-check halio-is-active-icon"></i>' : '<i class="fa fa-times halio-is-active-icon"></i>'; ?>
            </div>
          </td>
          <td>
            <form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>" onsubmit="return confirm('<?php _e('Are you sure you want to delete this Pricing Condition?', 'halio'); ?>');" class="table-action-form">
              <input type="hidden" name="delete_poly_pricing_condition[id]" value="<?php echo $poly_pricing_condition->id; ?>">
              <input type="submit" value="<?php _e('Delete', 'halio'); ?>" class="btn btn-danger pricing-condition-action-button">
            </form>
            <form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>" class="table-action-form">
              <input type="hidden" name="change_poly_pricing_condition[id]" value="<?= $poly_pricing_condition->id; ?>">
              <input type="hidden" name="change_poly_pricing_condition[action]" value="<?= $poly_pricing_condition->is_active ? 'deactivate' : 'activate'; ?>"><?php
              if ($poly_pricing_condition->is_active) {
                ?><input type="submit" value="<?php _e('Deactivate', 'halio'); ?>" class="btn btn-warning"><?php
              } else {
                ?><input type="submit" value="<?php _e('Activate', 'halio'); ?>" class="btn btn-success"><?php
              }
            ?></form>
            <form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>" class="table-action-form">
              <input type="hidden" name="copy_polygon_pricing_condition[id]" value="<?= $poly_pricing_condition->id; ?>">
              <input type="submit" value="<?php _e('Copy', 'halio'); ?>" class="btn btn-primary">
            </form>
            <a href="<?= halio_edit_polygon_pricing_condition_path($poly_pricing_condition->id); ?>" class="btn btn-default"><?php _e('Edit', 'halio'); ?></a>
          </td>
        </tr><?php
      }
    } else {
      ?><tr class="info">
        <td colspan="10" class="center"><?php _e('No results found.', 'halio'); ?></td>
      </tr><?php
    }
  ?></tbody>
</table>

<h3 class="header center"><?php
  _e('Create a new geolocation-based condition', 'halio');
?></h3>

<form method="post" class="form-horizontal halio-settings-form" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>">

  <input type="hidden" id="HalioMapStartingCountry" value="<?= halio_get_settings_row('map_starting_country')->value; ?>">

  <div class="form-group">
    <label for="HalioNewPPCName" class="col-sm-3 control-label"><?php
      _e('Pricing Condition Name', 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" class="form-control halio__ppc--name new" id="HalioNewPPCName" placeholder="<?php _e('Name', 'halio'); ?>" name="poly_pricing_condition[name]">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The name you use to remember why you created this condition.', 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <label for="HalioNewPPCVehicleId" class="col-sm-3 control-label"><?php
      _e('Vehicle', 'halio');
    ?></label>
    <div class="col-sm-5">
      <select class="form-control halio__ppc--vehicle-id new" name="poly_pricing_condition[vehicle_id]" id="HalioNewPPCVehicleId">
        <option disabled selected><?php
          _e('Please select a vehicle...', 'halio');
        ?></option>
        <option value="0"><?php
          _e('All Vehicles', 'halio');
        ?></option><?php
        foreach ($vehicles as $vehicle) {
          ?><option value="<?= $vehicle->id; ?>"><?= $vehicle->name; ?></option><?php
        }
      ?></select>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The vehicle this condition applies to.', 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <label for="HalioNewPPCIncreaseOrFixed" class="col-sm-3 control-label"><?php
      _e('Fixed Price / Increased Price', 'halio');
    ?></label>
    <div class="col-sm-5">
      <select class="form-control halio__ppc--increase-or-fixed new" name="poly_pricing_condition[increase_or_fixed]" id="HalioNewPPCIncreaseOrFixed">
        <option disabled selected><?php
          _e('Please select a pricing option...', 'halio');
        ?></option>
        <option value="fixed"><?php
          _e('Fixed Price', 'halio');
        ?></option>
        <option value="increase"><?php
          _e('Increase', 'halio');
        ?></option>
      </select>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('Do you want this condition to make the price <strong>fixed</strong> for this fare, or increase it by the <strong>Increase Amount</strong> and <strong>Increase Multiplier</strong>?', 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <label for="HalioNewPPCFixedAmount" class="col-sm-3 control-label"><?php
      _e('Fixed Price', 'halio');
    ?></label>
    <div class="col-sm-5">
      <div class="input-group">
        <div class="input-group-addon"><?= get_woocommerce_currency_symbol(); ?></div>
        <input type="number" min="0" step="any" class="form-control halio__ppc--fixed-amount new" id="HalioNewPPCFixedAmount" name="poly_pricing_condition[fixed_amount]" value="0.00">
      </div>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The amount this fare will be, no matter the distance/time etc.', 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <label for="HalioNewPPCIncreaseAmount" class="col-sm-3 control-label"><?php
      _e('Increase Amount', 'halio');
    ?></label>
    <div class="col-sm-5">
      <div class="input-group">
        <div class="input-group-addon"><?= get_woocommerce_currency_symbol(); ?></div>
        <input type="number" min="0" step="any" class="form-control halio__ppc--increase-amount new" id="HalioNewPPCIncreaseAmount" name="poly_pricing_condition[increase_amount]" value="0.00" disabled="disabled">
      </div>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The amount this condition increases the fare by.', 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <label for="HalioNewPPCIncreaseMultiplier" class="col-sm-3 control-label"><?php
      _e('Increase Multiplier', 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="number" min="0" step="any" class="form-control halio__ppc--increase-multiplier new" id="HalioNewPPCIncreaseMultiplier" name="poly_pricing_condition[increase_multiplier]" value="1" disabled="disabled">
    </div>
    <div class="col-sm-4 helper-text">
      <p><?php
        _e("The multiplier this condition increases the fare by. If you don't want the fare to be multiplied leave the value at 1.", 'halio');
      ?></p>

      <p><?php
        _e('<u>NOTICE:</u> If you are adding an Increased amount <strong>and</strong> an increase multiplier the amount will be added first, then the total will be multiplied.', 'halio');
      ?></p>

      <p><?php
        _e('E.g. <strong>(total_fare + increase_amount) x increase_multiplier.</strong>', 'halio');
      ?></p>
    </div>
  </div>

  <div class="form-group">
    <label for="HalioNewPPCPickUpAreaSelector" class="col-sm-3 control-label"><?php
      _e('Pick Up Specific Area / Anywhere', 'halio');
    ?></label>
    <div class="col-sm-5">
      <select class="form-control halio__ppc--pick-up-area-selector new" name="poly_pricing_condition[pick_up_location]" id="HalioNewPPCPickUpAreaSelector">
        <option disabled selected><?php
          _e('Please select a pick up location constraint...', 'halio');
        ?></option>
        <option value="anywhere"><?php
          _e('Anywhere', 'halio');
        ?></option>
        <option value="specific_area"><?php
          _e('Specific Area', 'halio');
        ?></option>
      </select>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('Do you want this condition to apply to people being picked up in a specific area or anywhere?', 'halio');
    ?></div>
  </div>

  <div class="form-group new-ppc-pick-up-area-map" style="display: none;">
    <label for="HalioNewPPCPickUpArea" class="col-sm-3 control-label"><?php
      _e('Pick Up Area', 'halio');
    ?></label>
    <input type="hidden" name="poly_pricing_condition[pick_up_coordinates]" id="HalioNewPPCPickUpCoordinates" class="halio__ppc--pick-up-coordinates new">
    <div class="col-sm-5 polygon-pricing-condition-map-container">
      <div class="pricing-condition-map-actions">
        <a href="#" class="btn btn-primary" id="ppc-pick-up-delete-shape"><?php
          _e('Delete Shape', 'halio');
        ?></a>
      </div>

      <div id="polygon_pricing_condition_pick_up_map"></div>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The area this condition applies to.', 'halio');
    ?><br><br><?php
      _e("To delete a shape, click the shape on the map, then press 'Delete Shape'.", 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <label for="HalioNewPPCDropOffAreaSelector" class="col-sm-3 control-label"><?php
      _e('Drop Off Specific Area / Anywhere', 'halio');
    ?></label>
    <div class="col-sm-5">
      <select class="form-control halio__ppc--drop-off-area-selector new" name="poly_pricing_condition[drop_off_location]" id="HalioNewPPCDropOffAreaSelector">
        <option disabled selected><?php
          _e('Please select a drop off location constraint...', 'halio');
        ?></option>
        <option value="anywhere"><?php
          _e('Anywhere', 'halio');
        ?></option>
        <option value="specific_area"><?php
          _e('Specific Area', 'halio');
        ?></option>
      </select>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('Do you want this condition to apply to people being dropped off in a specific area or anywhere?', 'halio');
    ?></div>
  </div>

  <div class="form-group new-ppc-drop-off-area-map" style="display: none;">
    <label for="HalioNewPPCDropOffArea" class="col-sm-3 control-label"><?php
      _e('Drop Off Area', 'halio');
    ?></label>
    <input type="hidden" name="poly_pricing_condition[drop_off_coordinates]" id="HalioNewPPCDropOffCoordinates" class="halio__ppc--drop-off-coordinates new">
    <div class="col-sm-5 polygon-pricing-condition-map-container">
      <div class="pricing-condition-map-actions">
        <a href="#" class="btn btn-primary" id="ppc-drop-off-delete-shape"><?php
          _e('Delete Shape', 'halio');
        ?></a>
      </div>

      <div id="polygon_pricing_condition_drop_off_map"></div>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The area this condition applies to.', 'halio');
    ?><br><br><?php
      _e("To delete a shape, click the shape on the map, then press 'Delete Shape'.", 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <label for="HalioNewPPCOneWayOrBoth" class="col-sm-3 control-label"><?php
      _e('One Directional or Each Way', 'halio');
    ?></label>
    <div class="col-sm-5">
      <select class="form-control halio__ppc--one-way-or-both new" name="poly_pricing_condition[one_way_or_both]" id="HalioNewPPCOneWayOrBoth">
        <option value="one_way"><?php
          _e('One Direction', 'halio');
        ?></option>
        <option value="each_way"><?php
          _e('Each Way', 'halio');
        ?></option>
      </select>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('Do you want this condition to apply only from the starting area specified -> destination area specified or do you want it to apply to customers picked up/dropped off in either area?', 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <div class="col-sm-offset-3 col-sm-5 center">
      <input type="submit" value="<?php _e('Create', 'halio'); ?>" class="btn btn-large btn-primary">
    </div>
  </div>
</form>
