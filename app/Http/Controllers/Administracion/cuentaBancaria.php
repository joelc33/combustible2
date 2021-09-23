<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//
use gobela\Models\Administracion\tab_banco;
use gobela\Models\Administracion\tab_tipo_cuenta_bancaria;
use gobela\Models\Administracion\tab_cuenta_bancaria;
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

class cuentaBancaria extends Controller
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

        $tab_cuenta_bancaria = tab_cuenta_bancaria::select( 'administracion.tab_cuenta_bancaria.id', 'administracion.tab_cuenta_bancaria.nu_cuenta_bancaria', 'administracion.tab_cuenta_bancaria.de_cuenta_bancaria', 't01.de_banco', 't02.de_tipo_cuenta_bancaria', 'administracion.tab_cuenta_bancaria.in_activo', 'administracion.tab_cuenta_bancaria.created_at', 'administracion.tab_cuenta_bancaria.updated_at')
        ->join('administracion.tab_banco as t01','t01.id','=','administracion.tab_cuenta_bancaria.id_tab_banco')
        ->join('administracion.tab_tipo_cuenta_bancaria as t02','t02.id','=','administracion.tab_cuenta_bancaria.id_tab_tipo_cuenta_bancaria')
        ->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('administracion.cuentaBancaria.lista')->with([
          'tab_cuenta_bancaria' => $tab_cuenta_bancaria,
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
        
        $tab_tipo_cuenta_bancaria = tab_tipo_cuenta_bancaria::select( 'id','de_tipo_cuenta_bancaria', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();    
        
        $tab_banco = tab_banco::select( 'id','de_banco', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();        

        return View::make('administracion.cuentaBancaria.nuevo')->with([
            'data'  => $data,
            'tab_tipo_cuenta_bancaria'  => $tab_tipo_cuenta_bancaria,
            'tab_banco'  => $tab_banco
        ]);
    }

        /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function editar($id)
    {
        
        $tab_tipo_cuenta_bancaria = tab_tipo_cuenta_bancaria::select( 'id','de_tipo_cuenta_bancaria', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();    
        
        $tab_banco = tab_banco::select( 'id','de_banco', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();        
        
        $data = tab_cuenta_bancaria::select( 'administracion.tab_cuenta_bancaria.id', 'administracion.tab_cuenta_bancaria.nu_cuenta_bancaria', 'administracion.tab_cuenta_bancaria.de_cuenta_bancaria', 'id_tab_banco', 'id_tab_tipo_cuenta_bancaria', 'nu_contrato', 'in_fondo_tercero','administracion.tab_cuenta_bancaria.in_activo', 'administracion.tab_cuenta_bancaria.created_at', 'administracion.tab_cuenta_bancaria.updated_at')
        ->where('id', '=', $id)
        ->first();

        return View::make('administracion.cuentaBancaria.editar')->with([
            'data'  => $data,
            'tab_tipo_cuenta_bancaria'  => $tab_tipo_cuenta_bancaria,
            'tab_banco'  => $tab_banco            
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

          $validador = Validator::make( $request->all(), tab_cuenta_bancaria::$validarEditar);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = tab_cuenta_bancaria::find($id);
            $tabla->nu_cuenta_bancaria = $request->numero_cuenta_bancaria;
            $tabla->de_cuenta_bancaria = $request->descripcion;
            $tabla->id_tab_banco = $request->banco;
            $tabla->id_tab_tipo_cuenta_bancaria = $request->tipo_cuenta;
            $tabla->nu_contrato = $request->numero_contrato;
            $tabla->in_fondo_tercero = $request->fondo_tercero;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
            return Redirect::to('/administracion/cuentaBancaria/lista');

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }else{

          $validador = Validator::make( $request->all(), tab_cuenta_bancaria::$validarCrear);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = new tab_cuenta_bancaria;
            $tabla->nu_cuenta_bancaria = $request->numero_cuenta_bancaria;
            $tabla->de_cuenta_bancaria = $request->descripcion;
            $tabla->id_tab_banco = $request->banco;
            $tabla->id_tab_tipo_cuenta_bancaria = $request->tipo_cuenta;
            $tabla->nu_contrato = $request->numero_contrato;
            $tabla->in_fondo_tercero = $request->fondo_tercero;
            $tabla->in_activo = true;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro creado con Exito!');
            return Redirect::to('/administracion/cuentaBancaria/lista');

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

        $tabla = tab_cuenta_bancaria::find( $request->get("id"));
        $tabla->delete();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
        return Redirect::to('/administracion/cuentaBancaria/lista');

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

        $tabla = tab_cuenta_bancaria::find( $id);
        $tabla->in_activo = false;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro despublicado con Exito!');
        return Redirect::to('/administracion/cuentaBancaria/lista');

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

        $tabla = tab_cuenta_bancaria::find( $id);
        $tabla->in_activo = true;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro publicado con Exito!');
        return Redirect::to('/administracion/cuentaBancaria/lista');

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }
}
