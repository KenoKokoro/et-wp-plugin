<?php

namespace EasyTranslate\Fields;

class CredentialSectionHandler
{
    const SANDBOX_CLIENT_ID_FIELD = 'et_api_sandbox_client_id';
    const SANDBOX_CLIENT_SECRET_FIELD = 'et_api_sandbox_client_secret';
    const SANDBOX_LOGIN_USERNAME_FIELD = 'et_api_sandbox_login_username';
    const SANDBOX_LOGIN_PASSWORD_FIELD = 'et_api_sandbox_login_password';
    const SANDBOX_ACCESS_TOKEN_FIELD = 'et_api_sandbox_access_token';
    const SANDBOX_ACCESS_TOKEN_TTL_FIELD = 'et_api_sandbox_access_token_ttl';

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var string
     */
    private $optionName;

    public function __construct(string $optionName)
    {
        $this->optionName = $optionName;
        $this->options = get_option($optionName);
    }

    /**
     * Show the client ID field
     */
    public function sandboxClientIdHandler(): void
    {
        $this->textField(self::SANDBOX_CLIENT_ID_FIELD, 'Client ID from our platform.');
    }

    /**
     * Show the client secret field
     */
    public function sandboxClientSecretHandler(): void
    {
        $this->textField(self::SANDBOX_CLIENT_SECRET_FIELD, 'Client Secret from our platform.');
    }

    /**
     * Show the username field
     */
    public function sandboxUsernameHandler(): void
    {
        $this->textField(self::SANDBOX_LOGIN_USERNAME_FIELD, 'Login Username from our platform.');
    }

    /**
     * Show the password field
     */
    public function sandboxPasswordHandler(): void
    {
        $this->textField(self::SANDBOX_LOGIN_PASSWORD_FIELD, 'Login Password from our platform.');
    }

    /**
     * Show the access token field
     */
    public function sandboxAccessToken(): void
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
    public function sandboxAccessTokenTtl(): void
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