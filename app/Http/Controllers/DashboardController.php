<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request): Response
    {
        // Estatísticas de Escolas
        $totalEscolas = Tenant::count();
        $escolasAtivas = Tenant::where('ativo', true)->count();
        $escolasInativas = Tenant::where('ativo', false)->count();

        // Estatísticas de Planos
        $totalPlanos = Plan::count();
        $planosAtivos = Plan::where('ativo', true)->count();
        $planosInativos = Plan::where('ativo', false)->count();

        // Estatísticas de Assinaturas
        $totalAssinaturas = Subscription::count();
        $assinaturasAtivas = Subscription::where('status', 'ativa')->count();
        $assinaturasCanceladas = Subscription::where('status', 'cancelada')->count();
        $assinaturasPendentes = Subscription::where('status', 'pendente')->count();

        // Estatísticas de Usuários
        $totalUsuarios = User::count();
        $usuariosAtivos = User::where('ativo', true)->count();
        $usuariosInativos = User::where('ativo', false)->count();

        return Inertia::render('Dashboard', [
            'stats' => [
                'escolas' => [
                    'total' => $totalEscolas,
                    'ativas' => $escolasAtivas,
                    'inativas' => $escolasInativas,
                ],
                'planos' => [
                    'total' => $totalPlanos,
                    'ativos' => $planosAtivos,
                    'inativos' => $planosInativos,
                ],
                'assinaturas' => [
                    'total' => $totalAssinaturas,
                    'ativas' => $assinaturasAtivas,
                    'canceladas' => $assinaturasCanceladas,
                    'pendentes' => $assinaturasPendentes,
                ],
                'usuarios' => [
                    'total' => $totalUsuarios,
                    'ativos' => $usuariosAtivos,
                    'inativos' => $usuariosInativos,
                ],
            ],
        ]);
    }
}

