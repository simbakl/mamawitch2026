<?php

namespace App\Http\Controllers;

use App\Models\MusicTrack;
use Illuminate\Support\Facades\Storage;

class AdminAudioController extends Controller
{
    public function stream(MusicTrack $track)
    {
        $disk = Storage::disk('pro-audio');

        if (! $disk->exists($track->file_path)) {
            abort(404, 'Fichier audio introuvable.');
        }

        $mimeType = str_ends_with($track->file_path, '.wav') ? 'audio/wav' : 'audio/mpeg';

        return response()->stream(function () use ($disk, $track) {
            $stream = $disk->readStream($track->file_path);
            fpassthru($stream);
            fclose($stream);
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Length' => $disk->size($track->file_path),
            'Content-Disposition' => 'inline',
            'Accept-Ranges' => 'bytes',
        ]);
    }
}
