<?php

namespace App\Http\Controllers\Autenticar;
//*******agregar esta linea******//
use App\Models\Autenticar\tab_usuario;
use App\Models\Autenticar\tab_privilegio_menu;
use App\Models\Autenticar\tab_menu;
use Auth;
use View;
use Redirect;
use Session;
use Captcha;
use Response;
use Validator;
use URL;
use DB;
use Crypt;
use Mail;
//*******************************//
use Illuminate\Http\Request;

use App\Http\Requests\loginRequest;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class autenticarController extends Controller
{
    /**
     * Create a new authentication controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\Guard  $auth
     * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
        $this->middleware('optimizar');
        $this->middleware('guest', ['except' => 'salir']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
       
        // Verificamos si hay sesión activa
        if (Auth::check()){
        // Si tenemos sesión activa mostrará la página de inicio
        return Redirect::to('/');
        }
        // Si no hay sesión activa mostramos el formulario
        //return View::make('autenticar.login.form');
        return View::make('auth.login');
    }

    /**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function salir()
	{
		// Cerramos la sesión
		Auth::logout();
		// redirect
		Session::flash('msg', 'Sesion cerrada con exito!');
		return Redirect::to('/');
    }

    /**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function validar(loginRequest $request)
	{

		if ($this->auth->attempt(['da_login' => $request->usuario, 'password' => $request->contraseña, 'in_activo' => TRUE]))
		{

            $data = tab_usuario::select('id', 'id_tab_rol', 'da_login', 'da_password', 'id_tab_empresa')
            ->where('id', '=', Auth::user()->id)
            ->where('in_activo', '=', TRUE)
            ->first();

            $credencial = tab_privilegio_menu::join('autenticacion.tab_privilegio as t01','t01.id','=','autenticacion.tab_privilegio_menu.id_tab_privilegio')
            ->join('autenticacion.tab_menu as t02','t02.id','=','t01.id_tab_menu')
            ->join('autenticacion.tab_rol_menu as t03','t03.id','=','autenticacion.tab_privilegio_menu.id_tab_rol_menu')
            ->select('de_privilegio', DB::raw("autenticacion.tab_privilegio_menu.in_estatus as in_habilitado"))
            ->where('id_tab_rol', '=', $data->id_tab_rol)->get()->toArray();
  
            Session::put('usuario', $data);
            Session::put('rol', $data->id_tab_rol);
            Session::put('arbol', tab_menu::arbol( $data->id_tab_rol));
            Session::put('empresa', $data->id_tab_empresa);
            Session::put( array('credencial' => $credencial));
            Session::put('ejercicio', date('Y'));

            //return redirect('/inicio');
            return redirect('/inicio');
            
		}else{

            return Redirect::back()->withErrors([
                'da_mensaje' => 'Las credenciales que has introducido no coinciden con nuestros registros. Intente de Nuevo.',
            ])->withInput($request->except("contraseña"));

        }

    }
}
