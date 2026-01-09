<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\View\View;

class AppOrganizationsController extends Controller
{
    public function index(): View
    {
        $organizations = Organization::latest()->get();

        return view('app.organizations.index', compact('organizations'));
    }

    public function show(int $id): View
    {
        $organization = Organization::findOrFail($id);

        return view('app.organizations.show', compact('organization'));
    }
}
