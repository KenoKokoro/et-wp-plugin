<div class="wrap">
  <h1><?php echo __('EasyTranslate Integration') ?></h1>
  <form method="post" action="options.php">
      <?php
      settings_errors();
      submit_button();
      settings_fields('easy-translate-api-group');
      do_settings_sections('easy_translate_api_page');
      submit_button();
      ?>

  </form>
</div>