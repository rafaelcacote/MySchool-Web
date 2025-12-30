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
    protected $table = 'shared.professores';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'nome_completo',
        'cpf',
        'data_nascimento',
        'telefone',
        'email',
        'endereco',
        'endereco_numero',
        'endereco_complemento',
        'endereco_bairro',
        'endereco_cep',
        'endereco_cidade',
        'endereco_estado',
        'endereco_pais',
        'ativo',
        'formacao',
        'especializacao',
        'observacoes',
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
            'ativo' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the tenant that owns the teacher.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}

