<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_forma_pago extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_forma_pago';

    public static $validarCrear = array(
                "codigo" => "required|min:1|max:600",
                "descripcion" => "required|min:1|max:600"
	);

	public static $validarEditar = array(
                "codigo" => "required|min:1|max:600",
		"descripcion" => "required|min:1|max:600"
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'id':
              return $query->where('de_forma_pago', 'ILIKE', "%{$q}%");
          break;
            default:
              return $query;
          break;
      }
    }
}
