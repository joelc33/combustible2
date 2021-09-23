<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_movimiento_financiero extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_movimiento_financiero';

    public static $validarCrear = array(
                "banco" => "required|numeric|min:1",
                "cuenta_bancaria" => "required|numeric|min:1", 
                "numero_transaccion" => "required|numeric",
                "fe_transaccion" => "required",        
                "monto" => "required|numeric|min:1", 
                "tipo_movimiento" => "required|numeric|min:1",
                "tipo_documento" => "required|numeric|min:1",
                "subtipo_documento" => "required|numeric|min:1",        
                "descripcion" => "required|min:1|max:600"
	);

	public static $validarEditar = array(         
                "banco" => "required|numeric|min:1",
                "cuenta_bancaria" => "required|numeric|min:1", 
                "numero_transaccion" => "required|numeric",
                "fe_transaccion" => "required",        
                "monto" => "required|numeric|min:1", 
                "tipo_movimiento" => "required|numeric|min:1",
                "tipo_documento" => "required|numeric|min:1",
                "subtipo_documento" => "required|numeric|min:1",        
                "descripcion" => "required|min:1|max:600"
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'id':
              return $query->where('de_movimiento', 'ILIKE', "%{$q}%");
          break;
            default:
              return $query;
          break;
      }
    }
}
