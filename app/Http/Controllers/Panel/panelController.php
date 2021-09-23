<?php

namespace App\Http\Controllers\Panel;
//*******agregar esta linea******//
use App\Models\Configuracion\tab_ejercicio_fiscal;
use App\Models\Autenticar\tab_notificacion;
use App\Models\Autenticar\tab_usuario;
use App\Models\Proceso\tab_solicitud;
use App\Models\Configuracion\tab_proceso_usuario;
use App\Models\Configuracion\tab_solicitud_usuario;
use App\Models\Configuracion\tab_especialidad;
use App\Models\Configuracion\tab_instituto;
use Session;
use Response;
use Validator;
use DB;
use View;
use Redirect;
use URL;
use Auth;
//*******************************//
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class panelController extends Controller
{
    public function __construct()
    {
        $this->middleware('optimizar');
        $this->middleware('auth');
    }

        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function inicio()
    {    

        $proceso = tab_proceso_usuario::getListaProcesoAsignado(Auth::user()->id);
        $tramite = tab_solicitud_usuario::getListaTramiteAsignado(Auth::user()->id);

        $total_pendiente = tab_solicitud::getPendiente( $proceso, $tramite);
        $total_en_proceso = tab_solicitud::getEnProceso( $proceso, $tramite);
        $total_completo = tab_solicitud::getCompleto( $proceso, $tramite);
        $total_anulado = tab_solicitud::getAnulado( $proceso, $tramite);
        $total = tab_solicitud::getTodo( $proceso, $tramite);

        return View::make('dashboard')->with([
            'total_pendiente'  => $total_pendiente,
            'total_en_proceso'  => $total_en_proceso,
            'total_completo'  => $total_completo,
            'total_anulado'  => $total_anulado,
            'total'  => $total
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ejercicio()
    {
  
        $tab_especialidad = tab_especialidad::getEspecialidad(Auth::user()->id);
        $tab_instituto    = tab_instituto::getInstituto(Auth::user()->id);
  
        return View::make('inicio.ejercicio')->with([
            'tab_especialidad'  => $tab_especialidad,
            'tab_instituto' => $tab_instituto
        ]);
    }

        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ejercicioInicio(Request $request)
    {

        $validator= Validator::make($request->all(), tab_usuario::$validarLogin);

        if ($validator->fails()){
            return Redirect::back()->withErrors( $validator)->withInput( $request->all());
        }
  

        $especialidad = tab_especialidad::where('id','=',$request->especialidad)->first();
        $instituto    = tab_instituto::where('id','=',$request->instituto)->first();

     

        Session::put('id_especialidad', $request->especialidad);
        Session::put('id_instituto', $request->instituto);
        Session::put('nb_especialidad', $especialidad->de_especialidad);
        Session::put('nb_instituto', $instituto->de_instituto);


        Session::flash('msg_side_overlay', 'Especialidad Seleccionada con Exito!');

        return redirect('/inicio');

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function notificacion(Request $request)
    {

        $tab_notificacion = tab_notificacion::select( 'id', 'de_notificacion', 'created_at', 'de_icono')
        ->where('id_tab_usuario', '=', Auth::user()->id)
        ->where('in_activo', '=', true);

        $response['success']  = 'true';
        $response['total']  = $tab_notificacion->count();

        $tab_notificacion = $tab_notificacion->orderby('id','DESC')->limit(5)->get()->toArray();

        $registros = null;

        foreach($tab_notificacion as $notificacion) {
            $registros[] = array(
                "id"  => trim($notificacion['id']),
                "de_notificacion"  => trim($notificacion['de_notificacion']),
                "de_icono"  => trim($notificacion['de_icono']),
                "TimeAgo"  => trim(tab_notificacion::getTimeAgo($notificacion['created_at']))
            );
        }

        $response['data']  = $registros;

		return Response::json($response, 200);
    }
}
