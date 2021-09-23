<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;

class tab_especialidad extends Model
{
    //Nombre de la conexion que utitlizara este modelo
	protected $connection= 'principal';

	//Todos los modelos deben extender la clase Eloquent
    protected $table = 'configuracion.tab_especialidad';

    protected function getEspecialidad($id_usuario){

    $tab_solicitud = tab_especialidad::select( 'configuracion.tab_especialidad.id','configuracion.tab_especialidad.de_especialidad')
        ->join('autenticacion.tab_usuario_especialidad as t01', 't01.id_especialidad', '=', 'configuracion.tab_especialidad.id')
        ->where('id_usuario', '=', $id_usuario)
        ->get();
        
        return $tab_solicitud;
    }

    public static $validar = array(
            "de_especialidad" => "required"
    );


    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'de_especialidad':
              return $query->where('de_especialidad', 'ILIKE', "%{$q}%");
          break;
            default:
              return $query;
          break;
      }
    }
}
