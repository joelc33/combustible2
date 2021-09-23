<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//
use gobela\Models\Administracion\tab_cuenta_contable;
use gobela\Models\Administracion\tab_anexo_contable;
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

class cuentaContableController extends Controller
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

        $tab_cuenta_contable = tab_cuenta_contable::select( 'administracion.tab_cuenta_contable.id','administracion.tab_cuenta_contable.co_cuenta_contable', 'administracion.tab_cuenta_contable.nu_cuenta_contable', 'administracion.tab_cuenta_contable.de_cuenta_contable', 'administracion.tab_cuenta_contable.nu_nivel', 'administracion.tab_cuenta_contable.in_activo', 't01.nu_anexo_contable')
        ->join('administracion.tab_anexo_contable as t01','t01.id','=','administracion.tab_cuenta_contable.id_tab_anexo_contable')                
        ->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('administracion.cuentaContable.lista')->with([
          'tab_cuenta_contable' => $tab_cuenta_contable,
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
        
        $tab_anexo_contable = tab_anexo_contable::select( 'id', 'nu_anexo_contable', 'de_anexo_contable', 'nu_cuenta_contable')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();         

        return View::make('administracion.cuentaContable.nuevo')->with([
            'data'  => $data,
            'tab_anexo_contable'  => $tab_anexo_contable
        ]);
    }

        /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function editar($id)
    {
        $data = tab_cuenta_contable::select( 'id', 'nu_cuenta_contable', 'de_cuenta_contable', 'nu_nivel','id_tab_anexo_contable', 'in_activo', 'created_at', 'updated_at')
        ->where('id', '=', $id)
        ->first();
        
        $tab_anexo_contable = tab_anexo_contable::select( 'id', 'nu_anexo_contable', 'de_anexo_contable', 'nu_cuenta_contable')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();            

        return View::make('administracion.cuentaContable.editar')->with([
            'data'  => $data,
            'tab_anexo_contable'  => $tab_anexo_contable
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

          $validador = Validator::make( $request->all(), tab_cuenta_contable::$validarEditar);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = tab_cuenta_contable::find($id);
            $tabla->nu_cuenta_contable = $request->cuenta;
            $tabla->de_cuenta_contable = $request->descripcion;
            $tabla->nu_nivel = $request->nu_nivel;
            $tabla->id_tab_anexo_contable = $request->anexo_contable;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
            return Redirect::to('/administracion/cuentaContable/lista');

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }else{

          $validador = Validator::make( $request->all(), tab_cuenta_contable::$validarCrear);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = new tab_cuenta_contable;
            $tabla->nu_cuenta_contable = $request->cuenta;
            $tabla->de_cuenta_contable = $request->descripcion;
            $tabla->nu_nivel = $request->nu_nivel;
            $tabla->id_tab_anexo_contable = $request->anexo_contable;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro creado con Exito!');
            return Redirect::to('/administracion/cuentaContable/lista');

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

        $tabla = tab_cuenta_contable::find( $request->get("id"));
        $tabla->delete();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
        return Redirect::to('/administracion/cuentaContable/lista');

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

        $tabla = tab_cuenta_contable::find( $id);
        $tabla->in_activo = false;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro despublicado con Exito!');
        return Redirect::to('/administracion/cuentaContable/lista');

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

        $tabla = tab_cuenta_contable::find( $id);
        $tabla->in_activo = true;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro publicado con Exito!');
        return Redirect::to('/administracion/cuentaContable/lista');

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }
}
