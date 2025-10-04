<?php
if (!defined('ABSPATH')) { exit; }


global $wpdb;
$pricing_condition = $wpdb->get_row('SELECT * FROM `' . $wpdb->prefix . 'halio_polygon_pricing_conditions` WHERE id = ' . esc_sql($_GET['polygon_pricing_condition_id']) . ';');
$vehicles = $wpdb->get_results('SELECT * FROM `' . $wpdb->prefix . 'halio_vehicles`');
$pu_polygon_coords = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "halio_polygon_coordinates` WHERE `polygon_pricing_condition_id` = " . $pricing_condition->id . " AND `type` = 'pick_up' ORDER BY `id` ASC;");
$do_polygon_coords = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "halio_polygon_coordinates` WHERE `polygon_pricing_condition_id` = " . $pricing_condition->id . " AND `type` = 'drop_off' ORDER BY `id` ASC;");

$pu_condensed_coords = '';
$do_condensed_coords = '';

foreach ($pu_polygon_coords as $polygon_coord) {
  $pu_condensed_coords .= $polygon_coord->latitude . ',' . $polygon_coord->longitude . '|';
}
foreach ($do_polygon_coords as $polygon_coord) {
  $do_condensed_coords .= $polygon_coord->latitude . ',' . $polygon_coord->longitude . '|';
}

$pu_condensed_coords = rtrim($pu_condensed_coords, "|");
$do_condensed_coords = rtrim($do_condensed_coords, "|");

