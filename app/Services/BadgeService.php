<?php

namespace App\Services;

use App\Models\VisitorAccess;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Service that renders visitor badges as PDF files with QR code.
 */
class BadgeService
{
    /**
     * Render a badge for the given access and store it on disk.
     */
    public function render(VisitorAccess $access): string
    {
        $qr = base64_encode(QrCode::format('png')->size(200)->generate($access->qr_token));

        $pdf = Pdf::loadView('pdf.badge', [
            'access' => $access,
            'qrImage' => $qr,
        ]);

        $filename = 'badges/' . $access->id . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        return $filename;
    }
}
