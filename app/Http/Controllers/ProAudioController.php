<?php

namespace App\Http\Controllers;

use App\Models\MusicTrack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ProAudioController extends Controller
{
    const CHUNK_SIZE = 1048576; // 1 MB

    /**
     * Return track metadata (total size, mime type, chunk count)
     */
    public function info(Request $request, MusicTrack $track)
    {
        if ($request->header('X-Audio-Stream') !== 'mw') {
            abort(403);
        }

        $this->authorizeAccess($request, $track);

        $disk = Storage::disk('pro-audio');

        if (! $disk->exists($track->file_path)) {
            abort(404);
        }

        $fileSize = $disk->size($track->file_path);
        $mimeType = str_ends_with(strtolower($track->file_path), '.wav') ? 'audio/wav' : 'audio/mpeg';
        $totalChunks = (int) ceil($fileSize / self::CHUNK_SIZE);

        // Generate token-protected chunk URLs (expire in 30s)
        $expires = time() + 30;
        $chunks = [];
        for ($i = 0; $i < $totalChunks; $i++) {
            $token = hash_hmac('sha256', "{$track->id}:{$i}:{$expires}", config('app.key'));
            $chunks[] = route('pro.audio.chunk', [
                'track' => $track->id,
                'index' => $i,
                'expires' => $expires,
                'token' => $token,
            ]);
        }

        return response()->json([
            'size' => $fileSize,
            'mime' => $mimeType,
            'chunks' => $chunks,
        ], 200, [
            'Cache-Control' => 'no-store',
        ]);
    }

    /**
     * Serve a single chunk of audio data
     */
    public function chunk(Request $request, MusicTrack $track, int $index)
    {
        // Block direct browser access — require custom header
        if ($request->header('X-Audio-Stream') !== 'mw') {
            abort(403);
        }

        // Verify token + expiration
        $expires = (int) $request->query('expires', 0);
        $token = $request->query('token', '');
        $expected = hash_hmac('sha256', "{$track->id}:{$index}:{$expires}", config('app.key'));

        if (! hash_equals($expected, $token) || $expires < time()) {
            abort(403, 'Token invalide ou expiré.');
        }

        $this->authorizeAccess($request, $track);

        $disk = Storage::disk('pro-audio');

        if (! $disk->exists($track->file_path)) {
            abort(404);
        }

        $fileSize = $disk->size($track->file_path);
        $start = $index * self::CHUNK_SIZE;

        if ($start >= $fileSize) {
            abort(416);
        }

        $length = min(self::CHUNK_SIZE, $fileSize - $start);
        $path = $disk->path($track->file_path);

        $handle = fopen($path, 'rb');
        fseek($handle, $start);
        $data = fread($handle, $length);
        fclose($handle);

        return response($data, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Length' => $length,
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Legacy full stream (for admin player only)
     */
    public function stream(Request $request, MusicTrack $track)
    {
        $this->authorizeAccess($request, $track);

        $disk = Storage::disk('pro-audio');

        if (! $disk->exists($track->file_path)) {
            abort(404);
        }

        $mimeType = str_ends_with(strtolower($track->file_path), '.wav') ? 'audio/wav' : 'audio/mpeg';
        $fileSize = $disk->size($track->file_path);

        return response()->stream(function () use ($disk, $track) {
            $stream = $disk->readStream($track->file_path);
            fpassthru($stream);
            fclose($stream);
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Length' => $fileSize,
            'Content-Disposition' => 'inline',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    protected function authorizeAccess(Request $request, MusicTrack $track): void
    {
        $user = $request->user();
        $proAccount = $user?->proAccount;

        if (! $proAccount) {
            abort(403);
        }

        $hasAccess = $proAccount->musicProjects()
            ->where('music_projects.id', $track->music_project_id)
            ->exists();

        if (! $hasAccess) {
            abort(403);
        }
    }
}
