<?php

namespace App\Http\Controllers;

use App\Models\Criterion;
use App\Models\Procurement;
use Illuminate\Http\Request;

class CriteriaController extends Controller
{
    public function store(Request $request, Procurement $procurement)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:100',
            'target' => 'required|string|max:255',
            'weight' => 'required|integer|min:1|max:100',
        ]);

        $order = $procurement->criteria()->max('order') + 1;

        $criterion = $procurement->criteria()->create([
            ...$validated,
            'order' => $order,
        ]);

        return response()->json([
            'success'   => true,
            'criterion' => $criterion,
            'total_weight' => $procurement->criteria()->sum('weight'),
        ]);
    }

    public function update(Request $request, Procurement $procurement, Criterion $criterion)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:100',
            'target' => 'required|string|max:255',
            'weight' => 'required|integer|min:1|max:100',
        ]);

        $criterion->update($validated);

        return response()->json([
            'success'      => true,
            'criterion'    => $criterion,
            'total_weight' => $procurement->criteria()->sum('weight'),
        ]);
    }

    public function destroy(Procurement $procurement, Criterion $criterion)
    {
        $criterion->delete();

        return response()->json([
            'success'      => true,
            'total_weight' => $procurement->criteria()->sum('weight'),
        ]);
    }
}
