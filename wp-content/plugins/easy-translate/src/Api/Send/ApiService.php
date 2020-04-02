<?php

namespace EasyTranslate\Api\Send;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class ApiService
{
    const WEB_HOOK_ACTION = 'et_api_web_hook_action';
    private const API_VERSION = 'v1';
    private const SANDBOX_URL = 'https://api.platform.sandbox.easytranslate.com';
    private const PRODUCTION_URL = 'https://api.platform.easytranslate.com';
    private const GRANT_TYPE_PASSWORD = 'password';

    const AVAILABLE_LANGUAGES = [
        'en' => 'English',
        'sv' => 'Swedish',
        'da' => 'Danish',
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
     * @var Client
     */
    private $httpClient;

    /**
     * ApiService constructor.
     * @param array $options ['client_id', 'client_secret', 'username', 'password', 'sandbox_mode', 'access_token', 'refresh_token']
     */
    public function __construct(array $options)
    {
        $this->setInitialValues($options);
        $this->createHttpClient();
    }

    public function login(): array
    {
        try {
            $response = $this->httpClient->post(
                '/oauth/token',
                ['form_params' => $this->loginCredentials()]
            );

            $body = $this->decodeBody($response);
            $this->accessToken = $body['access_token'];

            return $body;
        } catch (RequestException $exception) {
            $body = $this->decodeBody($exception->getResponse());

            return [
                'error' => $body['error_description'],
                'hint' => $body['hint'] ?? 'Please verify your credentials',
            ];
        }
    }

    /**
     * @return array
     */
    public function loggedCustomer(): array
    {
        $this->createHttpClient();
        $path = $this->versionedPath('user');
        try {
            $response = $this->httpClient->get($path);

            return $this->decodeBody($response)['data'];
        } catch (RequestException $exception) {
            $message = $this->decodeBody($exception->getResponse())['message'] ?? null;
            wp_die($message);
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
                    'name' => "WP-{$projectName}",
                    'callback_url' => "{$this->callbackUrl}/wp-admin/admin-ajax.php?action=" . ApiService::WEB_HOOK_ACTION,
                ],
            ]);

            return $this->decodeBody($response);
        } catch (RequestException $exception) {
            $message = $this->decodeBody($exception->getResponse())['message'] ?? null;
            wp_die($message);
        }
    }

    /**
     * @param string $path
     * @return array
     */
    public function getTargetContent(string $path): array
    {
        try {
            $response = $this->httpClient->get($path);

            return $this->decodeBody($response);
        } catch (RequestException $exception) {
            $message = $this->decodeBody($exception->getResponse())['message'] ?? null;
            wp_die($message);
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

    private function createHttpClient(): void
    {
        $url = ($this->sandboxMode === true) ? self::SANDBOX_URL : self::PRODUCTION_URL;
        $headers = ['User-Agent' => 'EasyTranslate/Wordpress+0.0.1'];
        if ($this->accessToken !== null) {
            $headers['Authorization'] = "Bearer {$this->accessToken}";
        }
        $this->httpClient = new Client(['base_uri' => $url, 'headers' => $headers]);
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