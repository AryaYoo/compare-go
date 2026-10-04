<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiVisionService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;
    private int    $maxTokens;
    private float  $temperature;

    public function __construct()
    {
        $this->apiKey      = config('gemini.api_key');
        $this->model       = config('gemini.model');
        $this->baseUrl     = config('gemini.base_url');
        $this->maxTokens   = config('gemini.max_tokens');
        $this->temperature = config('gemini.temperature');
    }

    /**
     * Analyze product images against procurement criteria.
     *
     * @param  array  $imagePaths  Absolute paths to compressed images
     * @param  array  $criteria    [['id'=>1,'name'=>'CPU','target'=>'i5 Gen12','weight'=>20], ...]
     * @return array               Parsed AI result
     */
    public function analyzeProduct(array $imagePaths, array $criteria): array
    {
        $prompt = $this->buildPrompt($criteria);

        $parts = [];

        // Add images as inline base64
        foreach ($imagePaths as $path) {
            if (!file_exists($path)) continue;

            $mime     = mime_content_type($path);
            $b64Data  = base64_encode(file_get_contents($path));

            $parts[] = [
                'inline_data' => [
                    'mime_type' => $mime,
                    'data'      => $b64Data,
                ],
            ];
        }

        // Add text prompt
        $parts[] = ['text' => $prompt];

        $payload = [
            'contents' => [
                ['parts' => $parts],
            ],
            'generationConfig' => [
                'temperature'     => $this->temperature,
                'maxOutputTokens' => $this->maxTokens,
            ],
        ];

        $modelsToTry = array_unique(array_filter([
            $this->model,
            'gemini-flash-latest',
            'gemini-3.5-flash',
            'gemini-3.7-flash',
            'gemini-3.8-flash',
        ]));

        $lastException = null;

        foreach ($modelsToTry as $modelName) {
            try {
                $url = "{$this->baseUrl}/{$modelName}:generateContent?key={$this->apiKey}";

                $response = Http::timeout(60)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($url, $payload);

                if (!$response->successful()) {
                    $status = $response->status();
                    $body   = $response->body();
                    Log::warning("Gemini model {$modelName} failed with HTTP {$status}", ['body' => substr($body, 0, 300)]);
                    
                    // If error is transient (503, 429, 404, etc.), try next model
                    if (in_array($status, [404, 429, 500, 502, 503, 504])) {
                        $lastException = new \RuntimeException("Gemini API error ({$modelName}): HTTP {$status} — {$body}");
                        usleep(500000); // 0.5s pause
                        continue;
                    }

                    throw new \RuntimeException("Gemini API error ({$modelName}): HTTP {$status} — {$body}");
                }

                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

                return $this->parseResponse($text);

            } catch (\RuntimeException $re) {
                if (str_contains($re->getMessage(), 'Gagal memparse')) {
                    throw $re;
                }
                $lastException = $re;
            } catch (\Throwable $e) {
                $lastException = $e;
                Log::warning("Gemini model {$modelName} exception: " . $e->getMessage());
            }
        }

        throw ($lastException ?? new \RuntimeException("Semua model Gemini gagal merespons."));
    }

    private function buildPrompt(array $criteria): string
    {
        $criteriaList = '';
        foreach ($criteria as $c) {
            $criteriaList .= "- ID: {$c['id']}, Kriteria: {$c['name']}, Target: {$c['target']}, Bobot: {$c['weight']}%\n";
        }

        return <<<PROMPT
Kamu adalah asisten pengadaan barang. Tugasmu adalah mengekstrak spesifikasi produk dari gambar iklan marketplace yang diberikan, lalu mencocokkannya dengan kriteria pengadaan dan memberikan skor.

Kriteria Pengadaan:
{$criteriaList}

Instruksi:
1. Baca semua teks dan spesifikasi yang terlihat di gambar.
2. Untuk setiap kriteria, temukan nilai yang paling relevan dari gambar.
3. Berikan skor 0-100 berdasarkan seberapa cocok nilai yang ditemukan dengan target. Skor 100 = sangat sesuai atau melebihi target. Skor 0 = sama sekali tidak sesuai atau tidak ditemukan.
4. Jika informasi tidak tersedia di gambar, tulis "Tidak ditemukan" untuk extracted_value dan beri skor 30.
5. Kembalikan HANYA JSON murni tanpa markdown, tanpa komentar.

Format JSON yang harus dikembalikan:
{
  "product_name": "nama produk dari gambar",
  "criteria_scores": [
    {
      "criteria_id": <id>,
      "extracted_value": "nilai yang ditemukan di gambar",
      "score": <0-100>,
      "reasoning": "alasan singkat pemberian skor"
    }
  ]
}
PROMPT;
    }

    private function parseResponse(string $text): array
    {
        // Strip markdown code blocks if present
        $text = preg_replace('/^```(?:json)?\s*/m', '', $text);
        $text = preg_replace('/```\s*$/m', '', $text);
        $text = trim($text);

        $result = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('Gemini JSON parse failed', ['raw' => $text]);
            throw new \RuntimeException('Gagal memparse respons AI. Pastikan API key valid dan model tersedia.');
        }

        return $result;
    }
}
