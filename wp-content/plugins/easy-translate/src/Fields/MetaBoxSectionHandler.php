<?php

namespace EasyTranslate\Fields;

class MetaBoxSectionHandler
{
    const SOURCE_LANGUAGE_FIELD = 'et_api_source_language_meta';
    const TARGET_LANGUAGES_FIELD = 'et_api_target_languages_meta';

    /**
     * @var string
     */
    private $nonceField;

    /**
     * @var string
     */
    private $nonceValue;

    public function __construct(string $nonceField, string $nonceValue)
    {
        $this->nonceField = $nonceField;
        $this->nonceValue = $nonceValue;
    }

    public function saveFields(int $postId, $post, ?bool $update = null)
    {
        if (!isset($_POST[$this->nonceField]) or !wp_verify_nonce($_POST[$this->nonceField], $this->nonceValue)) {
            return $postId;
        }

        if (!current_user_can("edit_post", $postId)) {
            return $postId;
        }

        $sourceLanguage = null;
        if ($source = ($_POST[self::SOURCE_LANGUAGE_FIELD] ?? null)) {
            $sourceLanguage = $source;
        }
        update_post_meta($postId, self::SOURCE_LANGUAGE_FIELD, $sourceLanguage);

        $targetLanguages = [];
        if ($target = ($_POST[self::TARGET_LANGUAGES_FIELD] ?? [])) {
            $targetLanguages = $target;
        }
        update_post_meta($postId, self::TARGET_LANGUAGES_FIELD, $targetLanguages);
    }
}