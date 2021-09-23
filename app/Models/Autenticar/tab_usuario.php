<?php

namespace App\Models\Autenticar;

use Illuminate\Auth\Authenticatable;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

//class tab_usuario extends Model
class tab_usuario extends Model implements AuthenticatableContract,
                                            AuthorizableContract,
                                            CanResetPasswordContract
{
    //
    use Authenticatable, Authorizable, CanResetPassword;
    
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    public function getAuthPassword() {
        return $this->da_password;
    }

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'autenticacion.tab_usuario';

    /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
    protected $fillable = ['da_email', 'da_login', 'da_password'];
    
    /**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
    protected $hidden = ['password', 'remember_token'];

    public static $validarLogin = array(
		"especialidad"    => "required",
		"instituto"    => "required"
	);

    public static $validarCrear = array(
		"usuario"    => "required|alpha_dash|min:1|max:30|unique:principal.autenticacion.tab_usuario,da_login",
		"correo"    => "required|email|unique:principal.autenticacion.tab_usuario,da_email",
		//"contraseña" => "required|alpha_dash|min:6|max:30",
		"nombre"    => "required",
	);
	
	public static $validarEditar = array(
		"usuario"    => "required|alpha_dash|min:1|max:30",
		"correo"    => "required|email",
		//"contraseña" => "required|alpha_dash|min:6|max:30",
		"nombre"    => "required",
	);

	public static $validarPass = array(
		"contraseña"  => "required|confirmed|alpha_dash|min:6|max:30",
		"contraseña_confirmation" => "required|alpha_dash|min:6|max:30"
	);

	public static $validarContrasena = array(
		"nombre"    => "required|min:1|max:90",
		"correo"    => "required|email",
		"valido" => "required|in:1",
		"contraseña_actual" => "required|alpha_dash|min:3|max:30",
		"contraseña" => "required|alpha_dash|min:3|max:30|confirmed",
		"contraseña_confirmation" => "required|alpha_dash|min:3|max:30"
	);

	public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'id':
              return $query->where('nb_usuario', 'ILIKE', "%{$q}%");
          break;
            default:
              return $query;
          break;
      }
    }
}
