<?php

global $wpdb;
$custom_checkout_fields = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "halio_custom_checkout_fields`;");

?>

<h2 class="header center"><?php
  _e('Checkout Customisation', 'halio');
?></h2>

<table class="table table-bordered table-hover">
  <thead>
    <tr>
      <th><?php _e('Label', 'halio'); ?></th>
      <th><?php _e('Placeholder', 'halio'); ?></th>
      <th><?php _e('Required?', 'halio'); ?></th>
      <th><?php _e('Active?', 'halio'); ?></th>
      <th><?php _e('Actions', 'halio'); ?></th>
    </tr>
  </thead>
  <tbody><?php
    if ( !empty($custom_checkout_fields) ) {
      foreach($custom_checkout_fields as $custom_field) {
        ?><tr>
          <td>
            <a href="<?= halio_edit_checkout_field_path($custom_field->id); ?>">
              <?= $custom_field->label; ?>
            </a>
          </td>
          <td><?= $custom_field->placeholder; ?></td>
          <td>
            <div class="halio-is-active-icon-container">
              <?= $custom_field->is_required == 1 ? '<i class="fa fa-check halio-is-active-icon"></i>' : '<i class="fa fa-times halio-is-active-icon"></i>'; ?>
            </div>
          </td>
          <td>
            <div class="halio-is-active-icon-container">
              <?= $custom_field->is_active == 1 ? '<i class="fa fa-check halio-is-active-icon"></i>' : '<i class="fa fa-times halio-is-active-icon"></i>'; ?>
            </div>
          </td>
          <td>
            <form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>" onsubmit="return confirm('<?php _e('Are you sure you want to delete this Checkout Field?', 'halio'); ?>');" class="table-action-form">
              <input type="hidden" name="delete_checkout_field[id]" value="<?php echo $custom_field->id; ?>">
              <input type="submit" value="<?php _e('Delete', 'halio'); ?>" class="btn btn-danger pricing-condition-action-button">
            </form>
            <form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>" class="table-action-form">
              <input type="hidden" name="change_checkout_field[id]" value="<?= $custom_field->id; ?>">
              <input type="hidden" name="change_checkout_field[action]" value="<?= $custom_field->is_active ? 'deactivate' : 'activate'; ?>"><?php
              if ($custom_field->is_active) {
                ?><input type="submit" value="<?php _e('Deactivate', 'halio'); ?>" class="btn btn-warning"><?php
              } else {
                ?><input type="submit" value="<?php _e('Activate', 'halio'); ?>" class="btn btn-success"><?php
              }
            ?></form>
            <a href="<?= halio_edit_checkout_field_path($custom_field->id); ?>" class="btn btn-default"><?php _e('Edit', 'halio'); ?></a>
          </td>
        </tr><?php
      }
    } else {
      ?><tr class="info">
        <td colspan="5" class="center"><?php _e('No results found.', 'halio'); ?></td>
      </tr><?php
    }
  ?></tbody>
</table>

<h3 class="header center"><?php
  _e('Create a new custom checkout field that can be added to orders', 'halio');
?></h3>

