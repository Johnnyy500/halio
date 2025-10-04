<?php

global $wpdb;
$vehicles = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "halio_vehicles`;");

?><form class="form-horizontal halio-settings-form" method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>"><?php

  $can_edit_occupants = halio_get_settings_row('form_can_edit_occupants');
  ?><div class="form-group">
    <label for="HalioFormSettingCanChangeOccupants" class="col-sm-3 control-label"><?php
      _e('User can chose number of occupants', 'halio');
    ?></label>
    <div class="col-sm-5">
      <select class="form-control" name="setting[<?= $can_edit_occupants->id; ?>][value]" id="HalioFormSettingCanChangeOccupants">
        <option value="1" <?php if ($can_edit_occupants->value) echo 'selected'; ?>><?php
          _e('True', 'halio');
        ?></option>
        <option value="0" <?php if (!$can_edit_occupants->value) echo 'selected'; ?>><?php
          _e('False', 'halio');
        ?></option>
      </select>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('If this is false, the number of occupants will not be taken into account when calculating cost, even if Price per Occupant for that vehicle is set.', 'halio');
    ?></div>
  </div><?php

  $can_edit_vehicle_type = halio_get_settings_row('form_can_edit_vehicle_type');
  ?><div class="form-group">
    <label for="HalioFormSettingCanChangeVehicle" class="col-sm-3 control-label"><?php
      _e('User can chose vehicle type', 'halio');
    ?></label>
    <div class="col-sm-5">
      <select class="form-control halio__setting--can-edit-vehicle-type" name="setting[<?= $can_edit_vehicle_type->id; ?>][value]" id="HalioFormSettingCanChangeVehicle">
        <option value="1" <?php if ($can_edit_vehicle_type->value) echo 'selected'; ?>><?php
          _e('True', 'halio');
        ?></option>
        <option value="0" <?php if (!$can_edit_vehicle_type->value) echo 'selected'; ?>><?php
          _e('False', 'halio');
        ?></option>
      </select>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('If this is false, the user will not see the vehicle type dropdown and the vehicle used will be the one specified below.', 'halio');
    ?></div>
  </div><?php

  $default_vehicle_id = halio_get_settings_row('form_default_vehicle_id');
  ?><div class="form-group">
    <label for="HalioFormSettingCanChangeVehicle" class="col-sm-3 control-label"><?php
      _e('Default Vehicle', 'halio');
    ?></label>
    <div class="col-sm-5">
      <select class="form-control halio__setting--default-vehicle-id" name="setting[<?= $default_vehicle_id->id; ?>][value]" id="HalioFormSettingCanChangeVehicle" <?php if ($can_edit_vehicle_type->value) echo 'disabled'; ?>><?php
      if ( empty($default_vehicle_id->value)) {
        ?><option selected disabled><?php
          _e('Select a vehicle...', 'halio');
        ?></option><?php
      }

      foreach ($vehicles as $vehicle) {
        ?><option value="<?= $vehicle->id; ?>"><?= $vehicle->name; ?></option><?php
      }
      ?></select>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e("The vehicle to be used if 'User can chose vehicle type' is false.", 'halio');
    ?></div>
  </div><?php

  $can_edit_direction = halio_get_settings_row('form_can_edit_direction');
  ?><div class="form-group">
    <label for="HalioFormSettingCanChangeVehicle" class="col-sm-3 control-label"><?php
      _e('User can chose direction', 'halio');
    ?></label>
    <div class="col-sm-5">
      <select class="form-control" name="setting[<?= $can_edit_direction->id; ?>][value]" id="HalioFormSettingCanChangeVehicle">
        <option value="1" <?php if ($can_edit_direction->value) echo 'selected'; ?>><?php
          _e('True', 'halio');
        ?></option>
        <option value="0" <?php if (!$can_edit_direction->value) echo 'selected'; ?>><?php
          _e('False', 'halio');
        ?></option>
      </select>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('If this is false, the user will book a one way trip by default and will not be able to change this.', 'halio');
    ?></div>
  </div><?php

  $show_distance = halio_get_settings_row('form_show_distance_in_estimate');
  ?><div class="form-group">
    <label for="HalioFormSettingShowDistance" class="col-sm-3 control-label"><?php
      _e('Show distance in price estimate box', 'halio');
    ?></label>
    <div class="col-sm-5">
      <select class="form-control" name="setting[<?= $show_distance->id; ?>][value]" id="HalioFormSettingShowDistance">
        <option value="1" <?php if ($show_distance->value) echo 'selected'; ?>><?php
          _e('True', 'halio');
        ?></option>
        <option value="0" <?php if (!$show_distance->value) echo 'selected'; ?>><?php
          _e('False', 'halio');
        ?></option>
      </select>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('Can the user see the distance of the trip in the price estimate box?', 'halio');
    ?></div>
  </div><?php

  $show_duration = halio_get_settings_row('form_show_duration_in_estimate');
  ?><div class="form-group">
    <label for="HalioFormSettingShowDuration" class="col-sm-3 control-label"><?php
      _e('Show duration in price estimate box', 'halio');
    ?></label>
    <div class="col-sm-5">
      <select class="form-control" name="setting[<?= $show_duration->id; ?>][value]" id="HalioFormSettingShowDuration">
        <option value="1" <?php if ($show_duration->value) echo 'selected'; ?>><?php
          _e('True', 'halio');
        ?></option>
        <option value="0" <?php if (!$show_duration->value) echo 'selected'; ?>><?php
          _e('False', 'halio');
        ?></option>
      </select>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('Can the user see the duration of the trip in the price estimate box?', 'halio');
    ?></div>
  </div><?php

  $title_or_image = halio_get_settings_row('form_show_title_or_image');
  ?><div class="form-group">
    <label for="HalioFormSettingTitleOrImage" class="col-sm-3 control-label"><?php
      _e('Show Title or Image', 'halio');
    ?></label>
    <div class="col-sm-5">
      <select class="form-control halio__setting--title-or-image edit" name="setting[<?= $title_or_image->id; ?>][value]" id="HalioFormSettingTitleOrImage">
        <option value="title" <?php if ($title_or_image->value == 'title') echo 'selected'; ?>><?php
          _e('Title', 'halio');
        ?></option>
        <option value="image" <?php if ($title_or_image->value == 'image') echo 'selected'; ?>><?php
          _e('Image', 'halio');
        ?></option>
      </select>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('In the header of the form, show a text title, or an image from a specified URL.', 'halio');
    ?></div>
  </div><?php

  $form_title = halio_get_settings_row('form_title');
  ?><div class="form-group">
    <label for="HalioFormSettingTitle" class="col-sm-3 control-label"><?php
      _e('Form Title', 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Form Title', 'halio'); ?>" class="form-control halio__setting--form-title edit" id="HalioFormSettingTitle" name="setting[<?= $form_title->id; ?>][value]" value="<?= $form_title->value; ?>" <?php if ($title_or_image->value == 'image') echo 'disabled'; ?>>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The title of the form, if you chose to show a title.', 'halio');
    ?></div>
  </div><?php

  $form_title_image = halio_get_settings_row('form_title_image');
  ?><div class="form-group">
    <label for="HalioFormSettingImageURL" class="col-sm-3 control-label"><?php
      _e('Form Image URL', 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="url" placeholder="<?php _e('Form Image URL', 'halio'); ?>" class="form-control halio__setting--form-image-url edit" id="HalioFormSettingImageURL" name="setting[<?= $form_title_image->id; ?>][value]" value="<?= $form_title_image->value; ?>" <?php if ($title_or_image->value == 'title') echo 'disabled'; ?>>
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('The URL of the image to be displayed at the top of the form.', 'halio');
    ?></div>
  </div>

  <h4 class="col-sm-offset-3 col-sm-5 header"><?php
    _e('Labels', 'halio');
  ?></h4><?php

  $estimate_cost_label = halio_get_settings_row('form_estimate_cost_label');
  ?><div class="form-group">
    <label for="HalioFormSettingEstimateCostLabel" class="col-sm-3 control-label"><?php
      _e("Text in 'Estimate Cost' button", 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Estimate Cost label', 'halio'); ?>" class="form-control" id="HalioFormSettingEstimateCostLabel" name="setting[<?= $estimate_cost_label->id; ?>][value]" value="<?= $estimate_cost_label->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"></div>
  </div><?php

  $estimating_cost_label = halio_get_settings_row('form_estimating_cost_label');
  ?><div class="form-group">
    <label for="HalioFormSettingEstimatingCostLabel" class="col-sm-3 control-label"><?php
      _e('Estimating Cost text', 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Estimating Cost text', 'halio'); ?>" class="form-control" id="HalioFormSettingEstimatingCostLabel" name="setting[<?= $estimating_cost_label->id; ?>][value]" value="<?= $estimating_cost_label->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e("Text in 'Estimate Cost' button when loading price", 'halio');
    ?></div>
  </div><?php

  $starting_address_label = halio_get_settings_row('form_starting_address_label');
  ?><div class="form-group">
    <label for="HalioFormSettingStartingAddressLabel" class="col-sm-3 control-label"><?php
      _e("Label for Starting Address field", 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Starting Address label', 'halio'); ?>" class="form-control" id="HalioFormSettingStartingAddressLabel" name="setting[<?= $starting_address_label->id; ?>][value]" value="<?= $starting_address_label->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('If the user can type the address this will be the placeholder for the starting address.', 'halio');
    ?></div>
  </div><?php

  $starting_select_text = halio_get_settings_row('form_starting_address_select_text');
  ?><div class="form-group">
    <label for="HalioFormSettingStartingAddressSelectText" class="col-sm-3 control-label"><?php
      _e("Starting Address Select Text", 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Starting Address Select text', 'halio'); ?>" class="form-control" id="HalioFormSettingStartingAddressSelectText" name="setting[<?= $starting_select_text->id; ?>][value]" value="<?= $starting_select_text->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('If Fixed Addresses are used, this text will be displayed as the placeholder for the starting address drop-down.', 'halio');
    ?></div>
  </div><?php

  $destination_address_label = halio_get_settings_row('form_destination_address_label');
  ?><div class="form-group">
    <label for="HalioFormSettingDestinationAddressLabel" class="col-sm-3 control-label"><?php
      _e("Label for Destination Address field", 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Destination Address Label', 'halio'); ?>" class="form-control" id="HalioFormSettingDestinationAddressLabel" name="setting[<?= $destination_address_label->id; ?>][value]" value="<?= $destination_address_label->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('If the user can type the address this will be the placeholder for the destination] address.', 'halio');
    ?></div>
  </div><?php

  $destination_select_text = halio_get_settings_row('form_destination_address_select_text');
  ?><div class="form-group">
    <label for="HalioFormSettingDestinationAddressSelectText" class="col-sm-3 control-label"><?php
      _e("Destination Address Select Text", 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Destination Address Select text', 'halio'); ?>" class="form-control" id="HalioFormSettingDestinationAddressSelectText" name="setting[<?= $destination_select_text->id; ?>][value]" value="<?= $destination_select_text->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"><?php
      _e('If Fixed Addresses are used, this text will be displayed as the placeholder for the destination address drop-down.', 'halio');
    ?></div>
  </div><?php

  $vehicle_type_label = halio_get_settings_row('form_vehicle_type_label');
  ?><div class="form-group">
    <label for="HalioFormSettingVehicleTypeLabel" class="col-sm-3 control-label"><?php
      _e("Label for Vehicle Type field", 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Vehicle Type label', 'halio'); ?>" class="form-control" id="HalioFormSettingVehicleTypeLabel" name="setting[<?= $vehicle_type_label->id; ?>][value]" value="<?= $vehicle_type_label->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"></div>
  </div><?php

  $number_of_occupants_label = halio_get_settings_row('form_number_of_occupants_label');
  ?><div class="form-group">
    <label for="HalioFormSettingNumberOfOccupantsLabel" class="col-sm-3 control-label"><?php
      _e("Label for Number of Occupants field", 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Number of Occupants label', 'halio'); ?>" class="form-control" id="HalioFormSettingNumberOfOccupantsLabel" name="setting[<?= $number_of_occupants_label->id; ?>][value]" value="<?= $number_of_occupants_label->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"></div>
  </div><?php

  $direction_label = halio_get_settings_row('form_direction_label');
  ?><div class="form-group">
    <label for="HalioFormSettingDirectionLabel" class="col-sm-3 control-label"><?php
      _e("Label for Direction field", 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Direction label', 'halio'); ?>" class="form-control" id="HalioFormSettingDirectionLabel" name="setting[<?= $direction_label->id; ?>][value]" value="<?= $direction_label->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"></div>
  </div><?php

  $pick_up_time_label = halio_get_settings_row('form_pick_up_time_label');
  ?><div class="form-group">
    <label for="HalioFormSettingPickUpTimeLabel" class="col-sm-3 control-label"><?php
      _e("Label for Pick up Time field", 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Pick Up Time label', 'halio'); ?>" class="form-control" id="HalioFormSettingPickUpTimeLabel" name="setting[<?= $pick_up_time_label->id; ?>][value]" value="<?= $pick_up_time_label->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"></div>
  </div><?php

  $return_pick_up_time_label = halio_get_settings_row('form_return_pick_up_time_label');
  ?><div class="form-group">
    <label for="HalioFormSettingReturnPickUpTimeLabel" class="col-sm-3 control-label"><?php
      _e('Label for Return Pick up Time field', 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Return Pick Up Time label', 'halio'); ?>" class="form-control" id="HalioFormSettingReturnPickUpTimeLabel" name="setting[<?= $return_pick_up_time_label->id; ?>][value]" value="<?= $return_pick_up_time_label->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"></div>
  </div><?php

  $book_button_text = halio_get_settings_row('form_book_button_text');
  ?><div class="form-group">
    <label for="HalioFormSettingBookButtonText" class="col-sm-3 control-label"><?php
      _e('Text for Book button', 'halio');
    ?></label>
    <div class="col-sm-5">
      <input type="text" placeholder="<?php _e('Book Button text', 'halio'); ?>" class="form-control" id="HalioFormSettingBookButtonText" name="setting[<?= $book_button_text->id; ?>][value]" value="<?= $book_button_text->value; ?>">
    </div>
    <div class="col-sm-4 helper-text"></div>
  </div>

  <div class="form-group">
    <div class="col-sm-offset-3 col-sm-5 center">
      <input type="submit" value="<?php _e('Update', 'halio'); ?>" class="btn btn-large btn-primary">
    </div>
  </div>
</form>
