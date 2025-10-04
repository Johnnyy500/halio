<?php

global $wpdb;
$pricing_condition = $wpdb->get_row("SELECT * FROM `" . $wpdb->prefix . "halio_time_pricing_conditions` WHERE id = " . esc_sql($_GET['time_pricing_condition_id']) . ";");
$vehicles = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "halio_vehicles`");

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
    _e('Edit time-based condition', 'halio');
  ?></h1>

  <form method="post" class="form-horizontal halio-settings-form" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>">

    <input type="hidden" name="edit_time_pricing_condition[id]" value="<?= $pricing_condition->id; ?>">

    <div class="form-group">
      <label for="HalioEditTPCName" class="col-sm-3 control-label"><?php
        _e('Pricing Condition Name', 'halio');
      ?></label>
      <div class="col-sm-5">
        <input type="text" class="form-control halio__tpc--name edit" id="HalioEditTPCName" placeholder="<?php _e('Name', 'halio'); ?>" name="edit_time_pricing_condition[name]" value="<?= $pricing_condition->name; ?>">
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('The name you use to remember why you created this condition.', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioEditTPCActive" class="col-sm-3 control-label"><?php
        _e('Active?', 'halio');
      ?></label>
      <div class="col-sm-5">
        <select class="form-control halio__tpc--active edit" name="edit_time_pricing_condition[is_active]" id="HalioEditTPCActive">
          <option value="1" <?php if ($pricing_condition->is_active) echo 'selected'; ?>><?php
            _e('True', 'halio');
          ?></option>
          <option value="0" <?php if (!$pricing_condition->is_active) echo 'selected'; ?>><?php
            _e('False', 'halio');
          ?></option>
        </select>
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('The vehicle this condition applies to.', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioEditTPCStartingTime" class="col-sm-3 control-label"><?php
        _e('Start Time', 'halio');
      ?></label>
      <div class="col-sm-5">
        <input type="time" class="form-control halio__tpc--start-time edit" id="HalioEditTPCStartingTime" name="edit_time_pricing_condition[starting_time]" value="<?= $pricing_condition->starting_time; ?>">
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('The starting time of the pricing condition (HH:MM), <strong>24 hour format</strong>.', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioEditTPCEndingTime" class="col-sm-3 control-label"><?php
        _e('End Time', 'halio');
      ?></label>
      <div class="col-sm-5">
        <input type="time" class="form-control halio__tpc--ending-time edit" id="HalioEditTPCEndingTime" name="edit_time_pricing_condition[ending_time]" value="<?= $pricing_condition->ending_time; ?>">
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('The ending time of the pricing condition (HH:MM), <strong>24 hour format</strong>.', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioEditTPCDays" class="col-sm-3 control-label"><?php
        _e('Days', 'halio');
      ?></label>
      <div class="col-sm-5">
        <div class="checkbox">
          <label>
            <input type="checkbox" name="edit_time_pricing_condition[monday]" value="1" <?php if ($pricing_condition->monday) echo 'checked'; ?>><?php
            _e('Monday', 'halio');
          ?></label>
        </div>
        <div class="checkbox">
          <label>
            <input type="checkbox" name="edit_time_pricing_condition[tuesday]" value="1" <?php if ($pricing_condition->tuesday) echo 'checked'; ?>><?php
            _e('Tuesday', 'halio');
          ?></label>
        </div>
        <div class="checkbox">
          <label>
            <input type="checkbox" name="edit_time_pricing_condition[wednesday]" value="1" <?php if ($pricing_condition->wednesday) echo 'checked'; ?>><?php
            _e('Wednesday', 'halio');
          ?></label>
        </div>
        <div class="checkbox">
          <label>
            <input type="checkbox" name="edit_time_pricing_condition[thursday]" value="1" <?php if ($pricing_condition->thursday) echo 'checked'; ?>><?php
            _e('Thursday', 'halio');
          ?></label>
        </div>
        <div class="checkbox">
          <label>
            <input type="checkbox" name="edit_time_pricing_condition[friday]" value="1" <?php if ($pricing_condition->friday) echo 'checked'; ?>><?php
            _e('Friday', 'halio');
          ?></label>
        </div>
        <div class="checkbox">
          <label>
            <input type="checkbox" name="edit_time_pricing_condition[saturday]" value="1" <?php if ($pricing_condition->saturday) echo 'checked'; ?>><?php
            _e('Saturday', 'halio');
          ?></label>
        </div>
        <div class="checkbox">
          <label>
            <input type="checkbox" name="edit_time_pricing_condition[sunday]" value="1" <?php if ($pricing_condition->sunday) echo 'checked'; ?>><?php
            _e('Sunday', 'halio');
          ?></label>
        </div>
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('The days this pricing condition applies to.', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioEditTPCVehicleId" class="col-sm-3 control-label"><?php
        _e('Vehicle', 'halio');
      ?></label>
      <div class="col-sm-5">
        <select class="form-control halio__tpc--vehicle-id edit" name="edit_time_pricing_condition[vehicle_id]" id="HalioEditTPCVehicleId">
          <option disabled selected><?php
            _e('Please select a vehicle...', 'halio');
          ?></option>
          <option value="0" <?php if ($pricing_condition->vehicle_id == 0) echo 'selected'; ?>><?php
            _e('All Vehicles', 'halio');
          ?></option><?php
          foreach ($vehicles as $vehicle) {
            ?><option value="<?= $vehicle->id; ?>"  <?php if ($pricing_condition->vehicle_id == $vehicle->id) echo 'selected'; ?>><?= $vehicle->name; ?></option><?php
          }
        ?></select>
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('The vehicle this condition applies to.', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioEditTPCIncreaseAmount" class="col-sm-3 control-label"><?php
        _e('Increase Amount', 'halio');
      ?></label>
      <div class="col-sm-5">
        <div class="input-group">
          <div class="input-group-addon"><?= get_woocommerce_currency_symbol(); ?></div>
          <input type="number" min="0" step="any" class="form-control halio__tpc--increase-amount edit" id="HalioEditTPCIncreaseAmount" name="edit_time_pricing_condition[increase_amount]" value="<?= $pricing_condition->increase_amount; ?>">
        </div>
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('The amount this condition increases the fare by.', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioEditTPCIncreaseMultiplier" class="col-sm-3 control-label"><?php
        _e('Increase Multiplier', 'halio');
      ?></label>
      <div class="col-sm-5">
        <input type="number" min="0" step="any" class="form-control halio__tpc--multiplier edit" id="HalioEditTPCIncreaseMultiplier" name="edit_time_pricing_condition[increase_multiplier]" value="<?= $pricing_condition->increase_multiplier; ?>">
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
      <div class="col-sm-offset-3 col-sm-5 center">
        <input type="submit" value="<?php _e('Update', 'halio'); ?>" class="btn btn-large btn-primary">
      </div>
    </div>
  </form>
</div>
