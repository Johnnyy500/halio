<?php

global $wpdb;
$checkout_field = $wpdb->get_row("SELECT * FROM `" . $wpdb->prefix . "halio_custom_checkout_fields` WHERE `id` = " . esc_sql($_GET['checkout_field_id']) . ";");

?><div class="halio-settings-page form-settings"><?php

  if ( isset($flash) ) {
    ?><div class="alert alert-<?= $flash['type']; ?>" role="alert">
      <?= $flash['message']; ?>
    </div><?php
  }

  ?><div class="pull-right">
    <a href="<?= admin_url('/admin.php?page=halio-form-design'); ?>" class="btn btn-default"><?php
      _e('All Checkout Fields', 'halio');
    ?></a>
  </div>

  <h1 class="center">Edit Custom Checkout Field</h1>

  <form method="post" class="form-horizontal halio-settings-form" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>">
    <input type="hidden" name="edit_checkout_field[id]" value="<?= $checkout_field->id; ?>">

    <div class="form-group">
      <label for="HalioEditCheckoutFieldLabel" class="col-sm-3 control-label"><?php
        _e('Field Label', 'halio');
      ?></label>
      <div class="col-sm-5">
        <input type="text" class="form-control halio__checkout-field--label edit" id="HalioEditCheckoutFieldLabel" placeholder="_e('The label displayed next to the input field.', 'halio');" name="edit_checkout_field[label]" value="<?= $checkout_field->label; ?>">
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('The label displayed next to the input field.', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioEditCheckoutFieldPlaceholder" class="col-sm-3 control-label"><?php
        _e('Field Placeholder', 'halio');
      ?></label>
      <div class="col-sm-5">
        <input type="text" class="form-control halio__checkout-field--placeholder edit" id="HalioEditCheckoutFieldPlaceholder" placeholder="<?php _e('Placeholder', 'halio'); ?>" name="edit_checkout_field[placeholder]" value="<?= $checkout_field->placeholder; ?>">
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('The text displayed in the text field if nothing has been input.', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioEditCheckoutFieldRequired" class="col-sm-3 control-label"><?php
        _e('Required?', 'halio');
      ?></label>
      <div class="col-sm-5">
        <select class="form-control halio__checkout-field--required edit" name="edit_checkout_field[is_required]" id="HalioEditCheckoutFieldRequired">
          <option value="1" <?php if ($checkout_field->is_required) echo 'selected'; ?>><?php
            _e('True', 'halio');
          ?></option>
          <option value="0" <?php if (!$checkout_field->is_required) echo 'selected'; ?>><?php
            _e('False', 'halio');
          ?></option>
        </select>
      </div>
      <div class="col-sm-4 helper-text"><?php
        _e('Does the user have to provide a value for this field or can they pay for the service without a value?', 'halio');
      ?></div>
    </div>

    <div class="form-group">
      <label for="HalioEditCheckoutFieldActive" class="col-sm-3 control-label"><?php
        _e('Active?', 'halio');
      ?></label>
      <div class="col-sm-5">
        <select class="form-control halio__checkout-field--active edit" name="edit_checkout_field[is_active]" id="HalioEditCheckoutFieldActive">
          <option value="1" <?php if ($checkout_field->is_active) echo 'selected'; ?>><?php
            _e('True', 'halio');
          ?></option>
          <option value="0" <?php if (!$checkout_field->is_active) echo 'selected'; ?>><?php
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
        <input type="submit" value="<?php _e('Update', 'halio'); ?>" class="btn btn-large btn-primary">
      </div>
    </div>
  </form>
</div>
