<?php

namespace App\Http\Controllers\Configuracion;
//*******agregar esta linea******//
use App\Models\Configuracion\tab_solicitud;
use App\Models\Configuracion\tab_proceso;
use App\Models\Configuracion\tab_ruta;
use App\Models\Configuracion\tab_entorno;
use View;
use Validator;
use Response;
use DB;
use Session;
use Redirect;
//*******************************//
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class rutaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function lista( Request $request, $id)
    {
        $sortBy = 'id';
        $orderBy = 'desc';
        $perPage = 5;
        $q = null;
        $columnas = [
          ['valor'=>'bnumberdialed', 'texto'=>'Número de Origen'],
          ['valor'=>'bnumberdialed', 'texto'=>'Número de Destino']
        ];

        if ($request->has('orderBy')){
            $orderBy = $request->query('orderBy');
        }
        if ($request->has('sortBy')){
            $sortBy = $request->query('sortBy');
        } 
        if ($request->has('perPage')){
            $perPage = $request->query('perPage');
        } 
        if ($request->has('q')){
            $q = $request->query('q');
        }

        $tab_ruta = tab_ruta::select( 'configuracion.tab_ruta.id', 'de_proceso', 'id_tab_solicitud', 'nu_orden', 'in_datos', 'nb_controlador', 
        'nb_accion', 'nb_reporte')
        ->join('configuracion.tab_proceso as t01','t01.id','=','configuracion.tab_ruta.id_tab_proceso')
        ->where('id_tab_solicitud', '=', $id)
        //->where('in_activo', '=', true)
        //->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('configuracion.ruta.lista')->with([
          'tab_ruta' => $tab_ruta,
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'q' => $q,
          'id' => $id
        ]);
    }

        /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function nuevo( $id)
    {
        $data = array( "id_tab_solicitud" => $id);

        $tab_proceso = tab_proceso::orderBy('id','asc')
        ->get();

        $tab_entorno = tab_entorno::orderBy('id','asc')
        ->get();

        return View::make('configuracion.ruta.nuevo')->with([
            'data'  => $data,
            'tab_proceso'  => $tab_proceso,
            'tab_entorno'  => $tab_entorno
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function editar($id)
    {
        $data = tab_ruta::select( 'id', 'id_tab_proceso', 'id_tab_solicitud', 'nu_orden', 'in_datos', 'nb_controlador', 
        'nb_accion', 'nb_reporte', 'in_activo', 'created_at', 'updated_at', 'id_tab_entorno')
        ->where('id', '=', $id)
        ->first();

        $tab_proceso = tab_proceso::orderBy('id','asc')
        ->get();

        $tab_entorno = tab_entorno::orderBy('id','asc')
        ->get();

        return View::make('configuracion.ruta.editar')->with([
            'data'  => $data,
            'tab_proceso'  => $tab_proceso,
            'tab_entorno'  => $tab_entorno
        ]);
    }

        /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request, $id = NULL)
    {
        DB::beginTransaction();

            if($id!=''||$id!=null){

            $validador = Validator::make( $request->all(), tab_ruta::$validarEditar);
            if ($validador->fails()) {
                return Redirect::back()->withErrors( $validador)->withInput( $request->all());
            }

            try {

                $tabla = tab_ruta::find($id);
                $tabla->id_tab_proceso = $request->proceso;
                //$tabla->id_tab_solicitud = $request->solicitud;
                $tabla->nu_orden = $request->orden;
                if (array_key_exists('in_datos', $request->all())) {
                $tabla->in_datos = true;
                }else{
                $tabla->in_datos = false;
                }
                $tabla->nb_controlador = $request->controlador;
                $tabla->nb_accion = $request->accion;
                $tabla->nb_reporte = $request->reporte;
                $tabla->id_tab_entorno = $request->entorno;
                $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
            return Redirect::to('/configuracion/ruta/lista'.'/'.$request->solicitud);

            }catch (\Illuminate\Database\QueryException $e)
            {
                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());
            }

        }else{

            $validador = Validator::make( $request->all(), tab_ruta::$validarCrear);
            if ($validador->fails()) {
                return Redirect::back()->withErrors( $validador)->withInput( $request->all());
            }

            try {

                $tabla = new tab_ruta;
                $tabla->id_tab_proceso = $request->proceso;
                $tabla->id_tab_solicitud = $request->solicitud;
                $tabla->nu_orden = $request->orden;
                if (array_key_exists('in_datos', $request->all())) {
                  $tabla->in_datos = true;
                }else{
                  $tabla->in_datos = false;
                }
                $tabla->nb_controlador = $request->controlador;
                $tabla->nb_accion = $request->accion;
                $tabla->nb_reporte = $request->reporte;
                $tabla->id_tab_entorno = $request->entorno;
                $tabla->in_activo = true;
                $tabla->save();

                DB::commit();

                Session::flash('msg_side_overlay', 'Registro creado con Exito!');
                return Redirect::to('/configuracion/ruta/lista'.'/'.$request->solicitud);

            }catch (\Illuminate\Database\QueryException $e)
            {
                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());
            }

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar( Request $request)
    {
        DB::beginTransaction();
        try {

            $tabla = tab_ruta::find( $request->get("id"));
            $tabla->delete();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
            return Redirect::to('/configuracion/ruta/lista'.'/'.$tabla->id_tab_solicitud);

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
    }
}
