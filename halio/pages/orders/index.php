<?php
if (!defined('ABSPATH')) { exit; }


global $wpdb;
$orders = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "posts` WHERE `post_type` = 'shop_order';");

?><div class="halio-settings-page"><?php

  if ( isset($flash) ) {
    ?><div class="alert alert-<?= $flash['type']; ?>" role="alert">
      <?= $flash['message']; ?>
    </div><?php
  }

  ?><h1>Orders</h1>

  <form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Order</th>
          <th>Starting Address</th>
          <th>Destination Address</th>
          <th>Vehicle</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody><?php
        if ( !empty($orders) ) {
          foreach($orders as $order) {
            ?><tr>
              <td>
                <a href="<?= halio_view_order_path($order->ID); ?>">
                  #<?= $order->ID; ?>
                </a>
              </td>
              <td><?php
                echo halio_get_post_meta($order->ID, 'Starting Address', 'starting_address', true);
              ?></td>
              <td><?php
                echo halio_get_post_meta($order->ID, 'Destination Address', 'destination_address', true);
              ?></td>
              <td><?php
                echo halio_get_post_meta($order->ID, 'Vehicle', 'vehicle', true);
              ?></td>
              <td>
                <form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>" onsubmit="return confirm('Are you sure you want to delete this Vehicle?');" class="table-action-form">
                  <input type="hidden" name="delete_vehicle[id]" value="<?= $vehicle->id; ?>">
                  <input type="submit" value="Delete" class="btn btn-danger">
                </form>
                <a href="<?= halio_view_order_path($order->ID); ?>" class="btn btn-default">View</a>
              </td>
            </tr><?php
          }
        } else {
          ?><tr class="info">
            <td colspan="10" class="center">No results found.</td>
          </tr><?php
        }
      ?></tbody>
    </table>
  </form>
</div>
