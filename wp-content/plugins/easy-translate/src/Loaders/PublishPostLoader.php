<?php

namespace EasyTranslate\Loaders;

use EasyTranslate\Api\Send\ApiService;
use EasyTranslate\Fields\FieldNameMapper;
use EasyTranslate\Fields\MetaBoxSectionHandler;
use WP_Post;

class PublishPostLoader implements LoaderInterface
{
    const META_TRANSLATION_FIELD = 'et_api_is_translation_meta';
    const META_TASK_ID_FIELD = 'et_api_task_id_meta';

    /**
     * Set the hooks required to translate the publisher post
     */
    public function load(): void
    {
        add_action('transition_post_status', [$this, 'sendContentForTranslate'], 10, 4);
    }

    /**
     * @param string  $newStatus
     * @param string  $oldStatus
     * @param WP_Post $post
     */
    public function sendContentForTranslate(string $newStatus, string $oldStatus, WP_Post $post): void
    {
        if ($newStatus !== 'publish' or $oldStatus === 'publish') {
            return;
        }

        $source = get_post_meta($post->ID, MetaBoxSectionHandler::SOURCE_LANGUAGE_FIELD, true);
        $target = get_post_meta($post->ID, MetaBoxSectionHandler::TARGET_LANGUAGES_FIELD, true);
        if (empty($target) or empty($source)) {
            return;
        }
        $options = get_option(SettingsLoader::OPTION_NAME);

        $service = new ApiService(FieldNameMapper::map($options));
        $content = [
            'post_title' => $post->post_title,
            'post_content' => $post->post_content,
            'post_excerpt' => $post->post_exceprt,
            'post_name' => $post->post_name,
        ];
        $projectName = "{$content['post_name']}-{$post->ID}";

        $this->createTranslatedPosts(
            $service->translate($source, $target, $content, $projectName),
            $post
        );
    }

    /**
     * @param array   $response
     * @param WP_Post $post
     */
    private function createTranslatedPosts(array $response, WP_Post $post): void
    {
        $tasks = array_filter($response['included'], function(array $included) {
            return $included['type'] === 'task';
        });

        foreach ($tasks as $task) {
            $attributes = $task['attributes'];
            $postId = wp_insert_post([
                'post_title' => "{$post->post_title}-({$attributes['target_language']})",
                'post_name' => "{$post->post_name}-{$attributes['target_language']}",
                'post_type' => $post->type,
            ]);

            update_post_meta($postId, self::META_TRANSLATION_FIELD, true);
            update_post_meta($postId, self::META_TASK_ID_FIELD, $task['id']);
        }
    }
}