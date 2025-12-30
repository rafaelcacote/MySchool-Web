<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionsController extends Controller
{
    /**
     * Display a listing of the subscriptions.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'status']);

        $subscriptions = Subscription::query()
            ->with(['tenant:id,nome', 'plan:id,nome,preco'])
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $search = trim($search);
                $query->whereHas('tenant', function ($q) use ($search) {
                    $q->where('nome', 'ilike', "%{$search}%");
                })->orWhereHas('plan', function ($q) use ($search) {
                    $q->where('nome', 'ilike', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, function ($query, string $status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('subscriptions/Index', [
            'subscriptions' => $subscriptions,
            'filters' => $filters,
        ]);
    }
}

