<?php

namespace App\Models\Proceso;
//*******agregar esta linea******//
use Session;
//*******************************//
use Illuminate\Database\Eloquent\Model;

class tab_solicitud extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'proceso.tab_solicitud';

    public static $validarCrear = array(
        "solicitud" => "required|numeric",
        "observacion" => "required|min:1|max:1200",
        //"usuario" => "required"
    );

    public static $validarEditar = array(
        "solicitud" => "required|numeric",
        "observacion" => "required|min:1|max:1200",
        //"usuario" => "required"
    );

    public function scopeSearch($query, $q, $sortBy)
    {
        switch ($sortBy) {
            case 'cedula':
                return $query->where('t05.cedula', 'like', "'%{$q}%'");
            break;
                default:
                return $query;
            break;
        }
    }

    /**
	 * @return object
	 */
	public function tipo(){
		return $this->belongsTo('App\Models\Configuracion\tab_solicitud','id_tab_tipo_solicitud');
    }
    
    /**
	 * @return object
	 */
	public function usuario(){
		return $this->belongsTo('App\Models\Autenticacion\tab_usuario','id_tab_usuario');
    }
    
    /**
	 * @return object
	 */
	public function estatus(){
		return $this->belongsTo('App\Models\Configuracion\tab_estatus','id_tab_estatus');
    }
    
    protected function getPendiente( $proceso, $tramite){

        $tab_solicitud = tab_solicitud::select( 'proceso.tab_solicitud.id')
        ->join('proceso.tab_ruta as t01', 'proceso.tab_solicitud.id', '=', 't01.id_tab_solicitud')
        ->join('configuracion.tab_proceso as t02', 't02.id', '=', 't01.id_tab_proceso')
        ->join('autenticacion.tab_usuario as t03', 't03.id', '=', 't01.id_tab_usuario')
        ->join('configuracion.tab_solicitud as t04', 't04.id', '=', 'proceso.tab_solicitud.id_tab_tipo_solicitud')
        ->where('in_actual', '=', true)
        ->where('proceso.tab_solicitud.in_activo', '=', true)
        ->where('id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
        ->whereIn('t01.id_tab_proceso', $proceso)
        ->whereIn('proceso.tab_solicitud.id_tab_tipo_solicitud', $tramite)
        ->where('t01.nu_orden', '=', 1)
        ->where('t01.id_tab_estatus', '=', 1)
        ->count();
        
        return $tab_solicitud;

    }
    
    protected function getEnProceso( $proceso, $tramite){

        $tab_solicitud = tab_solicitud::select( 'proceso.tab_solicitud.id')
        ->join('proceso.tab_ruta as t01', 'proceso.tab_solicitud.id', '=', 't01.id_tab_solicitud')
        ->join('configuracion.tab_proceso as t02', 't02.id', '=', 't01.id_tab_proceso')
        ->join('autenticacion.tab_usuario as t03', 't03.id', '=', 't01.id_tab_usuario')
        ->join('configuracion.tab_solicitud as t04', 't04.id', '=', 'proceso.tab_solicitud.id_tab_tipo_solicitud')
        ->where('in_actual', '=', true)
        ->where('proceso.tab_solicitud.in_activo', '=', true)
        ->where('id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
        ->whereIn('t01.id_tab_proceso', $proceso)
        ->whereIn('proceso.tab_solicitud.id_tab_tipo_solicitud', $tramite)
        ->where('t01.nu_orden', '>', 1)
        ->where('t01.id_tab_estatus', '=', 1)
        ->count();
        
        return $tab_solicitud;

    }
    
    protected function getCompleto( $proceso, $tramite){

        $tab_solicitud = tab_solicitud::select( 'proceso.tab_solicitud.id')
        ->join('proceso.tab_ruta as t01', 'proceso.tab_solicitud.id', '=', 't01.id_tab_solicitud')
        ->join('configuracion.tab_proceso as t02', 't02.id', '=', 't01.id_tab_proceso')
        ->join('autenticacion.tab_usuario as t03', 't03.id', '=', 't01.id_tab_usuario')
        ->join('configuracion.tab_solicitud as t04', 't04.id', '=', 'proceso.tab_solicitud.id_tab_tipo_solicitud')
        ->where('in_actual', '=', true)
        ->where('proceso.tab_solicitud.in_activo', '=', true)
        ->where('id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
        ->whereIn('t01.id_tab_proceso', $proceso)
        ->whereIn('proceso.tab_solicitud.id_tab_tipo_solicitud', $tramite)
        ->where('t01.id_tab_estatus', '=', 2)
        ->count();
        
        return $tab_solicitud;

    }
    
    protected function getAnulado( $proceso, $tramite){

        $tab_solicitud = tab_solicitud::select( 'proceso.tab_solicitud.id')
        ->join('proceso.tab_ruta as t01', 'proceso.tab_solicitud.id', '=', 't01.id_tab_solicitud')
        ->join('configuracion.tab_proceso as t02', 't02.id', '=', 't01.id_tab_proceso')
        ->join('autenticacion.tab_usuario as t03', 't03.id', '=', 't01.id_tab_usuario')
        ->join('configuracion.tab_solicitud as t04', 't04.id', '=', 'proceso.tab_solicitud.id_tab_tipo_solicitud')
        ->where('in_actual', '=', true)
        ->where('proceso.tab_solicitud.in_activo', '=', true)
        ->where('id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
        ->whereIn('t01.id_tab_proceso', $proceso)
        ->whereIn('proceso.tab_solicitud.id_tab_tipo_solicitud', $tramite)
        ->where('t01.id_tab_estatus', '=', 4)
        ->count();
        
        return $tab_solicitud;

    }
    
    protected function getTodo( $proceso, $tramite){

        $tab_solicitud = tab_solicitud::select( 'proceso.tab_solicitud.id')
        //->where('id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
        ->whereIn('proceso.tab_solicitud.id_tab_tipo_solicitud', $tramite)
        ->count();
        
        return $tab_solicitud;

	}

}
