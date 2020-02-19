<?php

namespace EasyTranslate\Loaders;

use EasyTranslate\Api\Send\ApiService;
use EasyTranslate\Fields\MetaBoxSectionHandler;

class MetaBoxLoader implements LoaderInterface
{
    private const NAME = 'et_api_meta_box';

    /**
     * @var string
     */
    private $nonceField = self::NAME . '-nonce';

    public function load(): void
    {
        add_action('add_meta_boxes', [$this, 'registerMetaBox']);
        add_action('save_post', [$this, 'saveMetaValues'], 10, 3);
    }

    /**
     * Register the meta box to appear on the side
     */
    public function registerMetaBox(): void
    {
        add_meta_box(
            self::NAME,
            __('EasyTranslate'),
            [$this, 'showMetaBoxFields'],
            null,
            'side'
        );
    }

    /**
     * Show the HTML form
     * @param $post
     */
    public function showMetaBoxFields($post): void
    {
        wp_nonce_field(basename(__FILE__), $this->nonceField);
        $availableLanguages = ApiService::AVAILABLE_LANGUAGES;

        require_once plugin_dir_path(__FILE__) . '../../html/easy-translate-meta-box.php';
    }

    /**
     * @param int  $postId
     * @param      $post
     * @param bool $update
     */
    public function saveMetaValues(int $postId, $post, ?bool $update = null): void
    {
        (new MetaBoxSectionHandler($this->nonceField, basename(__FILE__)))->saveFields($postId, $post, $update);
    }
}