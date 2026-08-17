<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;

class AnnouncementController extends Controller
{
    public function index(): JsonResponse
    {
        $announcements = Announcement::query()
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(9);

        return response()->json([
            'data' => AnnouncementResource::collection($announcements),
            'meta' => [
                'current_page' => $announcements->currentPage(),
                'last_page' => $announcements->lastPage(),
                'total' => $announcements->total(),
            ],
        ]);
    }

    public function show(Announcement $announcement): JsonResponse
    {
        abort_unless($announcement->is_published, 404);

        return response()->json(['data' => new AnnouncementResource($announcement)]);
    }
}
