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
     * Constructor
     *
     * @param  string $address HyperDB Server Address
     *
     * @return string
     */
    public function __construct(string $address = 'http://localhost:8765')
    {
        $this->address = $address;
    }

    public function http(string $url = '', string $method = 'GET', string $body = ''): string
    {
        $client = new \GuzzleHttp\Client();
        return (string) $client->request($method, $this->address . '/' . $url, $body === '' ? [] : ['body' => $body])->getBody();
    }

    const Status_PONG = 'PONG';
    const Status_TRUE = 'YES';
    const Status_OK   = 'OK';

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
