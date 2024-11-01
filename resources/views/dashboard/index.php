<?php if (! defined('ABSPATH')) {
    exit;
} ?>

<div class="wpx-shortcodes-manager-light wrap">
  <h1>
    <?php _e('Shortcodes') ?>
    <a href="<?php echo add_query_arg('view', 'create') ?>" class="page-title-action"><?php _e('Add New') ?></a>
  </h1>

  <?php $feedback = get_transient('feedback');
if ($feedback) : ?>
    <div class="notice notice-success is-dismissible">
      <p>
        <?php echo $feedback ?>
      </p>
    </div>
  <?php endif; ?>

  <?php $errors = get_transient('errors');
if ($errors) : ?>
    <div class="notice notice-error is-dismissible">
      <p>
        <?php echo $errors ?>
      </p>
    </div>
  <?php endif; ?>

  <div>
    <form method="post">
      <?php
    $table->prepare_items();
$table->display(); ?>
    </form>
  </div>

</div>  