<?php

namespace App\Http\Controllers\Configuracion;
//*******agregar esta linea******//
use App\Models\Autenticar\tab_usuario_especialidad;
use App\Models\Configuracion\tab_especialidad;
use App\Models\Proceso\tab_ruta;
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

class especialidadController extends Controller
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

        $tab_usuario_especialidad = tab_usuario_especialidad::select( 'autenticacion.tab_usuario_especialidad.id', 'in_principal', 'de_especialidad')
        ->join('configuracion.tab_especialidad as t01', 't01.id', '=', 'autenticacion.tab_usuario_especialidad.id_especialidad')
        ->where('id_usuario', '=', $id)
        //->where('in_activo', '=', true)
        //->search($q, $sortBy)
        ->orderBy('de_especialidad', $orderBy)
        ->paginate($perPage);

        return View::make('autenticar.usuario.especialidad.lista')->with([
          'tab_proceso_usuario' => $tab_usuario_especialidad,
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'q' => $q,
          'id' => $id
        ]);
    }


      /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function index(Request $request)
    {
        $sortBy = 'de_especialidad';
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

        $tab_especialidad = tab_especialidad::search($q, $sortBy)
        ->orderBy('de_especialidad', $orderBy)
        ->paginate($perPage);

        return View::make('configuracion.especialidad.lista')->with([
          'tab_especialidad' => $tab_especialidad,
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
    public function nuevo($id)
    {
        $data = array( "id_usuario" => $id);

        $especialidad = tab_usuario_especialidad::getListaEspecialidadAsignado( $id);

        $tab_especialidad = tab_especialidad::whereNotIn('id', $especialidad)->orderBy('id','asc')
        ->get();

        return View::make('autenticar.usuario.especialidad.nuevo')->with([
            'data'  => $data,
            'tab_especialidad'  => $tab_especialidad
        ]);
    }


     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save( Request $request)
    {
        DB::beginTransaction();
  
            try {

                $validator= Validator::make($request->all(), tab_especialidad::$validar);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                if(empty($request->id)){
                     $tabla = new tab_especialidad;
                }
                else{
                     $tabla = tab_especialidad::find($request->id);
                }  

                             
                $tabla->de_especialidad = $request->de_especialidad; 
                $tabla->save();

                DB::commit();
                
                Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
                return Redirect::to('/configuracion/especialidad');

            }catch (\Illuminate\Database\QueryException $e){

                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());

            }
  
        
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function delete( Request $request, $id)
    {
        DB::beginTransaction();
        try {

            $cant_ruta         = tab_ruta::where('id_especialidad','=',$id)->count();
            $cant_especialidad = tab_usuario_especialidad::where('id_especialidad','=',$id)->count();            

            if($cant_ruta>0){
                  Session::flash('msg_side_overlay', 'El registro se encuentra asociado a historia de pacientes!');
                  return Redirect::to('/configuracion/especialidad');
            }

            if($cant_especialidad>0){
                  Session::flash('msg_side_overlay', 'El registro se encuenta asociado a usuarios!');
                  return Redirect::to('/configuracion/especialidad');
            }


            $tabla = tab_especialidad::find($id);
            $tabla->delete();
            DB::commit();

            Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
           return Redirect::to('/configuracion/especialidad');

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();

            $response['success']  = 'false';
            $response['msg']  = array('ERROR ('.$e->getCode().'):'=> $e->getMessage());
            return Response::json($response, 200);
        }
    }

     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
     
        $especialidad = '';
        if(!empty($id))
            $especialidad = tab_especialidad::where('id','=',$id)->first();


        return View::make('configuracion.especialidad.editar')->with([
            'data'  => $especialidad
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function new()
    {
        return View::make('configuracion.especialidad.editar')->with([
            'data'  => ''
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

                $validator= Validator::make($request->all(), tab_usuario_especialidad::$validarEditar);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                $tabla = tab_usuario_especialidad::find($id);
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

                $validator = Validator::make($request->all(), tab_usuario_especialidad::$validarCrear);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                $tabla = new tab_usuario_especialidad;
                $tabla->id_especialidad = $request->id_especialidad;
                $tabla->id_usuario = $request->id_usuario;
                $tabla->in_principal = false;
                $tabla->save();

                DB::commit();

                Session::flash('msg_side_overlay', 'Registro guardado con Exito!');
                return Redirect::to('/configuracion/usuario/especialidad'.'/'.$tabla->id_usuario);

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
            $tabla = tab_usuario_especialidad::find($id);
            $tabla->delete();
            /*$tab_proceso_usuario->in_activo = false;
            $tab_proceso_usuario->save();*/
            DB::commit();

            Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
           return Redirect::to('/configuracion/usuario/especialidad'.'/'.$tabla->id_usuario);

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

            $data = tab_usuario_especialidad::select( 'id_usuario')
            ->where('id', '=', $id)
            ->first();

            $tabla = tab_usuario_especialidad::find( $id);
            $tabla->in_principal = false;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro despublicado con Exito!');
            return Redirect::to('/configuracion/usuario/especialidad'.'/'.$tabla->id_usuario);

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

            $data = tab_usuario_especialidad::select( 'id_usuario')
            ->where('id', '=', $id)
            ->first();

            $proceso = tab_usuario_especialidad::where('id_usuario', '=', $data->id_usuario)->update(array('in_principal' => FALSE));

            $tabla = tab_usuario_especialidad::find( $id);
            $tabla->in_principal = true;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro publicado con Exito!');
            return Redirect::to('/configuracion/usuario/especialidad'.'/'.$tabla->id_usuario);

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
    }
}
