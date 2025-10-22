<?php

namespace App\Jobs;

use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Automatically anonymize visitors that have been inactive for long periods.
 */
class AnonymizeVisitorsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $threshold = Carbon::now()->subMonths(6);

        Visitor::whereNull('anonymized_at')
            ->where(function ($query) use ($threshold) {
                $query->whereNull('updated_at')->orWhere('updated_at', '<', $threshold);
            })
            ->chunkById(50, function ($visitors) {
                foreach ($visitors as $visitor) {
                    $visitor->update([
                        'full_name' => 'Anonimizado',
                        'document_number' => null,
                        'email' => null,
                        'phone' => null,
                        'photo_path' => null,
                        'photo_hash' => null,
                        'anonymized_at' => now(),
                    ]);

                    Log::info('Visitor anonymized by job', ['visitor_id' => $visitor->id]);
                }
            });
    }
}
