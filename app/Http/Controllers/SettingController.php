<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(403, 'Akses terbatas untuk administrator.');
        }

        $showDemoAccounts = Setting::get('show_demo_accounts', true);

        // Reset if date changed
        $todayStr = now()->toDateString();
        $storedDate = Setting::get('gemini_usage_date', $todayStr);
        if ($storedDate !== $todayStr) {
            Setting::set('gemini_requests_today', 0);
            Setting::set('gemini_tokens_today', 0);
            Setting::set('gemini_usage_date', $todayStr);
        }

        $requestsToday = (int) Setting::get('gemini_requests_today', 0);
        if ($requestsToday === 0) {
            $analyzedCount = Candidate::whereDate('analyzed_at', today())->count();
            if ($analyzedCount > 0) {
                $requestsToday = $analyzedCount;
                Setting::set('gemini_requests_today', $requestsToday);
            }
        }

        $tokensToday = (int) Setting::get('gemini_tokens_today', 0);
        $maxDailyRequests = 1500;
        $remainingRequests = max(0, $maxDailyRequests - $requestsToday);
        $activeModel = config('gemini.model', 'gemini-1.5-flash');
        $quotaPercent = min(100, round(($requestsToday / $maxDailyRequests) * 100, 1));

        return view('settings.index', compact(
            'showDemoAccounts',
            'requestsToday',
            'maxDailyRequests',
            'remainingRequests',
            'tokensToday',
            'activeModel',
            'quotaPercent'
        ));
    }

    public function toggleDemoAccounts(Request $request): JsonResponse|RedirectResponse
    {
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(403, 'Akses terbatas untuk administrator.');
        }

        $current = Setting::get('show_demo_accounts', true);
        $newVal = $request->has('show_demo_accounts')
            ? filter_var($request->input('show_demo_accounts'), FILTER_VALIDATE_BOOLEAN)
            : !$current;

        Setting::set('show_demo_accounts', $newVal);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'show_demo_accounts' => $newVal,
                'message' => $newVal ? 'Info akun demo berhasil ditampilkan pada halaman login.' : 'Info akun demo berhasil dinonaktifkan dari halaman login.'
            ]);
        }

        return redirect()->back()->with('success', 'Preferensi visibilitas akun demo berhasil diperbarui.');
    }

    public function resetAiUsage(): RedirectResponse
    {
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(403, 'Akses terbatas untuk administrator.');
        }

        Setting::set('gemini_requests_today', 0);
        Setting::set('gemini_tokens_today', 0);
        Setting::set('gemini_usage_date', now()->toDateString());

        return redirect()->back()->with('success', 'Hitungan pemakaian kuota AI hari ini berhasil di-reset.');
    }
}
