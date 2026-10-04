<?php

namespace App\Http\Controllers;

use App\Jobs\AnalyzeCandidateJob;
use App\Models\Candidate;
use App\Models\Procurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class CandidateController extends Controller
{
    public function create(Procurement $procurement)
    {
        return view('candidates.create', compact('procurement'));
    }

    public function store(Request $request, Procurement $procurement)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'images'   => 'required|array|min:1|max:3',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $savedPaths = [];

        foreach ($request->file('images') as $file) {
            $filename  = 'candidates/' . uniqid() . '.webp';
            $fullPath  = Storage::disk('public')->path($filename);

            // Ensure directory exists
            Storage::disk('public')->makeDirectory('candidates');

            // Compress: resize to max 900px width, quality 75, save as webp
            $image = Image::read($file->getRealPath());
            $image->scaleDown(width: 900);
            $image->toWebp(quality: 75)->save($fullPath);

            $savedPaths[] = $filename;
        }

        $candidate = $procurement->candidates()->create([
            'name'      => $request->name,
            'images'    => $savedPaths,
            'ai_status' => 'pending',
        ]);

        // Dispatch analysis job
        AnalyzeCandidateJob::dispatch($candidate);

        return redirect()->route('procurements.show', $procurement)
            ->with('success', "Kandidat \"{$candidate->name}\" ditambahkan. AI sedang menganalisis...");
    }

    public function show(Procurement $procurement, Candidate $candidate)
    {
        $candidate->load(['scores.criterion', 'procurement.criteria']);

        return view('candidates.show', compact('procurement', 'candidate'));
    }

    public function destroy(Procurement $procurement, Candidate $candidate)
    {
        // Delete images from storage
        foreach ($candidate->images ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }

        $candidate->delete();

        return redirect()->route('procurements.show', $procurement)
            ->with('success', 'Kandidat berhasil dihapus.');
    }

    public function status(Procurement $procurement, Candidate $candidate)
    {
        return response()->json([
            'ai_status'   => $candidate->ai_status,
            'status_label'=> $candidate->status_label,
            'total_score' => $candidate->total_score,
            'ai_error'    => $candidate->ai_error,
        ]);
    }

    public function reanalyze(Procurement $procurement, Candidate $candidate)
    {
        if ($candidate->ai_status === 'processing') {
            return response()->json(['error' => 'Sedang diproses.'], 422);
        }

        $candidate->update(['ai_status' => 'pending', 'ai_error' => null]);
        AnalyzeCandidateJob::dispatch($candidate);

        return response()->json(['success' => true]);
    }
}
