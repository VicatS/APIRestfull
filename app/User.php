<?php

namespace App;

use App\Transformers\UserTransformer;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * @method static truncate()
 * @method static findOrFail(int $id)
 * @method static create(array $campos)
 * @method static where()
 * @method isVerificado()
 * @property mixed id
 * @property mixed name
 * @property mixed email
 * @property mixed verified
 * @property mixed admin
 */
class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    const USUARIO_VERIFICADO = '1';
    const USUARIO_NO_VERIFICADO = '0';

    const USUARIO_ADMINISTRADOR = 'true';
    const USUARIO_REGULAR = 'false';

    public $transformer = UserTransformer::class;

    protected $table = 'users';
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'verified',
        'verification_token',
        'admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
    ];

    // setear => mutator
    public function setNameAttribute($valor)
    {
        $this->attributes['name'] = strtolower($valor);
    }
    // accesor nombre
    public function getNameAttribute($valor) {
        return ucfirst($valor);
    }

    public function setEmailAttribute($valor)
    {
        $this->attributes['email'] = strtolower($valor);
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // User Verified
    public function esVerificado() {
        return $this->verified == User::USUARIO_VERIFICADO;
    }

    // User Admin
    public function esAdministrador() {
        return $this->admin == User::USUARIO_ADMINISTRADOR;
    }

    // Token de Verificacion
    public static function generarVerificationToken() {
        return Str::random(40);
    }

}
