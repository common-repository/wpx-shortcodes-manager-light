<?php if (! defined('ABSPATH')) {
    exit;
} ?>

<div class="wpxsm-settings wrap">

  <h2><?php _e('Shortcodes Manager Settings', 'wpx-shortcodes-manager-light') ?></h2>

  <?php if (isset($feedback)) : ?>
    <div class="updated notice is-dismissible">
      <p>
        <?php echo $feedback ?>
      </p>
    </div>
  <?php endif; ?>

  <div class="wpbones-tabs">
    <?php WPXShortcodesManagerLight\PureCSSTabs\PureCSSTabsProvider::openTab(__('Shortcodes Analysis', 'wpx-shortcodes-manager-light'), null, true) ?>
    <form method="post"
          action="">

      <?php wp_nonce_field('wpx-shortcodes-manager-settings'); ?>

      <div class="wpxsm-info">
        <?php _e('Enable a Shortcodes Analysis when you edit a post and display an Alert Message whether found any <strong>unregistered</strong> shortcodes.', 'wpx-shortcodes-manager-light') ?>
      </div>

      <p>
        <?php
        echo WPXShortcodesManagerLight\PureCSSSwitch\Html\HtmlTagSwitchButton::name('general/enable_alert_unregistered_shortcodes')
                                                                             ->checked($plugin->options->get('general/enable_alert_unregistered_shortcodes'))
                                                                             ->right_label(__('Enable Shortcodes Analysis', 'wpx-shortcodes-manager-light'));
?>
      </p>

      <p class="submit clearfix">
        <button name="reset-to-default"
                class="button button-secondary"><?php _e('Reset to default', 'wpx-shortcodes-manager-light') ?></button>
        <button class="button button-primary right"><?php _e('Save changes', 'wpx-shortcodes-manager-light') ?></button>
      </p>

    </form>
    <?php WPXShortcodesManagerLight\PureCSSTabs\PureCSSTabsProvider::closeTab() ?>
  </div>


</div>