<form method="post" class="form-horizontal halio-settings-form" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>">
  <div class="form-group">
    <label for="HalioNewCheckoutFieldLabel" class="col-sm-3 control-label"><?php
      _e('Field Label', 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" class="form-control halio__checkout-field--label new" id="HalioNewCheckoutFieldLabel" placeholder="<?php _e('Label', 'halio'); ?>" name="new_checkout_field[label]">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The label displayed next to the input field.', 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <label for="HalioNewCheckoutFieldPlaceholder" class="col-sm-3 control-label"><?php
      _e('Field Placeholder', 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" class="form-control halio__checkout-field--placeholder new" id="HalioNewCheckoutFieldPlaceholder" placeholder="<?php _e('Placeholder', 'halio'); ?>" name="new_checkout_field[placeholder]">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The text displayed in the text field if nothing has been input.', 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <label for="HalioNewCheckoutFieldRequired" class="col-sm-3 control-label"><?php
      _e('Required?', 'halio');
    ?></label>
    <div class="col-sm-5">
      <select class="form-control halio__checkout-field--required new" name="new_checkout_field[is_required]" id="HalioNewCheckoutFieldRequired">
        <option value="1"><?php
          _e('True', 'halio');
        ?></option>
        <option value="0"><?php
          _e('False', 'halio');
        ?></option>
      </select>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('Does the user have to provide a value for this field or can they pay for the service without a value?', 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <label for="HalioNewCheckoutFieldActive" class="col-sm-3 control-label"><?php
      _e('Active?', 'halio');
    ?></label>
    <div class="col-sm-5">
      <select class="form-control halio__checkout-field--active new" name="new_checkout_field[is_active]" id="HalioNewCheckoutFieldActive">
        <option value="1"><?php
          _e('True', 'halio');
        ?></option>
        <option value="0"><?php
          _e('False', 'halio');
        ?></option>
      </select>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('Will this field be activated on creation?', 'halio');
    ?></div>
  </div>

  <div class="form-group">
    <div class="col-sm-offset-3 col-sm-5 center">
      <input type="submit" value="<?php _e('Create', 'halio'); ?>" class="btn btn-large btn-primary">
    </div>
  </div>
</form>

<h4 class="col-sm-offset-3 col-sm-5 header"><?php _e('Labels', 'halio'); ?></h4>

<form class="form-horizontal halio-settings-form" method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>"><?php

  $starting_address_label = halio_get_settings_row('checkout_starting_address_label');
  ?><div class="form-group">
    <label for="HalioFormSettingStartingAddressLabel" class="col-sm-3 control-label"><?php
      _e("Label for 'Starting Address' in checkout", 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Starting Address label', 'halio'); ?>" class="form-control" id="HalioFormSettingStartingAddressLabel" name="setting[<?= $starting_address_label->id; ?>][value]" value="<?= $starting_address_label->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The label on the checkout page for starting address.', 'halio');
    ?></div>
  </div><?php

  $destination_address_label = halio_get_settings_row('checkout_destination_address_label');
  ?><div class="form-group">
    <label for="HalioFormSettingDestinationAddressLabel" class="col-sm-3 control-label"><?php
      _e("Label for 'Destination Address' in checkout", 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Destination Address label', 'halio'); ?>" class="form-control" id="HalioFormSettingDestinationAddressLabel" name="setting[<?= $destination_address_label->id; ?>][value]" value="<?= $destination_address_label->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The label on the checkout page for destination address.', 'halio');
    ?></div>
  </div><?php

  $vehicle_label = halio_get_settings_row('checkout_vehicle_label');
  ?><div class="form-group">
    <label for="HalioFormSettingVehicleLabel" class="col-sm-3 control-label"><?php
      _e("Label for 'Vehicle' in checkout", 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Vehicle label', 'halio'); ?>" class="form-control" id="HalioFormSettingVehicleLabel" name="setting[<?= $vehicle_label->id; ?>][value]" value="<?= $vehicle_label->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The label on the checkout page for vehicle.', 'halio');
    ?></div>
  </div><?php

  $occupants_label = halio_get_settings_row('checkout_occupants_label');
  ?><div class="form-group">
    <label for="HalioFormSettingOccupantsLabel" class="col-sm-3 control-label"><?php
      _e("Label for 'Occupants' in checkout", 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Occupants label', 'halio'); ?>" class="form-control" id="HalioFormSettingOccupantsLabel" name="setting[<?= $occupants_label->id; ?>][value]" value="<?= $occupants_label->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The label on the checkout page for occupants.', 'halio');
    ?></div>
  </div><?php

  $pick_up_time_label = halio_get_settings_row('checkout_pick_up_time_label');
  ?><div class="form-group">
    <label for="HalioFormSettingPickUpTimeLabel" class="col-sm-3 control-label"><?php
      _e("Label for 'Pick Up Time' in checkout", 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Pick Up Time label', 'halio'); ?>" class="form-control" id="HalioFormSettingPickUpTimeLabel" name="setting[<?= $pick_up_time_label->id; ?>][value]" value="<?= $pick_up_time_label->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The label on the checkout page for pick up time.', 'halio');
    ?></div>
  </div><?php

  $direction_label = halio_get_settings_row('checkout_direction_label');
  ?><div class="form-group">
    <label for="HalioFormSettingDirectionLabel" class="col-sm-3 control-label"><?php
      _e("Label for 'Direction' in checkout", 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Direction label', 'halio'); ?>" class="form-control" id="HalioFormSettingDirectionLabel" name="setting[<?= $direction_label->id; ?>][value]" value="<?= $direction_label->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The label on the checkout page for direction.', 'halio');
    ?></div>
  </div><?php

  $duration_label = halio_get_settings_row('checkout_duration_label');
  ?><div class="form-group">
    <label for="HalioFormSettingDurationLabel" class="col-sm-3 control-label"><?php
      _e("Label for 'Duration' in checkout", 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Duration label', 'halio'); ?>" class="form-control" id="HalioFormSettingDurationLabel" name="setting[<?= $duration_label->id; ?>][value]" value="<?= $duration_label->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The label on the checkout page for duration.', 'halio');
    ?></div>
  </div><?php

  $distance_label = halio_get_settings_row('checkout_distance_label');
  ?><div class="form-group">
    <label for="HalioFormSettingDistanceLabel" class="col-sm-3 control-label"><?php
      _e("Label for 'Distance' in checkout", 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Distance label', 'halio'); ?>" class="form-control" id="HalioFormSettingDistanceLabel" name="setting[<?= $distance_label->id; ?>][value]" value="<?= $distance_label->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The label on the checkout page for distance.', 'halio');
    ?></div>
  </div><?php

  $one_way_label = halio_get_settings_row('checkout_one_way_label');
  ?><div class="form-group">
    <label for="HalioFormSettingOneWayLabel" class="col-sm-3 control-label"><?php
      _e("'One Way' text", 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('One Way text', 'halio'); ?>" class="form-control" id="HalioFormSettingOneWayLabel" name="setting[<?= $one_way_label->id; ?>][value]" value="<?= $one_way_label->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('Text that will display when trip is one way.', 'halio');
    ?></div>
  </div><?php

  $return_label = halio_get_settings_row('checkout_return_label');
  ?><div class="form-group">
    <label for="HalioFormSettingReturnLabel" class="col-sm-3 control-label"><?php
      _e("'Return' text", 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Return text', 'halio'); ?>" class="form-control" id="HalioFormSettingReturnLabel" name="setting[<?= $return_label->id; ?>][value]" value="<?= $return_label->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('Text that will display when trip is return.', 'halio');
    ?></div>
  </div><?php

  ?><div class="form-group">
    <div class="col-sm-offset-3 col-sm-5 center">
      <input type="submit" value="<?php _e('Update', 'halio'); ?>" class="btn btn-large btn-primary">
    </div>
  </div>
</form>
