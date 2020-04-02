<div class="wrap">
  <h1><?php echo __('EasyTranslate Integration') ?></h1>
  <form method="post" action="options.php">
      <?php
      settings_errors();
      submit_button('Save Changes');
      settings_fields('easy-translate-api-group');
      do_settings_sections(\EasyTranslate\Loaders\SettingsLoader::PAGE_NAME);
      submit_button('Save Changes');
      ?>
  </form>
</div>