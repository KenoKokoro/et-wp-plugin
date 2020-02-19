<?php

namespace EasyTranslate\Loaders;

use EasyTranslate\Api\Send\ApiService;
use EasyTranslate\Fields\FieldNameMapper;
use EasyTranslate\Fields\SandboxCredentialSectionHandler;

/**
 * Fired during plugin activation
 * This class defines all code necessary to build the settings page
 * @since      0.0.1
 * @package    EasyTranslate
 * @subpackage EasyTranslate/src
 * @author     Stefan Brankovikj <sbk@easytranslate.com>
 */
class SettingsLoader implements LoaderInterface
{
    const OPTION_NAME = 'easy_translate_api_integration';
    const SANDBOX_MODE_FIELD = 'et_api_sandbox_mode';
    const PAGE_NAME = 'easy_translate_api_sandbox_page';
    const CREDENTIAL_SANDBOX_SECTION_NAME = 'et_api_sandbox_credentials_section';

    /**
     * Boot the loader
     */
    public function load(): void
    {
        add_action('admin_menu', [$this, 'registerPageMenu']);
        add_action('admin_init', [$this, 'initializeSettingsPage']);
    }

    /**
     * Register the settings menu for the settings page
     */
    public function registerPageMenu(): void
    {
        add_menu_page(
            __('EasyTranslate'),
            __('EasyTranslate'),
            'manage_options',
            self::PAGE_NAME,
            [$this, 'renderOptionsPage']
        );
    }

    /**
     * Show the HTML form for the settings
     */
    public function renderOptionsPage(): void
    {
        require_once plugin_dir_path(__FILE__) . '../../html/easy-translate-options.php';
    }

    /**
     * Register the form fields for the settings page
     */
    public function initializeSettingsPage(): void
    {
        $handler = new SandboxCredentialSectionHandler();
        register_setting(
            'easy-translate-api-group',
            self::OPTION_NAME,
            [$handler, 'sanitizeBeforeSave']
        );

        $this->registerCredentialsSection($handler);
    }

    public function showApiCredentialsSectionInfo(): void
    {
        echo __('Enter your sandbox credentials bellow');
    }

    /**
     * @param SandboxCredentialSectionHandler $handler
     */
    private function registerCredentialsSection(SandboxCredentialSectionHandler $handler): void
    {
        add_settings_section(
            self::CREDENTIAL_SANDBOX_SECTION_NAME, // ID
            'Login Sandbox Credentials', // Title
            [$this, 'showApiCredentialsSectionInfo'], // Callback
            self::PAGE_NAME // Page
        );

        $handler->showFields();
    }
}
