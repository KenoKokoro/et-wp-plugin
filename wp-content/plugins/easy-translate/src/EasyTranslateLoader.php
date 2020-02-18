<?php

require_once plugin_dir_path(__DIR__) . 'src/Loaders/EasyTranslateSettingsLoader.php';

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
        EasyTranslateSettingsLoader::class,
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
            /** @var EasyTranslateLoaderInterface $instance */
            $instance = new $className;
            $instance->load();
        }
    }
}

return new EasyTranslateLoader();
