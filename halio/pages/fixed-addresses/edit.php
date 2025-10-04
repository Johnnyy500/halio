<?php

global $wpdb;
$fixed_address = $wpdb->get_row("SELECT * FROM `" . $wpdb->prefix . "halio_fixed_addresses` WHERE id = " . esc_sql($_GET['fixed_address_id']) . ";");

?><div class="halio-settings-page"><?php
  if ( isset($flash) ) {
    ?><div class="alert alert-<?= $flash['type']; ?>" role="alert">
      <?= $flash['message']; ?>
    </div><?php
  }

  ?><h1 class="center"><?php
    _e('Edit Fixed Addresses', 'halio');
  ?></h1>

  <form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>" class="form-horizontal">

    <input type="hidden" name="edit_fixed_address[id]" value="<?= $fixed_address->id; ?>">

    <div class="form-group">
      <label for="HalioNewFixedAddressAddress" class="col-sm-3 control-label"><?php
        _e('Address', 'halio');
      ?></label>
      <div class="col-sm-5">
        <input type="text" class="form-control" name="edit_fixed_address[address]" id="HalioNewFixedAddressAddress" aria-describedby="starting-address-status" autofocus value="<?= $fixed_address->address; ?>">
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('The address of the destination.', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioNewFixedAddressPrettyAddress" class="col-sm-3 control-label"><?php
        _e('Pretty Address', 'halio');
      ?></label>
      <div class="col-sm-5">
        <input type="text" class="form-control" name="edit_fixed_address[pretty_address]" id="HalioNewFixedAddressPrettyAddress" placeholder="<?php _e('Pretty Address', 'halio'); ?>" value="<?= $fixed_address->pretty_address; ?>">
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('The address that users see in the drop-down, if you want this to be the same as the actual address leave this field blank.', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioNewFixedAddressOriginOrDestination" class="col-sm-3 control-label"><?php
        _e('Address', 'halio');
      ?></label>
      <div class="col-sm-5">
        <select name="edit_fixed_address[origin_or_destination]" class="form-control" id="HalioNewFixedAddressOriginOrDestination">
          <option value="origin" <?php if ($fixed_address->origin_or_destination == 'origin') echo 'selected'; ?>><?php
            _e('Starting Address', 'halio');
          ?></option>
          <option value="destination" <?php if ($fixed_address->origin_or_destination == 'destination') echo 'selected'; ?>><?php
            _e('Destination Address', 'halio');
          ?></option>
        </select>
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('Should this address be displayed in the drop-down for starting addressed or destination address?', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioNewFixedAddressActive" class="col-sm-3 control-label"><?php
        _e('Active?', 'halio');
      ?></label>
      <div class="col-sm-5">
        <select name="edit_fixed_address[is_active]" class="form-control" id="HalioNewFixedAddressActive">
          <option value="1" <?php if ($fixed_address->is_active) echo 'selected'; ?>><?php
            _e('True', 'halio');
          ?></option>
          <option value="0" <?php if (!$fixed_address->is_active) echo 'selected'; ?>><?php
            _e('False', 'halio');
          ?></option>
        </select>
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('Is this address visible to customers?', 'halio');
      ?></div>
    </div>


    <div class="form-group">
      <div class="col-sm-offset-3 col-sm-5 center">
        <input type="submit" value="<?php _e('Update', 'halio'); ?>" class="btn btn-success">
      </div>
    </div>
  </form>
</div>
