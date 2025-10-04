<?php
if (!defined('ABSPATH')) { exit; }


global $wpdb;
$vehicles = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "halio_vehicles`;");

$units = halio_get_settings_row('units')->value == 'miles' ? __('Mile', 'halio') : 'KM';

?><div class="halio-settings-page"><?php

  if ( isset($flash) ) {
    ?><div class="alert alert-<?= $flash['type']; ?>" role="alert">
      <?= $flash['message']; ?>
    </div><?php
  }

  ?><h1 class="center"><?php
    _e('Vehicles', 'halio');
  ?></h1>

  <form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>">
    <table class="table table-bordered table-hover halio-vehicles-table">
      <thead>
        <tr>
          <th><?php _e('Name', 'halio'); ?></th>
          <th><?php _e('Pasenger Space', 'halio'); ?></th>
          <th><?php _e('Suitcase Space', 'halio'); ?></th>
          <th><?php _e('Number Owned', 'halio'); ?></th>
          <th><?php _e('Starting Fare', 'halio'); ?> (<?= get_woocommerce_currency_symbol(); ?>)</th>
          <th><?= get_woocommerce_currency_symbol(); ?> / <?= $units; ?></th>
          <th><?= get_woocommerce_currency_symbol(); ?> / <?php _e('Minute', 'halio'); ?></th>
          <th><?= get_woocommerce_currency_symbol(); ?> / <?php _e('Occupant', 'halio'); ?></th>
          <th><?php _e('Active?', 'halio'); ?></th>
          <th><?php _e('Actions', 'halio'); ?></th>
        </tr>
      </thead>
      <tbody><?php
        if ( !empty($vehicles) ) {
          foreach($vehicles as $vehicle) {
            ?><tr>
              <td>
                <a href="<?= halio_edit_vehicle_path($vehicle->id); ?>">
                  <?= $vehicle->name; ?>
                </a>
              </td>
              <td><?= $vehicle->passenger_space; ?></td>
              <td><?= $vehicle->suitcase_space; ?></td>
              <td><?= $vehicle->number_owned; ?></td>
              <td>
                <span class="currency"><?= get_woocommerce_currency_symbol(); ?></span>
                <?= $vehicle->starting_fare; ?>
              </td>
              <td>
                <span class="currency"><?= get_woocommerce_currency_symbol(); ?></span>
                <?php echo $vehicle->price_per_unit_distance; ?>
              </td>
              <td>
                <span class="currency"><?= get_woocommerce_currency_symbol(); ?></span>
                <?php echo $vehicle->price_per_minute; ?>
              </td>
              <td>
                <span class="currency"><?= get_woocommerce_currency_symbol(); ?></span>
                <?php echo $vehicle->price_per_occupant; ?>
              </td>
              <td>
                <div class="halio-is-active-icon-container">
                  <?= $vehicle->is_active == 1 ? '<i class="fa fa-check halio-is-active-icon"></i>' : '<i class="fa fa-times halio-is-active-icon"></i>'; ?>
                </div>
              </td>
              <td>
                <form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>" onsubmit="return confirm('<?php _e('Are you sure you want to delete this Vehicle?', 'halio'); ?>');" class="table-action-form">
                  <input type="hidden" name="delete_vehicle[id]" value="<?= $vehicle->id; ?>">
                  <input type="submit" value="<?php _e('Delete', 'halio'); ?>" class="btn btn-danger">
                </form>
                <form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>" class="table-action-form">
                  <input type="hidden" name="change_vehicle[id]" value="<?= $vehicle->id; ?>">
                  <input type="hidden" name="change_vehicle[action]" value="<?= $vehicle->is_active ? 'deactivate' : 'activate'; ?>"><?php
                  if ($vehicle->is_active) {
                    ?><input type="submit" value="<?php _e('Deactivate', 'halio'); ?>" class="btn btn-warning"><?php
                  } else {
                    ?><input type="submit" value="<?php _e('Activate', 'halio'); ?>" class="btn btn-success"><?php
                  }
                ?></form>
                <a href="<?= halio_edit_vehicle_path($vehicle->id); ?>" class="btn btn-default"><?php _e('Edit', 'halio'); ?></a>
              </td>
            </tr><?php
          }
        } else {
          ?><tr class="info">
            <td colspan="10" class="center"><?php _e('No results found.', 'halio'); ?></td>
          </tr><?php
        }
        ?><form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>">
          <tr>
            <td>
              <input type="text" name="new_vehicle[name]" id="HalioNewVehicleName" required>
            </td>
            <td>
              <input type="number" min="1" name="new_vehicle[passenger_space]" id="HalioNewVehiclePassengerSpace">
            </td>
            <td>
              <input type="number" min="0" name="new_vehicle[suitcase_space]" id="HalioNewVehicleSuitcaseSpace">
            </td>
            <td>
              <input type="number" min="1" name="new_vehicle[number_owned]" id="HalioNewVehicleNumberOwned">
            </td>
            <td>
              <span class="currency"><?= get_woocommerce_currency_symbol(); ?></span>
              <input type="number" min="0" step="any" name="new_vehicle[starting_fare]" id="HalioNewVehicleStartingFare">
            </td>
            <td>
              <span class="currency"><?= get_woocommerce_currency_symbol(); ?></span>
              <input type="number" min="0" step="any" name="new_vehicle[price_per_unit_distance]" id="HalioNewVehiclePricePerUnitDistance">
            </td>
            <td>
              <span class="currency"><?= get_woocommerce_currency_symbol(); ?></span>
              <input type="number" min="0" step="any" name="new_vehicle[price_per_minute]" id="HalioNewVehiclePricePerMinute">
            </td>
            <td>
              <span class="currency"><?= get_woocommerce_currency_symbol(); ?></span>
              <input type="number" min="0" step="any" name="new_vehicle[price_per_occupant]" id="HalioNewVehiclePricePerOccupant">
            </td>
            <td></td>
            <td>
              <input type="submit" value="<?php _e('Create', 'halio'); ?>" class="btn btn-success">
            </td>
          </tr>
        </form>
      </tbody>
    </table>
  </form>
</div>
