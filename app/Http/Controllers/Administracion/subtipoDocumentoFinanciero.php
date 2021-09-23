<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//
use gobela\Models\Administracion\tab_subtipo_documento_financiero;
use gobela\Models\Administracion\tab_tipo_documento_financiero;
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

class subtipoDocumentoFinanciero extends Controller
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
                

        $tab_subtipo_documento_financiero = tab_subtipo_documento_financiero::select( 'administracion.tab_subtipo_documento_financiero.id', 'administracion.tab_subtipo_documento_financiero.nu_subtipo_documento_financiero', 'administracion.tab_subtipo_documento_financiero.de_subtipo_documento_financiero', 't01.de_tipo_documento_financiero', 'administracion.tab_subtipo_documento_financiero.in_activo', 'administracion.tab_subtipo_documento_financiero.created_at', 'administracion.tab_subtipo_documento_financiero.updated_at')
        ->join('administracion.tab_tipo_documento_financiero as t01', 'administracion.tab_subtipo_documento_financiero.id_tab_tipo_documento_financiero', '=', 't01.id')
        ->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('administracion.subtipoDocumentoFinanciero.lista')->with([
          'tab_subtipo_documento_financiero' => $tab_subtipo_documento_financiero,
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
        
        $tab_tipo_documento_financiero = tab_tipo_documento_financiero::select( 'id','nu_tipo_documento_financiero','de_tipo_documento_financiero', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();        

        return View::make('administracion.subtipoDocumentoFinanciero.nuevo')->with([
            'tab_tipo_documento_financiero' => $tab_tipo_documento_financiero,
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
        
        $tab_tipo_documento_financiero = tab_tipo_documento_financiero::select( 'id','nu_tipo_documento_financiero','de_tipo_documento_financiero', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();
        
        $data = tab_subtipo_documento_financiero::select( 'id', 'nu_subtipo_documento_financiero', 'de_subtipo_documento_financiero', 'id_tab_tipo_documento_financiero', 'in_activo', 'created_at', 'updated_at')
        ->where('id', '=', $id)
        ->first();

        return View::make('administracion.subtipoDocumentoFinanciero.editar')->with([
            'tab_tipo_documento_financiero' => $tab_tipo_documento_financiero,            
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

          $validador = Validator::make( $request->all(), tab_subtipo_documento_financiero::$validarEditar);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = tab_subtipo_documento_financiero::find($id);
            $tabla->id_tab_tipo_documento_financiero = $request->tipo_documento;
            $tabla->nu_subtipo_documento_financiero = $request->codigo;
            $tabla->de_subtipo_documento_financiero = $request->descripcion; 
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
            return Redirect::to('/administracion/subtipoDocumentoFinanciero/lista');

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }else{

          $validador = Validator::make( $request->all(), tab_subtipo_documento_financiero::$validarCrear);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = new tab_subtipo_documento_financiero;
            $tabla->id_tab_tipo_documento_financiero = $request->tipo_documento;            
            $tabla->nu_subtipo_documento_financiero = $request->codigo;
            $tabla->de_subtipo_documento_financiero = $request->descripcion; 
            $tabla->in_activo = true;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro creado con Exito!');
            return Redirect::to('/administracion/subtipoDocumentoFinanciero/lista');

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

        $tabla = tab_subtipo_documento_financiero::find( $request->get("id"));
        $tabla->delete();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
        return Redirect::to('/administracion/subtipoDocumentoFinanciero/lista');

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

        $tabla = tab_subtipo_documento_financiero::find( $id);
        $tabla->in_activo = false;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro despublicado con Exito!');
        return Redirect::to('/administracion/subtipoDocumentoFinanciero/lista');

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

        $tabla = tab_subtipo_documento_financiero::find( $id);
        $tabla->in_activo = true;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro publicado con Exito!');
        return Redirect::to('/administracion/subtipoDocumentoFinanciero/lista');

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }
}
