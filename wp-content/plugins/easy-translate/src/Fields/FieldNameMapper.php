<?php

namespace EasyTranslate\Fields;

use EasyTranslate\Loaders\SettingsLoader;

class FieldNameMapper
{
    /**
     * @var array
     */
    private $options;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * @param array $options
     * @return array
     */
    public static function map(array $options): array
    {
        $instance = new self($options);

        return $instance->determine();
    }

    /**
     * @return array
     */
    public function determine(): array
    {
        if (($options[SettingsLoader::SANDBOX_MODE_FIELD] ?? true) === true) {
            return $this->sandboxFields();
        }

        return $this->productionFields();
    }

    /**
     * @return array
     */
    private function sandboxFields(): array
    {
        return [
            'client_id' => $this->options[SandboxCredentialSectionHandler::SANDBOX_CLIENT_ID_FIELD] ?? null,
            'client_secret' => $this->options[SandboxCredentialSectionHandler::SANDBOX_CLIENT_SECRET_FIELD] ?? null,
            'username' => $this->options[SandboxCredentialSectionHandler::SANDBOX_LOGIN_USERNAME_FIELD] ?? null,
            'password' => $this->options[SandboxCredentialSectionHandler::SANDBOX_LOGIN_PASSWORD_FIELD] ?? null,
            'access_token' => $this->options[SandboxCredentialSectionHandler::SANDBOX_ACCESS_TOKEN_FIELD] ?? null,
            'callback_url' => $this->options[SandboxCredentialSectionHandler::SANDBOX_CALLBACK_URL] ?? null,
        ];
    }

    /**
     * @return array
     */
    private function productionFields(): array
    {
        return $this->sandboxFields();
    }
}