<?php
if (!defined('ABSPATH')) { exit; }


class HalioShortcodes {

  public static function shortcode_found($attributes) {
    $form = new HalioHtmlForm();

    $options = shortcode_atts(array(
      // default values
      'verticle' => false,
      'vertical' => false
    ), $attributes);

    return $form->get_form($options);
  }
}
