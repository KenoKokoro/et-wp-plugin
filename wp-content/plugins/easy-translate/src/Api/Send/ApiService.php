<?php

namespace EasyTranslate\Api\Send;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class ApiService
{
    private const API_VERSION = 'v1';
    private const SANDBOX_URL = 'https://api.platform.sandbox.easytranslate.com';
    private const PRODUCTION_URL = 'https://api.platform.easytranslate.com';
    private const GRANT_TYPE_PASSWORD = 'password';

    const AVAILABLE_LANGUAGES = [
        'en' => 'English',
        'sw' => 'Swedish',
        'dk' => 'Danish',
        'no' => 'Norwegian',
    ];

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var bool
     */
    private $sandboxMode = true;

    /**
     * @var string|null
     */
    private $accessToken = null;

    /**
     * @var string|null
     */
    private $callbackUrl = null;

    /**
     * @var
     */
    private $httpClient;

    /**
     * ApiService constructor.
     * @param array $options ['client_id', 'client_secret', 'username', 'password', 'sandbox_mode', 'access_token', 'refresh_token']
     */
    public function __construct(array $options)
    {
        $this->setInitialValues($options);
        $this->httpClient = $this->createHttpClient();
    }

    public function login(): array
    {
        try {
            $response = $this->httpClient->post(
                '/oauth/token',
                ['form_params' => $this->loginCredentials()]
            );

            return $this->decodeBody($response);
        } catch (RequestException $exception) {
            $body = $this->decodeBody($exception->getResponse());

            return [
                'error' => $body['error_description'],
                'hint' => $body['hint'] ?? 'Please verify your credentials',
            ];
        }
    }

    /**
     * @param string      $source
     * @param array       $target
     * @param array       $content
     * @param string|null $projectName
     * @return array
     */
    public function translate(string $source, array $target, array $content, string $projectName = null): array
    {
        $path = $this->versionedPath('projects');
        try {
            $response = $this->httpClient->post($path, [
                'json' => [
                    'source_language' => $source,
                    'target_languages' => $target,
                    'content' => $content,
                    'name' => $projectName,
                    'callback_url' => $this->callbackUrl,
                ],
            ]);

            return $this->decodeBody($response);
        } catch (RequestException $exception) {
            return $this->decodeBody($exception->getResponse());
        }
    }

    /**
     * Set the credentials/login details
     * @param array $options
     */
    private function setInitialValues(array $options = []): void
    {
        $this->clientId = $options['client_id'] ?? null;
        $this->clientSecret = $options['client_secret'] ?? null;
        $this->username = $options['username'] ?? null;
        $this->password = $options['password'] ?? null;
        $this->accessToken = $options['access_token'] ?? null;
        $this->sandboxMode = $options['sandbox_mode'] ?? true;
        $this->callbackUrl = $options['callback_url'] ?? null;
    }

    /**
     * @return Client
     */
    private function createHttpClient(): Client
    {
        $url = ($this->sandboxMode === true) ? self::SANDBOX_URL : self::PRODUCTION_URL;
        $headers = ['User-Agent' => 'EasyTranslate/Wordpress+0.0.1'];
        if ($this->accessToken !== null) {
            $headers['Authorization'] = "Bearer {$this->accessToken}";
        }

        return new Client(['base_uri' => $url, 'headers' => $headers]);
    }

    /**
     * @return array
     */
    private function loginCredentials(): array
    {
        return [
            'grant_type' => self::GRANT_TYPE_PASSWORD,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'username' => $this->username,
            'password' => $this->password,
            'scope' => '',
        ];
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    private function decodeBody(ResponseInterface $response): array
    {
        return json_decode((string)$response->getBody(), true);
    }

    /**
     * @param string $path
     * @return string
     */
    private function versionedPath(string $path): string
    {
        return '/api/' . self::API_VERSION . "/{$path}";
    }
}