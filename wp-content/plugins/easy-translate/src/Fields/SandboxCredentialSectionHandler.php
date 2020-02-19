<?php

namespace EasyTranslate\Fields;

use EasyTranslate\Api\Send\ApiService;
use EasyTranslate\Loaders\SettingsLoader;

class SandboxCredentialSectionHandler
{
    const SANDBOX_CLIENT_ID_FIELD = 'et_api_sandbox_client_id';
    const SANDBOX_CLIENT_SECRET_FIELD = 'et_api_sandbox_client_secret';
    const SANDBOX_LOGIN_USERNAME_FIELD = 'et_api_sandbox_login_username';
    const SANDBOX_LOGIN_PASSWORD_FIELD = 'et_api_sandbox_login_password';
    const SANDBOX_ACCESS_TOKEN_FIELD = 'et_api_sandbox_access_token';
    const SANDBOX_ACCESS_TOKEN_TTL_FIELD = 'et_api_sandbox_access_token_ttl';
    const SANDBOX_CALLBACK_URL = 'et_api_sandbox_callback_url';

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var string
     */
    private $optionName;

    public function __construct()
    {
        $this->optionName = SettingsLoader::OPTION_NAME;
        $this->options = get_option($this->optionName);
    }

    public function sanitizeBeforeSave(?array $input)
    {
        $newInput = [];

        foreach ($input as $id => $value) {
            $newInput[$id] = sanitize_text_field($value);
        }

        $service = new ApiService(FieldNameMapper::map($newInput));
        $response = $service->login();
        if ($response['error'] ?? false) {
            add_settings_error(
                self::SANDBOX_ACCESS_TOKEN_FIELD,
                'access-token-error',
                $response['error'] ?? '',
                'error'
            );
            add_settings_error(
                self::SANDBOX_ACCESS_TOKEN_FIELD,
                'access-token-error',
                $response['hint'] ?? '',
                'info'
            );
            $newInput[self::SANDBOX_ACCESS_TOKEN_FIELD] = '';
            $newInput[self::SANDBOX_ACCESS_TOKEN_TTL_FIELD] = '';

            return $newInput;
        }
        $newInput[self::SANDBOX_ACCESS_TOKEN_FIELD] = $response['access_token'];
        $newInput[self::SANDBOX_ACCESS_TOKEN_TTL_FIELD] = date(
            'Y-m-d H:i:s',
            strtotime('now') + $response['expires_in']
        );

        return $newInput;
    }

    public function showFields(): void
    {
        add_settings_field(
            self::SANDBOX_CLIENT_ID_FIELD, // ID
            __('Client ID'), // Title
            [$this, 'clientIdHandler'], // Callback
            SettingsLoader::PAGE_NAME, // Page
            SettingsLoader::CREDENTIAL_SANDBOX_SECTION_NAME // Section
        );
        add_settings_field(
            self::SANDBOX_CLIENT_SECRET_FIELD,
            __('Client Secret'),
            [$this, 'clientSecretHandler'],
            SettingsLoader::PAGE_NAME,
            SettingsLoader::CREDENTIAL_SANDBOX_SECTION_NAME
        );
        add_settings_field(
            self::SANDBOX_LOGIN_USERNAME_FIELD,
            __('Username'),
            [$this, 'usernameHandler'],
            SettingsLoader::PAGE_NAME,
            SettingsLoader::CREDENTIAL_SANDBOX_SECTION_NAME
        );
        add_settings_field(
            self::SANDBOX_LOGIN_PASSWORD_FIELD,
            __('Password'),
            [$this, 'passwordHandler'],
            SettingsLoader::PAGE_NAME,
            SettingsLoader::CREDENTIAL_SANDBOX_SECTION_NAME
        );
        add_settings_field(
            self::SANDBOX_ACCESS_TOKEN_FIELD,
            __('Access Token'),
            [$this, 'accessToken'],
            SettingsLoader::PAGE_NAME,
            SettingsLoader::CREDENTIAL_SANDBOX_SECTION_NAME
        );
        add_settings_field(
            self::SANDBOX_ACCESS_TOKEN_TTL_FIELD,
            __('Access Token Valid Until'),
            [$this, 'accessTokenTtl'],
            SettingsLoader::PAGE_NAME,
            SettingsLoader::CREDENTIAL_SANDBOX_SECTION_NAME
        );
        add_settings_field(
            self::SANDBOX_CALLBACK_URL,
            __('Webhook URL'),
            [$this, 'callbackUrl'],
            SettingsLoader::PAGE_NAME,
            SettingsLoader::CREDENTIAL_SANDBOX_SECTION_NAME
        );
    }

    /**
     * Show the client ID field
     */
    public function clientIdHandler(): void
    {
        $this->textField(self::SANDBOX_CLIENT_ID_FIELD, 'Client ID from our platform.');
    }

    /**
     * Show the client secret field
     */
    public function clientSecretHandler(): void
    {
        $this->textField(self::SANDBOX_CLIENT_SECRET_FIELD, 'Client Secret from our platform.');
    }

    /**
     * Show the username field
     */
    public function usernameHandler(): void
    {
        $this->textField(self::SANDBOX_LOGIN_USERNAME_FIELD, 'Login Username from our platform.');
    }

    /**
     * Show the password field
     */
    public function passwordHandler(): void
    {
        $this->textField(self::SANDBOX_LOGIN_PASSWORD_FIELD, 'Login Password from our platform.');
    }

    public function callbackUrl(): void
    {
        $this->textField(self::SANDBOX_CALLBACK_URL, 'Callback URL');
    }

    /**
     * Show the access token field
     */
    public function accessToken(): void
    {
        if ($this->options[self::SANDBOX_ACCESS_TOKEN_FIELD] ?? false) {
            echo "<p><strong>Access token is set properly.</strong></p>";

            return;
        }

        echo "<p class=\"notice notice-error settings-error\"><span><strong>Access token is not yet set.</strong></span></p>";
    }

    /**
     * Show the access token TTL field
     */
    public function accessTokenTtl(): void
    {
        if ($this->options[self::SANDBOX_ACCESS_TOKEN_TTL_FIELD] ?? false) {
            echo "<p><strong>{$this->options[self::SANDBOX_ACCESS_TOKEN_TTL_FIELD]}</strong></p>";

            return;
        }

        echo "<p class=\"notice notice-error settings-error\"><strong>Access token is not yet set.</strong></p>";
    }

    /**
     * Base wrapper for the text fields
     * @param string $id
     * @param string $placeholder
     */
    private function textField(string $id, string $placeholder): void
    {
        printf(
            "<input type=\"text\" id=\"{$id}\" name=\"{$this->optionName}[{$id}]\" value=\"%s\" placeholder=\"{$placeholder}\"/>",
            esc_attr($this->options[$id] ?? '')
        );
    }

    /**
     * Base wrapper for the text fields
     * @param string $id
     * @param string $placeholder
     */
    private function passwordField(string $id, string $placeholder): void
    {
        printf(
            "<input type=\"password\" id=\"{$id}\" name=\"{$this->optionName}[{$id}]\" value=\"%s\" placeholder=\"{$placeholder}\"/>",
            isset($this->options[$id]) ? '**********' : ''
        );
    }
}