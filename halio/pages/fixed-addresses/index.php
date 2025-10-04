<?php

global $wpdb;
$fixed_addresses = $wpdb->get_results('SELECT * FROM `' . $wpdb->prefix . 'halio_fixed_addresses`');


?><div class="halio-settings-page form-settings"><?php

  if ( isset($flash) ) {
    ?><div class="alert alert-<?= $flash['type']; ?>" role="alert">
      <?= $flash['message']; ?>
    </div><?php
  }

  ?><h1 class="center"><?php
    _e('Fixed Addresses', 'halio');
  ?></h1>

  <form class="form-horizontal halio-settings-form" method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>"><?php

    $origin_fixed_address = halio_get_settings_row('use_fixed_addresses_for_origin');
    ?><div class="form-group">
      <label for="HalioSettingApiKey" class="col-sm-3 control-label"><?php
        _e('Use Fixed Addresses for Starting Address', 'halio');
      ?></label>
      <div class="col-sm-5">
        <select class="form-control" name="setting[<?= $origin_fixed_address->id; ?>][value]" id="HalioEditPPCActive">
          <option value="1" <?php if ($origin_fixed_address->value) echo 'selected'; ?>><?php
            _e('True', 'halio');
          ?></option>
          <option value="0" <?php if (!$origin_fixed_address->value) echo 'selected'; ?>><?php
            _e('False', 'halio');
          ?></option>
        </select>
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('Do you want users to only be able to select from the addresses specified below for the starting address?', 'halio');
      ?></div>
    </div><?php

    $destination_fixed_address = halio_get_settings_row('use_fixed_addresses_for_destination');
    ?><div class="form-group">
      <label for="HalioSettingApiKey" class="col-sm-3 control-label"><?php
        _e('Use Fixed Addresses for Destination Address', 'halio');
      ?></label>
      <div class="col-sm-5">
        <select class="form-control" name="setting[<?= $destination_fixed_address->id; ?>][value]" id="HalioEditPPCActive">
          <option value="1" <?php if ($destination_fixed_address->value) echo 'selected'; ?>><?php
            _e('True', 'halio');
          ?></option>
          <option value="0" <?php if (!$destination_fixed_address->value) echo 'selected'; ?>><?php
            _e('False', 'halio');
          ?></option>
        </select>
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('Do you want users to only be able to select from the addresses specified below for the destination address?', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <div class="col-sm-offset-3 col-sm-5 center">
        <input type="submit" value="<?php _e('Update', 'halio'); ?>" class="btn btn-large btn-primary">
      </div>
    </div>
  </form>

  <table class="table table-bordered table-hover fixed-address-table">
    <thead>
      <tr>
        <th><?php _e('Address', 'halio'); ?></th>
        <th><?php _e('Pretty Address', 'halio'); ?></th>
        <th><?php _e('Starting / Destination Address', 'halio'); ?></th>
        <th><?php _e('Active?', 'halio'); ?></th>
        <th class="actions-column"><?php _e('Actions', 'halio'); ?></th>
      </tr>
    </thead>
    <tbody><?php
      if ( !empty($fixed_addresses) ) {
        foreach($fixed_addresses as $fixed_address) {
          ?><tr>
            <td>
              <a href="<?= halio_edit_fixed_address_path($fixed_address->id); ?>"><?= $fixed_address->address; ?></a>
            </td>
            <td><?= $fixed_address->pretty_address; ?></td>
            <td><?php
              if ($fixed_address->origin_or_destination == 'origin') {
                _e('Starting Address', 'halio');
              } elseif ($fixed_address->origin_or_destination == 'destination') {
                _e('Destination Address', 'halio');
              }
            ?></td>
            <td>
              <div class="halio-is-active-icon-container">
                <?= $fixed_address->is_active == 1 ? '<i class="fa fa-check halio-is-active-icon"></i>' : '<i class="fa fa-times halio-is-active-icon"></i>'; ?>
              </div>
            </td>
            <td>
              <form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>" onsubmit="return confirm('<?php _e('Are you sure you want to delete this Fixed Address?', 'halio'); ?>');" class="table-action-form">
                <input type="hidden" name="delete_fixed_address[id]" value="<?= $fixed_address->id; ?>">
                <input type="submit" value="<?php _e('Delete', 'halio'); ?>" class="btn btn-danger">
              </form>
              <form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>" class="table-action-form">
                <input type="hidden" name="change_fixed_address[id]" value="<?= $fixed_address->id; ?>">
                <input type="hidden" name="change_fixed_address[action]" value="<?= $fixed_address->is_active ? 'deactivate' : 'activate'; ?>"><?php
                if ($fixed_address->is_active) {
                  ?><input type="submit" value="<?php _e('Deactivate', 'halio'); ?>" class="btn btn-warning"><?php
                } else {
                  ?><input type="submit" value="<?php _e('Activate', 'halio'); ?>" class="btn btn-success"><?php
                }
              ?></form>
              <a href="<?= halio_edit_fixed_address_path($fixed_address->id); ?>" class="btn btn-default"><?php _e('Edit', 'halio'); ?></a>
            </td>
          </tr><?php
        }
      } else {
        ?><tr class="info">
          <td colspan="5" class="center"><?php
            _e('No results found.', 'halio');
          ?></td>
        </tr><?php
      }
    ?></tbody>
  </table>

  <form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>" class="form-horizontal">

    <div class="form-group">
      <label for="HalioNewFixedAddressAddress" class="col-sm-3 control-label"><?php
        _e('Address', 'halio');
      ?></label>
      <div class="col-sm-5">
        <input type="text" class="form-control" name="new_fixed_address[address]" id="HalioNewFixedAddressAddress" aria-describedby="starting-address-status" autofocus>
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
        <input type="text" class="form-control" name="new_fixed_address[pretty_address]" id="HalioNewFixedAddressPrettyAddress" placeholder="<?php _e('Pretty Address', 'halio'); ?>">
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
        <select name="new_fixed_address[origin_or_destination]" class="form-control" id="HalioNewFixedAddressOriginOrDestination">
          <option selected disabled><?php
            _e('Please select an address type...', 'halio');
          ?></option>
          <option value="origin"><?php
            _e('Starting Address', 'halio');
          ?></option>
          <option value="destination"><?php
            _e('Destination Address', 'halio');
          ?></option>
        </select>
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('Should this address be displayed in the drop-down for starting addresses or destination address?', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioNewFixedAddressActive" class="col-sm-3 control-label"><?php
        _e('Active?', 'halio');
      ?></label>
      <div class="col-sm-5">
        <select name="new_fixed_address[is_active]" class="form-control" id="HalioNewFixedAddressActive">
          <option value="1"><?php
            _e('True', 'halio');
          ?></option>
          <option value="0"><?php
            _e('False', 'halio');
          ?></option>
        </select>
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('Is this address visible to customers on creation?', 'halio');
      ?></div>
    </div>


    <div class="form-group">
      <div class="col-sm-offset-3 col-sm-5 center">
        <input type="submit" value="<?php _e('Create', 'halio'); ?>" class="btn btn-success">
      </div>
    </div>
  </form>
</div>
