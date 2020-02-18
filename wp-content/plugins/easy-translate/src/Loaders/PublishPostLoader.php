<?php

namespace EasyTranslate\Loaders;

use EasyTranslate\Api\Send\ApiService;
use EasyTranslate\Fields\FieldNameMapper;
use WP_Post;

class PublishPostLoader implements LoaderInterface
{
    /**
     * Set the hooks required to translate the publisher post
     */
    public function load(): void
    {
        add_action('publish_post', [$this, 'sendContentForTranslate'], 10, 2);
    }

    /**
     * @param int      $id
     * @param WP_Post $post
     */
    public function sendContentForTranslate(int $id, WP_Post $post): void
    {
        $options = get_option(SettingsLoader::OPTION_NAME);

        $service = new ApiService(FieldNameMapper::map($options));
        $content = [
            'post_title' => $post->post_title,
            'post_content' => $post->post_content,
            'post_excerpt' => $post->post_exceprt,
            'post_name' => $post->post_name,
        ];

        $service->translate($content);
    }
}