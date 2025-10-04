<?php
if (!defined('ABSPATH')) { exit; }


$days = array(
  'sunday' => __('Sunday', 'halio'),
  'monday' => __('Monday', 'halio'),
  'tuesday' => __('Tuesday', 'halio'),
  'wednesday' => __('Wednesday', 'halio'),
  'thursday' => __('Thursday', 'halio'),
  'friday' => __('Friday', 'halio'),
  'saturday' => __('Saturday', 'halio')
);

?><h1 class="center"><?php
  printf(
    __('Edit %s Availability', 'halio'),
    $vehicle->name
  );
?></h1><?php

?><div class="row"><?php
  $index = 0;

  foreach ($days as $key => $translated_day) {
    $vats = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "halio_vehicle_availability_time` WHERE `day` = " . $index . " AND `vehicle_id` = " . $vehicle->id);

    ?><div class="col-md-4">
      <div class="panel panel-default halio-vat-panel">
        <div class="panel-heading">
          <div class="pull-right">
            <form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>" class="table-action-form">
              <input type="hidden" name="change_vat[id]" value="<?= $vehicle->id; ?>">
              <input type="hidden" name="change_vat[action]" value="<?= $vehicle->{'active_' . $key} ? 'deactivate' : 'activate'; ?>">
              <input type="hidden" name="change_vat[day]" value="<?= $index; ?>"><?php
              if ($vehicle->{'active_' . $key}) {
                ?><input type="submit" value="<?php _e('Deactivate', 'halio'); ?>" class="btn btn-warning"><?php
              } else {
                ?><input type="submit" value="<?php _e('Activate', 'halio'); ?>" class="btn btn-success"><?php
              }
            ?></form>
          </div>
          <h3 class="panel-title"><?= $translated_day; ?></h3>
        </div>
        <div class="panel-body"><?php
          if ($vehicle->{'active_' . $key}) {
            ?><table class="table">
              <thead>
                <th><?php _e('Start Time', 'halio'); ?></th>
                <th><?php _e('End Time', 'halio'); ?></th>
                <th><?php _e('Delete', 'halio'); ?></th>
              </thead>
              <tbody><?php
                if ( empty($vats) ) {
                  ?><tr class="info">
                    <td colspan="3" class="center"><?php
                      _e('No Availability Rules, available all day by default', 'halio');
                    ?></td>
                  </tr><?
                } else {
                  foreach ($vats as $vat) {
                    ?><tr>
                      <td><?= $vat->starting_time; ?></td>
                      <td><?= $vat->ending_time; ?></td>
                      <td>
                        <form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>" onsubmit="return confirm('<?php _e('Are you sure you want to delete this Vehicle Availability Time?', 'halio'); ?>');" class="table-action-form">
                          <input type="hidden" name="delete_vat[id]" value="<?= $vat->id; ?>">
                          <input type="submit" value="<?php _e('Delete', 'halio'); ?>" class="btn btn-danger">
                        </form>
                      </td>
                    </tr><?php
                  }
                }
              ?></tbody>
            </table>

            <form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>">
              <input type="hidden" name="new_vehicle_availability_time[day]" value="<?= $index; ?>">

              <div class="form-group">
                <label for="HalioNewVAT<?= $key; ?>StartTime"><?php _e('Start Time (24hr, HH:MM:SS)', 'halio'); ?></label>
                <input type="time" class="form-control halio__new--vat-start-time new <?= $key; ?>" id="HalioNewVAT<?= $key; ?>StartTime" name="new_vehicle_availability_time[starting_time]" step="1">
              </div>

              <div class="form-group">
                <label for="HalioNewVAT<?= $key; ?>EndTime"><?php _e('End Time (24hr, HH:MM:SS)', 'halio'); ?></label>
                <input type="time" class="form-control halio__new--vat-end-time new <?= $key; ?>" id="HalioNewVAT<?= $key; ?>EndTime" name="new_vehicle_availability_time[ending_time]" step="1">
              </div>

              <div class="form-group center">
                <input type="submit" value="<?php _e('Create', 'halio'); ?>" class="btn btn-large btn-primary">
              </div>
            </form><?php
          } else {
            ?><h3 class="center"><?php
              printf(
                __('Inactive on %s', 'halio'),
                $translated_day
              );
            ?></h3><?php
          }
        ?></div>
      </div>
    </div><?php

    if ($index == 2) {
      ?></div><div class="row"><?php
    }

    $index++;
  }
?></div>
