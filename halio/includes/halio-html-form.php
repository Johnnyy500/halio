<?php
if (!defined('ABSPATH')) { exit; }


class HalioHtmlForm {
  public function get_form($options = array()) {
    ob_start();
    require_once HALIO_PLUGIN_DIR . '/includes/pages/form.php';
    $output_string = ob_get_contents();
    ob_end_clean();

    return $output_string;
  }
}
