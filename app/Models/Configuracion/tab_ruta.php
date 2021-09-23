<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;

class tab_ruta extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'configuracion.tab_ruta';

    public static $validarCrear = array(
        "proceso" => "required|numeric",
        "solicitud" => "required|numeric",
        "orden" => "required|numeric",
        "entorno" => "required|numeric",
        //"controlador" => "required|min:1|max:600",
        //"accion" => "required|min:1|max:600",
        //"reporte" => "required|min:1|max:600"
        );

        public static $validarEditar = array(
        "proceso" => "required|numeric",
        "solicitud" => "required|numeric",
        "orden" => "required|numeric",
        "entorno" => "required|numeric",
        //"controlador" => "required|min:1|max:600",
        //"accion" => "required|min:1|max:600",
        //"reporte" => "required|min:1|max:600"
    );

    /**
     * @return object
     */
    public function entorno(){
        return $this->belongsTo('bpm\Models\Configuracion\tab_entorno','id_tab_entorno');
    }
    
    protected function getVerificaRuta($codigo){

        if (tab_ruta::where('id_tab_solicitud', '=', $codigo)->where('nu_orden', '=', 1)->exists()) {

            $tab_configuracion_ruta = tab_ruta::select( 'id', 'id_tab_proceso', 'id_tab_solicitud', 'nu_orden', 'in_datos', 'nb_controlador', 
            'nb_accion', 'nb_reporte', 'in_activo', 'created_at', 'updated_at')
            ->where('id_tab_solicitud', '=', $codigo)
            ->where('nu_orden', '=', 1)
            ->first();

            return $tab_configuracion_ruta->id_tab_proceso;

        }
    }

    protected function getInCargarDatos( $solicitud){

        $data = tab_ruta::select( 'configuracion.tab_ruta.in_datos')
        ->join('proceso.tab_ruta as t01', function ($j) {
            $j->on('configuracion.tab_ruta.id_tab_proceso','=','t01.id_tab_proceso')
                ->on('configuracion.tab_ruta.id_tab_solicitud','=','t01.id_tab_tipo_solicitud');
        })
        ->where('t01.id_tab_solicitud', '=', $solicitud)
        ->where('t01.in_actual', '=', true)
        ->first();
        
        return $data->in_datos;

    }
}
