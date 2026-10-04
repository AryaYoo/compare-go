<?php

namespace App\Jobs;

use App\Models\Candidate;
use App\Models\CandidateScore;
use App\Services\GeminiVisionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AnalyzeCandidateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;
    public int $tries   = 2;

    public function __construct(public Candidate $candidate) {}

    public function handle(GeminiVisionService $gemini): void
    {
        $candidate = $this->candidate;
        $candidate->update(['ai_status' => 'processing']);

        try {
            $procurement = $candidate->procurement()->with('criteria')->first();
            $criteria    = $procurement->criteria;

            if ($criteria->isEmpty()) {
                throw new \RuntimeException('Tidak ada kriteria yang ditentukan untuk pengadaan ini.');
            }

            // Resolve absolute paths for images
            $imagePaths = collect($candidate->images ?? [])
                ->map(fn($p) => Storage::disk('public')->path($p))
                ->filter(fn($p) => file_exists($p))
                ->values()
                ->toArray();

            if (empty($imagePaths)) {
                throw new \RuntimeException('Tidak ada gambar yang ditemukan untuk dianalisis.');
            }

            $criteriaData = $criteria->map(fn($c) => [
                'id'     => $c->id,
                'name'   => $c->name,
                'target' => $c->target,
                'weight' => $c->weight,
            ])->toArray();

            // Call Gemini Vision API
            $result = $gemini->analyzeProduct($imagePaths, $criteriaData);

            // Save scores
            $candidate->scores()->delete();

            $weightedScoreSum = 0;
            $totalWeight      = $criteria->sum('weight');

            foreach ($result['criteria_scores'] ?? [] as $scoreData) {
                $criterion = $criteria->firstWhere('id', $scoreData['criteria_id']);
                if (!$criterion) continue;

                CandidateScore::create([
                    'candidate_id'    => $candidate->id,
                    'criteria_id'     => $criterion->id,
                    'extracted_value' => $scoreData['extracted_value'] ?? null,
                    'score'           => $scoreData['score'] ?? 0,
                    'reasoning'       => $scoreData['reasoning'] ?? null,
                ]);

                $weightedScoreSum += ($scoreData['score'] ?? 0) * ($criterion->weight / 100);
            }

            $totalScore = $totalWeight > 0 ? $weightedScoreSum : 0;

            $candidate->update([
                'ai_result'   => $result,
                'total_score' => round($totalScore, 2),
                'ai_status'   => 'done',
                'analyzed_at' => now(),
                'ai_error'    => null,
            ]);

            Log::info("Candidate #{$candidate->id} analyzed. Score: {$totalScore}");

        } catch (\Throwable $e) {
            Log::error("AnalyzeCandidateJob failed for candidate #{$candidate->id}", [
                'error' => $e->getMessage(),
            ]);

            $candidate->update([
                'ai_status' => 'failed',
                'ai_error'  => $e->getMessage(),
            ]);
        }
    }
}
