<?php

namespace App\Http\Controllers\Autenticar;
//*******agregar esta linea******//
use App\Models\Autenticar\tab_usuario;
use App\Models\Autenticar\tab_rol;
use App\Models\Configuracion\tab_empresa;
use View;
use Validator;
use Response;
use DB;
use Session;
use Redirect;
use Auth;
use Hash;
use Crypt;
//*******************************//
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class usuarioController extends Controller
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

        $tab_usuario = tab_usuario::select( 'autenticacion.tab_usuario.id', 'nb_usuario', 'da_email', 'da_login', 
        'autenticacion.tab_usuario.in_activo', 'de_rol', 'nb_empresa')
        ->join('autenticacion.tab_rol as t01', 't01.id', '=', 'autenticacion.tab_usuario.id_tab_rol')
        ->join('configuracion.tab_empresa as t02', 't02.id', '=', 'autenticacion.tab_usuario.id_tab_empresa')
        //->where('in_activo', '=', true)
        //->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('autenticar.usuario.lista')->with([
          'tab_usuario' => $tab_usuario,
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
        $tab_rol = tab_rol::orderby('id','ASC')
        ->get();

        $tab_empresa = tab_empresa::orderby('id','ASC')
        ->get();

        return View::make('autenticar.usuario.nuevo')->with([
            'tab_rol'  => $tab_rol,
            'tab_empresa'  => $tab_empresa
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function editar($id)
    {

        $tab_rol = tab_rol::orderby('id','ASC')
        ->get();

        $tab_empresa = tab_empresa::orderby('id','ASC')
        ->get();

        $data = tab_usuario::select( 'id', 'nb_usuario', 'da_email', 'da_login', 'da_password', 'remember_token', 
        'in_activo', 'created_at', 'updated_at', 'id_tab_rol', 
        'id_tab_empresa')
        ->where('id', '=', $id)
        ->first();

        return View::make('autenticar.usuario.editar')->with([
            'data'  => $data,
            'tab_rol'  => $tab_rol,
            'tab_empresa'  => $tab_empresa
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

                $validator= Validator::make($request->all(), tab_usuario::$validarEditar);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                $tab_usuario = tab_usuario::find($id);
                $tab_usuario->nb_usuario = $request->nombre;
                $tab_usuario->da_email = $request->correo; 
                $tab_usuario->da_login = $request->usuario;
                $tab_usuario->id_tab_rol = $request->rol; 
                $tab_usuario->id_tab_empresa = $request->empresa; 
                $tab_usuario->save();

                DB::commit();
                
                Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
                return Redirect::to('/autenticar/usuario/lista');

            }catch (\Illuminate\Database\QueryException $e){

                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());

            }
  
        }else{
  
            try {

                $validator = Validator::make($request->all(), tab_usuario::$validarCrear);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                $tab_usuario = new tab_usuario;
                $tab_usuario->nb_usuario = $request->nombre;
                $tab_usuario->da_email = $request->correo; 
                $tab_usuario->da_login = $request->usuario; 
                $tab_usuario->da_password = bcrypt(123456);
                //$tab_usuario->da_pass_recuperar = Crypt::encrypt(123456); 
                $tab_usuario->id_tab_rol = $request->rol; 
                $tab_usuario->id_tab_empresa = $request->empresa; 
                $tab_usuario->in_activo = true;
                $tab_usuario->save();

                DB::commit();

                Session::flash('msg_side_overlay', 'Registro Guardado con Exito!');
                return Redirect::to('/autenticar/usuario/lista');

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

        $tabla = tab_usuario::find( $id);
        $tabla->in_activo = false;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro deshabilitado con Exito!');
        return Redirect::to('/autenticar/usuario/lista');

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

        $tabla = tab_usuario::find( $id);
        $tabla->in_activo = true;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro habilitado con Exito!');
        return Redirect::to('/autenticar/usuario/lista');

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
    }
}
