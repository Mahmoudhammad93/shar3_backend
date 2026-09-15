# Lesson media — frontend API contract

Backend-only reference for the Next.js student portal (source lives in `website/`).

## Student course detail (`GET /api/v1/student/courses/{courseId}`)

Each lesson object includes legacy and new fields:

```json
{
  "id": 1,
  "title_ar": "...",
  "content_ar": "...",
  "video_url": "https://...",
  "media_type": "video",
  "video_provider": "bunny",
  "bunny": {
    "library_id": "12345",
    "video_id": "guid",
    "status": "ready",
    "embed_url": "https://iframe.mediadelivery.net/embed/12345/guid",
    "playback_url": "https://cdn.example.com/guid/play_720p.mp4"
  },
  "audio": null
}
```

### Video

- Prefer `bunny.embed_url` with Bunny Player when `video_provider === "bunny"` and `bunny.status === "ready"`.
- Fallback: `video_url` for legacy YouTube/external links when `bunny` is absent.

### Audio

- When `media_type === "audio"`, use `audio.url` (`GET /api/v1/student/lessons/{id}/audio`) with the student Bearer token.
- Supports `Range` for seeking.

### Text

- When `media_type === "text"`, render `content_ar` / `content_en` only.

## Admin Bunny upload (Filament)

1. Save lesson with `media_type = video`.
2. On edit, use the Bunny uploader (TUS direct to Bunny after `POST /admin/lessons/{id}/bunny/create`).
3. Poll `GET /admin/lessons/{id}/bunny/status` or rely on webhook updating `bunny_status`.

Secrets (`BUNNY_STREAM_API_KEY`, Google OAuth) never appear in API responses.
