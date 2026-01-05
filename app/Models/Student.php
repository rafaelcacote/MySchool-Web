<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'shared';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'escola.alunos';

    public function getTable(): string
    {
        // Em SQLite (testes), não existe schema. A migration cria a tabela como `alunos`.
        if ($this->getConnection()->getDriverName() === 'sqlite') {
            return 'alunos';
        }

        return parent::getTable();
    }

    protected function alunoResponsavelPivotTable(): string
    {
        return $this->getConnection()->getDriverName() === 'sqlite'
            ? 'aluno_responsavel'
            : 'escola.aluno_responsavel';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'usuario_id',
        'matricula',
        'serie',
        'turma',
        'data_nascimento',
        'data_matricula',
        'informacoes_medicas',
        'ativo',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_nascimento' => 'date',
            'data_matricula' => 'date',
            'ativo' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the tenant that owns the student.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user (usuário) that owns the student.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Get the parents (responsáveis) for the student.
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(Responsavel::class, $this->alunoResponsavelPivotTable(), 'aluno_id', 'responsavel_id')
            ->withPivot(['tenant_id', 'principal'])
            ->wherePivot('tenant_id', $this->tenant_id);
    }
}
