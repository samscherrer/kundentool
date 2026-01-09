<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PortalDashboardController extends Controller
{
    public function index(): View
    {
        return view('portal.dashboard');
    }
}
