<?php

namespace App\Http\Controllers\Autenticar;
//*******agregar esta linea******//
use App\Models\Configuracion\tab_proceso_usuario;
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

class procesoController extends Controller
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

        $tab_proceso_usuario = tab_proceso_usuario::select( 'configuracion.tab_proceso_usuario.id', 'in_principal', 'de_proceso')
        ->join('configuracion.tab_proceso as t01', 't01.id', '=', 'configuracion.tab_proceso_usuario.id_tab_proceso')
        ->where('id_tab_usuario', '=', $id)
        //->where('in_activo', '=', true)
        //->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('autenticar.usuario.proceso.lista')->with([
          'tab_proceso_usuario' => $tab_proceso_usuario,
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
        $data = array( "id_tab_usuario" => $id);

        $proceso = tab_proceso_usuario::getListaProcesoAsignado( $id);

        $tab_proceso = tab_proceso::whereNotIn('id', $proceso)->orderBy('id','asc')
        ->get();

        return View::make('autenticar.usuario.proceso.nuevo')->with([
            'data'  => $data,
            'tab_proceso'  => $tab_proceso
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

                $validator= Validator::make($request->all(), tab_proceso_usuario::$validarEditar);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                $tabla = tab_proceso_usuario::find($id);
                $tabla->mi_campo = $request->descripcion; 
                $tabla->save();

                DB::commit();
                
                Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
                return Redirect::to('/autenticar/usuario/proceso'.'/'.$tabla->id_tab_usuario);

            }catch (\Illuminate\Database\QueryException $e){

                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());

            }
  
        }else{
  
            try {

                $validator = Validator::make($request->all(), tab_proceso_usuario::$validarCrear);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                $tabla = new tab_proceso_usuario;
                $tabla->id_tab_proceso = $request->proceso;
                $tabla->id_tab_usuario = $request->usuario;
                $tabla->in_principal = false;
                $tabla->in_activo = true;
                $tabla->save();

                DB::commit();

                Session::flash('msg_side_overlay', 'Registro guardado con Exito!');
                return Redirect::to('/autenticar/usuario/proceso'.'/'.$tabla->id_tab_usuario);

            }catch (\Illuminate\Database\QueryException $e){

                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());

            }
        }
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function eliminar( Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $tabla = tab_proceso_usuario::find( $request->id);
            $tabla->delete();
            /*$tab_proceso_usuario->in_activo = false;
            $tab_proceso_usuario->save();*/
            DB::commit();

            Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
            return Redirect::to('/autenticar/usuario/proceso'.'/'.$tabla->id_tab_usuario);

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();

            $response['success']  = 'false';
            $response['msg']  = array('ERROR ('.$e->getCode().'):'=> $e->getMessage());
            return Response::json($response, 200);
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

            $data = tab_proceso_usuario::select( 'id_tab_usuario')
            ->where('id', '=', $id)
            ->first();

            $tabla = tab_proceso_usuario::find( $id);
            $tabla->in_principal = false;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro despublicado con Exito!');
            return Redirect::to('/autenticar/usuario/proceso'.'/'.$data->id_tab_usuario);

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

            $data = tab_proceso_usuario::select( 'id_tab_usuario')
            ->where('id', '=', $id)
            ->first();

            $proceso = tab_proceso_usuario::where('id_tab_usuario', '=', $data->id_tab_usuario)->update(array('in_principal' => FALSE));

            $tabla = tab_proceso_usuario::find( $id);
            $tabla->in_principal = true;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro publicado con Exito!');
            return Redirect::to('/autenticar/usuario/proceso'.'/'.$data->id_tab_usuario);

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
    }
}
