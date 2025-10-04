<?php
if (!defined('ABSPATH')) { exit; }


class HalioCalendar {

  public static function get_events() {
    global $wpdb;
    global $woocommerce;

    $product_id = halio_get_settings_row('wc_product_id')->value;
    $orders = array();

    $post_search_args = array(
      'post_type' => 'shop_order',
      'post_status' => array('wc-processing', 'wc-completed'),
      'posts_per_page' => '-1'
    );
    $post_search = new WP_Query($post_search_args);

    foreach ($post_search->posts as $customer_order) {
      $order = new WC_Order( $customer_order->ID );

      // Only show orders that are still viable e.g. not cancelled
      $unacceptable_status = array( 'wc-cancelled', 'wc-refunded', 'wc-failed', 'trash' );

      if (
        in_array(get_post_status($order->get_id()), $unacceptable_status) || // not acceptable e.g. cancelled
        !get_post_meta($order->get_id(), 'starting_lat', true) // not Halio order
      ) {
        continue;
      }

      $order->starting_address = halio_get_post_meta($order->get_id(), 'Starting Address', 'starting_address', true);
      $order->destination_address = halio_get_post_meta($order->get_id(), 'Destination Address', 'destination_address', true);

      $pick_up_time = halio_get_post_meta($order->get_id(), 'Pick Up Time', 'pick_up_time', true);

      $pick_up_datetime = DateTime::createFromFormat("d/m/Y H:i", $pick_up_time);
      $order->pick_up_time = $pick_up_datetime->format(DateTime::ISO8601);

      // If using the updated Halio and duration in seconds was added to order,
      // end time can be specified
      if ( $duration_in_seconds = get_post_meta($order->get_id(), 'duration_in_seconds', true) ) {
        $order->estimated_drop_off_time = date('d/m/Y H:i', $pick_up_datetime->getTimestamp() + $duration_in_seconds);
        $order->estimated_drop_off_time_iso_8601 = date('c', $pick_up_datetime->getTimestamp() + $duration_in_seconds);
      }
      $order->url = halio_wc_view_order_path($order->get_id());

      // Converts WooCommerce order status into human-friendly version.
      $pretty_status = str_replace('wc-', '', get_post_status($order->get_id()));
      $pretty_status = ucwords(str_replace('-', ' ', $pretty_status));

      $vehicle = $wpdb->get_row("SELECT * FROM `" . $wpdb->prefix . "halio_vehicles` WHERE `id` = '" . halio_get_post_meta($order->get_id(), 'Vehicle ID', 'vehicle_id', true) . "';");
      $order->vehicle_name = $vehicle->name;

      $order->description = "
        <h2>
          <a href='$order->url'>#" . $order->get_id() . "</a>
          " . $order->get_billing_first_name() . " " . $order->get_billing_last_name() . "</br>
          <small>
            (<a href='mailto:" . $order->get_billing_email() . "'>" . $order->get_billing_email() . "</a>; " . $order->get_billing_phone() . ")
          </small>
        </h2>

        <p>
          <strong>" . __('Starting Address', 'halio') . ":</strong> $order->starting_address</br>
          <strong>" . __('Destination Address', 'halio') . ":</strong> $order->destination_address</br>
          <strong>" . __('Vehicle', 'halio') . ":</strong>
            <a href='" . halio_edit_vehicle_path($vehicle->id) . "'>$vehicle->name</a></br>
          <strong>" . __('Pick Up Time', 'halio') . ":</strong> $pick_up_time</br>
          <strong>" . __('Duration', 'halio') . ":</strong> " . halio_get_post_meta($order->get_id(), 'Duration', 'duration', true) . "</br>
          <strong>" . __('Distance', 'halio') . ":</strong> " . halio_get_post_meta($order->get_id(), 'Distance', 'distance', true) . "</br>
          <strong>" . __('Order Status', 'halio') . ":</strong> $pretty_status</br>
        </p>";

      array_push($orders, $order);

      if ( halio_get_post_meta($order->get_id(), 'Return Pick Up Time', 'return_pick_up_time', true) ) {
        $return_order = clone($order);

        $ret_pick_up_time = halio_get_post_meta($order->get_id(), 'Return Pick Up Time', 'return_pick_up_time', true);

        if ( $ret_pick_up_time ) {
          $return_pick_up_datetime = DateTime::createFromFormat("d/m/Y H:i", $ret_pick_up_time);
          $return_order->pick_up_time = $return_pick_up_datetime->format(DateTime::ISO8601);
        }

        // If using the updated Halio and duration in seconds was added to order,
        // end time can be specified
        if ( $duration_in_seconds = get_post_meta($order->get_id(), 'duration_in_seconds', true) ) {
          $return_order->estimated_drop_off_time = date('d/m/Y H:i', $return_pick_up_datetime->getTimestamp() + $duration_in_seconds);
          $return_order->estimated_drop_off_time_iso_8601 = date('c', $return_pick_up_datetime->getTimestamp() + $duration_in_seconds);
        }

        // Swap addresses as it's return.
        $return_order->starting_address = $order->destination_address;
        $return_order->destination_address = $order->starting_address;

        $return_order->description = "
          <h2>
            <a href='$order->url'>#" . $order->get_id() . "</a>
            " . $order->get_billing_first_name() . " " . $order->get_billing_last_name() . "</br>
            <small>
              (<a href='mailto:" . $order->get_billing_email() . "'>" . $order->get_billing_email() . "</a>; " . $order->get_billing_phone() . ")
            </small>
          </h2>

          <p>
            <strong>" . __('Starting Address', 'halio') . ":</strong> $return_order->starting_address</br>
            <strong>" . __('Destination Address', 'halio') . ":</strong> $return_order->destination_address</br>
            <strong>" . __('Vehicle', 'halio') . ":</strong>
              <a href='" . halio_edit_vehicle_path($vehicle->id) . "'>$vehicle->name</a></br>
            <strong>" . __('Pick Up Time', 'halio') . ":</strong> $ret_pick_up_time</br>
            <strong>" . __('Duration', 'halio') . ":</strong> " . halio_get_post_meta($order->get_id(), 'Duration', 'duration', true) . "</br>
            <strong>" . __('Distance', 'halio') . ":</strong> " . halio_get_post_meta($order->get_id(), 'Distance', 'distance', true) . "</br>
            <strong>" . __('Order Status', 'halio') . ":</strong> $pretty_status</br>
          </p>";

        array_push($orders, $return_order);
      }
    }

    echo json_encode($orders);
    wp_die();
  }
}
