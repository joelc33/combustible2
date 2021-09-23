<?php

namespace App\Http\Controllers\Telemedicina;
//*******agregar esta linea******//
use App\Models\Telemedicina\tab_persona;
use App\Models\Configuracion\tab_municipio;
use App\Models\Configuracion\tab_sexo;
use App\Models\Configuracion\tab_nacionalidad;
use View;
use Validator;
use Response;
use DB;
use Session;
use Redirect;
use Carbon\Carbon;
//*******************************//
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class persona extends Controller
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

        $tab_persona = tab_persona::select( 'id', 'cedula', 'nombres', 'apellidos', 'telefono', 'direccion')
        //->where('in_activo', '=', true)
        ->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('telemedicina.persona.lista')->with([
          'tab_persona' => $tab_persona,
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
        
        $tab_sexo = tab_sexo::select( 'id','de_sexo')
        ->orderby('id','ASC')
        ->get();        
        
        $tab_municipio = tab_municipio::select( 'id','de_municipio')
        ->orderby('id','ASC')
        ->get();  

        $tab_nacionalidad = tab_nacionalidad::select( 'id','de_nacionalidad')
        ->orderby('id','ASC')
        ->get();        

        return View::make('telemedicina.persona.nuevo')->with([
            'data'             => $data,
            'tab_municipio'     => $tab_municipio,
            'tab_sexo'          => $tab_sexo,
            'tab_nacionalidad' => $tab_nacionalidad 
        ]);
    }

        /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function editar($id)
    {
        
        $tab_municipio = tab_municipio::select( 'id','de_municipio')
        ->orderby('id','ASC')
        ->get();
        
        $tab_sexo = tab_sexo::select( 'id','de_sexo')
        ->orderby('id','ASC')
        ->get();        
        
        $data = tab_persona::select( 'id', 'cedula', 'nombres', 'apellidos', 'id_sexo', 'telefono', 'direccion', 'correo', 'id_municipio', 'fe_nacimiento')
        ->where('id', '=', $id)
        ->first();

        return View::make('telemedicina.persona.editar')->with([
            'data'  => $data,
            'tab_municipio'  => $tab_municipio,
            'tab_sexo'  => $tab_sexo
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

          $validador = Validator::make( $request->all(), tab_persona::$validarEditar);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {
            $fe_nacimiento = Carbon::parse($request->fe_nacimiento)->format('Y-m-d');
            $tabla = tab_persona::find($id);
            $tabla->cedula = $request->cedula;
            $tabla->nombres = $request->nombres;
            $tabla->apellidos = $request->apellido;
            $tabla->id_sexo = $request->sexo;
            $tabla->telefono = $request->telefono;
            $tabla->direccion = $request->direccion;
            $tabla->fe_nacimiento = $fe_nacimiento;
            $tabla->correo = $request->correos;
            $tabla->id_municipio = $request->municipio;
            $tabla->id_nacionalidad = $request->nacionalidad;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
            return Redirect::to('/proceso/consulta/listapaciente');

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }else{

          $validador = Validator::make( $request->all(), tab_persona::$validarCrear);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {
              
            if($request->persona){
            $tabla = tab_persona::find($request->persona);    
            }else{  
            $tabla = new tab_persona;
            }
            $fe_nacimiento = Carbon::parse($request->fe_nacimiento)->format('Y-m-d');
            $tabla->cedula = $request->cedula;
            $tabla->nombres = $request->nombres;
            $tabla->apellidos = $request->apellido;
            $tabla->id_sexo = $request->sexo;
            $tabla->telefono = $request->telefono;
            $tabla->direccion = $request->direccion;
            $tabla->fe_nacimiento = $fe_nacimiento;
            $tabla->correo = $request->correos;
            $tabla->id_municipio = $request->municipio;
            $tabla->id_nacionalidad = $request->nacionalidad;
            $tabla->save();

            DB::commit();

            if($request->persona){
            Session::flash('msg_side_overlay', 'Registro Editado con Exito!');    
            }else{  
            Session::flash('msg_side_overlay', 'Registro creado con Exito!');
            }            
            
            return Redirect::to('/proceso/consulta/listapaciente');

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

        $tabla = tab_banco::find( $request->get("id"));
        $tabla->delete();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
        return Redirect::to('/administracion/banco/lista');

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

        $tabla = tab_banco::find( $id);
        $tabla->in_activo = false;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro despublicado con Exito!');
        return Redirect::to('/administracion/banco/lista');

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

        $tabla = tab_banco::find( $id);
        $tabla->in_activo = true;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro publicado con Exito!');
        return Redirect::to('/administracion/banco/lista');

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }
    
    public function buscar(Request $request)
    {

      if (tab_persona::where('cedula', '=', $request->cedula)
      ->exists()) {

        $tab_persona = tab_persona::select( 'id', 'cedula', 'nombres', 'apellidos', 'id_sexo', 'telefono', 'direccion','correo','id_municipio','id_nacionalidad',DB::raw("to_char(fe_nacimiento,'dd-mm-yyyy')  as fe_nacimiento"))
        ->where('cedula', '=', $request->cedula)
        ->first()->toArray();

        $response['success']  = 'true';
        $response['data']  = $tab_persona;

        return Response::json($response, 200);

      }else{

        $response['success']  = 'false';
        $response['data']  = '';
        $response['msg']  = '';

        return Response::json($response, 200);

      }

    }    


    
}
