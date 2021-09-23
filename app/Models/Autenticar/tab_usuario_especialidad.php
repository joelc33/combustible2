<?php

namespace App\Models\Autenticar;

use Illuminate\Database\Eloquent\Model;

class tab_usuario_especialidad extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'autenticacion.tab_usuario_especialidad';

     public static $validarCrear = array(
        "id_especialidad" => "required|integer",
        "id_usuario" => "required|integer"
    );

    protected function getListaEspecialidadAsignado( $usuario){

        $data = tab_usuario_especialidad::select( 'id_especialidad')
        ->where('id_usuario', '=', $usuario)
        ->get();

        $registro=array();
        $i=0;
        foreach($data as $especialidad){
            $registro[$i] = $especialidad->id_especialidad;
            $i++;
        }

        return $registro;

    }

}
