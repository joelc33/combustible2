<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_concepto_retencion extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_concepto_retencion';

    public static $validarCrear = array(
                "tipo_documento" => "required|numeric|min:1",
                "porcentaje_retencion" => "required|numeric|min:1",
                "monto_minimo" => "required|numeric|min:1",
                "concepto" => "required|min:1|max:600",
                "numero_concepto" => "required|min:1|max:600",
                "sustraendo" => "required|numeric|min:1", 
	);

	public static $validarEditar = array(
                "tipo_documento" => "required|numeric|min:1",
                "porcentaje_retencion" => "required|numeric|min:1",
                "monto_minimo" => "required|numeric|min:1",
                "concepto" => "required|min:1|max:600",
                "numero_concepto" => "required|min:1|max:600",
                "sustraendo" => "required|numeric|min:1", 
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'id':
              return $query->where('de_concepto', 'ILIKE', "%{$q}%");
          break;
            default:
              return $query;
          break;
      }
    }
}
