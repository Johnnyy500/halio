<div class="halio-settings-page form-settings"><?php

  if ( isset($flash) ) {
    ?><div class="alert alert-<?= $flash['type']; ?>" role="alert">
      <?= $flash['message']; ?>
    </div><?php
  }

  ?><h1 class="center"><?php
    _e('Form Design', 'halio');
  ?></h1>

  <div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active">
        <a href="#font-end-tab" aria-controls="home" role="tab" data-toggle="tab"><?php
          _e('Front End', 'halio');
        ?></a>
      </li>
      <li role="presentation">
        <a href="#checkout-tab" aria-controls="profile" role="tab" data-toggle="tab"><?php
          _e('Checkout', 'halio');
        ?></a>
      </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="font-end-tab"><?php
        require_once HALIO_PLUGIN_DIR . '/pages/form-design/tabs/front_end.php';
      ?></div>

      <div role="tabpanel" class="tab-pane" id="checkout-tab"><?php
        require_once HALIO_PLUGIN_DIR . '/pages/form-design/tabs/checkout.php';
      ?></div>
    </div>
  </div>
</div>
