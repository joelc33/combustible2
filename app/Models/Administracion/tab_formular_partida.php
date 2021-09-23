<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_formular_partida extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_formular_partida';

    public static $validarCrear = array(
                "tipo_ingreso" => "required|numeric",
                "ambito" => "required|numeric",
                "aplicacion" => "required|numeric",
                "partida" => "required|numeric",
                "monto" => "required|numeric",
	);

	public static $validarEditar = array(
                "tipo_ingreso" => "required|numeric",
                "ambito" => "required|numeric",
                "aplicacion" => "required|numeric",
                "partida" => "required|numeric",
                "monto" => "required|numeric",
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'id':
              return $query->where('co_partida', 'ILIKE', "%{$q}%");
          break;
            default:
              return $query;
          break;
      }
    }
}
