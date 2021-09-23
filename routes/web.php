<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});
//*Modulos de Autenticacion*/
Route::group(['namespace' => 'Autenticar'], function(){
	/*Llamadas al controlador autenticar*/
	Route::group(['prefix' => '/'], function(){
		Route::get('', 'autenticarController@login'); // Mostrar login
        Route::post('autenticar', 'autenticarController@validar'); // Verificar datos
        Route::get('autenticar', 'autenticarController@salir'); // Finalizar sesión
        Route::get('recuperar', 'passwordController@recuperar'); // Recuperar
        Route::post('recuperar/enviar', 'passwordController@enviar'); // Recuperar Password
        Route::get('recuperar/{confirmacion}', 'passwordController@confirmar');
        Route::post('recuperar/cambiar', 'passwordController@guardar');
	});
});

Route::group(['namespace' => 'Combustible'], function(){
	/*Llamadas al controlador autenticar*/
	Route::group(['prefix' => 'combustible'], function(){
		Route::get('lista', 'vehiculoController@listaVehiculo'); 
		Route::get('registroVehiculo', 'vehiculoController@nuevo'); 
		Route::post('vehiculo/guardar', 'vehiculoController@guardar'); 
		Route::post('vehiculo/buscar', 'vehiculoController@buscar'); 
		Route::post('vehiculo/buscarPersona', 'vehiculoController@buscarPersona'); 
		
		Route::get('despacho/registrar', 'despachoController@registrar'); 
		Route::get('despacho/nuevo', 'despachoController@nuevo'); 
		Route::post('despacho/guardar', 'despachoController@guardar'); 
		Route::get('despacho/listaGerencia/{id}', 'despachoController@listaGerencia'); 
		Route::get('despacho/listaGerencia', 'despachoController@listaGerencia'); 
		Route::get('despacho/agregarGerencia/{id}', 'despachoController@agregarGerencia'); 
		Route::post('despacho/guardarDespachoGerencia', 'despachoController@guardarDespachoGerencia'); 	
		Route::get('despacho/detalleVehiculo/{id_gerencia}/{id}', 'despachoController@detalleVehiculo'); 
		Route::post('despacho/eliminar', 'despachoController@eliminar'); 
		Route::post('despacho/eliminarGerencia', 'despachoController@eliminarGerencia'); 	
		
		Route::get('despacho/apoyo', 'despachoController@apoyo'); 	
		Route::get('despacho/listaApoyo/{id}', 'despachoController@listaApoyo'); 
		Route::get('despacho/agregarApoyo/{id}', 'despachoController@agregarApoyo'); 
		Route::post('despacho/guardarApoyo', 'despachoController@guardarApoyo'); 	
    });
});

Route::group(['namespace' => 'Panel'], function(){
    Route::get('inicio', 'panelController@inicio');
	Route::get('inicio/notificacion', 'panelController@notificacion');
	Route::get('ejercicio', 'panelController@ejercicio');
	Route::post('ejercicio', 'panelController@ejercicioInicio');
});

Route::group(['namespace' => 'Reporte'], function(){
	//*Modulo de Proceso*/
	Route::group(['prefix' => 'reporte'], function(){
		Route::get('morbilidad', 'reporteController@morbilidad');
	    Route::post('imprimirmorbilidad', 'reporteController@imprimirmorbilidad');
	});
});

Route::group(['namespace' => 'Telemedicina'], function(){
  	Route::get('consulta/listapaciente', 'consultaController@listapaciente');
    Route::get('consulta/informe/{id}', 'consultaController@informe');
    Route::get('consulta/historicoInforme/{id}', 'consultaController@historicoInforme');
	Route::post('consulta/registrarInforme', 'consultaController@guardarInforme');
        
	Route::group(['prefix' => 'telemedicina'], function(){
	Route::get('persona/lista', 'persona@lista');
        Route::get('persona/nuevo', 'persona@nuevo');
        Route::get('persona/editar/{id}', 'persona@editar');
        Route::post('persona/buscar', 'persona@buscar');
        Route::post('persona/guardar', 'persona@guardar');
        Route::post('persona/guardar/{id}', 'persona@guardar');
        Route::post('registrarConsulta', 'consultaController@guardar');
        Route::post('registrarConsulta/{id}', 'consultaController@guardar');        
	});        
        
});

