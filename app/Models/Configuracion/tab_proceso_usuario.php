<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;

class tab_proceso_usuario extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'configuracion.tab_proceso_usuario';

    public static $validarCrear = array(
        "proceso" => "required|integer",
        "usuario" => "required|integer"
    );

    protected function getListaProcesoAsignado( $usuario){

        $data = tab_proceso_usuario::select( 'id_tab_proceso')
        ->where('id_tab_usuario', '=', $usuario)
        ->where('in_activo', '=', true)
        ->get();

        $registro=array();
        $i=0;
        foreach($data as $proceso){
            $registro[$i] = $proceso->id_tab_proceso;
            $i++;
        }

        return $registro;

    }
}
