<?php
if (!defined('ABSPATH')) { exit; }


$time_pricing_conditions = $wpdb->get_results('SELECT * FROM `' . $wpdb->prefix . 'halio_time_pricing_conditions`');

?><table class="table table-bordered table-hover">
  <thead>
    <tr>
      <th><?php _e('Name', 'halio'); ?></th>
      <th><?php _e('Start Time', 'halio'); ?></th>
      <th><?php _e('End Time', 'halio'); ?></th>
      <th><?php _e('Days', 'halio'); ?></th>
      <th><?php _e('Vehicle', 'halio'); ?></th>
      <th><?php _e('Increase Amount', 'halio'); ?></th>
      <th><?php _e('Increase Multiplier', 'halio'); ?></th>
      <th><?php _e('Active?', 'halio'); ?></th>
      <th><?php _e('Actions', 'halio'); ?></th>
    </tr>
  </thead>
  <tbody><?php
    if ( !empty($time_pricing_conditions) ) {
      foreach ($time_pricing_conditions as $time_pricing_condition) {
        ?><tr>
          <td>
            <a href="<?= halio_edit_time_pricing_condition_path($time_pricing_condition->id); ?>"><?= $time_pricing_condition->name; ?></a>
          </td>
          <td><?= $time_pricing_condition->starting_time; ?></td>
          <td><?= $time_pricing_condition->ending_time; ?></td>
          <td><?php
            $days = array(
              'monday' => __('Monday', 'halio'),
              'tuesday' => __('Tuesday', 'halio'),
              'wednesday' => __('Wednesday', 'halio'),
              'thursday' => __('Thursday', 'halio'),
              'friday' => __('Friday', 'halio'),
              'saturday' => __('Saturday', 'halio'),
              'sunday' => __('Sunday', 'halio')
            );
          ?><ul><?php
              foreach ($days as $day => $translation) {
                if ($time_pricing_condition->{$day}) {
                  ?><li><?= ucwords($translation); ?></li><?php
                }
              }
            ?></ul>
          </td>
          <td><?php
            if ($time_pricing_condition->vehicle_id == 0) {
              _e('All Vehicles', 'halio');
            } else {
              $vehicle = $wpdb->get_row("SELECT * FROM `" . $wpdb->prefix . "halio_vehicles` WHERE `id` = '" . $time_pricing_condition->vehicle_id . "';");
              echo '<a href="' . halio_edit_vehicle_path($vehicle->id) . '">' . $vehicle->name . '</a>';
            }
          ?></td>
          <td>
            <?= get_woocommerce_currency_symbol(); ?>
            <?= $time_pricing_condition->increase_amount; ?>
          </td>
          <td><?= $time_pricing_condition->increase_multiplier; ?></td>
          <td>
            <div class="halio-is-active-icon-container">
              <?= $time_pricing_condition->is_active == 1 ? '<i class="fa fa-check halio-is-active-icon"></i>' : '<i class="fa fa-times halio-is-active-icon"></i>'; ?>
            </div>
          </td>
          <td>
            <form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>" onsubmit="return confirm('<?php _e('Are you sure you want to delete this Pricing Condition?', 'halio'); ?>');" class="table-action-form">
              <input type="hidden" name="delete_time_pricing_condition[id]" value="<?php echo $time_pricing_condition->id; ?>">
              <input type="submit" value="<?php _e('Delete', 'halio'); ?>" class="btn btn-danger pricing-condition-action-button">
            </form>
            <form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>" class="table-action-form">
              <input type="hidden" name="change_time_pricing_condition[id]" value="<?= $time_pricing_condition->id; ?>">
              <input type="hidden" name="change_time_pricing_condition[action]" value="<?= $time_pricing_condition->is_active ? 'deactivate' : 'activate'; ?>"><?php
              if ($time_pricing_condition->is_active) {
                ?><input type="submit" value="<?php _e('Deactivate', 'halio'); ?>" class="btn btn-warning"><?php
              } else {
                ?><input type="submit" value="<?php _e('Activate', 'halio'); ?>" class="btn btn-success"><?php
              }
            ?></form>
            <a href="<?= halio_edit_time_pricing_condition_path($time_pricing_condition->id); ?>" class="btn btn-default">Edit</a>
          </td>
        </tr><?php
      }
    } else {
      ?><tr class="info">
        <td colspan="9" class="center"><?php
          _e('No results found.', 'halio');
        ?></td>
      </tr><?php
    }
  ?></tbody>
</table>

