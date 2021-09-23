<?php

namespace App\Models\Autenticar;

use Illuminate\Database\Eloquent\Model;

class tab_usuario_instituto extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'autenticacion.tab_usuario_instituto';

    public static $validarCrear = array(
        "id_instituto" => "required|integer",
        "id_usuario" => "required|integer"
    );

    protected function getListainstitutoAsignado( $usuario){

        $data = tab_usuario_instituto::select( 'id_instituto')
        ->where('id_usuario', '=', $usuario)
        ->get();

        $registro=array();
        $i=0;
        foreach($data as $instituto){
            $registro[$i] = $instituto->id_instituto;
            $i++;
        }

        return $registro;

    }
}
