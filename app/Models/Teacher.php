<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
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
    protected $table = 'escola.professores';

    public function getTable(): string
    {
        // Em SQLite (testes), não existe schema. A migration cria a tabela como `professores`.
        if ($this->getConnection()->getDriverName() === 'sqlite') {
            return 'professores';
        }

        return parent::getTable();
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
        'disciplinas',
        'especializacao',
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
            'ativo' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the disciplinas attribute (PostgreSQL array to PHP array).
     */
    public function getDisciplinasAttribute(?string $value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Remove chaves { } e converte para array
        $value = trim($value, '{}');
        if ($value === '') {
            return [];
        }

        // Split por vírgula e remove aspas
        return array_map(function ($item) {
            return trim($item, '"');
        }, explode(',', $value));
    }

    /**
     * Set the disciplinas attribute (PHP array to PostgreSQL array).
     */
    public function setDisciplinasAttribute(?array $value): void
    {
        if ($value === null || empty($value)) {
            $this->attributes['disciplinas'] = null;

            return;
        }

        // Converte array PHP para formato PostgreSQL {val1,val2}
        $escaped = array_map(function ($item) {
            return '"'.str_replace('"', '\"', $item).'"';
        }, $value);

        $this->attributes['disciplinas'] = '{'.implode(',', $escaped).'}';
    }

    /**
     * Get the tenant that owns the teacher.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user associated with the teacher.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
