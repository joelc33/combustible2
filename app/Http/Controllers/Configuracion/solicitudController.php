<?php

namespace App\Http\Controllers\Configuracion;
//*******agregar esta linea******//
use App\Models\Configuracion\tab_solicitud;
use App\Models\Configuracion\tab_proceso;
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

class solicitudController extends Controller
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
    public function lista( Request $request)
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

        $tab_solicitud = tab_solicitud::select( 'configuracion.tab_solicitud.id', 'de_proceso', 'de_solicitud', 'in_ver', 'configuracion.tab_solicitud.in_activo', 'nu_identificador')
        ->join('configuracion.tab_proceso as t01','t01.id','=','configuracion.tab_solicitud.id_tab_proceso')
        //->where('in_activo', '=', true)
        ->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('configuracion.solicitud.lista')->with([
          'tab_solicitud' => $tab_solicitud,
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'q' => $q
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function nuevo()
    {
        $data = array( "id" => null);

        $tab_proceso = tab_proceso::orderBy('id','asc')
        ->get();

        return View::make('configuracion.solicitud.nuevo')->with([
            'data'  => $data,
            'tab_proceso'  => $tab_proceso
        ]);
    }

        /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function editar($id)
    {
        $data = tab_solicitud::select( 'id', 'id_tab_proceso', 'de_solicitud', 'in_ver', 'in_activo', 'created_at', 
        'updated_at', 'nu_identificador')
        ->where('id', '=', $id)
        ->first();

        $tab_proceso = tab_proceso::orderBy('id','asc')
        ->get();

        return View::make('configuracion.solicitud.editar')->with([
            'data'  => $data,
            'tab_proceso'  => $tab_proceso
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

            $validador = Validator::make( $request->all(), tab_solicitud::$validarEditar);
            if ($validador->fails()) {
                return Redirect::back()->withErrors( $validador)->withInput( $request->all());
            }

            try {

                $tabla = tab_solicitud::find($id);
                $tabla->id_tab_proceso = $request->proceso;
                $tabla->de_solicitud = $request->descripcion;
                $tabla->nu_identificador = $request->identificador;
                //$tabla->in_ver = $request->visible;
                if (array_key_exists('visible', $request->all())) {
                  $tabla->in_ver = true;
                }else{
                  $tabla->in_ver = false;
                }
                $tabla->save();

                DB::commit();

                Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
                return Redirect::to('/configuracion/solicitud/lista');

            }catch (\Illuminate\Database\QueryException $e)
            {
                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());
            }

        }else{

            $validador = Validator::make( $request->all(), tab_solicitud::$validarCrear);
            if ($validador->fails()) {
                return Redirect::back()->withErrors( $validador)->withInput( $request->all());
            }

            try {

                $tabla = new tab_solicitud;
                $tabla->id_tab_proceso = $request->proceso;
                $tabla->de_solicitud = $request->descripcion;
                $tabla->nu_identificador = $request->identificador;
                //$tabla->in_ver = $request->visible; 
                if (array_key_exists('visible', $request->all())) {
                  $tabla->in_ver = true;
                }else{
                  $tabla->in_ver = false;
                }
                $tabla->in_activo = true;
                $tabla->save();

                DB::commit();

                Session::flash('msg_side_overlay', 'Registro creado con Exito!');
                return Redirect::to('/configuracion/solicitud/lista');

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

            $tabla = tab_solicitud::find( $request->get("id"));
            $tabla->delete();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
            return Redirect::to('/configuracion/solicitud/lista');

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
    }

                /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deshabilitar( $id)
    {
        DB::beginTransaction();
        try {

            $tabla = tab_solicitud::find( $id);
            $tabla->in_activo = false;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro despublicado con Exito!');
            return Redirect::to('/configuracion/solicitud/lista');

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function habilitar( $id)
    {
        DB::beginTransaction();
        try {

            $tabla = tab_solicitud::find( $id);
            $tabla->in_activo = true;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro publicado con Exito!');
            return Redirect::to('/configuracion/solicitud/lista');

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
    }
}