<h3 class="header center"><?php
  _e('Create a new time-based condition', 'halio');
?></h3>

<form method="post" class="form-horizontal halio-settings-form" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>">

  <input type="hidden" id="HalioMapStartingCountry" value="<?= halio_get_settings_row('map_starting_country')->value; ?>">

  <div class="form-group">
    <label for="HalioNewTPCName" class="col-sm-3 control-label"><?php
      _e('Pricing Condition Name', 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" class="form-control halio__tpc--name new" id="HalioNewTPCName" placeholder="<?php _e('Name', 'halio'); ?>" name="time_pricing_condition[name]">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The name you use to remember why you created this condition.', 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <label for="HalioNewTPCStartingTime" class="col-sm-3 control-label"><?php
      _e('Start Time', 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="time" class="form-control halio__tpc--start-time new" id="HalioNewTPCStartingTime" name="time_pricing_condition[starting_time]">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The starting time of the pricing condition (HH:MM), <strong>24 hour format</strong>.', 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <label for="HalioNewTPCEndingTime" class="col-sm-3 control-label"><?php
      _e('End Time', 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="time" class="form-control halio__tpc--ending-time new" id="HalioNewTPCEndingTime" name="time_pricing_condition[ending_time]">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The ending time of the pricing condition (HH:MM), <strong>24 hour format</strong>.', 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <label for="HalioNewTPCDays" class="col-sm-3 control-label"><?php
      _e('Days', 'halio');
    ?></label>
    <div class="col-sm-5">
      <div class="checkbox">
        <label>
          <input type="checkbox" name="time_pricing_condition[monday]" value="1"><?php
          _e('Monday', 'halio');
        ?></label>
      </div>
      <div class="checkbox">
        <label>
          <input type="checkbox" name="time_pricing_condition[tuesday]" value="1"><?php
          _e('Tuesday', 'halio');
        ?></label>
      </div>
      <div class="checkbox">
        <label>
          <input type="checkbox" name="time_pricing_condition[wednesday]" value="1"><?php
          _e('Wednesday', 'halio');
        ?></label>
      </div>
      <div class="checkbox">
        <label>
          <input type="checkbox" name="time_pricing_condition[thursday]" value="1"><?php
          _e('Thursday', 'halio');
        ?></label>
      </div>
      <div class="checkbox">
        <label>
          <input type="checkbox" name="time_pricing_condition[friday]" value="1"><?php
          _e('Friday', 'halio');
        ?></label>
      </div>
      <div class="checkbox">
        <label>
          <input type="checkbox" name="time_pricing_condition[saturday]" value="1"><?php
          _e('Saturday', 'halio');
        ?></label>
      </div>
      <div class="checkbox">
        <label>
          <input type="checkbox" name="time_pricing_condition[sunday]" value="1"><?php
          _e('Sunday', 'halio');
        ?></label>
      </div>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The days this pricing condition applies to.', 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <label for="HalioNewTPCVehicleId" class="col-sm-3 control-label"><?php
      _e('Vehicle', 'halio');
    ?></label>
    <div class="col-sm-5">
      <select class="form-control halio__tpc--vehicle-id new" name="time_pricing_condition[vehicle_id]" id="HalioNewTPCVehicleId">
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
    <label for="HalioNewTPCIncreaseAmount" class="col-sm-3 control-label"><?php
      _e('Increase Amount', 'halio');
    ?></label>
    <div class="col-sm-5">
      <div class="input-group">
        <div class="input-group-addon"><?= get_woocommerce_currency_symbol(); ?></div>
        <input type="number" min="0" step="any" class="form-control halio__tpc--increase-amount new" id="HalioNewTPCIncreaseAmount" name="time_pricing_condition[increase_amount]" value="0.00">
      </div>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The amount this condition increases the fare by.', 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <label for="HalioNewTPCIncreaseMultiplier" class="col-sm-3 control-label"><?php
      _e('Increase Multiplier', 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="number" min="0" step="any" class="form-control halio__tpc--multiplier new" id="HalioNewTPCIncreaseMultiplier" name="time_pricing_condition[increase_multiplier]" value="1">
    </div>
    <div class="col-sm-4 helper-text">
      <p><?php
        _e("The multiplier this condition increases the fare by. <strong>If you don't want the fare to be multiplied leave the value at 1.</strong>", 'halio');
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
    <div class="col-sm-offset-3 col-sm-5 center">
      <input type="submit" value="<?php _e('Create', 'halio'); ?>" class="btn btn-large btn-primary">
    </div>
  </div>
</form>
