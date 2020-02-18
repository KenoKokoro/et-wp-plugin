<?php

require_once plugin_dir_path(__FILE__) . 'EasyTranslateLoaderInterface.php';
require_once plugin_dir_path(__DIR__) . 'Fields/EasyTranslateCredentialSectionHandler.php';

/**
 * Fired during plugin activation
 * This class defines all code necessary to build the settings page
 * @since      0.0.1
 * @package    EasyTranslate
 * @subpackage EasyTranslate/src
 * @author     Stefan Brankovikj <sbk@easytranslate.com>
 */
class EasyTranslateSettingsLoader implements EasyTranslateLoaderInterface
{
    private const OPTION_NAME = 'easy_translate_api_integration';
    private const PAGE_NAME = 'easy_translate_api_page';
    private const CREDENTIAL_SECTION_NAME = 'et_api_credentials_section';

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
        register_setting(
            'easy-translate-api-group',
            self::OPTION_NAME,
            [$this, 'sanitizeBeforeSave']
        );

        $this->registerCredentialsSection();
    }

    public function showApiCredentialsSectionInfo(): void
    {
        echo __('Enter your settings bellow');
    }

    public function sanitizeBeforeSave(?array $input)
    {
        $newInput = [];

        // TODO: Add error handling with API call
        if (!($input[EasyTranslateCredentialSectionHandler::CLIENT_ID_FIELD] ?? false)) {
            add_settings_error(
                self::CREDENTIAL_SECTION_NAME,
                self::CREDENTIAL_SECTION_NAME . '-error',
                'Error',
                'error'
            );
        }
        foreach ($input as $id => $value) {
            $newInput[$id] = sanitize_text_field($value);
        }

        return $newInput;
    }

    private function registerCredentialsSection(): void
    {
        $credentialHandler = new EasyTranslateCredentialSectionHandler(self::OPTION_NAME);

        add_settings_section(
            self::CREDENTIAL_SECTION_NAME, // ID
            'Login Credentials', // Title
            [$this, 'showApiCredentialsSectionInfo'], // Callback
            self::PAGE_NAME // Page
        );
        add_settings_field(
            EasyTranslateCredentialSectionHandler::CLIENT_ID_FIELD, // ID
            __('Client ID'), // Title
            [$credentialHandler, 'clientIdHandler'], // Callback
            self::PAGE_NAME, // Page
            self::CREDENTIAL_SECTION_NAME // Section
        );
        add_settings_field(
            EasyTranslateCredentialSectionHandler::CLIENT_SECRET_FIELD,
            __('Client Secret'),
            [$credentialHandler, 'clientSecretHandler'],
            self::PAGE_NAME,
            self::CREDENTIAL_SECTION_NAME
        );
        add_settings_field(
            EasyTranslateCredentialSectionHandler::LOGIN_USERNAME_FIELD,
            __('Username'),
            [$credentialHandler, 'usernameHandler'],
            self::PAGE_NAME,
            self::CREDENTIAL_SECTION_NAME
        );
        add_settings_field(
            EasyTranslateCredentialSectionHandler::LOGIN_PASSWORD_FIELD,
            __('Password'),
            [$credentialHandler, 'passwordHandler'],
            self::PAGE_NAME,
            self::CREDENTIAL_SECTION_NAME
        );
    }
}
