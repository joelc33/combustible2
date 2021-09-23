<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_retencion extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_retencion';

    public static $validarCrear = array(
                "tipo_retencion" => "required|numeric|min:1",
                "descripcion" => "required|min:1|max:600",
                "nu_cuenta_contable_retencion_por_pagar" => "required",
                "nu_cuenta_contable_deposito_tercero" => "required",        
	);

	public static $validarEditar = array(
                "tipo_retencion" => "required|numeric|min:1",
                "descripcion" => "required|min:1|max:600",
                "nu_cuenta_contable_retencion_por_pagar" => "required",
                "nu_cuenta_contable_deposito_tercero" => "required",
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'id':
              return $query->where('de_retencion', 'ILIKE', "%{$q}%");
          break;
            default:
              return $query;
          break;
      }
    }
}
