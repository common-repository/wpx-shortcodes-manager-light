<?php if (! defined('ABSPATH')) {
    exit;
} ?>

<div class="wpx-shortcodes-manager-light wrap">
  <h2>
    <?php _e('Create a new own Shortcode') ?>
  </h2>

  <div class="wpbones-tabs">

    <?php WPXShortcodesManagerLight\PureCSSTabs\PureCSSTabsProvider::openTab(__('Editor', 'wpx-shortcodes-manager-light'), null, true) ?>

    <form method="post">
      <?php wp_nonce_field('wpx-shortcodes-manager-settings'); ?>
      <input type="hidden"
             name="action"
             value="store"/>

      <label for="shortcode_name">
        <?php _e('Shortocde', 'wpx-shortcodes-manager-light') ?>:
        <input type="text"
               id="shortcode_name"
               name="shortcode_name"
               placeholder="<?php _e('eg: notice') ?>"/>
      </label>

      <p class="wpxsm-info">
        <?php _e('This is the shortcode name that you will use in you post content.', 'wpx-shortcodes-manager-light') ?>
      </p>

      <textarea
          id="html"
          name="html"
          class="wpxsm-textarea"
          placeholder="<?php echo esc_attr(__('Eg: <span color="#f00">I am red $content</span>', 'wpx-shortcodes-manager-light')) ?>"></textarea>

      <p class="wpxsm-info">
        <?php _e('Edit any HTML markup and use the <code>$content</code> placeholder if you wish wrap shortcode content.', 'wpx-shortcodes-manager-light') ?>
      </p>

      <p class="submit clearfix">
        <button
            class="button button-primary right"><?php _e('Save changes', 'wpx-shortcodes-manager-light') ?></button>
      </p>

    </form>

    <?php WPXShortcodesManagerLight\PureCSSTabs\PureCSSTabsProvider::closeTab(); ?>

  </div>

</div>  