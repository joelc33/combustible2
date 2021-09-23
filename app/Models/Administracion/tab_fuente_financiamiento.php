<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;

class tab_fuente_financiamiento extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_fuente_financiamiento';

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
                return $query->where('de_fuente_financiamiento', '=', $q);
            break;
                default:
                return $query;
            break;
      }
    }
}