//*Modulos de Tablas de Configuracion*/
Route::group(['namespace' => 'Configuracion'], function(){
    //*Modulo de Configuracion*/
    Route::group(['prefix' => 'configuracion'], function(){
			//*Modulo de Proceso*/
			Route::get('proceso/lista', 'procesoController@lista');
			Route::get('proceso/nuevo', 'procesoController@nuevo');
			Route::get('proceso/editar/{id}', 'procesoController@editar');
			Route::post('proceso/guardar', 'procesoController@guardar');
			Route::post('proceso/guardar/{id}', 'procesoController@guardar');
			Route::get('proceso/deshabilitar/{id}', 'procesoController@deshabilitar');
			Route::get('proceso/habilitar/{id}', 'procesoController@habilitar');
			Route::post('proceso/eliminar', 'procesoController@eliminar');
            //*Modulo de Solicitud*/
			Route::get('solicitud/lista', 'solicitudController@lista');
			Route::get('solicitud/nuevo', 'solicitudController@nuevo');
			Route::get('solicitud/editar/{id}', 'solicitudController@editar');
			Route::post('solicitud/guardar', 'solicitudController@guardar');
			Route::post('solicitud/guardar/{id}', 'solicitudController@guardar');
			Route::get('solicitud/deshabilitar/{id}', 'solicitudController@deshabilitar');
			Route::get('solicitud/habilitar/{id}', 'solicitudController@habilitar');
			Route::post('solicitud/eliminar', 'solicitudController@eliminar');
			//*Modulo de Ruta*/
			Route::get('ruta/lista/{id}', 'rutaController@lista');
			Route::get('ruta/nuevo/{id}', 'rutaController@nuevo');
			Route::get('ruta/editar/{id}', 'rutaController@editar');
			Route::post('ruta/guardar', 'rutaController@guardar');
			Route::post('ruta/guardar/{id}', 'rutaController@guardar');
			Route::post('ruta/eliminar', 'rutaController@eliminar');
			//*Modulo de Ejercicio Fiscal*/
			Route::get('ejercicio/lista', 'ejercicioFiscalController@lista');
			Route::get('ejercicio/nuevo', 'ejercicioFiscalController@nuevo');
			Route::get('ejercicio/editar/{id}', 'ejercicioFiscalController@editar');
			Route::post('ejercicio/guardar', 'ejercicioFiscalController@guardar');
			Route::post('ejercicio/guardar/{id}', 'ejercicioFiscalController@guardar');
			Route::get('ejercicio/deshabilitar/{id}', 'ejercicioFiscalController@deshabilitar');
			Route::get('ejercicio/habilitar/{id}', 'ejercicioFiscalController@habilitar');


			Route::get('usuario/especialidad/{id}', 'especialidadController@lista');			
			Route::get('usuario/especialidad/nuevo/{id}', 'especialidadController@nuevo');	
			Route::post('usuario/especialidad/guardar', 'especialidadController@guardar');
			Route::get('usuario/especialidad/deshabilitar/{id}', 'especialidadController@deshabilitar');
			Route::get('usuario/especialidad/habilitar/{id}', 'especialidadController@habilitar');
			Route::post('usuario/especialidad/eliminar/{id}', 'especialidadController@eliminar');


			Route::get('especialidad', 'especialidadController@index');
			Route::get('especialidad/editar/{id}', 'especialidadController@editar');
			Route::get('especialidad/nuevo', 'especialidadController@new');
			Route::post('especialidad/guardar', 'especialidadController@save');
			Route::post('especialidad/eliminar/{id}', 'especialidadController@delete');


			Route::get('instituto', 'institutoController@index');
			Route::get('instituto/editar/{id}', 'institutoController@editar');
			Route::get('instituto/nuevo', 'institutoController@new');
			Route::post('instituto/guardar', 'institutoController@save');
			Route::post('instituto/eliminar/{id}', 'institutoController@delete');
			

			Route::get('usuario/instituto/{id}', 'institutoController@lista');			
			Route::get('usuario/instituto/nuevo/{id}', 'institutoController@nuevo');	
			Route::post('usuario/instituto/guardar', 'institutoController@guardar');
			Route::get('usuario/instituto/deshabilitar/{id}', 'institutoController@deshabilitar');
			Route::get('usuario/instituto/habilitar/{id}', 'institutoController@habilitar');
			Route::post('usuario/instituto/eliminar/{id}', 'institutoController@eliminar');


			Route::get('gerencia/lista', 'gerenciaController@lista');
			Route::get('gerencia/deshabilitar/{id}', 'gerenciaController@deshabilitar');
			Route::get('gerencia/habilitar/{id}', 'gerenciaController@habilitar');
			Route::get('gerencia/editar/{id}', 'gerenciaController@editar');
			Route::post('gerencia/guardar', 'gerenciaController@guardar');
			Route::get('gerencia/nuevo', 'gerenciaController@nuevo');



    });
});
//*Modulos de Tablas de Proceso*/
Route::group(['namespace' => 'Proceso'], function(){
	//*Modulo de Proceso*/
	Route::group(['prefix' => 'proceso'], function(){
		//*Controlador de solicitud de Modelo tab_solicitud *//
		Route::get('solicitud/lista', 'solicitudController@lista');
        Route::get('consulta/listapaciente', 'consultaController@listapaciente');
        Route::get('consulta/informe/{id}', 'consultaController@informe');
        Route::get('consulta/historicoInforme/{id}', 'consultaController@historicoInforme');
		Route::get('solicitud/pendiente', 'solicitudController@pendiente');
		Route::get('solicitud/completo', 'solicitudController@completo');
		Route::get('solicitud/todo', 'solicitudController@todo');
		Route::post('solicitud/estatus', 'solicitudController@estatus');
		Route::get('solicitud/estado', 'solicitudController@estado');
		Route::post('solicitud/procesar', 'solicitudController@procesar');
		Route::post('solicitud/storeLista', 'solicitudController@storeLista');
		Route::post('solicitud/storeLista/completo', 'solicitudController@storeListaCompleto');
		Route::post('solicitud/storeLista/todo', 'solicitudController@storeListaTodo');
		Route::get('solicitud/nuevo', 'solicitudController@nuevo');
		Route::get('solicitud/nuevo/{id}', 'solicitudController@nuevo');
		Route::get('solicitud/editar/{id}', 'solicitudController@editar');
		Route::post('solicitud/guardar', 'solicitudController@guardar');
		Route::post('consulta/registrarInforme', 'consultaController@guardarInforme');
		Route::post('solicitud/guardar/{id}', 'solicitudController@guardar');
		Route::post('solicitud/eliminar', 'solicitudController@eliminar');
		Route::post('solicitud/habilitar', 'solicitudController@habilitar');
		Route::post('solicitud/avanzar', 'solicitudController@avanzar');
		Route::post('solicitud/procesar/{id}', 'rutaController@procesar');
		Route::get('solicitud/identificador', 'solicitudController@identificador');
		Route::post('solicitud/detalle', 'solicitudController@detalle');
		Route::post('solicitud/detalle/ver', 'solicitudController@detalleVer');
		Route::get('ruta/lista/{id}', 'rutaController@lista');
		Route::get('ruta/datos/{id}', 'rutaController@datos');
		Route::get('ruta/enviar/{id}', 'rutaController@enviar');
		Route::post('ruta/lista', 'rutaController@lista');
		Route::post('ruta/lista/ver', 'rutaController@listaVer');
		Route::get('ruta/lista/ver/{id}', 'rutaController@listaVer');
		Route::post('ruta/storeLista', 'rutaController@storeLista');
		Route::post('ruta/datos', 'rutaController@datos');
		Route::post('documento/lista', 'documentoController@lista');
		Route::get('documento/lista/{id}', 'documentoController@lista');
		Route::post('documento/lista/ver', 'documentoController@listaVer');
		Route::get('documento/lista/ver/{id}', 'documentoController@listaVer');
		Route::post('documento/storeLista', 'documentoController@storeLista');
		Route::post('documento/nuevo', 'documentoController@nuevo');
		Route::get('documento/nuevo/{id}', 'documentoController@nuevo');
		Route::get('documento/editar/{id}', 'documentoController@editar');
		Route::post('documento/guardar', 'documentoController@guardar');
		Route::post('documento/guardar/{id}', 'documentoController@guardar');
		Route::post('documento/eliminar/{id}', 'documentoController@eliminar');
		Route::get('documento/ver/{id}/{t}', 'documentoController@verAnexo');
		Route::get('reporte/ver/{id}/{t}', 'documentoController@verReporte');

		Route::get('documento/enviar/{id}', 'documentoController@mail');
		Route::get('ruta/expediente/{id}', 'rutaController@expediente');
		Route::get('ruta/referir/{id}', 'rutaController@referir');
		Route::post('ruta/registrarReferido', 'rutaController@guardarReferido');
		Route::get('ruta/listaReferido/', 'rutaController@listaReferido');
		Route::get('ruta/procesarReferido/{id}', 'rutaController@procesarReferido');
	});
});
//*Modulos de Autenticacion*/
Route::group(['namespace' => 'Autenticar'], function(){
	//*Modulo de usuarios*/
	Route::group(['prefix' => 'autenticar'], function(){
		//*Controlador de usuario de Modelo tab_usuario *//
		Route::get('usuario/lista', 'usuarioController@lista');
		Route::post('usuario/storeLista', 'usuarioController@storeLista');
		Route::get('usuario/nuevo', 'usuarioController@nuevo');
		Route::get('usuario/editar/{id}', 'usuarioController@editar');
		Route::post('usuario/guardar', 'usuarioController@guardar');
		Route::post('usuario/guardar/{id}', 'usuarioController@guardar');
		Route::post('usuario/eliminar', 'usuarioController@eliminar');
		Route::get('usuario/deshabilitar/{id}', 'usuarioController@deshabilitar');
		Route::get('usuario/habilitar/{id}', 'usuarioController@habilitar');
		Route::get('usuario/proceso/{id}', 'procesoController@lista');
		Route::get('usuario/proceso/nuevo/{id}', 'procesoController@nuevo');	
		Route::get('usuario/solicitud/{id}', 'usuarioSolicitudController@lista');
		Route::get('usuario/rol', 'usuarioController@rol');
		Route::get('usuario/empresa', 'usuarioController@empresa');
		Route::get('usuario/contrasena', 'usuarioController@contrasena');
		Route::post('usuario/contrasena', 'usuarioController@guardarContrasena');
		Route::get('usuario/clave/{id}', 'usuarioController@clave');
		Route::post('usuario/clave', 'usuarioController@guardarClave');
		//*Controlador de proceso de Modelo tab_proceso_usuario *//
		Route::post('usuario/proceso', 'procesoController@proceso');
		Route::post('usuario/proceso/lista', 'procesoController@lista');
		Route::post('usuario/proceso/storeLista', 'procesoController@storeLista');
		Route::get('usuario/proceso/nuevo', 'procesoController@nuevo');
		Route::get('usuario/proceso/editar/{id}', 'procesoController@editar');
		Route::post('usuario/proceso/guardar', 'procesoController@guardar');
		Route::post('usuario/proceso/guardar/{id}', 'procesoController@guardar');
		Route::post('usuario/proceso/eliminar/{id}', 'procesoController@eliminar');
		Route::get('usuario/proceso/deshabilitar/{id}', 'procesoController@deshabilitar');
		Route::get('usuario/proceso/habilitar/{id}', 'procesoController@habilitar');
		Route::post('usuario/proceso/habilitar', 'procesoController@habilitar');
		//*Controlador de usuarioSolicitud de Modelo tab_solicitud_usuario *//
		Route::get('usuario/solicitud/lista/{id}', 'usuarioSolicitudController@lista');
		Route::post('usuario/solicitud/storeLista', 'usuarioSolicitudController@storeLista');
		Route::get('usuario/solicitud/nuevo', 'usuarioSolicitudController@nuevo');
		Route::get('usuario/solicitud/editar/{id}', 'usuarioSolicitudController@editar');
		Route::post('usuario/solicitud/guardar', 'usuarioSolicitudController@guardar');
		Route::post('usuario/solicitud/guardar/{id}', 'usuarioSolicitudController@guardar');
		Route::post('usuario/solicitud/eliminar', 'usuarioSolicitudController@eliminar');
		Route::post('usuario/solicitud/habilitar', 'usuarioSolicitudController@habilitar');
		//*Controlador de rol de Modelo tab_rol *//
		Route::get('rol/lista', 'rolController@lista');
		Route::post('rol/storeLista', 'rolController@storeLista');
		Route::get('rol/nuevo', 'rolController@nuevo');
		Route::get('rol/editar/{id}', 'rolController@editar');
		Route::post('rol/guardar', 'rolController@guardar');
		Route::post('rol/guardar/{id}', 'rolController@guardar');
		Route::post('rol/eliminar', 'rolController@eliminar');
		Route::get('rol/deshabilitar/{id}', 'rolController@deshabilitar');
		Route::get('rol/habilitar/{id}', 'rolController@habilitar');
		Route::post('rol/opcion', 'rolController@opcion');
		Route::post('rol/opcion/storeLista', 'rolController@opcionStoreLista');
		Route::post('rol/opcion/si', 'rolController@opcionSi');
		Route::post('rol/opcion/no', 'rolController@opcionNo');
		Route::get('rol/bandeja', 'rolController@bandeja');
                Route::get('rol/menu/{id}', 'rolMenuController@lista');
                Route::post('rol/menu/guardar', 'rolMenuController@guardar');                
		//*Controlador de sesion de Modelo tab_sessions *//
		Route::get('sesion/lista', 'sesionController@lista');
		Route::post('sesion/storeLista', 'sesionController@storeLista');
		Route::get('sesion/nuevo', 'sesionController@nuevo');
		Route::get('sesion/editar/{id}', 'sesionController@editar');
		Route::post('sesion/guardar', 'sesionController@guardar');
		Route::post('sesion/guardar/{id}', 'sesionController@guardar');
		Route::post('sesion/eliminar', 'sesionController@eliminar');
		Route::post('sesion/habilitar', 'sesionController@habilitar');
	});
});

