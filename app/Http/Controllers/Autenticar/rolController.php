<?php

namespace App\Http\Controllers\Autenticar;
//*******agregar esta linea******//
use App\Models\Autenticar\tab_rol;
use View;
use Validator;
use Response;
use DB;
use Session;
use Redirect;
//*******************************//
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class rolController extends Controller
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

        $tab_rol = tab_rol::select( 'id', 'de_rol', 'in_estatus')
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('autenticar.rol.lista')->with([
          'tab_rol' => $tab_rol,
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


        return View::make('autenticar.rol.nuevo')->with([
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


        $data = tab_rol::select( 'id', 'de_rol', 'in_estatus')
        ->where('id', '=', $id)
        ->first();

        return View::make('autenticar.rol.editar')->with([
            'data'  => $data
        ]);
    }

        /**
    * Update the specified resource in storage.
    *
    * @param  int  $id
    * @return Response
    */
    public function guardar( Request $request, $id = NULL)
    {
        DB::beginTransaction();

        if($id!=''||$id!=null){
  
            try {

                $validator= Validator::make($request->all(), tab_rol::$validarEditar);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                $tab_rol = tab_rol::find($id);
                $tab_rol->de_rol = $request->descripcion;
                $tab_rol->save();

                DB::commit();
                
                Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
                return Redirect::to('/autenticar/rol/lista');

            }catch (\Illuminate\Database\QueryException $e){

                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());

            }
  
        }else{
  
            try {

                $validator = Validator::make($request->all(), tab_rol::$validarCrear);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                $tab_rol = new tab_rol;
                $tab_rol->de_rol = $request->descripcion;
                $tab_rol->id_tab_bandeja = 1;
                $tab_rol->in_estatus = true;
                $tab_rol->save();

                DB::commit();

                Session::flash('msg_side_overlay', 'Registro Guardado con Exito!');
                return Redirect::to('/autenticar/rol/lista');

            }catch (\Illuminate\Database\QueryException $e){

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
    public function deshabilitar( $id)
    {
      DB::beginTransaction();
      try {

        $tabla = tab_rol::find( $id);
        $tabla->in_estatus = false;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro deshabilitado con Exito!');
        return Redirect::to('/autenticar/rol/lista');

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

        $tabla = tab_rol::find( $id);
        $tabla->in_estatus = true;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro habilitado con Exito!');
        return Redirect::to('/autenticar/rol/lista');

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
    }
}
