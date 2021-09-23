<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_area_estrategica extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_area_estrategica';

    public static $validarCrear = array(
        "descripcion" => "required",
        "tx_sigla" => "required"
    );

    public static $validarEditar = array(
        "descripcion" => "required",
        "tx_sigla" => "required"
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
            case 'id':
                return $query->where('de_area_estrategica', '=', $q);
            break;
                default:
                return $query;
            break;
      }
    }
}
