<?php

namespace EasyTranslate\Loaders;

use EasyTranslate\Api\Send\ApiService;
use EasyTranslate\Fields\FieldNameMapper;
use WP_Query;

class WebHookActionLoader implements LoaderInterface
{
    public function load(): void
    {
        add_action('wp_ajax_nopriv_' . ApiService::WEB_HOOK_ACTION, [$this, 'handle']);
    }

    public function handle(): void
    {
        $task = json_decode(file_get_contents("php://input"), true)['data'];

        $args = [
            'post_type' => 'any',
            'post_status' => 'any',
            'meta_query' => [
                [
                    'key' => PublishPostLoader::META_TASK_ID_FIELD,
                    'value' => $task['id'],
                ],
            ],
        ];
        $posts = get_posts($args);
        $post = $posts[0] ?? null;
        if (empty($post)) {
            wp_die();
        }

        $content = $this->fetchContent($task);
        $content['ID'] = $post->ID;

        wp_update_post($content);
    }

    /**
     * @param array $task
     * @return array
     */
    private function fetchContent(array $task): array
    {
        $options = get_option(SettingsLoader::OPTION_NAME);
        $service = new ApiService(FieldNameMapper::map($options));

        return $service->getTargetContent($task['attributes']['target_content']);
    }
}