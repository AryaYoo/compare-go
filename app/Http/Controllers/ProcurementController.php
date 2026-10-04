<?php

namespace App\Http\Controllers;

use App\Models\Procurement;
use Illuminate\Http\Request;

class ProcurementController extends Controller
{
    public function index()
    {
        $procurements = Procurement::withCount('candidates')
            ->with('criteria')
            ->latest()
            ->get();

        return view('procurements.index', compact('procurements'));
    }

    public function create()
    {
        return view('procurements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $procurement = Procurement::create([
            ...$validated,
            'status' => 'draft',
        ]);

        return redirect()->route('procurements.show', $procurement)
            ->with('success', 'Pengadaan berhasil dibuat.');
    }

    public function show(Procurement $procurement)
    {
        $procurement->load([
            'criteria' => fn($q) => $q->orderBy('order'),
            'candidates.scores.criterion',
        ]);

        return view('procurements.show', compact('procurement'));
    }

    public function edit(Procurement $procurement)
    {
        return view('procurements.edit', compact('procurement'));
    }

    public function update(Request $request, Procurement $procurement)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status'      => 'required|in:draft,active,completed',
        ]);

        $procurement->update($validated);

        return redirect()->route('procurements.show', $procurement)
            ->with('success', 'Pengadaan berhasil diperbarui.');
    }

    public function destroy(Procurement $procurement)
    {
        $procurement->delete();

        return redirect()->route('procurements.index')
            ->with('success', 'Pengadaan berhasil dihapus.');
    }
}
