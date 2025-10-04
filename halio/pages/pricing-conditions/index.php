<?php

global $wpdb;
$vehicles = $wpdb->get_results('SELECT * FROM `' . $wpdb->prefix . 'halio_vehicles`');

?><div class="halio-settings-page"><?php

  if ( isset($flash) ) {
    ?><div class="alert alert-<?= $flash['type']; ?>" role="alert">
      <?= $flash['message']; ?>
    </div><?php
  }

  ?><h1 class="center"><?php
    _e('Pricing Conditions', 'halio');
  ?></h1>

  <div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active">
        <a href="#geolocation-tab" aria-controls="home" role="tab" data-toggle="tab"><?php
          _e('Geolocation', 'halio');
        ?></a>
      </li>
      <li role="presentation">
        <a href="#time-of-day-tab" aria-controls="profile" role="tab" data-toggle="tab"><?php
          _e('Time of Day', 'halio');
        ?></a>
      </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="geolocation-tab"><?php
        require_once HALIO_PLUGIN_DIR . '/pages/pricing-conditions/tabs/polygon_tab.php';
      ?></div>

      <div role="tabpanel" class="tab-pane" id="time-of-day-tab"><?php
        require_once HALIO_PLUGIN_DIR . '/pages/pricing-conditions/tabs/time_tab.php';
      ?></div>
    </div>
  </div>
</div>