Route::group(['namespace' => 'Administracion'], function(){
	
	Route::group(['prefix' => 'solicitud'], function(){
		Route::post('solicitudAyuda/guardar', 'solicitudAyuda@guardar');
		Route::post('compra/requisicion/detalle/guardar', 'compraRequisicion@guardarDetalle');
		Route::post('compra/requisicion/detalle/{id}/borrar', 'compraRequisicion@borrarDetalle');
		Route::post('compra/requisicion/guardar', 'compraRequisicion@guardar');
		Route::post('compra/contrato/guardar', 'compraContrato@guardar');
		Route::post('compra/contrato/detalle/guardar', 'compraContrato@guardarDetalle');
		Route::post('compra/contrato/detalle/{id}/borrar', 'compraContrato@borrarDetalle');
		Route::post('compra/partida/guardar', 'compraPresupuesto@guardar');
		Route::post('compra/partida/detalle/guardar', 'compraPresupuesto@guardarDetalle');
		Route::post('compra/partida/detalle/editar', 'compraCertificado@editarDetalle');
		Route::post('compra/partida/detalle/{id}/borrar', 'compraPresupuesto@borrarDetalle');
		Route::get('compra/partida/catalogo', 'compraPresupuesto@catalogo');
		Route::post('compra/partida/proyac', 'compraPresupuesto@proyectoAc');
		Route::post('compra/partida/proyacae', 'compraPresupuesto@proyectoAcAe');
		Route::post('compra/partida/vigente', 'compraPresupuesto@partida');
		Route::post('compra/orden/guardar', 'compraOrdenCompra@guardar');
		Route::post('compra/presupuesto/comprometer', 'compraCertificado@comprometer');
		Route::post('compra/presupuesto/descomprometer', 'compraCertificado@descomprometer');
		Route::post('compra/contabilidad/guardar', 'compraContabilidad@guardar');
		Route::post('compra/contabilidad/detalle/guardar', 'compraContabilidad@guardarDetalle');
		Route::post('compra/contabilidad/retenciones', 'compraContabilidad@retenciones');
		Route::post('compra/contabilidad/detalle/{id}/borrar', 'compraContabilidad@borrarDetalle');
	});
});

