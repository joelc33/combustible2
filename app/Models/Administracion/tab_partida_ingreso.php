<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_partida_ingreso extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_partida_ingreso';

    public static $validarCrear = array(
                "aplicacion" => "required|numeric",
                "partida" => "required|numeric",
                "monto" => "required|numeric"        
	);

	public static $validarEditar = array(
		"aplicacion" => "required|numeric",
                "partida" => "required|numeric",
                "monto" => "required|numeric"
    );    
    
    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'id':
              return $query->where('nu_partida', 'ILIKE', "%{$q}%");
          break;
            default:
              return $query;
          break;
      }
    }    
}
