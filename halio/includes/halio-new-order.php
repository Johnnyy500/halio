<?php

class HalioNewOrder {

  public function check_for_new_order() {
    global $woocommerce;
    global $wpdb;

    if ( isset($_POST['halio_starting_address']) && !isset($_POST['billing_first_name']) ) {

      $required_fields = array(
        'halio_starting_address' => 'Starting Address',
        'halio_destination_address' => 'Destination Address',
        'halio_pick_up_time' => 'Pick up Time'
      );

      if ( halio_get_settings_row('form_can_edit_vehicle_type')->value ) {
        $required_fields['halio_vehicle_id'] = 'Vehicle';
      }

      if ( halio_get_settings_row('form_can_edit_direction')->value ) {
        $required_fields['halio_direction'] = 'Direction';

        if ( $_POST['halio_direction'] == 'return' ) {
          $required_fields['halio_return_pick_up_time'] = 'Return Pick Up Time';
        }
      }

      if ( halio_get_settings_row('form_can_edit_occupants')->value ) {
        $required_fields['halio_occupants'] = 'Occupants';
      }

      $error = 'The following fields are required: ';
      $error_fields = array();

      foreach ($required_fields as $key => $value) {
        if ( !isset($_POST[$key]) || empty($_POST[$key]) ) {
          $error .= "$value, ";
          array_push($error_fields, $key);
        }
      };

      $error = preg_replace('/, $/', '', $error);

      if ( empty($error_fields) ) {
        $product_id = (int) halio_get_settings_row('wc_product_id')->value;
        $woocommerce->cart->add_to_cart($product_id);

        $this->set_checkout_journey_information();

        if ($woocommerce->cart->get_cart_contents_count() > 0) {
          wp_redirect($woocommerce->cart->get_checkout_url());
          exit;
        }
      } else {
        global $flash;
        $flash = array(
          'type' => 'warning',
          'message' => $error . '.'
        );
      }
    }

    if ( !class_exists('WooCommerce') ) {
      $flash = array(
        'type' => 'danger',
        'message' => 'You need to install WooCommerce. Without it Halio will not work.'
      );
    }
  }

  public function customise_checkout_fields($wc_fields) {
    if ( halio_product_in_cart() ) {
      $this->check_quote_price_on_checkout();

      global $wpdb;
      $custom_fields = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "halio_custom_checkout_fields` WHERE `is_active` = 1");

      foreach ($custom_fields as $field) {
        $wc_fields['order'][$field->key] = array(
          'label' => $field->label,
          'placeholder' => $field->placeholder,
          'required' => $field->is_required,
          'class' => array(),
          'clear' => true
         );
      }
    }

