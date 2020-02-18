<?php
/**
 * Plugin Name: EasyTranslate
 * Plugin URI: https://easytranslate.com
 * Description: Translations made easy.
 * Version: 0.0.1
 * Author: Stefan Brankovikj
 */

// If this file is called directly, abort.
use EasyTranslate\Activator;
use EasyTranslate\Deactivator;
use EasyTranslate\EasyTranslateLoader;

if (!defined('WPINC')) {
    die;
}

require plugin_dir_path(__FILE__) . 'vendor/autoload.php';

// Current plugin version
define('EASY_TRANSLATE_V1_VERSION', '0.0.1');

/**
 * Activate the plugin and call the activation hooks
 */
if (!function_exists('et_v1_activate_plugin')) {
    function et_v1_activate_plugin(): void
    {
        Activator::activate();
    }
}

/**
 * Deactivate the plugin and call the deactivation hooks
 */
if (!function_exists('et_v1_deactivate_plugin')) {
    function et_v1_deactivate_plugin(): void
    {
        Deactivator::deactivate();
    }
}

register_activation_hook(__FILE__, 'et_v1_activate_plugin');
register_deactivation_hook(__FILE__, 'et_v1_deactivate_plugin');

/**
 * Start the plugin execution
 */
if (!function_exists('et_v1_handle_plugin')) {
    function et_v1_handle_plugin(): void
    {
        (new EasyTranslateLoader)->execute();
    }
}

et_v1_handle_plugin();
