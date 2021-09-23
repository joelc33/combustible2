<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;

class tab_proceso extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'configuracion.tab_proceso';

    public static $validarCrear = array(
		    "descripcion" => "required|min:1|max:600|unique:tab_proceso,de_proceso"
	  );

	  public static $validarEditar = array(
		    "descripcion" => "required|min:1|max:600"
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'id':
              return $query->where('de_proceso', 'ILIKE', "%{$q}%");
          break;
            default:
              return $query;
          break;
      }
    }
}
