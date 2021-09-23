<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_tipo_credito_adicional extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_tipo_credito_adicional';

    public static $validarCrear = array(
        "descripcion" => "required"
    );

    public static $validarEditar = array(
        "descripcion" => "required"
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
            case 'id':
                return $query->where('de_tipo_credito_adicional', '=', $q);
            break;
                default:
                return $query;
            break;
      }
    }
}
