<?php

/**
 * HyperDB PHP Client
 *
 * @author  Afaan Bilal
 * @link    https://afaan.dev
 * @license MIT
 */

declare(strict_types=1);

namespace AfaanBilal;

class HyperDB
{
    /**
     * HyperDB Server Address
     *
     * @var string
     */
    private string $address;

    /**
     * Username
     *
     * @var string
     */
    public string $username;

    /**
     * Password
     *
     * @var string
     */
    public string $password;

    /**
     * Authentication enabled?
     *
     * @var bool
     */
    private bool $authEnabled = false;

    /**
     * Authentication token (JWT).
     *
     * @var string
     */
    private string $token = "";

    /**
     * Constructor
     *
     * @param  string $address HyperDB Server Address
     * @param  string $username Username
     * @param  string $password Password
     *
     * @return string
     */
    public function __construct(string $address = 'http://localhost:8765', string $username = '', string $password = '')
    {
        $this->address = $address;

        $this->username = $username;
        $this->password = $password;

        if ($this->username !== '' && $this->password !== '') {
            $this->authEnabled = true;
        }
    }

    const Status_PONG = 'PONG';
    const Status_TRUE = 'YES';
    const Status_OK   = 'OK';
    const Status_INVALID_CREDENTIALS = 'INVALID_CREDENTIALS';
    const Status_AUTH_FAILED = 'AUTH_FAILED';

    public function http(string $url = '', string $method = 'GET', string $body = ''): string
    {
        $client = new \GuzzleHttp\Client();
        return (string) $client->request($method, $this->address . '/' . $url, $body === '' ? [] : ['body' => $body])->getBody();

        $options = [];

        if ($body !== "") {
            $options['body'] = $body;
        }

        if ($this->authEnabled) {
            if ($this->token === "") {
                $this->auth();
            }

            $options['headers'] = ['Auth' => $this->token];
        }

        $response = (string) $client->request($method, $this->address . '/' . $url, $options)->getBody();

        if ($response === self::Status_AUTH_FAILED) {
            $this->auth();
            $options['headers']['Auth'] = $this->token;
            $response = (string) $client->request($method, $this->address . '/' . $url, $options)->getBody();
        }

        return $response;
    }

    private function auth(): void
    {
        $client = new \GuzzleHttp\Client();
        $authResponse = (string) $client->request('POST', $this->address . '/auth', ['headers' => ['username' => $this->username, 'password' => $this->password]])->getBody();

        if ($authResponse === self::Status_INVALID_CREDENTIALS) {
            throw new \Exception("Invalid credentials.");
        }

        $this->token = $authResponse;
    }

    public function ping(): bool
    {
        return $this->http('ping') === self::Status_PONG;
    }

    public function version(): string
    {
        return $this->http();
    }

    public function has(string $key): bool
    {
        return $this->http("has/{$key}") === self::Status_TRUE;
    }

    public function get(string $key): string
    {
        return $this->http("data/{$key}");
    }

    public function set(string $key, string $value): string
    {
        return $this->http("data/{$key}", 'POST', $value);
    }

    public function delete(string $key): bool
    {
        return $this->http("data/{$key}", 'DELETE') === self::Status_OK;
    }

    public function all(): array
    {
        return json_decode($this->http('data'), true);
    }

    public function clear(): bool
    {
        return $this->http('data', 'DELETE') === self::Status_OK;
    }

    public function empty(): bool
    {
        return $this->http('empty') === self::Status_TRUE;
    }

    public function save(): bool
    {
        return $this->http('save', 'POST') === self::Status_OK;
    }

    public function reload(): bool
    {
        return $this->http('reload', 'POST') === self::Status_OK;
    }

    public function reset(): bool
    {
        return $this->http('reset', 'DELETE') === self::Status_OK;
    }
}
