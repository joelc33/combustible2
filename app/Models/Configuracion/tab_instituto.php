<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;

class tab_instituto extends Model
{
     //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'configuracion.tab_instituto';

    protected function getInstituto( $id_usuario){
   		$resultado = tab_instituto::select( 'configuracion.tab_instituto.id','configuracion.tab_instituto.de_instituto')
	        ->join('autenticacion.tab_usuario_instituto as t01', 't01.id_instituto', '=', 'configuracion.tab_instituto.id')
	        ->where('id_usuario', '=', $id_usuario)
	        ->get();
        
        return $resultado;
    }

    public static $validar = array(
            "de_instituto" => "required"
    );


    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
          case 'de_instituto':
              return $query->where('de_instituto', 'ILIKE', "%{$q}%");
          break;
            default:
              return $query;
          break;
      }
    }
}
