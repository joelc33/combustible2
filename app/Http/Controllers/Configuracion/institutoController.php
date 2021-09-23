<?php

namespace App\Http\Controllers\Configuracion;
//*******agregar esta linea******//
use App\Models\Autenticar\tab_usuario_instituto;
use App\Models\Configuracion\tab_instituto;
use App\Models\Proceso\tab_ruta;
use View;
use Validator;
use Response;
use DB;
use Session;
use Redirect;
use Mail;
//*******************************//
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class institutoController extends Controller
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

        $tab_usuario_instituto = tab_usuario_instituto::select( 'autenticacion.tab_usuario_instituto.id', 'in_principal', 'de_instituto')
        ->join('configuracion.tab_instituto as t01', 't01.id', '=', 'autenticacion.tab_usuario_instituto.id_instituto')
        ->where('id_usuario', '=', $id)
        //->where('in_activo', '=', true)
        //->search($q, $sortBy)
        ->orderBy('de_instituto', $orderBy)
        ->paginate($perPage);

        return View::make('autenticar.usuario.instituto.lista')->with([
          'tab_usuario_instituto' => $tab_usuario_instituto,
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
        $data = array( "id_usuario" => $id);

        $instituto = tab_usuario_instituto::getListainstitutoAsignado( $id);

        $tab_instituto = tab_instituto::whereNotIn('id', $instituto)->orderBy('id','asc')
        ->get();

        

        return View::make('autenticar.usuario.instituto.nuevo')->with([
            'data'  => $data,
            'tab_instituto'  => $tab_instituto
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

                $validator= Validator::make($request->all(), tab_usuario_instituto::$validarEditar);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                $tabla = tab_usuario_instituto::find($id);
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

                $validator = Validator::make($request->all(), tab_usuario_instituto::$validarCrear);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                $tabla = new tab_usuario_instituto;
                $tabla->id_instituto = $request->id_instituto;
                $tabla->id_usuario = $request->id_usuario;
                $tabla->in_principal = false;
                $tabla->save();

                DB::commit();

                Session::flash('msg_side_overlay', 'Registro guardado con Exito!');
                return Redirect::to('/configuracion/usuario/instituto'.'/'.$tabla->id_usuario);

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
            $tabla = tab_usuario_instituto::find($id);
            $tabla->delete();
            /*$tab_proceso_usuario->in_activo = false;
            $tab_proceso_usuario->save();*/
            DB::commit();

            Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
           return Redirect::to('/configuracion/usuario/instituto'.'/'.$tabla->id_usuario);

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

            $data = tab_usuario_instituto::select( 'id_usuario')
            ->where('id', '=', $id)
            ->first();

            $tabla = tab_usuario_instituto::find( $id);
            $tabla->in_principal = false;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro despublicado con Exito!');
            return Redirect::to('/configuracion/usuario/instituto'.'/'.$tabla->id_usuario);

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

            $data = tab_usuario_instituto::select( 'id_usuario')
            ->where('id', '=', $id)
            ->first();

            $proceso = tab_usuario_instituto::where('id_usuario', '=', $data->id_usuario)->update(array('in_principal' => FALSE));

            $tabla = tab_usuario_instituto::find( $id);
            $tabla->in_principal = true;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro publicado con Exito!');
            return Redirect::to('/configuracion/usuario/instituto'.'/'.$tabla->id_usuario);

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
    }

    

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function index(Request $request)
    {
        $sortBy = 'de_instituto';
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

        $tab_instituto = tab_instituto::search($q, $sortBy)
        ->orderBy('de_instituto', $orderBy)
        ->paginate($perPage);     
        

        return View::make('configuracion.instituto.lista')->with([
          'tab_instituto' => $tab_instituto,
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
    public function save( Request $request)
    {
        DB::beginTransaction();
  
            try {

                $validator= Validator::make($request->all(), tab_instituto::$validar);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                if(empty($request->id)){
                     $tabla = new tab_instituto;
                }
                else{
                     $tabla = tab_instituto::find($request->id);
                }  

                             
                $tabla->de_instituto = $request->de_instituto; 
                $tabla->save();

                DB::commit();
                
                Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
                return Redirect::to('/configuracion/instituto');

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

            $cant_ruta         = tab_ruta::where('id_instituto','=',$id)->count();
            $cant_instituto = tab_usuario_instituto::where('id_instituto','=',$id)->count();            

            if($cant_ruta>0){
                  Session::flash('msg_side_overlay', 'El registro se encuentra asociado a historia de pacientes!');
                  return Redirect::to('/configuracion/instituto');
            }

            if($cant_instituto>0){
                  Session::flash('msg_side_overlay', 'El registro se encuenta asociado a usuarios!');
                  return Redirect::to('/configuracion/instituto');
            }


            $tabla = tab_instituto::find($id);
            $tabla->delete();
            DB::commit();

            Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
           return Redirect::to('/configuracion/instituto');

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
     
        $instituto = '';
        if(!empty($id))
            $instituto = tab_instituto::where('id','=',$id)->first();


        return View::make('configuracion.instituto.editar')->with([
            'data'  => $instituto
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function new()
    {
        return View::make('configuracion.instituto.editar')->with([
            'data'  => ''
        ]);
    }




}
