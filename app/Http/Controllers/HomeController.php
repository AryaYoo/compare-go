<?php

namespace App\Http\Controllers;

use App\Models\Procurement;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home dashboard page.
     */
    public function index()
    {
        $totalProcurements = Procurement::count();
        $recentProcurements = Procurement::latest()->take(5)->get();

        return view('home', compact('totalProcurements', 'recentProcurements'));
    }
}
