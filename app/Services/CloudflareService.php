<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Сервис для работы с Cloudflare API
 */
class CloudflareService
{
    private string $apiToken;
    private string $apiEmail;
    private string $apiKey;
    private string $baseUrl = 'https://api.cloudflare.com/client/v4';

    public function __construct()
    {
        $this->apiToken = (string) config('services.cloudflare.api_token', '');
        $this->apiEmail = (string) config('services.cloudflare.api_email', '');
        $this->apiKey = (string) config('services.cloudflare.api_key', '');
    }

    /**
     * Проверка настроен ли сервис
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiToken) || (!empty($this->apiEmail) && !empty($this->apiKey));
    }

    /**
     * Получить заголовки для API запросов
     */
    private function getHeaders(): array
    {
        if (!empty($this->apiToken)) {
            return [
                'Authorization' => "Bearer {$this->apiToken}",
                'Content-Type' => 'application/json',
            ];
        }

        return [
            'X-Auth-Email' => $this->apiEmail,
            'X-Auth-Key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Создать DNS зону (добавить домен)
     *
     * @param string $domain Домен для добавления
     * @param string $accountId Account ID (опционально)
     * @return array Результат создания зоны
     */
    public function createZone(string $domain, ?string $accountId = null): array
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('Cloudflare не настроен. Проверьте конфигурацию.');
        }

        $data = [
            'name' => $domain,
            'jump_start' => true, // Автоматически добавить DNS записи
        ];

        if ($accountId) {
            $data['account'] = ['id' => $accountId];
        }

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/zones", $data);

            $result = $response->json();

            if (!$response->successful() || !($result['success'] ?? false)) {
                $errors = $result['errors'] ?? [['message' => 'Unknown error']];
                throw new \RuntimeException('Ошибка Cloudflare: ' . $errors[0]['message']);
            }

            return $result['result'] ?? [];
        } catch (\Throwable $e) {
            Log::error('Cloudflare createZone error', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Получить информацию о зоне
     */
    public function getZone(string $zoneId): array
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('Cloudflare не настроен.');
        }

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/zones/{$zoneId}");

            $result = $response->json();

            if (!$response->successful() || !($result['success'] ?? false)) {
                throw new \RuntimeException('Ошибка получения информации о зоне');
            }

            return $result['result'] ?? [];
        } catch (\Throwable $e) {
            Log::error('Cloudflare getZone error', [
                'zone_id' => $zoneId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Найти зону по домену
     */
    public function findZoneByDomain(string $domain): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/zones", [
                    'name' => $domain,
                    'status' => 'active',
                ]);

            $result = $response->json();

            if (!$response->successful() || !($result['success'] ?? false)) {
                return null;
            }

            $zones = $result['result'] ?? [];
            return !empty($zones) ? $zones[0] : null;
        } catch (\Throwable $e) {
            Log::error('Cloudflare findZoneByDomain error', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Получить NS записи зоны
     */
    public function getZoneNameservers(string $zoneId): array
    {
        $zone = $this->getZone($zoneId);
        return $zone['name_servers'] ?? [];
    }

    /**
     * Добавить или обновить A запись
     */
    public function setARecord(string $zoneId, string $name, string $ip, int $ttl = 3600, bool $proxied = true): array
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('Cloudflare не настроен.');
        }

        // Сначала проверяем, есть ли уже A запись
        $existingRecord = $this->findDnsRecord($zoneId, 'A', $name);

        $data = [
            'type' => 'A',
            'name' => $name,
            'content' => $ip,
            'ttl' => $ttl,
            'proxied' => $proxied,
        ];

        try {
            if ($existingRecord) {
                // Обновляем существующую запись
                $response = Http::withHeaders($this->getHeaders())
                    ->put("{$this->baseUrl}/zones/{$zoneId}/dns_records/{$existingRecord['id']}", $data);
            } else {
                // Создаем новую запись
                $response = Http::withHeaders($this->getHeaders())
                    ->post("{$this->baseUrl}/zones/{$zoneId}/dns_records", $data);
            }

            $result = $response->json();

            if (!$response->successful() || !($result['success'] ?? false)) {
                $errors = $result['errors'] ?? [['message' => 'Unknown error']];
                throw new \RuntimeException('Ошибка Cloudflare: ' . $errors[0]['message']);
            }

            return $result['result'] ?? [];
        } catch (\Throwable $e) {
            Log::error('Cloudflare setARecord error', [
                'zone_id' => $zoneId,
                'name' => $name,
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Найти DNS запись
     */
    private function findDnsRecord(string $zoneId, string $type, string $name): ?array
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/zones/{$zoneId}/dns_records", [
                    'type' => $type,
                    'name' => $name,
                ]);

            $result = $response->json();

            if (!$response->successful() || !($result['success'] ?? false)) {
                return null;
            }

            $records = $result['result'] ?? [];
            return !empty($records) ? $records[0] : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Установить SSL режим на Flexible
     */
    public function setSslMode(string $zoneId, string $mode = 'flexible'): bool
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('Cloudflare не настроен.');
        }

        if (!in_array($mode, ['off', 'flexible', 'full', 'strict'])) {
            throw new \InvalidArgumentException('Неверный режим SSL. Доступны: off, flexible, full, strict');
        }

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->patch("{$this->baseUrl}/zones/{$zoneId}/settings/ssl", [
                    'value' => $mode,
                ]);

            $result = $response->json();

            return $response->successful() && ($result['success'] ?? false);
        } catch (\Throwable $e) {
            Log::error('Cloudflare setSslMode error', [
                'zone_id' => $zoneId,
                'mode' => $mode,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Получить статус зоны
     */
    public function getZoneStatus(string $zoneId): array
    {
        $zone = $this->getZone($zoneId);

        // Получаем SSL режим отдельно
        $sslMode = 'unknown';
        try {
            $sslResponse = Http::withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/zones/{$zoneId}/settings/ssl");
            $sslResult = $sslResponse->json();
            if ($sslResponse->successful() && ($sslResult['success'] ?? false)) {
                $sslMode = $sslResult['result']['value'] ?? 'unknown';
            }
        } catch (\Throwable $e) {
            // Игнорируем ошибки получения SSL
        }

        return [
            'status' => $zone['status'] ?? 'unknown',
            'name' => $zone['name'] ?? '',
            'nameservers' => $zone['name_servers'] ?? [],
            'ssl_mode' => $sslMode,
            'development_mode' => $zone['development_mode'] ?? 0,
        ];
    }

    /**
     * Проверить доступность домена
     */
    public function checkDomainAvailability(string $domain): bool
    {
        try {
            $response = Http::timeout(5)
                ->get("https://{$domain}");

            return $response->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Очистить кеш зоны
     */
    public function purgeCache(string $zoneId, bool $purgeEverything = true): bool
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('Cloudflare не настроен.');
        }

        try {
            $payload = $purgeEverything
                ? ['purge_everything' => true]
                : [];

            $response = Http::withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/zones/{$zoneId}/purge_cache", $payload);

            $result = $response->json();

            if (!$response->successful() || !($result['success'] ?? false)) {
                $errors = $result['errors'] ?? [['message' => 'Unknown error']];
                throw new \RuntimeException('Ошибка Cloudflare: ' . $errors[0]['message']);
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Cloudflare purgeCache error', [
                'zone_id' => $zoneId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
