<?php

namespace EasyTranslate\Loaders;

use EasyTranslate\Api\Send\ApiService;

class MetaBoxLoader implements LoaderInterface
{
    private const NAME = 'et_api_meta_box';

    public function load(): void
    {
        add_action('add_meta_boxes', [$this, 'registerMetaBox']);
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
     * @param $object
     */
    public function showMetaBoxFields($object): void
    {
        wp_nonce_field(basename(__FILE__), self::NAME . '-nonce');
        $availableLanguages = ApiService::AVAILABLE_LANGUAGES;

        require_once plugin_dir_path(__FILE__) . '../../html/easy-translate-meta-box.php';
    }
}