?><div class="halio-settings-page"><?php

  if ( isset($flash) ) {
    ?><div class="alert alert-<?= $flash['type']; ?>" role="alert">
      <?= $flash['message']; ?>
    </div><?php
  }

  ?><div class="pull-right">
    <a href="<?= admin_url('/admin.php?page=halio-pricing-conditions'); ?>" class="btn btn-default"><?php
      _e('All Pricing Conditions', 'halio');
    ?></a>
  </div>

  <h1 class="header center"><?php
    _e('Edit geolocation-based condition', 'halio');
  ?></h1>

  <form method="post" class="form-horizontal halio-settings-form" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>">

    <input type="hidden" id="HalioMapStartingCountry" value="<?= halio_get_settings_row('map_starting_country')->value; ?>">
    <input type="hidden" name="edit_poly_pricing_condition[id]" value="<?= $pricing_condition->id; ?>">

    <div class="form-group">
      <label for="HalioEditPPCName" class="col-sm-3 control-label"><?php
        _e('Pricing Condition Name', 'halio');
      ?></label>
      <div class="col-sm-5">
        <input type="text" class="form-control halio__ppc--name edit" id="HalioEditPPCName" placeholder="<?php _e('Vehicle', 'halio'); ?>" name="edit_poly_pricing_condition[name]" value="<?= $pricing_condition->name; ?>">
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('The name you use to remember why you created this condition.', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioEditPPCVehicleId" class="col-sm-3 control-label"><?php
        _e('Vehicle', 'halio');
      ?></label>
      <div class="col-sm-5">
        <select class="form-control halio__ppc--vehicle-id edit" name="edit_poly_pricing_condition[vehicle_id]" id="HalioEditPPCVehicleId">
          <option value="0" <?php if ($pricing_condition->vehicle_id == 0) echo 'selected'; ?>><?php
            _e('All Vehicles', 'halio');
          ?></option><?php
          foreach ($vehicles as $vehicle) {
            ?><option value="<?= $vehicle->id; ?>" <?php if ($vehicle->id == $pricing_condition->vehicle_id) echo 'selected'; ?>><?= $vehicle->name; ?></option><?php
          }
        ?></select>
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('The vehicle this rule applies to.', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioEditPPCActive" class="col-sm-3 control-label"><?php
        _e('Active', 'halio');
      ?></label>
      <div class="col-sm-5">
        <select class="form-control halio__ppc--active edit" name="edit_poly_pricing_condition[is_active]" id="HalioEditPPCActive">
          <option value="1" <?php if ($pricing_condition->is_active) echo 'selected'; ?>><?php
            _e('True', 'halio');
          ?></option>
          <option value="0" <?php if (!$pricing_condition->is_active) echo 'selected'; ?>><?php
            _e('False', 'halio');
          ?></option>
        </select>
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('Should this condition be applied to new orders being made?', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioEditPPCIncreaseOrFixed" class="col-sm-3 control-label"><?php
        _e('Fixed Price / Increased Price', 'halio');
      ?></label>
      <div class="col-sm-5">
        <select class="form-control halio__ppc--increase-or-fixed edit" name="edit_poly_pricing_condition[increase_or_fixed]" id="HalioEditPPCIncreaseOrFixed">
          <option value="fixed" <?php if ($pricing_condition->increase_or_fixed == 'fixed') echo 'selected'; ?>><?php
            _e('Fixed Price', 'halio');
          ?></option>
          <option value="increase" <?php if ($pricing_condition->increase_or_fixed == 'increase') echo 'selected'; ?>><?php
            _e('Increase', 'halio');
          ?></option>
        </select>
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('Do you want this rule to make the price <strong>fixed</strong> for this fare, or increase it by the <strong>Increase Amount</strong> and <strong>Increase Multiplier</strong>?', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioEditPPCFixedAmount" class="col-sm-3 control-label"><?php
        _e('Fixed Price', 'halio');
      ?></label>
      <div class="col-sm-5">
        <div class="input-group">
          <div class="input-group-addon"><?= get_woocommerce_currency_symbol(); ?></div>
          <input type="number" min="0" step="any" class="form-control halio__ppc--fixed-amount edit" id="HalioEditPPCFixedAmount" name="edit_poly_pricing_condition[fixed_amount]" value="<?= $pricing_condition->fixed_amount; ?>" <?php if ($pricing_condition->increase_or_fixed == 'increase') echo 'disabled="disabled"'; ?>>
        </div>
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('The amount this fare will be, no matter the distance/time etc.', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioEditPPCIncreaseAmount" class="col-sm-3 control-label"><?php
        _e('Increase Amount', 'halio');
      ?></label>
      <div class="col-sm-5">
        <div class="input-group">
          <div class="input-group-addon"><?= get_woocommerce_currency_symbol(); ?></div>
          <input type="number" min="0" step="any" class="form-control halio__ppc--increase-amount edit" id="HalioEditPPCIncreaseAmount" name="edit_poly_pricing_condition[increase_amount]" value="<?= $pricing_condition->increase_amount; ?>" <?php if ($pricing_condition->increase_or_fixed == 'fixed') echo 'disabled="disabled"'; ?>>
        </div>
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('The amount this condition increases the fare by.', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioEditPPCIncreaseMultiplier" class="col-sm-3 control-label"><?php
        _e('Increase Multiplier', 'halio');
      ?></label>
      <div class="col-sm-5">
        <input type="number" min="0" step="any" class="form-control halio__ppc--increase-multiplier edit" id="HalioEditPPCIncreaseMultiplier" name="edit_poly_pricing_condition[increase_multiplier]" value="<?= $pricing_condition->increase_multiplier; ?>" <?php if ($pricing_condition->increase_or_fixed == 'fixed') echo 'disabled="disabled"'; ?>>
      </div>
      <div class="col-sm-4 helper-text">
        <p><?php
          _e("The multiplier this condition increases the fare by. If you don't want the fare to be multiplied leave the value at 1.", 'halio');
        ?></p>

        <p><?php
          _e("<u>NOTICE:</u> If you are adding an Increased amount <strong>and</strong> an increase multiplier the amount will be added first, then the total will be multiplied.", 'halio');
        ?></p>

        <p><?php
          _e("E.g. <strong>(total_fare + increase_amount) x increase_multiplier.</strong>", 'halio');
        ?></p>
      </div>
    </div>

    <div class="form-group">
      <label for="HalioEditPPCPickUpAreaSelector" class="col-sm-3 control-label"><?php
        _e('Pick Up Specific Area / Anywhere', 'halio');
      ?></label>
      <div class="col-sm-5">
        <select class="form-control halio__ppc--pick-up-area-selector edit" name="edit_poly_pricing_condition[pick_up_location]" id="HalioEditPPCPickUpAreaSelector">
          <option value="anywhere" <?php if ($pricing_condition->pick_up_location == 'anywhere') echo 'selected'; ?>><?php
            _e('Anywhere', 'halio');
          ?></option>
          <option value="specific_area" <?php if ($pricing_condition->pick_up_location == 'specific_area') echo 'selected'; ?>><?php
            _e('Specific Area', 'halio');
          ?></option>
        </select>
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('Do you want this rule to apply to people being picked up in a specific area or anywhere?', 'halio');
      ?></div>
    </div>

    <div class="form-group new-ppc-pick-up-area-map" <?php if ($pricing_condition->pick_up_location == 'anywhere') echo 'style="display: none;"'; ?>>
      <label for="HalioEditPPCPickUpArea" class="col-sm-3 control-label"><?php
        _e('Pick Up Area', 'halio');
      ?></label>
      <input type="hidden" name="edit_poly_pricing_condition[pick_up_coordinates]" id="HalioEditPPCPickUpCoordinates" value="<?= $pu_condensed_coords; ?>" class="halio__ppc--pick-up-coordinates edit">
      <div class="col-sm-5 polygon-pricing-condition-map-container">
        <div class="pricing-condition-map-actions">
          <a href="#" class="btn btn-primary" id="ppc-pick-up-delete-shape"><?php
            _e('Delete Shape', 'halio');
          ?></a>
        </div>

        <div id="polygon_pricing_condition_pick_up_map" data-edit="true"></div>
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('The pick up area this rule applies to.', 'halio');
      ?><br><br><?php
        _e("To delete a shape, click the shape on the map, then press 'Delete Shape'.", 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioEditPPCDropOffAreaSelector" class="col-sm-3 control-label"><?php
        _e('Drop Off Specific Area / Anywhere', 'halio');
      ?></label>
      <div class="col-sm-5">
        <select class="form-control halio__ppc--drop-off-area-selector edit" name="edit_poly_pricing_condition[drop_off_location]" id="HalioEditPPCDropOffAreaSelector">
          <option value="anywhere" <?php if ($pricing_condition->drop_off_location == 'anywhere') echo 'selected'; ?>><?php
            _e('Anywhere', 'halio');
          ?></option>
          <option value="specific_area" <?php if ($pricing_condition->drop_off_location == 'specific_area') echo 'selected'; ?>><?php
            _e('Specific Area', 'halio');
          ?></option>
        </select>
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('Do you want this rule to apply to people being dropped off in a specific area or anywhere?', 'halio');
      ?></div>
    </div>

    <div class="form-group new-ppc-drop-off-area-map" <?php if ($pricing_condition->drop_off_location == 'anywhere') echo 'style="display: none;"'; ?>>
      <label for="HalioEditPPCDropOffArea" class="col-sm-3 control-label"><?php
        _e('Pick Up Area', 'halio');
      ?></label>
      <input type="hidden" name="edit_poly_pricing_condition[drop_off_coordinates]" id="HalioEditPPCDropOffCoordinates" value="<?= $do_condensed_coords; ?>" class="halio__ppc--drop-off-coordinates edit">
      <div class="col-sm-5 polygon-pricing-condition-map-container">
        <div class="pricing-condition-map-actions">
          <a href="#" class="btn btn-primary" id="ppc-drop-off-delete-shape"><?php
            _e('Delete Shape', 'halio');
          ?></a>
        </div>

        <div id="polygon_pricing_condition_drop_off_map"></div>
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('The drop off area this rule applies to.', 'halio');
      ?><br><br><?php
        _e("To delete a shape, click the shape on the map, then press 'Delete Shape'.", 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioEditPPCOneWayOrBoth" class="col-sm-3 control-label"><?php
        _e('One Directional or Each Way', 'halio');
      ?></label>
      <div class="col-sm-5">
        <select class="form-control halio__ppc--one-way-or-both edit" name="edit_poly_pricing_condition[one_way_or_both]" id="HalioEditPPCOneWayOrBoth">
          <option value="one_way" <?php if ($pricing_condition->one_way_or_both == 'one_way') echo 'selected'; ?>><?php
            _e('One Direction', 'halio');
          ?></option>
          <option value="each_way" <?php if ($pricing_condition->one_way_or_both == 'each_way') echo 'selected'; ?>><?php
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
        <input type="submit" value="<?php _e('Update', 'halio'); ?>" class="btn btn-large btn-primary">
      </div>
    </div>
  </form>
</div>
