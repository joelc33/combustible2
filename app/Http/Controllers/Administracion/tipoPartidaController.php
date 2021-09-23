<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//
use gobela\Models\Administracion\tab_tipo_partida;
use View;
use Validator;
use Response;
use DB;
use Session;
use Redirect;
//*******************************//
use Illuminate\Http\Request;

use gobela\Http\Requests;
use gobela\Http\Controllers\Controller;

class tipoPartidaController extends Controller
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

        $tab_tipo_partida = tab_tipo_partida::select( 'id', 'de_tipo_partida', 'in_activo', 'created_at', 'updated_at')
        //->where('in_activo', '=', true)
        ->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('administracion.tipoPartida.lista')->with([
          'tab_tipo_partida' => $tab_tipo_partida,
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

        return View::make('administracion.tipoPartida.nuevo')->with([
            'data'  => $data
        ]);
    }

        /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function editar($id)
    {
        $data = tab_tipo_partida::select( 'id', 'de_tipo_partida', 'in_activo', 'created_at', 'updated_at')
        ->where('id', '=', $id)
        ->first();

        return View::make('administracion.tipoPartida.editar')->with([
            'data'  => $data
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

          $validador = Validator::make( $request->all(), tab_tipo_partida::$validarEditar);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = tab_tipo_partida::find($id);
            $tabla->de_tipo_partida = $request->descripcion; 
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
            return Redirect::to('/administracion/tipoPartida/lista');

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }else{

          $validador = Validator::make( $request->all(), tab_tipo_partida::$validarCrear);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = new tab_tipo_partida;
            $tabla->de_tipo_partida = $request->descripcion;
            $tabla->in_activo = true;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro creado con Exito!');
            return Redirect::to('/administracion/tipoPartida/lista');

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

        $tabla = tab_tipo_partida::find( $request->get("id"));
        $tabla->delete();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
        return Redirect::to('/administracion/tipoPartida/lista');

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

        $tabla = tab_tipo_partida::find( $id);
        $tabla->in_activo = false;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro despublicado con Exito!');
        return Redirect::to('/administracion/tipoPartida/lista');

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

        $tabla = tab_tipo_partida::find( $id);
        $tabla->in_activo = true;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro publicado con Exito!');
        return Redirect::to('/administracion/tipoPartida/lista');

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }
}
