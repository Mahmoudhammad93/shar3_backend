<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GoogleDriveService
{
    private ?string $clientId;

    private ?string $clientSecret;

    private ?string $refreshToken;

    public function __construct()
    {
        $config = config('services.google_drive');

        $this->clientId = $config['client_id'] ?? null;
        $this->clientSecret = $config['client_secret'] ?? null;
        $this->refreshToken = $config['refresh_token'] ?? null;
    }

    public function isConfigured(): bool
    {
        return filled($this->clientId)
            && filled($this->clientSecret)
            && filled($this->refreshToken);
    }

    public function accessToken(): string
    {
        $this->ensureConfigured();

        return Cache::remember('google_drive_access_token', 3000, function () {
            try {
                $response = Http::asForm()
                    ->post('https://oauth2.googleapis.com/token', [
                        'client_id' => $this->clientId,
                        'client_secret' => $this->clientSecret,
                        'refresh_token' => $this->refreshToken,
                        'grant_type' => 'refresh_token',
                    ])
                    ->throw()
                    ->json();
            } catch (RequestException $exception) {
                Log::warning('Google Drive token refresh failed', [
                    'status' => $exception->response?->status(),
                ]);

                throw new RuntimeException('تعذّر المصادقة مع Google Drive.', 0, $exception);
            }

            $token = $response['access_token'] ?? null;

            if (! is_string($token) || $token === '') {
                throw new RuntimeException('Google Drive access token missing.');
            }

            return $token;
        });
    }

    /** @return array<string, mixed> */
    public function getFileMetadata(string $fileId): array
    {
        try {
            $response = Http::withToken($this->accessToken())
                ->get("https://www.googleapis.com/drive/v3/files/{$fileId}", [
                    'fields' => 'id,name,mimeType,size',
                    'supportsAllDrives' => true,
                ])
                ->throw()
                ->json();
        } catch (RequestException $exception) {
            if ($exception->response?->status() === 404) {
                throw new RuntimeException('ملف Google Drive غير موجود.');
            }

            Log::warning('Google Drive metadata failed', [
                'file_id' => $fileId,
                'status' => $exception->response?->status(),
            ]);

            throw new RuntimeException('تعذّر الوصول إلى ملف Google Drive.', 0, $exception);
        }

        return is_array($response) ? $response : [];
    }

    /**
     * @return array{0: \Psr\Http\Message\StreamInterface, 1: array<string, string>, 2: int|null}
     */
    public function openDownloadStream(string $fileId, ?string $resourceKey = null, ?string $range = null): array
    {
        $query = [
            'alt' => 'media',
            'supportsAllDrives' => 'true',
        ];

        if ($resourceKey) {
            $query['resourceKey'] = $resourceKey;
        }

        $request = Http::withToken($this->accessToken())
            ->withOptions(['stream' => true]);

        if ($range) {
            $request = $request->withHeaders(['Range' => $range]);
        }

        try {
            $response = $request->get("https://www.googleapis.com/drive/v3/files/{$fileId}", $query);
        } catch (RequestException $exception) {
            Log::warning('Google Drive download failed', [
                'file_id' => $fileId,
                'status' => $exception->response?->status(),
            ]);

            throw new RuntimeException('تعذّر تحميل ملف الصوت.', 0, $exception);
        }

        if (! $response->successful()) {
            throw new RuntimeException('تعذّر تحميل ملف الصوت.');
        }

        $headers = [];
        foreach (['Content-Type', 'Content-Length', 'Content-Range', 'Accept-Ranges'] as $header) {
            if ($response->header($header)) {
                $headers[$header] = $response->header($header);
            }
        }

        $totalSize = null;
        if (isset($headers['Content-Length']) && is_numeric($headers['Content-Length'])) {
            $totalSize = (int) $headers['Content-Length'];
        }

        return [$response->toPsrResponse()->getBody(), $headers, $totalSize];
    }

    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Google Drive is not configured.');
        }
    }
}