    return $wc_fields;
  }

  public function add_custom_checkout_fields($checkout) {
    global $wpdb;
    global $woocommerce;

    if ( halio_product_in_cart() ) {

      $vehicle_name = $wpdb->get_row("SELECT * FROM `" . $wpdb->prefix . "halio_vehicles` WHERE `id` = '" . $woocommerce->session->get('halio_vehicle_id') . "';")->name;

      if ( halio_get_settings_row('form_can_edit_direction')->value ) {
        if ( $woocommerce->session->get('halio_direction') == 'one_way' ) {
          $direction = halio_get_settings_row('checkout_one_way_label')->value;
        } else {
           $direction = halio_get_settings_row('checkout_return_label')->value;
        }
      } else {
        $direction = halio_get_settings_row('checkout_one_way_label')->value;
      }

      ?><div class="halio-checkout-group">
        <h3><?= __('Journey Information', 'halio'); ?></h3>

        <div class="halio-checkout-map-container">
          <div id="checkout_map"></div>
        </div>

        <input type="hidden" value="<?= $woocommerce->session->get('halio_starting_address'); ?>" name="halio_starting_address" id="halio_starting_address">
        <input type="hidden" value="<?= $woocommerce->session->get('halio_destination_address'); ?>" name="halio_destination_address" id="halio_destination_address">
        <input type="hidden" value="<?= $woocommerce->session->get('halio_occupants'); ?>" name="halio_occupants">
        <input type="hidden" value="<?= $woocommerce->session->get('halio_pick_up_time'); ?>" name="halio_pick_up_time">
        <input type="hidden" value="<?= $woocommerce->session->get('halio_return_pick_up_time'); ?>" name="halio_return_pick_up_time">
        <input type="hidden" value="<?= $direction; ?>" name="halio_direction">
        <input type="hidden" value="<?= $woocommerce->session->get('halio_duration_in_seconds'); ?>" name="halio_duration_in_seconds">
        <input type="hidden" value="<?= $woocommerce->session->get('halio_duration'); ?>" name="halio_duration">
        <input type="hidden" value="<?= $woocommerce->session->get('halio_distance'); ?>" name="halio_distance">
        <input type="hidden" value="<?= $woocommerce->session->get('halio_starting_coords_lat'); ?>" name="halio_starting_coords_lat" id="HalioStartingLat">
        <input type="hidden" value="<?= $woocommerce->session->get('halio_starting_coords_long'); ?>" name="halio_starting_coords_long" id="HalioStartingLong">
        <input type="hidden" value="<?= $woocommerce->session->get('halio_destination_coords_lat'); ?>" name="halio_destination_coords_lat" id="HalioDestinationLat">
        <input type="hidden" value="<?= $woocommerce->session->get('halio_destination_coords_long'); ?>" name="halio_destination_coords_long" id="HalioDestinationLong">

        <div class="form-group">
          <label class="col-sm-5 control-label"><?=
            halio_get_settings_row('checkout_starting_address_label')->value;
          ?></label>
          <div class="col-sm-7">
            <p class="static-form-control"><?= $woocommerce->session->get('halio_starting_address'); ?></p>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-5 control-label"><?=
            halio_get_settings_row('checkout_destination_address_label')->value;
          ?></label>
          <div class="col-sm-7">
            <p class="static-form-control"><?= $woocommerce->session->get('halio_destination_address'); ?></p>
          </div>
        </div><?php

        if ( halio_get_settings_row('form_can_edit_vehicle_type')->value ) {
          ?><div class="form-group">
            <label class="col-sm-5 control-label"><?=
              halio_get_settings_row('checkout_vehicle_label')->value;
            ?></label>
            <div class="col-sm-7">
              <p class="static-form-control"><?= $vehicle_name; ?></p>
              <input type="hidden" value="<?= $woocommerce->session->get('halio_vehicle_id'); ?>" name="halio_vehicle_id">
            </div>
          </div><?php
        }

        if ( halio_get_settings_row('form_can_edit_occupants')->value ) {
          ?><div class="form-group">
            <label class="col-sm-5 control-label"><?=
              halio_get_settings_row('checkout_occupants_label')->value;
            ?></label>
            <div class="col-sm-7">
              <p class="static-form-control"><?= $woocommerce->session->get('halio_occupants'); ?></p>
            </div>
          </div><?php
        }

        if ( halio_get_settings_row('form_can_edit_direction')->value ) {
          ?><div class="form-group">
            <label class="col-sm-5 control-label"><?=
              halio_get_settings_row('checkout_direction_label')->value;
            ?></label>
            <div class="col-sm-7">
              <p class="static-form-control"><?= $direction; ?></p>
            </div>
          </div><?php
        }

        ?><div class="form-group">
          <label class="col-sm-5 control-label"><?=
              halio_get_settings_row('checkout_pick_up_time_label')->value;
            ?></label>
          <div class="col-sm-7">
            <p class="static-form-control"><?= $woocommerce->session->get('halio_pick_up_time'); ?></p>
          </div>
        </div><?php

        if (
          $woocommerce->session->get('halio_return_pick_up_time') != '' &&
          halio_get_settings_row('form_can_edit_direction')->value
        ) {
          ?><div class="form-group">
            <label class="col-sm-5 control-label"><?=
              halio_get_settings_row('checkout_return_pick_up_time_label')->value;
            ?></label>
            <div class="col-sm-7">
              <p class="static-form-control"><?= $woocommerce->session->get('halio_return_pick_up_time'); ?></p>
            </div>
          </div><?php
        }

        ?><div class="form-group">
          <label class="col-sm-5 control-label"><?=
            halio_get_settings_row('checkout_duration_label')->value;
          ?></label>
          <div class="col-sm-7">
            <p class="static-form-control"><?= $woocommerce->session->get('halio_duration'); ?></p>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-5 control-label"><?=
            halio_get_settings_row('checkout_distance_label')->value;
          ?></label>
          <div class="col-sm-7">
            <p class="static-form-control"><?= $woocommerce->session->get('halio_distance'); ?></p>
          </div>
        </div>
      </div><?php
    }
  }

  public function halio_update_order_meta($order_id) {
    global $wpdb;

    if ( isset($_POST['halio_starting_address']) ) {

      update_post_meta($order_id, 'starting_address', $_POST['halio_starting_address']);
      update_post_meta($order_id, 'destination_address', $_POST['halio_destination_address']);
      update_post_meta($order_id, 'pick_up_time', $_POST['halio_pick_up_time']);
      update_post_meta($order_id, 'duration', $_POST['halio_duration']);
      update_post_meta($order_id, 'distance', $_POST['halio_distance']);
      update_post_meta($order_id, 'starting_lat', $_POST['halio_starting_coords_lat']);
      update_post_meta($order_id, 'starting_long', $_POST['halio_starting_coords_long']);
      update_post_meta($order_id, 'destination_lat', $_POST['halio_destination_coords_lat']);
      update_post_meta($order_id, 'destination_long', $_POST['halio_destination_coords_long']);
      update_post_meta($order_id, 'duration_in_seconds', $_POST['halio_duration_in_seconds']);

      if ( isset($_POST['halio_return_pick_up_time']) ) {
        update_post_meta($order_id, 'return_pick_up_time', $_POST['halio_return_pick_up_time']);
      }

      if ( halio_get_settings_row('form_can_edit_vehicle_type')->value ) {
        update_post_meta($order_id, 'vehicle_id', $_POST['halio_vehicle_id']);
      } else {
        $vehicle_id = halio_get_settings_row('form_default_vehicle_id')->value;
        update_post_meta($order_id, 'vehicle_id', $vehicle_id);
      }

      if ( halio_get_settings_row('form_can_edit_direction')->value ) {
        update_post_meta($order_id, 'direction', $_POST['halio_direction']);
      } else {
        update_post_meta($order_id, 'direction', 'One Way');
      }

      if ( halio_get_settings_row('form_can_edit_occupants')->value ) {
        update_post_meta($order_id, 'occupants', $_POST['halio_occupants']);
      } else {
        update_post_meta($order_id, 'occupants', 'N/A');
      }

      $custom_fields = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "halio_custom_checkout_fields` WHERE `is_active` = 1");

      foreach ($custom_fields as $field) {
        update_post_meta($order_id, $field->label, $_POST[$field->key]);
      }
    }
  }

  public function calculate_price($cart) {
    global $woocommerce;

    // Done this way to make it secure so user cant change value in post request
    foreach ($cart->cart_contents as $key => $value) {
      if ( $value['product_id'] == halio_get_settings_row('wc_product_id')->value ) {
        $value['data']->set_price($woocommerce->session->get('halio_price'));
      }
    }
  }

  public function order_details($order) {
    global $wpdb;

    if ( get_post_meta($order->get_id(), 'starting_lat', true) ) {

      // Needs to be done like this to ensure still works for customers on early version
      // before translation was implemented
      $fields = array(
        'starting_address' => array(
          'untranslated' => 'Starting Address',
          'translated' => __('Starting Address', 'halio')
        ),
        'destination_address' => array(
          'untranslated' => 'Destination Address',
          'translated' => __('Destination Address', 'halio')
        ),
        'vehicle_id' => array(
          'untranslated' => 'Vehicle',
          'translated' => __('Vehicle', 'halio')
        ),
        'pick_up_time' => array(
          'untranslated' => 'Pick Up Time',
          'translated' => __('Pick Up Time', 'halio')
        )
      );

      if ( halio_get_post_meta($order->get_id(), 'Direction', 'direction', true) == 'Return' ) {
        $fields = array_merge($fields, array(
          'return_pick_up_time' => array(
            'untranslated' => 'Return Pick Up Time',
            'translated' => __('Return Pick Up Time', 'halio')
          )
        ));
      }

      // Done like this to preserve order of array
      $fields = array_merge($fields, array(
        'duration' => array(
          'untranslated' => 'Duration',
          'translated' => __('Duration', 'halio')
        ),
        'distance' => array(
          'untranslated' => 'Distance',
          'translated' => __('Distance', 'halio')
        ),
        'direction' => array(
          'untranslated' => 'Direction',
          'translated' => __('Direction', 'halio')
        ),
        'occupants' => array(
          'untranslated' => 'Occupants',
          'translated' => __('Occupants', 'halio')
        )
      ));

      ?><h4>Journey Information</h4><?php

      foreach ($fields as $key => $val) {
        if ( ($key_value = get_post_meta($order->get_id(), $key, true)) != '' ) {
          $field = $key;
          $value = $key_value;
          $vehicle_id = get_post_meta($order->get_id(), 'vehicle_id', true);
        } else {
          $field = $val['untranslated'];
          $value = get_post_meta($order->get_id(), $val['untranslated'], true);
          $vehicle_id = get_post_meta($order->get_id(), 'Vehicle ID', true);
        }

        ?><p>
          <strong><?= $val['translated'] ?></strong><br><?php
          if ( $field == 'vehicle_id' || $field == 'Vehicle ID' ) {
            $vehicle = $wpdb->get_row("SELECT * FROM `" . $wpdb->prefix . "halio_vehicles` WHERE `id` = '" . get_post_meta($order->get_id(), $field, true) . "';");
            ?><a href="<?= halio_edit_vehicle_path($vehicle_id); ?>"><?= $vehicle->name; ?></a><?php
          } else {
            echo $value;
          }
        ?></p><?php

        if (
          ($field == 'starting_address' || $field == 'Starting Address') &&
          get_post_meta($order->get_id(), 'starting_lat', true) &&
          get_post_meta($order->get_id(), 'starting_long', true)
        ) {
          ?><input type="hidden" value="<?= get_post_meta($order->get_id(), 'starting_lat', true); ?>" id="HalioStartingLat">
          <input type="hidden" value="<?= get_post_meta($order->get_id(), 'starting_long', true); ?>" id="HalioStartingLong">

          <div id="halio_order_origin_map"></div><?php
        } elseif (
          ($field == 'destination_address' || $field == 'Destination Address') &&
          get_post_meta($order->get_id(), 'destination_lat', true) &&
          get_post_meta($order->get_id(), 'destination_long', true)
        ) {
          ?><input type="hidden" value="<?= get_post_meta($order->get_id(), 'destination_lat', true); ?>" id="HalioDestinationLat">
          <input type="hidden" value="<?= get_post_meta($order->get_id(), 'destination_long', true); ?>" id="HalioDestinationLong">

          <div id="halio_order_destination_map"></div><?php
        }
      }

      $custom_fields = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "halio_custom_checkout_fields` WHERE `is_active` = 1");

      foreach ($custom_fields as $field) {
        ?><p>
          <strong><?= $field->label; ?></strong><br><?php
          echo get_post_meta($order->get_id(), $field->label, true);
        ?></p><?php
      }
    }
  }

  public function email_content($order, $sent_to_admin) {
    if ( get_post_meta($order->get_id(), 'starting_lat', true) ) {
      global $wpdb;
      $custom_fields = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "halio_custom_checkout_fields` WHERE `is_active` = 1");

      $fields = array(
        'starting_address' => __('Starting Address', 'halio'),
        'destination_address' => __('Destination Address', 'halio'),
        'pick_up_time' => __('Pick Up Time', 'halio')
      );

      // If its return then add return pick up time as well
      if ( get_post_meta($order->get_id(), 'direction', true) == halio_get_settings_row('checkout_return_label')->value ) {
        $fields = array_merge($fields, array('return_pick_up_time' => __('Return Pick Up Time', 'halio')));
      }

      if ( halio_get_settings_row('form_can_edit_occupants')->value ) {
        $fields = array_merge($fields, array('occupants' => __('Occupants', 'halio')));
      }

      if ( halio_get_settings_row('form_can_edit_vehicle_type')->value ) {
        $fields = array_merge($fields, array('vehicle_id' => __('Vehicle', 'halio')));
      }

      if ( halio_get_settings_row('form_can_edit_direction')->value ) {
        $fields = array_merge($fields, array('direction' => __('Direction', 'halio')));
      }

      $maps_url = "https://maps.googleapis.com/maps/api/staticmap";
      $maps_url .= "?size=500x400";
      $maps_url .= "&markers=color:green%7Clabel:A%7C" . get_post_meta($order->get_id(), 'starting_lat', true) . "," . get_post_meta($order->get_id(), 'starting_long', true);
      $maps_url .= "&markers=color:red%7Clabel:B%7C" . get_post_meta($order->get_id(), 'destination_lat', true) . "," . get_post_meta($order->get_id(), 'destination_long', true);

      ?><img src="<?= $maps_url; ?>">

      <table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; margin-top: 20px;" border="1">
        <thead>
          <tr>
            <th class="td" scope="col" style="text-align: left; color: #737373; border: 1px solid #e4e4e4; padding: 12px;"><?php
              _e('Field', 'halio');
            ?></th>
            <th class="td" scope="col" style="text-align: left; color: #737373; border: 1px solid #e4e4e4; padding: 12px;"><?php
              _e('Value', 'halio');
            ?></th>
          </tr>
        </thead>
        <tbody><?php
          // Default fields
          foreach ($fields as $key => $translated_val) {
            $value = get_post_meta($order->get_id(), $key, true);

            if ( $key == 'vehicle_id' ) {
              $value = $wpdb->get_row("SELECT * FROM `" . $wpdb->prefix . "halio_vehicles` WHERE `id` = '" . get_post_meta($order->get_id(), $key, true) . "';")->name;
            }

            ?><tr class="order_item">
              <td class="td" style="text-align: left; vertical-align: middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap: break-word; color: #737373; padding: 12px;"><?= $translated_val; ?></td>
              <td class="td" style="text-align: left; vertical-align: middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; padding: 12px;"><?= $value; ?></td>
            </tr><?php
          }

          // Custom Fields
          foreach ($custom_fields as $custom_field) {
            ?><tr class="order_item">
              <td class="td" style="text-align: left; vertical-align: middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap: break-word; color: #737373; padding: 12px;"><?= $custom_field->label; ?></td>
              <td class="td" style="text-align: left; vertical-align: middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; padding: 12px;">
                <?= get_post_meta($order->get_id(), $custom_field->label, true); ?>
              </td>
            </tr><?php
          }
        ?></tbody>
      </table><?php
    }
  }

  private function set_checkout_journey_information() {
    global $woocommerce;

    // To be used for the user-friendly Journey Information at checkout

    if ( halio_get_settings_row('form_can_edit_vehicle_type')->value ) {
      $woocommerce->session->set('halio_vehicle_id', $_POST['halio_vehicle_id']);
    } else {
      $woocommerce->session->set('halio_vehicle_id', halio_get_settings_row('form_default_vehicle_id')->value);
    }

    if ( halio_get_settings_row('form_can_edit_occupants')->value ) {
      $woocommerce->session->set('halio_occupants', $_POST['halio_occupants']);
    } else {
      $woocommerce->session->set('halio_occupants', '0');
    }

    if ( halio_get_settings_row('form_can_edit_direction')->value ) {
      $woocommerce->session->set('halio_direction', $_POST['halio_direction']);
    } else {
      $woocommerce->session->set('halio_direction', halio_get_settings_row('checkout_one_way_label')->value);
    }

    $woocommerce->session->set('halio_distance', $_POST['halio_distance']);
    $woocommerce->session->set('halio_duration', $_POST['halio_duration']);
    $woocommerce->session->set('halio_pick_up_time', $_POST['halio_pick_up_time']);
    $woocommerce->session->set('halio_return_pick_up_time', $_POST['halio_return_pick_up_time']);
    $woocommerce->session->set('halio_starting_address', $_POST['halio_starting_address']);
    $woocommerce->session->set('halio_destination_address', $_POST['halio_destination_address']);
    $woocommerce->session->set('halio_price', $_POST['halio_price']);
    $woocommerce->session->set('halio_distance_in_meters', $_POST['halio_distance_in_meters']);
    $woocommerce->session->set('halio_duration_in_seconds', $_POST['halio_duration_in_seconds']);
    $woocommerce->session->set('halio_starting_coords_lat', $_POST['halio_starting_coords_lat']);
    $woocommerce->session->set('halio_starting_coords_long', $_POST['halio_starting_coords_long']);
    $woocommerce->session->set('halio_destination_coords_lat', $_POST['halio_destination_coords_lat']);
    $woocommerce->session->set('halio_destination_coords_long', $_POST['halio_destination_coords_long']);
  }

  // Ensures the user hasn't fiddled with the price using JS
  private function check_quote_price_on_checkout() {
    if ( is_checkout() ) {
      global $woocommerce;

      $options = array(
        'distance_in_meters' => $woocommerce->session->get('halio_distance_in_meters'),
        'duration' => $woocommerce->session->get('halio_duration_in_seconds'),
        'pick_up_time' => $woocommerce->session->get('halio_pick_up_time'),
        'starting_coords' => array(
          'lat' => $woocommerce->session->get('halio_starting_coords_lat'),
          'long' => $woocommerce->session->get('halio_starting_coords_long')
        ),
        'destination_coords' => array(
          'lat' => $woocommerce->session->get('halio_destination_coords_lat'),
          'long' => $woocommerce->session->get('halio_destination_coords_long')
        )
      );

      if ( halio_get_settings_row('form_can_edit_occupants')->value ) {
        $options['occupants'] = $woocommerce->session->get('halio_occupants');
      }

      if ( halio_get_settings_row('form_can_edit_direction')->value ) {
        $options['journey_direction'] = $woocommerce->session->get('halio_direction');

        if ( $options['journey_direction'] == 'return' ) {
          $options['return_pick_up_time'] = $woocommerce->session->get('halio_return_pick_up_time');
        }
      }

      if ( halio_get_settings_row('form_can_edit_vehicle_type')->value ) {
        $options['vehicle_id'] = $woocommerce->session->get('halio_vehicle_id');
      }

      $estimator = new HalioPriceEstimator();
      $fare_price = $estimator->estimate_price($options);

      // Compare floats must be done like this because PHP
      $minimum_allowed_diff = 0.01;
      $session_price = floatval($woocommerce->session->get('halio_price'));
      $calc_price = round(floatval($fare_price['price']), 2);

      if( abs($session_price - $calc_price) >= $minimum_allowed_diff) {
        wp_redirect('/');
        exit;
      }
    }
  }

  // Displayed on Thankyou and View Order pages
  public function woocommerce_user_page_order_info($order_id) {

    if ( get_post_meta($order_id, 'starting_lat', true) ) {

      global $wpdb;
      global $woocommerce;

      $vehicle_name = $wpdb->get_row("SELECT * FROM `" . $wpdb->prefix . "halio_vehicles` WHERE `id` = '" . halio_get_post_meta($order_id, 'Vehicle ID', 'vehicle_id', true) . "';")->name;

      ?><h2>Journey Details</h2>

      <div class="halio-checkout-group">

        <input type="hidden" value="<?= get_post_meta($order_id, 'starting_lat', true); ?>" name="halio_starting_coords_lat" id="HalioStartingLat">
        <input type="hidden" value="<?= get_post_meta($order_id, 'starting_long', true); ?>" name="halio_starting_coords_long" id="HalioStartingLong">
        <input type="hidden" value="<?= get_post_meta($order_id, 'destination_lat', true); ?>" name="halio_destination_coords_lat" id="HalioDestinationLat">
        <input type="hidden" value="<?= get_post_meta($order_id, 'destination_long', true); ?>" name="halio_destination_coords_long" id="HalioDestinationLong">

        <div class="halio-checkout-map-container">
          <div id="thankyou_map"></div>
        </div>

        <div class="form-group">
          <label class="col-sm-5 control-label"><?=
            halio_get_settings_row('checkout_starting_address_label')->value;
          ?></label>
          <div class="col-sm-7">
            <p class="static-form-control"><?= halio_get_post_meta($order_id, 'Starting Address', 'starting_address', true); ?></p>
            <input type="hidden" value="<?= halio_get_post_meta($order_id, 'Starting Address', 'starting_address', true); ?>" id="halio_starting_address">
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-5 control-label"><?=
            halio_get_settings_row('checkout_destination_address_label')->value;
          ?></label>
          <div class="col-sm-7">
            <p class="static-form-control"><?= halio_get_post_meta($order_id, 'Destination Address', 'destination_address', true); ?></p>
            <input type="hidden" value="<?= halio_get_post_meta($order_id, 'Destination Address', 'destination_address', true); ?>" id="halio_destination_address">
          </div>
        </div><?php

        if ( halio_get_settings_row('form_can_edit_vehicle_type')->value ) {
          ?><div class="form-group">
            <label class="col-sm-5 control-label"><?=
              halio_get_settings_row('checkout_vehicle_label')->value;
            ?></label>
            <div class="col-sm-7">
              <p class="static-form-control"><?= $vehicle_name; ?></p>
            </div>
          </div><?php
        }

        if ( halio_get_settings_row('form_can_edit_occupants')->value ) {
          ?><div class="form-group">
            <label class="col-sm-5 control-label"><?=
              halio_get_settings_row('checkout_occupants_label')->value;
            ?></label>
            <div class="col-sm-7">
              <p class="static-form-control"><?= halio_get_post_meta($order_id, 'Occupants', 'occupants', true); ?></p>
            </div>
          </div><?php
        }

        if ( halio_get_settings_row('form_can_edit_direction')->value ) {
          ?><div class="form-group">
            <label class="col-sm-5 control-label"><?=
              halio_get_settings_row('checkout_direction_label')->value;
            ?></label>
            <div class="col-sm-7">
              <p class="static-form-control"><?= halio_get_post_meta($order_id, 'Direction', 'direction', true); ?></p>
            </div>
          </div><?php
        }

        ?><div class="form-group">
          <label class="col-sm-5 control-label"><?=
              halio_get_settings_row('checkout_pick_up_time_label')->value;
            ?></label>
          <div class="col-sm-7">
            <p class="static-form-control"><?= halio_get_post_meta($order_id, 'Pick Up Time', 'pick_up_time', true); ?></p>
          </div>
        </div><?php

        if ( halio_get_post_meta($order_id, 'Direction', 'direction', true) == halio_get_settings_row('checkout_return_label')->value ) {
          ?><div class="form-group">
            <label class="col-sm-5 control-label"><?=
                halio_get_settings_row('checkout_return_pick_up_time_label')->value;
              ?></label>
            <div class="col-sm-7">
              <p class="static-form-control"><?= halio_get_post_meta($order_id, 'Return Pick Up Time', 'return_pick_up_time', true); ?></p>
            </div>
          </div><?php
        }

        ?><div class="form-group">
          <label class="col-sm-5 control-label"><?=
            halio_get_settings_row('checkout_duration_label')->value;
          ?></label>
          <div class="col-sm-7">
            <p class="static-form-control"><?= halio_get_post_meta($order_id, 'Duration', 'duration', true); ?></p>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-5 control-label"><?=
            halio_get_settings_row('checkout_distance_label')->value;
          ?></label>
          <div class="col-sm-7">
            <p class="static-form-control"><?= halio_get_post_meta($order_id, 'Distance', 'distance', true); ?></p>
          </div>
        </div>
      </div><?php
    }
  }
}
