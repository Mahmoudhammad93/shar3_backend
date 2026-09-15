<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Services\BunnyStreamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BunnyStreamWebhookController extends Controller
{
    public function __invoke(Request $request, BunnyStreamService $bunny): JsonResponse
    {
        $rawBody = $request->getContent();
        $signature = $request->header('X-BunnyStream-Signature');

        if (! $bunny->verifyWebhookSignature(
            $rawBody,
            is_string($signature) ? $signature : null,
            $request->header('X-BunnyStream-Signature-Version'),
            $request->header('X-BunnyStream-Signature-Algorithm'),
        )) {
            Log::warning('Rejected Bunny Stream webhook: invalid signature');

            return response()->json(['message' => 'Unauthorized'], 401);
        }

        /** @var array<string, mixed> $payload */
        $payload = json_decode($rawBody, true) ?: [];

        $videoId = $payload['VideoGuid'] ?? $payload['videoGuid'] ?? null;
        $status = isset($payload['Status']) ? (int) $payload['Status'] : null;

        if (! is_string($videoId) || $videoId === '') {
            return response()->json(['message' => 'Ignored'], 202);
        }

        $lesson = Lesson::query()->where('bunny_video_id', $videoId)->first();

        if (! $lesson) {
            Log::info('Bunny webhook for unknown video', ['video_id' => $videoId]);

            return response()->json(['message' => 'No matching lesson'], 202);
        }

        $lesson->update([
            'bunny_status' => $bunny->mapRemoteStatus($status)->value,
        ]);

        return response()->json(['message' => 'OK']);
    }
}
