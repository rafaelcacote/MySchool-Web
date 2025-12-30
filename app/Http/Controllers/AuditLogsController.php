<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogsController extends Controller
{
    /**
     * Display a listing of the audit logs.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'acao', 'tipo_entidade']);

        $logs = AuditLog::query()
            ->with(['user:id,nome_completo,email', 'tenant:id,nome'])
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $search = trim($search);
                $query->where(function ($q) use ($search) {
                    $q->where('acao', 'ilike', "%{$search}%")
                        ->orWhere('tipo_entidade', 'ilike', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('nome_completo', 'ilike', "%{$search}%")
                                ->orWhere('email', 'ilike', "%{$search}%");
                        })
                        ->orWhereHas('tenant', function ($q) use ($search) {
                            $q->where('nome', 'ilike', "%{$search}%");
                        });
                });
            })
            ->when($filters['acao'] ?? null, function ($query, string $acao) {
                $query->where('acao', $acao);
            })
            ->when($filters['tipo_entidade'] ?? null, function ($query, string $tipo) {
                $query->where('tipo_entidade', $tipo);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Get unique actions and entity types for filters
        $acoes = AuditLog::query()
            ->distinct()
            ->orderBy('acao')
            ->pluck('acao')
            ->toArray();

        $tiposEntidade = AuditLog::query()
            ->distinct()
            ->orderBy('tipo_entidade')
            ->pluck('tipo_entidade')
            ->toArray();

        return Inertia::render('audit-logs/Index', [
            'logs' => $logs,
            'filters' => $filters,
            'acoes' => $acoes,
            'tiposEntidade' => $tiposEntidade,
        ]);
    }
}

