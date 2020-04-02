<?php

namespace EasyTranslate;

use EasyTranslate\Loaders\LoaderInterface;
use EasyTranslate\Loaders\MetaBoxLoader;
use EasyTranslate\Loaders\PublishPostLoader;
use EasyTranslate\Loaders\SettingsLoader;
use EasyTranslate\Loaders\WebHookActionLoader;

/**
 * Fired during plugin activation
 * This class defines all code necessary to run during the plugin's activation.
 * @since      0.0.1
 * @package    EasyTranslate
 * @subpackage EasyTranslate/src
 * @author     Stefan Brankovikj <sbk@easytranslate.com>
 */
class EasyTranslateLoader
{
    private $loaders = [
        MetaBoxLoader::class,
        SettingsLoader::class,
        PublishPostLoader::class,
        WebHookActionLoader::class,
    ];

    /**
     * Start the plugin
     * @return void
     */
    public function execute(): void
    {
        $this->bootLoaders();
    }

    private function bootLoaders(): void
    {
        foreach ($this->loaders as $className) {
            /** @var LoaderInterface $instance */
            $instance = new $className;
            $instance->load();
        }
    }
}

return new EasyTranslateLoader();
