<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

/**
 * Service responsible for decoding and storing webcam pictures securely.
 */
class VisitorPhotoService
{
    /**
     * Store the base64 payload as a JPEG file and return its storage path.
     */
    public function storeFromBase64(string $payload): array
    {
        $encoded = preg_replace('/^data:image\/[a-zA-Z]+;base64,/', '', $payload);
        $binary = base64_decode($encoded, true);

        if ($binary === false) {
            throw new \InvalidArgumentException('Imagem inválida.');
        }

        $hash = hash('sha256', $binary);
        $filename = 'visitors/' . Str::uuid() . '.jpg';

        $image = Image::make($binary)->encode('jpg', 85);
        Storage::disk('public')->put($filename, $image);

        return [
            'path' => $filename,
            'hash' => $hash,
        ];
    }
}
