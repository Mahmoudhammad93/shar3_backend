<?php

namespace App\Services;

use App\Enums\LessonBunnyStatus;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BunnyStreamService
{
    private string $libraryId;

    private string $apiKey;

    private ?string $cdnHostname;

    private ?string $webhookSigningKey;

    public function __construct()
    {
        $config = config('services.bunny_stream');

        $this->libraryId = (string) ($config['library_id'] ?? '');
        $this->apiKey = (string) ($config['api_key'] ?? '');
        $this->cdnHostname = $config['cdn_hostname'] ?? null;
        $this->webhookSigningKey = $config['webhook_signing_key'] ?? null;
    }

    public function libraryId(): string
    {
        return $this->libraryId;
    }

    public function isConfigured(): bool
    {
        return $this->libraryId !== '' && $this->apiKey !== '';
    }

    /** @return array<string, mixed> */
    public function createVideo(string $title): array
    {
        $this->ensureConfigured();

        try {
            $response = $this->client()
                ->post("/library/{$this->libraryId}/videos", [
                    'title' => $title,
                ])
                ->throw()
                ->json();
        } catch (RequestException $exception) {
            Log::warning('Bunny Stream create video failed', [
                'status' => $exception->response?->status(),
                'body' => $exception->response?->json(),
            ]);

            throw new RuntimeException('تعذّر إنشاء فيديو على Bunny Stream.', 0, $exception);
        }

        return is_array($response) ? $response : [];
    }

    public function uploadVideo(string $videoId, string $contents): void
    {
        $this->ensureConfigured();

        if ($contents === '') {
            throw new RuntimeException('ملف الفيديو فارغ.');
        }

        try {
            $this->client()
                ->timeout(3600)
                ->withBody($contents, 'application/octet-stream')
                ->put("/library/{$this->libraryId}/videos/{$videoId}")
                ->throw();
        } catch (RequestException $exception) {
            Log::warning('Bunny Stream upload failed', [
                'video_id' => $videoId,
                'status' => $exception->response?->status(),
            ]);

            throw new RuntimeException('تعذّر رفع الفيديو إلى Bunny Stream.', 0, $exception);
        }
    }

    /** @return array<string, mixed> */
    public function getVideo(string $videoId): array
    {
        $this->ensureConfigured();

        try {
            $response = $this->client()
                ->get("/library/{$this->libraryId}/videos/{$videoId}")
                ->throw()
                ->json();
        } catch (RequestException $exception) {
            Log::warning('Bunny Stream get video failed', [
                'video_id' => $videoId,
                'status' => $exception->response?->status(),
            ]);

            throw new RuntimeException('تعذّر جلب حالة الفيديو من Bunny Stream.', 0, $exception);
        }

        return is_array($response) ? $response : [];
    }

    public function deleteVideo(string $videoId): void
    {
        $this->ensureConfigured();

        try {
            $this->client()
                ->delete("/library/{$this->libraryId}/videos/{$videoId}")
                ->throw();
        } catch (RequestException $exception) {
            Log::warning('Bunny Stream delete video failed', [
                'video_id' => $videoId,
                'status' => $exception->response?->status(),
            ]);

            throw new RuntimeException('تعذّر حذف الفيديو من Bunny Stream.', 0, $exception);
        }
    }

    /** @return array{library_id: string, video_id: string, expiration: int, signature: string, endpoint: string} */
    public function tusUploadAuthorization(string $videoId, int $ttlSeconds = 3600): array
    {
        $this->ensureConfigured();

        $expiration = time() + max(60, $ttlSeconds);
        $signature = hash('sha256', $this->libraryId.$this->apiKey.$expiration.$videoId);

        return [
            'library_id' => $this->libraryId,
            'video_id' => $videoId,
            'expiration' => $expiration,
            'signature' => $signature,
            'endpoint' => 'https://video.bunnycdn.com/tusupload',
        ];
    }

    public function embedUrl(string $videoId): string
    {
        return "https://iframe.mediadelivery.net/embed/{$this->libraryId}/{$videoId}";
    }

    public function playerUrl(string $videoId): string
    {
        return "https://player.mediadelivery.net/embed/{$this->libraryId}/{$videoId}";
    }

    public function playbackUrl(string $videoId): ?string
    {
        if (! $this->cdnHostname) {
            return null;
        }

        $host = rtrim($this->cdnHostname, '/');

        return "https://{$host}/{$videoId}/play_720p.mp4";
    }

    public function mapRemoteStatus(?int $status): LessonBunnyStatus
    {
        if ($status === null) {
            return LessonBunnyStatus::Created;
        }

        return LessonBunnyStatus::fromBunnyStreamStatus($status);
    }

    public function verifyWebhookSignature(string $rawBody, ?string $signature, ?string $version, ?string $algorithm): bool
    {
        $secret = $this->webhookSigningKey ?: $this->apiKey;

        if ($secret === '' || $signature === null) {
            return false;
        }

        if ($version !== null && $version !== 'v1') {
            return false;
        }

        if ($algorithm !== null && $algorithm !== 'hmac-sha256') {
            return false;
        }

        $expected = hash_hmac('sha256', $rawBody, $secret);

        return hash_equals($expected, strtolower($signature));
    }

    private function client()
    {
        return Http::baseUrl('https://video.bunnycdn.com')
            ->withHeaders([
                'AccessKey' => $this->apiKey,
                'Accept' => 'application/json',
            ])
            ->timeout(120);
    }

    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Bunny Stream is not configured.');
        }
    }
}
