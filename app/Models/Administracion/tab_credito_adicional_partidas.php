<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;
use DB;

class tab_credito_adicional_partidas extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_credito_adicional_partidas';

    public static $validarCrearIngreso = array(
                "partida_ingreso" => "required|numeric|min:1",
                "monto_ingreso" => "required|numeric|min:1"
	);
    
    public static $validarCrearGasto = array(
                "partida_gasto" => "required|numeric|min:1",
                "tipo_ingreso" => "required|numeric|min:1",
                "ambito" => "required|numeric|min:1",
                "aplicacion" => "required|numeric|min:1",
                "clasificacion_economica" => "required|numeric|min:1",
                "area_estrategica" => "required|numeric|min:1",
                "tipo_gasto" => "required|numeric|min:1",
                "monto_gasto" => "required|numeric|min:1"
	);    


        
        
    protected function moIngreso($codigo){

        $tab_credito_adicional_partidas = tab_credito_adicional_partidas::select( DB::raw("coalesce( SUM(monto), 0) as mo_total"))
        ->whereNotNull('id_tab_partida_ingreso')
        ->where('id_tab_solicitud', '=', $codigo)                
        ->first();
    
        return $tab_credito_adicional_partidas->mo_total;
    } 
    
    protected function moGasto($codigo){

        $tab_credito_adicional_partidas = tab_credito_adicional_partidas::select( DB::raw("coalesce( SUM(monto), 0) as mo_total"))
        ->whereNotNull('id_tab_catalogo_partida')
        ->where('id_tab_solicitud', '=', $codigo)
        ->first();
    
        return $tab_credito_adicional_partidas->mo_total;
    }    


}
