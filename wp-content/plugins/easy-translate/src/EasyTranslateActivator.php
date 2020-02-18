<?php

/**
 * Fired during plugin activation
 * This class defines all code necessary to run during the plugin's activation.
 * @since      0.0.1
 * @package    EasyTranslate
 * @subpackage EasyTranslate/src
 * @author     Stefan Brankovikj <sbk@easytranslate.com>
 */
class EasyTranslateActivator
{
    /**
     * Activate the plugin
     * @return void
     */
    public static function activate(): void
    {
        register_setting('general', 'Test');
    }
}
