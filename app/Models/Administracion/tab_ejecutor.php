<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_ejecutor extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_ejecutor';

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
              return $query->where('de_ejecutor', 'ILIKE', "%{$q}%");
          break;
            default:
              return $query;
          break;
      }
    }
}
