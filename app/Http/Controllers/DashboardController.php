<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Responsavel;
use App\Models\Student;
use App\Models\Subscription;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\Turma;
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
        $user = $request->user();
        $isAdminGeral = $user->hasRole('Administrador Geral');

        if ($isAdminGeral) {
            return $this->adminGeralDashboard($request);
        }

        return $this->adminEscolaDashboard($request);
    }

    /**
     * Dashboard para Administrador Geral.
     */
    protected function adminGeralDashboard(Request $request): Response
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
            'dashboardType' => 'admin_geral',
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

    /**
     * Dashboard para Administrador Escola.
     */
    protected function adminEscolaDashboard(Request $request): Response
    {
        $user = $request->user();
        $tenantId = $request->session()->get('tenant_id');

        // Se não tem tenant_id na sessão, mas tem apenas um tenant, usar esse
        if (! $tenantId && $user->tenants->count() === 1) {
            $tenantId = $user->tenants->first()->id;
            $request->session()->put('tenant_id', $tenantId);
        }

        if (! $tenantId) {
            abort(404, 'Escola não encontrada');
        }

        // Estatísticas de Professores
        $totalProfessores = Teacher::where('tenant_id', $tenantId)->count();
        $professoresAtivos = Teacher::where('tenant_id', $tenantId)->where('ativo', true)->count();
        $professoresInativos = Teacher::where('tenant_id', $tenantId)->where('ativo', false)->count();

        // Estatísticas de Alunos
        $totalAlunos = Student::where('tenant_id', $tenantId)->count();
        $alunosAtivos = Student::where('tenant_id', $tenantId)->where('ativo', true)->count();
        $alunosInativos = Student::where('tenant_id', $tenantId)->where('ativo', false)->count();

        // Estatísticas de Turmas
        $totalTurmas = Turma::where('tenant_id', $tenantId)->count();
        $turmasAtivas = Turma::where('tenant_id', $tenantId)->where('ativo', true)->count();
        $turmasInativas = Turma::where('tenant_id', $tenantId)->where('ativo', false)->count();

        // Estatísticas de Responsáveis
        $totalResponsaveis = Responsavel::where('tenant_id', $tenantId)->count();
        // Responsáveis usam o relacionamento com User para verificar se está ativo
        $responsaveisAtivos = Responsavel::where('tenant_id', $tenantId)
            ->whereHas('user', function ($query) {
                $query->where('ativo', true);
            })
            ->count();
        $responsaveisInativos = $totalResponsaveis - $responsaveisAtivos;

        // Buscar informações do tenant atual
        $tenant = Tenant::find($tenantId);

        return Inertia::render('Dashboard', [
            'dashboardType' => 'admin_escola',
            'tenant' => $tenant ? [
                'id' => $tenant->id,
                'nome' => $tenant->nome,
                'logo_url' => $tenant->logo_url,
            ] : null,
            'stats' => [
                'professores' => [
                    'total' => $totalProfessores,
                    'ativos' => $professoresAtivos,
                    'inativos' => $professoresInativos,
                ],
                'alunos' => [
                    'total' => $totalAlunos,
                    'ativos' => $alunosAtivos,
                    'inativos' => $alunosInativos,
                ],
                'turmas' => [
                    'total' => $totalTurmas,
                    'ativas' => $turmasAtivas,
                    'inativas' => $turmasInativas,
                ],
                'responsaveis' => [
                    'total' => $totalResponsaveis,
                    'ativos' => $responsaveisAtivos,
                    'inativos' => $responsaveisInativos,
                ],
            ],
        ]);
    }
}
