<?php

use EasyTranslate\Fields\MetaBoxSectionHandler;

/** @var $post */
/** @var array $availableLanguages */
?>
<div>
  <div>
    <label for="<?php echo MetaBoxSectionHandler::SOURCE_LANGUAGE_FIELD; ?>">Source Language</label>
    <div>
      <select name="<?php echo MetaBoxSectionHandler::SOURCE_LANGUAGE_FIELD; ?>">
          <?php
          foreach ($availableLanguages as $iso => $name) {
              ?>
            <option value="<?php echo $iso; ?>"
                <?php echo (get_post_meta($post->ID, MetaBoxSectionHandler::SOURCE_LANGUAGE_FIELD,
                        true) === $iso) ? 'selected' : '' ?>
            ><?php echo $name; ?></option>
          <?php } ?>
      </select>
    </div>
  </div>
  <hr>
  <div>
    <label for="<?php echo MetaBoxSectionHandler::TARGET_LANGUAGES_FIELD; ?>">Target Languages</label>
      <?php
      foreach ($availableLanguages as $iso => $name) {
          ?>
        <div>
          <label for="meta-box-<?php echo $iso; ?>">
            <input type="checkbox" name="<?php echo MetaBoxSectionHandler::TARGET_LANGUAGES_FIELD; ?>[]"
                   id="meta-box-<?php echo $iso; ?>" value="<?php echo $iso; ?>"
                <?php echo (in_array($iso, get_post_meta($post->ID, MetaBoxSectionHandler::TARGET_LANGUAGES_FIELD,
                        true) ?? [])) ? 'checked' : '' ?>
            > <?php echo $name; ?>
          </label>
        </div>
      <?php } ?>
  </div>
</div>