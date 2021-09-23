<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_creacion_partida extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_creacion_partida';

    public static $validarCrear = array(
                "fuente_financiamiento" => "required|numeric|min:1",
                "nu_financiamiento" => "required|numeric|min:1", 
                "ejecutor" => "required|numeric|min:1",
                "proyecto_ac" => "required|numeric|min:1",        
                "accion_especifica" => "required|numeric|min:1", 
                "partida_gasto" => "required|numeric|min:1",
                "tipo_ingreso" => "required|numeric|min:1",
                "aplicacion" => "required|numeric|min:1",        
                "ambito" => "required|numeric|min:1",
                "clasificacion_economica" => "required|numeric|min:1",
                "area_estrategica" => "required|numeric|min:1",
                "tipo_gasto" => "required|numeric|min:1",
                "desagregado" => "required|numeric"
	);

	public static $validarEditar = array(         
                "fuente_financiamiento" => "required|numeric|min:1",
                "nu_financiamiento" => "required|numeric|min:1", 
                "ejecutor" => "required|numeric|min:1",
                "proyecto_ac" => "required|numeric|min:1",        
                "accion_especifica" => "required|numeric|min:1", 
                "partida_gasto" => "required|numeric|min:1",
                "tipo_ingreso" => "required|numeric|min:1",
                "aplicacion" => "required|numeric|min:1",        
                "ambito" => "required|numeric|min:1",
                "clasificacion_economica" => "required|numeric|min:1",
                "area_estrategica" => "required|numeric|min:1",
                "tipo_gasto" => "required|numeric|min:1",
                "desagregado" => "required|numeric"
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'id':
              return $query->where('de_partida', 'ILIKE', "%{$q}%");
          break;
            default:
              return $query;
          break;
      }
    }
}
