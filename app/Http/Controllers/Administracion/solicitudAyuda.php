<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//
use View;
use Validator;
use Response;
use DB;
use Session;
use Redirect;
use HelperReporte;
//*******************************//
use Illuminate\Http\Request;

use gobela\Http\Requests;
use gobela\Http\Controllers\Controller;

class solicitudAyuda extends Controller
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
    public function editarResponsabilidad( $request, $id, $ruta)
    {

        return View::make('administracion.solicitudAyuda.editarResponsabilidad')->with([
            'id' => $id,
            'ruta' => $ruta
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

                /*$validator= Validator::make($request->all(), tab_usuario::$validarEditar);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                $tab_usuario = tab_usuario::find($id);
                $tab_usuario->save();*/

                DB::commit();
                
                Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
                return Redirect::to('/proceso/ruta/lista/'.$request->solicitud);

            }catch (\Illuminate\Database\QueryException $e){

                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());

            }
  
        }else{
  
            try {

                /*$validator = Validator::make($request->all(), tab_usuario::$validarCrear);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                $tab_usuario = new tab_usuario;
                $tab_usuario->in_activo = true;
                $tab_usuario->save();*/

                HelperReporte::generarReporte($request->solicitud);

                DB::commit();

                Session::flash('msg_side_overlay', 'Registro Guardado con Exito!');
                return Redirect::to('/proceso/ruta/lista/'.$request->solicitud);

            }catch (\Illuminate\Database\QueryException $e){

                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());

            }
        }
    }
}
