<?php

namespace App\Http\Controllers\Configuracion;
//*******agregar esta linea******//
use App\Models\Configuracion\tab_gerencia;
use View;
use Validator;
use Response;
use DB;
use Session;
use Redirect;
//*******************************//
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class gerenciaController extends Controller
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
        $sortBy = 'de_gerencia';
        $orderBy = 'asc';
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

        $tab_gerencia= tab_gerencia::orderBy($sortBy, $orderBy)
        ->where('id','<>','1')
        ->paginate($perPage);

        return View::make('configuracion.gerencia.lista')->with([
          'tab_gerencia' => $tab_gerencia,
          'orderBy'      => $orderBy,
          'sortBy'       => $sortBy,
          'perPage'      => $perPage,
          'columnas'     => $columnas,
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


        return View::make('configuracion.gerencia.nuevo')->with([
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
        $data = tab_gerencia::where('id', '=', $id)
        ->first();

        return View::make('configuracion.gerencia.editar')->with([
            'data'  => $data
        ]);
    }

        /**
    * Update the specified resource in storage.
    *
    * @param  int  $id
    * @return Response
    */
    public function guardar( Request $request)
    {
        

            DB::beginTransaction();      
  
            try {

                $validator= Validator::make($request->all(), tab_gerencia::$validar);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                if(empty($request->id)){
                     $tab_gerencia = new tab_gerencia;
                     $tab_gerencia->in_estatus = true;
                }else{
                     $tab_gerencia = tab_gerencia::find($request->id);
                }

                $tab_gerencia->de_gerencia = $request->descripcion;
                $tab_gerencia->save();

                DB::commit();
                
                Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
                return Redirect::to('/configuracion/gerencia/lista');

            }catch (\Illuminate\Database\QueryException $e){

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

        $tabla = tab_gerencia::find( $id);
        $tabla->in_estatus = false;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro deshabilitado con Exito!');
        return Redirect::to('/configuracion/gerencia/lista');

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

        $tabla = tab_gerencia::find( $id);
        $tabla->in_estatus = true;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro habilitado con Exito!');
        return Redirect::to('/configuracion/gerencia/lista');

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
    }
}
