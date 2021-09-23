<?php

namespace App\Http\Controllers\Autenticar;
//*******agregar esta linea******//
use App\Models\Configuracion\tab_solicitud_usuario;
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

class usuarioSolicitudController extends Controller
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
    public function lista( $id)
    {
        $data = array("id_tab_usuario" => $id);
        $arbol = tab_solicitud_usuario::ArmaTramitePrivilegio( $id);

        return View::make('autenticar.usuario.solicitud.lista')
        ->with( 'data', $data)
        ->with( 'arbol', $arbol);
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

        try {

            $validator = Validator::make($request->all(), tab_solicitud_usuario::$validarCrear);

            if ($validator->fails()){
                return Redirect::back()->withErrors( $validator)->withInput( $request->all());
            }

            //Borrar todos los registros asociados al usuario
            $tab_solicitud_usuario = tab_solicitud_usuario::where('id_tab_usuario', '=', $request->usuario)->delete();
            //Actualizar las opciones seleccionadas en tab_rol_menu
            $opcion = $request->seleccion;

            foreach ($opcion as $key => $lista){
                $tab_solicitud_usuario = new tab_solicitud_usuario;
                $tab_solicitud_usuario->id_tab_solicitud = $key;
                $tab_solicitud_usuario->id_tab_usuario = $request->usuario;
                $tab_solicitud_usuario->in_activo = true;
                $tab_solicitud_usuario->save();
            }

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