Route::group(['namespace' => 'Administracion'], function(){
	
	Route::group(['prefix' => 'administracion'], function(){
            
        /*Modulo Ejecutor*/            
        Route::get('ejecutor/lista', 'ejecutorController@lista');
        Route::get('ejecutor/nuevo', 'ejecutorController@nuevo');
        Route::get('ejecutor/editar/{id}', 'ejecutorController@editar');
        Route::post('ejecutor/guardar', 'ejecutorController@guardar');
        Route::post('ejecutor/guardar/{id}', 'ejecutorController@guardar');
        Route::get('ejecutor/deshabilitar/{id}', 'ejecutorController@deshabilitar');
        Route::get('ejecutor/habilitar/{id}', 'ejecutorController@habilitar');
        Route::post('ejecutor/eliminar', 'ejecutorController@eliminar');    
        
        /*Modulo Tipo Presupuesto*/
        Route::get('tipoPresupuesto/lista', 'tipoPresupuestoController@lista');
        Route::get('tipoPresupuesto/nuevo', 'tipoPresupuestoController@nuevo');
        Route::get('tipoPresupuesto/editar/{id}', 'tipoPresupuestoController@editar');
        Route::post('tipoPresupuesto/guardar', 'tipoPresupuestoController@guardar');
        Route::post('tipoPresupuesto/guardar/{id}', 'tipoPresupuestoController@guardar');
        Route::get('tipoPresupuesto/deshabilitar/{id}', 'tipoPresupuestoController@deshabilitar');
        Route::get('tipoPresupuesto/habilitar/{id}', 'tipoPresupuestoController@habilitar');
        Route::post('tipoPresupuesto/eliminar', 'tipoPresupuestoController@eliminar');      
        
        
        /*Modulo sector Presupuesto*/
        Route::get('sectorPresupuesto/lista', 'sectorPresupuestoController@lista');
        Route::get('sectorPresupuesto/nuevo', 'sectorPresupuestoController@nuevo');
        Route::get('sectorPresupuesto/editar/{id}', 'sectorPresupuestoController@editar');
        Route::post('sectorPresupuesto/guardar', 'sectorPresupuestoController@guardar');
        Route::post('sectorPresupuesto/guardar/{id}', 'sectorPresupuestoController@guardar');
        Route::get('sectorPresupuesto/deshabilitar/{id}', 'sectorPresupuestoController@deshabilitar');
        Route::get('sectorPresupuesto/habilitar/{id}', 'sectorPresupuestoController@habilitar');
        Route::post('sectorPresupuesto/eliminar', 'sectorPresupuestoController@eliminar');   
        

        /*Modulo tipo Ingreso*/
        Route::get('tipoIngreso/lista', 'tipoIngresoController@lista');
        Route::get('tipoIngreso/nuevo', 'tipoIngresoController@nuevo');
        Route::get('tipoIngreso/editar/{id}', 'tipoIngresoController@editar');
        Route::post('tipoIngreso/guardar', 'tipoIngresoController@guardar');
        Route::post('tipoIngreso/guardar/{id}', 'tipoIngresoController@guardar');
        Route::get('tipoIngreso/deshabilitar/{id}', 'tipoIngresoController@deshabilitar');
        Route::get('tipoIngreso/habilitar/{id}', 'tipoIngresoController@habilitar');
        Route::post('tipoIngreso/eliminar', 'tipoIngresoController@eliminar');        
        
        
        /*Modulo ambito*/
        Route::get('ambito/lista', 'ambitoController@lista');
        Route::get('ambito/nuevo', 'ambitoController@nuevo');
        Route::get('ambito/editar/{id}', 'ambitoController@editar');
        Route::post('ambito/guardar', 'ambitoController@guardar');
        Route::post('ambito/guardar/{id}', 'ambitoController@guardar');
        Route::get('ambito/deshabilitar/{id}', 'ambitoController@deshabilitar');
        Route::get('ambito/habilitar/{id}', 'ambitoController@habilitar');
        Route::post('ambito/eliminar', 'ambitoController@eliminar'); 
        
        /*Modulo aplicacion*/
        Route::get('aplicacion/lista', 'aplicacionController@lista');
        Route::get('aplicacion/nuevo', 'aplicacionController@nuevo');
        Route::get('aplicacion/editar/{id}', 'aplicacionController@editar');
        Route::post('aplicacion/guardar', 'aplicacionController@guardar');
        Route::post('aplicacion/guardar/{id}', 'aplicacionController@guardar');
        Route::get('aplicacion/deshabilitar/{id}', 'aplicacionController@deshabilitar');
        Route::get('aplicacion/habilitar/{id}', 'aplicacionController@habilitar');
        Route::post('aplicacion/eliminar', 'aplicacionController@eliminar');         
        
        /*Modulo tipo Partida*/
        Route::get('tipoPartida/lista', 'tipoPartidaController@lista');
        Route::get('tipoPartida/nuevo', 'tipoPartidaController@nuevo');
        Route::get('tipoPartida/editar/{id}', 'tipoPartidaController@editar');
        Route::post('tipoPartida/guardar', 'tipoPartidaController@guardar');
        Route::post('tipoPartida/guardar/{id}', 'tipoPartidaController@guardar');
        Route::get('tipoPartida/deshabilitar/{id}', 'tipoPartidaController@deshabilitar');
        Route::get('tipoPartida/habilitar/{id}', 'tipoPartidaController@habilitar');
        Route::post('tipoPartida/eliminar', 'tipoPartidaController@eliminar');           
        
        /*Modulo catalogo Partida*/
        Route::get('catalogoPartida/lista', 'catalogoPartidaController@lista');
        Route::get('catalogoPartida/nuevo', 'catalogoPartidaController@nuevo');
        Route::get('catalogoPartida/editar/{id}', 'catalogoPartidaController@editar');
        Route::post('catalogoPartida/guardar', 'catalogoPartidaController@guardar');
        Route::post('catalogoPartida/guardar/{id}', 'catalogoPartidaController@guardar');
        Route::get('catalogoPartida/deshabilitar/{id}', 'catalogoPartidaController@deshabilitar');
        Route::get('catalogoPartida/habilitar/{id}', 'catalogoPartidaController@habilitar');
        Route::post('catalogoPartida/eliminar', 'catalogoPartidaController@eliminar');        
        
        /*Modulo Partida Ingreso*/
        Route::get('partidaIngreso/lista', 'partidaIngresoController@lista');
        Route::get('partidaIngreso/nuevo', 'partidaIngresoController@nuevo');
        Route::get('partidaIngreso/editar/{id}', 'partidaIngresoController@editar');
        Route::post('partidaIngreso/guardar', 'partidaIngresoController@guardar');
        Route::post('partidaIngreso/guardar/{id}', 'partidaIngresoController@guardar');
        Route::get('partidaIngreso/deshabilitar/{id}', 'partidaIngresoController@deshabilitar');
        Route::get('partidaIngreso/habilitar/{id}', 'partidaIngresoController@habilitar');
        Route::post('partidaIngreso/eliminar', 'partidaIngresoController@eliminar');  
        
        /*Modulo Anexo Contable*/
        Route::get('anexoContable/lista', 'anexoContableController@lista');
        Route::get('anexoContable/nuevo', 'anexoContableController@nuevo');
        Route::get('anexoContable/editar/{id}', 'anexoContableController@editar');
        Route::post('anexoContable/guardar', 'anexoContableController@guardar');
        Route::post('anexoContable/guardar/{id}', 'anexoContableController@guardar');
        Route::get('anexoContable/deshabilitar/{id}', 'anexoContableController@deshabilitar');
        Route::get('anexoContable/habilitar/{id}', 'anexoContableController@habilitar');
        Route::post('anexoContable/eliminar', 'anexoContableController@eliminar');     
        
        /*Modulo Cuenta Contable*/
        Route::get('cuentaContable/lista', 'cuentaContableController@lista');
        Route::get('cuentaContable/nuevo', 'cuentaContableController@nuevo');
        Route::get('cuentaContable/editar/{id}', 'cuentaContableController@editar');
        Route::post('cuentaContable/guardar', 'cuentaContableController@guardar');
        Route::post('cuentaContable/guardar/{id}', 'cuentaContableController@guardar');
        Route::get('cuentaContable/deshabilitar/{id}', 'cuentaContableController@deshabilitar');
        Route::get('cuentaContable/habilitar/{id}', 'cuentaContableController@habilitar');
        Route::post('cuentaContable/eliminar', 'cuentaContableController@eliminar');    
        
        /*Modulo Proveedor*/
        Route::get('proveedor/lista', 'proveedorController@lista');
        Route::get('proveedor/nuevo', 'proveedorController@nuevo');
        Route::get('proveedor/editar/{id}', 'proveedorController@editar');
        Route::post('proveedor/guardar', 'proveedorController@guardar');
        Route::post('proveedor/guardar/ramo', 'proveedorController@guardarRamo');
        Route::post('proveedor/guardar/{id}', 'proveedorController@guardar');
        Route::get('proveedor/deshabilitar/{id}', 'proveedorController@deshabilitar');
        Route::get('proveedor/habilitar/{id}', 'proveedorController@habilitar');
        Route::post('proveedor/eliminar', 'proveedorController@eliminar');
        Route::post('proveedor/eliminar/ramo', 'proveedorController@eliminarRamo');   
		Route::post('proveedor/municipio', 'proveedorController@municipio');
		Route::post('proveedor/buscar', 'proveedorController@buscar'); 
            Route::post('proveedor/retencion', 'proveedorController@retencion'); 
        Route::post('proveedor/guardarRetencion', 'proveedorController@guardarRetencion'); 
        Route::post('proveedor/eliminar/retencion', 'proveedorController@eliminarRetencion');
        
        /*Modulo formular Presupuesto*/
        Route::get('formularPresupuesto/lista', 'formularPresupuestoController@lista');
        Route::get('formularPresupuesto/nuevo', 'formularPresupuestoController@nuevo');
        Route::get('formularPresupuesto/editar/{id}', 'formularPresupuestoController@editar');
        Route::post('formularPresupuesto/guardar', 'formularPresupuestoController@guardar');
        Route::post('formularPresupuesto/guardar/{id}', 'formularPresupuestoController@guardar');
        Route::get('formularPresupuesto/deshabilitar/{id}', 'formularPresupuestoController@deshabilitar');
        Route::get('formularPresupuesto/habilitar/{id}', 'formularPresupuestoController@habilitar');
        Route::post('formularPresupuesto/eliminar', 'formularPresupuestoController@eliminar');
        Route::post('formularPresupuesto/generar', 'formularPresupuestoController@generar');
        Route::get('formularPresupuesto/accionEspecifica/lista/{id}', 'formularPresupuestoController@accionEspecificaLista');
        Route::get('formularPresupuesto/accionEspecifica/nuevo/{id}', 'formularPresupuestoController@accionEspecificaNuevo');
        Route::get('formularPresupuesto/accionEspecifica/editar/{id}', 'formularPresupuestoController@accionEspecificaEditar');
        Route::post('formularPresupuesto/accionEspecifica/guardar', 'formularPresupuestoController@accionEspecificaGuardar');
        Route::post('formularPresupuesto/accionEspecifica/guardar/{id}', 'formularPresupuestoController@accionEspecificaGuardar');
        Route::get('formularPresupuesto/accionEspecifica/deshabilitar/{id}', 'formularPresupuestoController@accionEspecificaDeshabilitar');
        Route::get('formularPresupuesto/accionEspecifica/habilitar/{id}', 'formularPresupuestoController@accionEspecificaHabilitar');
        Route::post('formularPresupuesto/accionEspecifica/eliminar', 'formularPresupuestoController@accionEspecificaEliminar');        
        Route::get('formularPresupuesto/partida/lista/{id}', 'formularPresupuestoController@partidaLista');
        Route::get('formularPresupuesto/partida/nuevo/{id}', 'formularPresupuestoController@partidaNuevo');
        Route::get('formularPresupuesto/partida/editar/{id}', 'formularPresupuestoController@partidaEditar');
        Route::post('formularPresupuesto/partida/guardar', 'formularPresupuestoController@partidaGuardar');
        Route::post('formularPresupuesto/partida/guardar/{id}', 'formularPresupuestoController@partidaGuardar');
        Route::get('formularPresupuesto/partida/deshabilitar/{id}', 'formularPresupuestoController@partidaDeshabilitar');
        Route::get('formularPresupuesto/partida/habilitar/{id}', 'formularPresupuestoController@partidaHabilitar');
		Route::post('formularPresupuesto/partida/eliminar', 'formularPresupuestoController@partidaEliminar');
		
		/*Modulo Productos*/            
		Route::get('producto/lista', 'productoController@lista');
		Route::get('producto/nuevo', 'productoController@nuevo');
		Route::get('producto/editar/{id}', 'productoController@editar');
		Route::post('producto/guardar', 'productoController@guardar');
		Route::post('producto/guardar/{id}', 'productoController@guardar');
		Route::get('producto/deshabilitar/{id}', 'productoController@deshabilitar');
		Route::get('producto/habilitar/{id}', 'productoController@habilitar');
		Route::post('producto/eliminar', 'productoController@eliminar');

		/*Modulo Unidad de Medida*/            
		Route::get('unidadMedida/lista', 'unidadMedidaController@lista');
		Route::get('unidadMedida/nuevo', 'unidadMedidaController@nuevo');
		Route::get('unidadMedida/editar/{id}', 'unidadMedidaController@editar');
		Route::post('unidadMedida/guardar', 'unidadMedidaController@guardar');
		Route::post('unidadMedida/guardar/{id}', 'unidadMedidaController@guardar');
		Route::get('unidadMedida/deshabilitar/{id}', 'unidadMedidaController@deshabilitar');
		Route::get('unidadMedida/habilitar/{id}', 'unidadMedidaController@habilitar');
		Route::post('unidadMedida/eliminar', 'unidadMedidaController@eliminar'); 
                
		/*Modulo ramo*/            
		Route::get('ramo/lista', 'ramoController@lista');
		Route::get('ramo/nuevo', 'ramoController@nuevo');
		Route::get('ramo/editar/{id}', 'ramoController@editar');
		Route::post('ramo/guardar', 'ramoController@guardar');
		Route::post('ramo/guardar/{id}', 'ramoController@guardar');
		Route::get('ramo/deshabilitar/{id}', 'ramoController@deshabilitar');
		Route::get('ramo/habilitar/{id}', 'ramoController@habilitar');
		Route::post('ramo/eliminar', 'ramoController@eliminar');  
                
		/*Modulo Tipo Proveedor*/            
		Route::get('tipoProveedor/lista', 'tipoProveedorController@lista');
		Route::get('tipoProveedor/nuevo', 'tipoProveedorController@nuevo');
		Route::get('tipoProveedor/editar/{id}', 'tipoProveedorController@editar');
		Route::post('tipoProveedor/guardar', 'tipoProveedorController@guardar');
		Route::post('tipoProveedor/guardar/{id}', 'tipoProveedorController@guardar');
		Route::get('tipoProveedor/deshabilitar/{id}', 'tipoProveedorController@deshabilitar');
		Route::get('tipoProveedor/habilitar/{id}', 'tipoProveedorController@habilitar');
		Route::post('tipoProveedor/eliminar', 'tipoProveedorController@eliminar');          
                
		/*Modulo Tipo Residencia Proveedor*/            
		Route::get('tipoResidenciaProveedor/lista', 'tipoResidenciaProveedorController@lista');
		Route::get('tipoResidenciaProveedor/nuevo', 'tipoResidenciaProveedorController@nuevo');
		Route::get('tipoResidenciaProveedor/editar/{id}', 'tipoResidenciaProveedorController@editar');
		Route::post('tipoResidenciaProveedor/guardar', 'tipoResidenciaProveedorController@guardar');
		Route::post('tipoResidenciaProveedor/guardar/{id}', 'tipoResidenciaProveedorController@guardar');
		Route::get('tipoResidenciaProveedor/deshabilitar/{id}', 'tipoResidenciaProveedorController@deshabilitar');
		Route::get('tipoResidenciaProveedor/habilitar/{id}', 'tipoResidenciaProveedorController@habilitar');
		Route::post('tipoResidenciaProveedor/eliminar', 'tipoResidenciaProveedorController@eliminar');                 
                
		/*Modulo Iva Retencion*/            
		Route::get('ivaRetencion/lista', 'ivaRetencionController@lista');
		Route::get('ivaRetencion/nuevo', 'ivaRetencionController@nuevo');
		Route::get('ivaRetencion/editar/{id}', 'ivaRetencionController@editar');
		Route::post('ivaRetencion/guardar', 'ivaRetencionController@guardar');
		Route::post('ivaRetencion/guardar/{id}', 'ivaRetencionController@guardar');
		Route::get('ivaRetencion/deshabilitar/{id}', 'ivaRetencionController@deshabilitar');
		Route::get('ivaRetencion/habilitar/{id}', 'ivaRetencionController@habilitar');
		Route::post('ivaRetencion/eliminar', 'ivaRetencionController@eliminar');                 
                
		/*Modulo Clasificacion Proveedor*/            
		Route::get('clasificacionProveedor/lista', 'clasificacionProveedorController@lista');
		Route::get('clasificacionProveedor/nuevo', 'clasificacionProveedorController@nuevo');
		Route::get('clasificacionProveedor/editar/{id}', 'clasificacionProveedorController@editar');
		Route::post('clasificacionProveedor/guardar', 'clasificacionProveedorController@guardar');
		Route::post('clasificacionProveedor/guardar/{id}', 'clasificacionProveedorController@guardar');
		Route::get('clasificacionProveedor/deshabilitar/{id}', 'clasificacionProveedorController@deshabilitar');
		Route::get('clasificacionProveedor/habilitar/{id}', 'clasificacionProveedorController@habilitar');
		Route::post('clasificacionProveedor/eliminar', 'clasificacionProveedorController@eliminar');   
		
		/*Modulo Tipo Contrato*/            
		Route::get('tipoContrato/lista', 'tipoContratoController@lista');
		Route::get('tipoContrato/nuevo', 'tipoContratoController@nuevo');
		Route::get('tipoContrato/editar/{id}', 'tipoContratoController@editar');
		Route::post('tipoContrato/guardar', 'tipoContratoController@guardar');
		Route::post('tipoContrato/guardar/{id}', 'tipoContratoController@guardar');
		Route::get('tipoContrato/deshabilitar/{id}', 'tipoContratoController@deshabilitar');
		Route::get('tipoContrato/habilitar/{id}', 'tipoContratoController@habilitar');
		Route::post('tipoContrato/eliminar', 'tipoContratoController@eliminar');

		/*Modulo Iva Factura*/            
		Route::get('ivaFactura/lista', 'ivaFacturaController@lista');
		Route::get('ivaFactura/nuevo', 'ivaFacturaController@nuevo');
		Route::get('ivaFactura/editar/{id}', 'ivaFacturaController@editar');
		Route::post('ivaFactura/guardar', 'ivaFacturaController@guardar');
		Route::post('ivaFactura/guardar/{id}', 'ivaFacturaController@guardar');
		Route::get('ivaFactura/deshabilitar/{id}', 'ivaFacturaController@deshabilitar');
		Route::get('ivaFactura/habilitar/{id}', 'ivaFacturaController@habilitar');
		Route::post('ivaFactura/eliminar', 'ivaFacturaController@eliminar'); 
                
		/*Modulo Banco*/            
		Route::get('banco/lista', 'banco@lista');
		Route::get('banco/nuevo', 'banco@nuevo');
		Route::get('banco/editar/{id}', 'banco@editar');
		Route::post('banco/guardar', 'banco@guardar');
		Route::post('banco/guardar/{id}', 'banco@guardar');
		Route::get('banco/deshabilitar/{id}', 'banco@deshabilitar');
		Route::get('banco/habilitar/{id}', 'banco@habilitar');
		Route::post('banco/eliminar', 'banco@eliminar');           
                
		/*Modulo tipo cuenta bancaria*/            
		Route::get('tipoCuentaBancaria/lista', 'tipoCuentaBancaria@lista');
		Route::get('tipoCuentaBancaria/nuevo', 'tipoCuentaBancaria@nuevo');
		Route::get('tipoCuentaBancaria/editar/{id}', 'tipoCuentaBancaria@editar');
		Route::post('tipoCuentaBancaria/guardar', 'tipoCuentaBancaria@guardar');
		Route::post('tipoCuentaBancaria/guardar/{id}', 'tipoCuentaBancaria@guardar');
		Route::get('tipoCuentaBancaria/deshabilitar/{id}', 'tipoCuentaBancaria@deshabilitar');
		Route::get('tipoCuentaBancaria/habilitar/{id}', 'tipoCuentaBancaria@habilitar');
		Route::post('tipoCuentaBancaria/eliminar', 'tipoCuentaBancaria@eliminar');     

		/*Modulo cuenta bancaria*/            
		Route::get('cuentaBancaria/lista', 'cuentaBancaria@lista');
		Route::get('cuentaBancaria/nuevo', 'cuentaBancaria@nuevo');
		Route::get('cuentaBancaria/editar/{id}', 'cuentaBancaria@editar');
		Route::post('cuentaBancaria/guardar', 'cuentaBancaria@guardar');
		Route::post('cuentaBancaria/guardar/{id}', 'cuentaBancaria@guardar');
		Route::get('cuentaBancaria/deshabilitar/{id}', 'cuentaBancaria@deshabilitar');
		Route::get('cuentaBancaria/habilitar/{id}', 'cuentaBancaria@habilitar');
		Route::post('cuentaBancaria/eliminar', 'cuentaBancaria@eliminar');

                
                /* Tramite movimiento Financiero*/                
                Route::post('movimientoFinanciero/cuentaBancaria', 'movimientoFinanciero@cuentaBancaria');                
                Route::post('movimientoFinanciero/subtipoDocumento', 'movimientoFinanciero@subtipoDocumento');    
                Route::post('movimientoFinanciero/guardar', 'movimientoFinanciero@guardar');  
                
		/*Modulo tipo de movimiento financiero*/            
		Route::get('tipoMovimientoFinanciero/lista', 'tipoMovimientoFinanciero@lista');
		Route::get('tipoMovimientoFinanciero/nuevo', 'tipoMovimientoFinanciero@nuevo');
		Route::get('tipoMovimientoFinanciero/editar/{id}', 'tipoMovimientoFinanciero@editar');
		Route::post('tipoMovimientoFinanciero/guardar', 'tipoMovimientoFinanciero@guardar');
		Route::post('tipoMovimientoFinanciero/guardar/{id}', 'tipoMovimientoFinanciero@guardar');
		Route::get('tipoMovimientoFinanciero/deshabilitar/{id}', 'tipoMovimientoFinanciero@deshabilitar');
		Route::get('tipoMovimientoFinanciero/habilitar/{id}', 'tipoMovimientoFinanciero@habilitar');
		Route::post('tipoMovimientoFinanciero/eliminar', 'tipoMovimientoFinanciero@eliminar'); 
                
		/*Modulo tipo de Documento financiero*/            
		Route::get('tipoDocumentoFinanciero/lista', 'tipoDocumentoFinanciero@lista');
		Route::get('tipoDocumentoFinanciero/nuevo', 'tipoDocumentoFinanciero@nuevo');
		Route::get('tipoDocumentoFinanciero/editar/{id}', 'tipoDocumentoFinanciero@editar');
		Route::post('tipoDocumentoFinanciero/guardar', 'tipoDocumentoFinanciero@guardar');
		Route::post('tipoDocumentoFinanciero/guardar/{id}', 'tipoDocumentoFinanciero@guardar');
		Route::get('tipoDocumentoFinanciero/deshabilitar/{id}', 'tipoDocumentoFinanciero@deshabilitar');
		Route::get('tipoDocumentoFinanciero/habilitar/{id}', 'tipoDocumentoFinanciero@habilitar');
		Route::post('tipoDocumentoFinanciero/eliminar', 'tipoDocumentoFinanciero@eliminar'); 

		/*Modulo subtipo de Documento financiero*/            
		Route::get('subtipoDocumentoFinanciero/lista', 'subtipoDocumentoFinanciero@lista');
		Route::get('subtipoDocumentoFinanciero/nuevo', 'subtipoDocumentoFinanciero@nuevo');
		Route::get('subtipoDocumentoFinanciero/editar/{id}', 'subtipoDocumentoFinanciero@editar');
		Route::post('subtipoDocumentoFinanciero/guardar', 'subtipoDocumentoFinanciero@guardar');
		Route::post('subtipoDocumentoFinanciero/guardar/{id}', 'subtipoDocumentoFinanciero@guardar');
		Route::get('subtipoDocumentoFinanciero/deshabilitar/{id}', 'subtipoDocumentoFinanciero@deshabilitar');
		Route::get('subtipoDocumentoFinanciero/habilitar/{id}', 'subtipoDocumentoFinanciero@habilitar');
		Route::post('subtipoDocumentoFinanciero/eliminar', 'subtipoDocumentoFinanciero@eliminar');                
                
		/*Modulo tipo retencion*/            
		Route::get('tipoRetencion/lista', 'tipoRetencion@lista');
		Route::get('tipoRetencion/nuevo', 'tipoRetencion@nuevo');
		Route::get('tipoRetencion/editar/{id}', 'tipoRetencion@editar');
		Route::post('tipoRetencion/guardar', 'tipoRetencion@guardar');
		Route::post('tipoRetencion/guardar/{id}', 'tipoRetencion@guardar');
		Route::get('tipoRetencion/deshabilitar/{id}', 'tipoRetencion@deshabilitar');
		Route::get('tipoRetencion/habilitar/{id}', 'tipoRetencion@habilitar');
		Route::post('tipoRetencion/eliminar', 'tipoRetencion@eliminar');      
                
		/*Modulo retenciones*/            
		Route::get('retencion/lista', 'retencion@lista');
		Route::get('retencion/nuevo', 'retencion@nuevo');
		Route::get('retencion/editar/{id}', 'retencion@editar');
		Route::post('retencion/guardar', 'retencion@guardar');
		Route::post('retencion/guardar/{id}', 'retencion@guardar');
		Route::get('retencion/deshabilitar/{id}', 'retencion@deshabilitar');
		Route::get('retencion/habilitar/{id}', 'retencion@habilitar');
		Route::post('retencion/eliminar', 'retencion@eliminar'); 
                
                Route::get('retencion/concepto/lista/{id}', 'retencion@listaConcepto'); 
                Route::get('retencion/concepto/nuevo/{id}', 'retencion@nuevoConcepto');                
                Route::get('retencion/concepto/editar/{id}', 'retencion@editarConcepto');
                Route::post('retencion/concepto/guardar', 'retencion@guardarConcepto');
                Route::post('retencion/concepto/guardar/{id}', 'retencion@guardarConcepto');
                Route::post('retencion/concepto/{id}/eliminar', 'retencion@eliminarConcepto');                
                
                /* Tramite Transferencia entre Cuentas*/                
                Route::post('transferenciaCuenta/cuentaBancaria', 'transferenciaCuenta@cuentaBancaria');                    
                Route::post('transferenciaCuenta/guardarAgregar', 'transferenciaCuenta@guardarAgregar');  
                Route::post('transferenciaCuenta/guardarAgregar/{id}', 'transferenciaCuenta@guardarAgregar');
                Route::post('transferenciaCuenta/aprobar', 'transferenciaCuenta@guardarAprobar');   
                
                /* Tramite Pago fondo de Tercero*/           
                Route::post('fondoTercero/retencion', 'fondoTercero@retencion');      
                Route::post('fondoTercero/montoRetencion', 'fondoTercero@montoRetencion');
                Route::post('fondoTercero/guardar', 'fondoTercero@guardar');  
                Route::post('fondoTercero/guardar/{id}', 'fondoTercero@guardar');
                Route::post('fondoTercero/guardarRetencion', 'fondoTercero@guardarRetencion');
                Route::post('fondoTercero/retencion/{id}/{ruta}/borrar', 'fondoTercero@eliminarRetencion');                
                
                Route::post('pagoNomina/guardar', 'pagoNomina@guardar');
                Route::post('pagoNomina/guardar/{id}', 'pagoNomina@guardar');
                Route::post('pagoNomina/guardarEditar', 'pagoNomina@guardarEditar');
		
                Route::post('asignarPartida/guardar', 'asignarPartida@guardar');
		Route::get('asignarPartida/catalogo', 'asignarPartida@catalogo');
		Route::post('asignarPartida/proyac', 'asignarPartida@proyectoAc');
		Route::post('asignarPartida/proyacae', 'asignarPartida@proyectoAcAe');
		Route::post('asignarPartida/vigente', 'asignarPartida@partida');  
                Route::post('asignarPartida/{id}/borrar', 'asignarPartida@borrar');
                
                Route::post('ordenPago/guardar', 'ordenPago@guardar');
                
                Route::get('tesoreria/pendiente', 'tesoreria@pendiente');
                Route::get('tesoreria/procesado', 'tesoreria@procesado');                
                Route::get('tesoreria/detallePendiente/{id}', 'tesoreria@detallePendiente');
                Route::post('tesoreria/guardar', 'tesoreria@guardar');
                Route::get('tesoreria/detalleProcesado/{id}', 'tesoreria@detalleProcesado');
                
                Route::post('creditoAdicional/nu_financiamiento', 'creditoAdicional@nu_financiamiento'); 
                Route::post('creditoAdicional/proyecto_ac', 'creditoAdicional@proyecto_ac');  
                Route::post('creditoAdicional/proyecto_ae', 'creditoAdicional@proyecto_ae'); 
                Route::post('creditoAdicional/partida_gasto', 'creditoAdicional@partida_gasto');
                Route::post('creditoAdicional/guardar', 'creditoAdicional@guardar');
                Route::post('creditoAdicional/{id}/generar', 'creditoAdicional@generar');
                Route::post('creditoAdicional/guardarPartidaIngreso', 'creditoAdicional@guardarPartidaIngreso');
                Route::post('creditoAdicional/guardarPartidaGasto', 'creditoAdicional@guardarPartidaGasto');
                Route::post('creditoAdicional/guardar/{id}', 'creditoAdicional@guardar');
                Route::post('creditoAdicional/eliminar', 'creditoAdicional@eliminarPartida');
                
		/*Modulo Fuente Financiamiento*/            
		Route::get('fuenteFinanciamiento/lista', 'fuenteFinanciamientoController@lista');
		Route::get('fuenteFinanciamiento/nuevo', 'fuenteFinanciamientoController@nuevo');
		Route::get('fuenteFinanciamiento/editar/{id}', 'fuenteFinanciamientoController@editar');
		Route::post('fuenteFinanciamiento/guardar', 'fuenteFinanciamientoController@guardar');
		Route::post('fuenteFinanciamiento/guardar/{id}', 'fuenteFinanciamientoController@guardar');
		Route::get('fuenteFinanciamiento/deshabilitar/{id}', 'fuenteFinanciamientoController@deshabilitar');
		Route::get('fuenteFinanciamiento/habilitar/{id}', 'fuenteFinanciamientoController@habilitar');
		Route::post('fuenteFinanciamiento/eliminar', 'fuenteFinanciamientoController@eliminar'); 

		/*Modulo Cuenta Contable Documento*/            
		Route::get('cuentaContableDocumento/lista', 'cuentaContableDocumentoController@lista');
		Route::get('cuentaContableDocumento/nuevo', 'cuentaContableDocumentoController@nuevo');
		Route::get('cuentaContableDocumento/editar/{id}', 'cuentaContableDocumentoController@editar');
		Route::post('cuentaContableDocumento/guardar', 'cuentaContableDocumentoController@guardar');
		Route::post('cuentaContableDocumento/guardar/{id}', 'cuentaContableDocumentoController@guardar');
		Route::get('cuentaContableDocumento/deshabilitar/{id}', 'cuentaContableDocumentoController@deshabilitar');
		Route::get('cuentaContableDocumento/habilitar/{id}', 'cuentaContableDocumentoController@habilitar');
		Route::post('cuentaContableDocumento/eliminar', 'cuentaContableDocumentoController@eliminar');
		Route::post('cuentaContableDocumento/ruta', 'cuentaContableDocumentoController@ruta'); 
                
                Route::post('crearPartida/nu_financiamiento', 'crearPartida@nu_financiamiento'); 
                Route::post('crearPartida/proyecto_ac', 'crearPartida@proyecto_ac');  
                Route::post('crearPartida/proyecto_ae', 'crearPartida@proyecto_ae'); 
                Route::post('crearPartida/partida_gasto', 'crearPartida@partida_gasto');
                Route::post('crearPartida/guardar', 'crearPartida@guardar');
                Route::post('crearPartida/eliminar', 'crearPartida@eliminar');
                Route::post('crearPartida/aprobar', 'crearPartida@guardarAprobar'); 
                Route::post('crearPartida/rechazar', 'crearPartida@guardarRechazar');                 
        
	});
        

        
	
});

