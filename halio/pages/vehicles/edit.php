<?php

global $wpdb;
$vehicle = $wpdb->get_row("SELECT * FROM `" . $wpdb->prefix . "halio_vehicles` WHERE `id` = '" . $_GET['vehicle_id'] . "';");

?><div class="halio-settings-page edit-vehicle">

  <div class="pull-right">
    <a href="<?= admin_url('/admin.php?page=halio-vehicles'); ?>" class="btn btn-default"><?php
      _e('All Vehicles', 'halio');
    ?></a>
  </div><?php

  if ( isset($flash) ) {
    ?><div class="alert alert-<?= $flash['type']; ?>" role="alert">
      <?= $flash['message']; ?>
    </div><?php
  }

  ?><div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active">
        <a href="#edit-details" aria-controls="home" role="tab" data-toggle="tab"><?php
          _e('Edit Details', 'halio');
        ?></a>
      </li>
      <li role="presentation">
        <a href="#edit-availability" aria-controls="profile" role="tab" data-toggle="tab"><?php
          _e('Edit Availability', 'halio');
        ?></a>
      </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="edit-details"><?php
        require_once HALIO_PLUGIN_DIR . '/pages/vehicles/tabs/details.php';
      ?></div>

      <div role="tabpanel" class="tab-pane" id="edit-availability"><?php
        require_once HALIO_PLUGIN_DIR . '/pages/vehicles/tabs/availability.php';
      ?></div>
    </div>
  </div>
</div>
