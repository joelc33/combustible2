<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//
use gobela\Models\Administracion\tab_partida_ingreso;
use gobela\Models\Administracion\tab_aplicacion;
use gobela\Models\Administracion\tab_catalogo_partida;
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

class partidaIngresoController extends Controller
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

        $tab_partida_ingreso = tab_partida_ingreso::select( 'id', 'id_tab_ejercicio_fiscal', 'co_partida', 'nu_partida', 'de_partida', 'nu_nivel', 'mo_inicial', 'mo_modificado', 'mo_devengado', 'mo_liquidado', 'mo_recaudado', 'in_activo', 'created_at', 'updated_at')
        //->where('in_activo', '=', true)
        ->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('administracion.partidaIngreso.lista')->with([
          'tab_partida_ingreso' => $tab_partida_ingreso,
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
        
        $tab_aplicacion = tab_aplicacion::select( 'id','nu_aplicacion', 'de_aplicacion', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();          
        
        $filtro_catalogo = tab_partida_ingreso::select('id_tab_catalogo_partida')
        ->where('id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))                
        ->get();        
        
        $tab_catalogo_partida = tab_catalogo_partida::select( 'id','co_partida', 'de_partida', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->where('id_tipo_partida', '=', 1)
        ->where('nu_nivel', '=', 5)
        ->whereNotIn('id',$filtro_catalogo)         
        ->orderby('id','ASC')
        ->get();          

        return View::make('administracion.partidaIngreso.nuevo')->with([
            'data'  => $data,
            'tab_aplicacion'  => $tab_aplicacion,
            'tab_catalogo_partida'  => $tab_catalogo_partida
        ]);
    }

        /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function editar($id)
    {
        
        $tab_aplicacion = tab_aplicacion::select( 'id','nu_aplicacion', 'de_aplicacion', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();  
        
        $data = tab_partida_ingreso::select( 'id', 'id_tab_aplicacion', 'id_tab_catalogo_partida', 'mo_inicial', 'in_activo', 'created_at', 'updated_at')
        ->where('id', '=', $id)
        ->first();
        
        $filtro_catalogo = tab_partida_ingreso::select('id_tab_catalogo_partida')
        ->where('id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))  
        ->whereNotIn('id',array($data->id))                
        ->get();        
        
        $tab_catalogo_partida = tab_catalogo_partida::select( 'id','co_partida', 'de_partida', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->where('id_tipo_partida', '=', 1)
        ->where('nu_nivel', '=', 5)
        ->whereNotIn('id',$filtro_catalogo)         
        ->orderby('id','ASC')
        ->get();          

        return View::make('administracion.partidaIngreso.editar')->with([
            'data'  => $data,
            'tab_aplicacion'  => $tab_aplicacion,
            'tab_catalogo_partida'  => $tab_catalogo_partida            
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
        
        $tab_catalogo_partida = tab_catalogo_partida::select( 'co_partida','nu_partida','nu_nivel','nu_pa','nu_ge','nu_es','nu_se','nu_sse','de_partida')              
        ->where('id', '=', $request->partida)
        ->first();        

        if($id!=''||$id!=null){

          $validador = Validator::make( $request->all(), tab_partida_ingreso::$validarEditar);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = tab_partida_ingreso::find($id);
            $tabla->co_partida = $tab_catalogo_partida->co_partida; 
            $tabla->nu_partida = $tab_catalogo_partida->nu_partida;
            $tabla->de_partida = $tab_catalogo_partida->de_partida;
            $tabla->nu_pa = $tab_catalogo_partida->nu_pa;
            $tabla->nu_ge = $tab_catalogo_partida->nu_ge;
            $tabla->nu_es = $tab_catalogo_partida->nu_es;
            $tabla->nu_se = $tab_catalogo_partida->nu_se;
            $tabla->nu_sse = $tab_catalogo_partida->nu_sse;
            $tabla->nu_nivel = $tab_catalogo_partida->nu_nivel;
            $tabla->id_tab_aplicacion = $request->aplicacion;
            $tabla->id_tab_catalogo_partida = $request->partida;
            $tabla->id_tab_ejercicio_fiscal = Session::get('ejercicio');
            $tabla->mo_inicial = $request->monto;
            $tabla->mo_modificado = 0;
            $tabla->mo_devengado = 0;
            $tabla->mo_liquidado = 0;
            $tabla->mo_recaudado = 0;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
            return Redirect::to('/administracion/partidaIngreso/lista');

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }else{

          $validador = Validator::make( $request->all(), tab_partida_ingreso::$validarCrear);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = new tab_partida_ingreso;
            $tabla->co_partida = $tab_catalogo_partida->co_partida; 
            $tabla->nu_partida = $tab_catalogo_partida->nu_partida;
            $tabla->de_partida = $tab_catalogo_partida->de_partida;
            $tabla->nu_pa = $tab_catalogo_partida->nu_pa;
            $tabla->nu_ge = $tab_catalogo_partida->nu_ge;
            $tabla->nu_es = $tab_catalogo_partida->nu_es;
            $tabla->nu_se = $tab_catalogo_partida->nu_se;
            $tabla->nu_sse = $tab_catalogo_partida->nu_sse;
            $tabla->nu_nivel = $tab_catalogo_partida->nu_nivel;
            $tabla->id_tab_aplicacion = $request->aplicacion;
            $tabla->id_tab_catalogo_partida = $request->partida;
            $tabla->id_tab_ejercicio_fiscal = Session::get('ejercicio');
            $tabla->mo_inicial = $request->monto;
            $tabla->mo_modificado = 0;
            $tabla->mo_devengado = 0;
            $tabla->mo_liquidado = 0;
            $tabla->mo_recaudado = 0;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro creado con Exito!');
            return Redirect::to('/administracion/partidaIngreso/lista');

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

        $tabla = tab_partida_ingreso::find( $request->get("id"));
        $tabla->delete();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
        return Redirect::to('/administracion/partidaIngreso/lista');

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

        $tabla = tab_partida_ingreso::find( $id);
        $tabla->in_activo = false;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro despublicado con Exito!');
        return Redirect::to('/administracion/partidaIngreso/lista');

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

        $tabla = tab_partida_ingreso::find( $id);
        $tabla->in_activo = true;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro publicado con Exito!');
        return Redirect::to('/administracion/partidaIngreso/lista');

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }
}
