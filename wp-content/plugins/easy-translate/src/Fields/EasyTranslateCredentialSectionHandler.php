<?php

class EasyTranslateCredentialSectionHandler
{
    const CLIENT_ID_FIELD = 'et_api_client_id';
    const CLIENT_SECRET_FIELD = 'et_api_client_secret';
    const LOGIN_USERNAME_FIELD = 'et_api_login_username';
    const LOGIN_PASSWORD_FIELD = 'et_api_login_password';

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
    public function clientIdHandler(): void
    {
        $this->textField(self::CLIENT_ID_FIELD, 'Client ID from our platform.');
    }

    /**
     * Show the client secret field
     */
    public function clientSecretHandler(): void
    {
        $this->textField(self::CLIENT_SECRET_FIELD, 'Client Secret from our platform.');
    }

    /**
     * Show the username field
     */
    public function usernameHandler(): void
    {
        $this->textField(self::LOGIN_USERNAME_FIELD, 'Login Username from our platform.');
    }

    /**
     * Show the password field
     */
    public function passwordHandler(): void
    {
        $this->passwordField(self::LOGIN_PASSWORD_FIELD, 'Login Password from our platform.');
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
            $this->options[$id] ?? esc_attr($this->options[$id])
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
            "<input type=\"password\" id=\"{$id}\" name=\"my_option_name[{$id}]\" value=\"%s\" placeholder=\"{$placeholder}\"/>",
            isset($this->options[$id]) ? '**********' : ''
        );
    }
}