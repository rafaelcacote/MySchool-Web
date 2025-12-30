<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Inertia\Inertia;
use Inertia\Response;

class SchoolProfileController extends Controller
{
    /**
     * Display the school profile.
     */
    public function show(): Response
    {
        $user = auth()->user();
        $tenant = $user->tenants()->first();

        if (! $tenant) {
            abort(404, 'Escola não encontrada');
        }

        return Inertia::render('school/profile/Show', [
            'tenant' => $tenant,
        ]);
    }
}

