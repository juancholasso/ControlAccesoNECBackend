<?php
$router->get('/', function () use ($router) {
    return $router->app->version();
});

Route::group(['middleware' => ['auth']], function ($router) {
});

// Neo Face
$router->get('/ws/neoface/sincronizar/{id}', ['uses' => 'NeoFaceController@SINCRONIZAR_USUARIO']);

//socket
$router->post('/ws/socket/acceder', ['uses' => 'SocketController@acceder']);

//camara
$router->get('/ws/camara/listarporneoface/{id}', ['uses' => 'CamaraController@listarPorNeoface']);

// Usuarios
$router->get('/ws/usuario/listar', ['uses' => 'UsuarioController@listar']);
$router->get('/ws/usuario/listartipousuario/{id}', ['uses' => 'UsuarioController@listarTipoUsuario']);
$router->get('/ws/usuario/consultar/{id}', ['uses' => 'UsuarioController@consultar']);
$router->post('/ws/usuario/insertar', ['uses' => 'UsuarioController@insertar']);
$router->post('/ws/usuario/foto', ['uses' => 'UsuarioController@foto']);
$router->post('/ws/usuario/actualizar', ['uses' => 'UsuarioController@actualizar']);
$router->delete('/ws/usuario/eliminar/{id}', ['uses' => 'UsuarioController@eliminar']);
$router->delete('/ws/usuario/eliminarneoface/{id}', ['uses' => 'UsuarioController@eliminarNeoface']);
$router->post('/ws/usuario/eliminacionmasiva', ['uses' => 'UsuarioController@eliminacionMasiva']);


//Tipos de usuarios
$router->get('/ws/tipousuario/listar',['uses' => 'TipoUsuarioController@listar']);
$router->get('/ws/tipousuario/consultar/{id}',['uses' => 'TipoUsuarioController@consultar']);
$router->post('/ws/tipousuario/insertar',['uses' => 'TipoUsuarioController@insertar']);
$router->put('/ws/tipousuario/actualizar',['uses' => 'TipoUsuarioController@actualizar']);
$router->delete('/ws/tipousuario/eliminar/{id}',['uses' => 'TipoUsuarioController@eliminar']);

// Tipo de documento
$router->get('/ws/tipodocumento/listar', ['uses' => 'TipoDocumentoController@listar']);
$router->get('/ws/tipodocumento/consultar/{id}', ['uses' => 'TipoDocumentoController@consultar']);
$router->post('/ws/tipodocumento/insertar', ['uses' => 'TipoDocumentoController@insertar']);
$router->delete('/ws/tipodocumento/eliminar/{id}', ['uses' => 'TipoDocumentoController@eliminar']);
$router->put('/ws/tipodocumento/actualizar', ['uses' => 'TipoDocumentoController@actualizar']);

// Permisos
$router->get('/ws/permiso/sticker/{id}', ['uses' => 'PermisoController@sticker']);
$router->get('/ws/permiso/listar', ['uses' => 'PermisoController@listar']);
$router->get('/ws/permiso/listarxusuario/{id}', ['uses' => 'PermisoController@listarPorUsuario']);
$router->get('/ws/permiso/consultar/{id}', ['uses' => 'PermisoController@consultar']);
$router->post('/ws/permiso/insertar', ['uses' => 'PermisoController@insertar']);
$router->put('/ws/permiso/actualizar', ['uses' => 'PermisoController@actualizar']);
$router->delete('/ws/permiso/eliminar/{id}', ['uses' => 'PermisoController@eliminar']);
$router->get('/ws/permiso/exportarpermiso/{id}/{fecha_inicial}/{fecha_final}', ['uses' => 'PermisoController@exportarPermiso']);
$router->post('/ws/permiso/insertarxsubsitio', ['uses' => 'PermisoController@insertarPermisoxSubsitio']);
$router->post('/ws/permiso/editarxsubsitio', ['uses' => 'PermisoController@editarPermisoxSubsitio']);





//Tipos de permiso
$router->get('/ws/tipopermiso/listar', ['uses' => 'TipoPermisoController@listar']);
$router->get('/ws/tipopermiso/consultar/{id}', ['uses' => 'TipoPermisoController@consultar']);
$router->post('/ws/tipopermiso/insertar', ['uses' => 'TipoPermisoController@insertar']);
$router->put('/ws/tipopermiso/actualizar', ['uses' => 'TipoPermisoController@actualizar']);
$router->delete('/ws/tipopermiso/eliminar/{id}', ['uses' => 'TipoPermisoController@eliminar']);

//Tipos de equipos
$router->get('/ws/tipoequipo/listar', ['uses' => 'TipoEquipoController@listar']);
$router->get('/ws/tipoequipo/consultar/{id}', ['uses' => 'TipoEquipoController@consultar']);
$router->post('/ws/tipoequipo/insertar', ['uses' => 'TipoEquipoController@insertar']);
$router->put('/ws/tipoequipo/actualizar', ['uses' => 'TipoEquipoController@actualizar']);
$router->delete('/ws/tipoequipo/eliminar/{id}', ['uses' => 'TipoEquipoController@eliminar']);

//Ingresos
$router->get('/ws/ingreso/listar', ['uses' => 'IngresoController@listar']);
$router->post('/ws/ingreso/insertar', ['uses' => 'IngresoController@insertar']);
$router->post('/ws/ingreso/ingresar', ['uses' => 'IngresoController@ingresar']);
$router->put('/ws/ingreso/actualizar', ['uses' => 'IngresoController@actualizar']);
$router->get('/ws/ingreso/sticker', ['uses' => 'IngresoController@sticker']);
$router->get('/ws/ingreso/contarusuarios/{tipo_usuario}/{fecha_inicial}/{fecha_final}', ['uses' => 'IngresoController@contar_usuarios']);
$router->get('/ws/ingreso/consultaingreso/{id}', ['uses' => 'IngresoController@consultaIngreso']);
$router->get('/ws/ingreso/exportaringreso/{id}/{fecha_inicial}/{fecha_final}', ['uses' => 'IngresoController@exportarIngreso']);
$router->get('/ws/ingreso/filtraringreso/{fecha_inicial}/{fecha_final}', ['uses' => 'IngresoController@filtrarxFechas']);
$router->get('/ws/ingreso/filtrarsitio/{id}', ['uses' => 'IngresoController@filtroxSitio']);

// Ingreso de equipos
$router->get('/ws/ingresoequipo/listar', ['uses' => 'IngresoEquipoController@listar']);
$router->get('/ws/ingresoequipo/listarxpermiso/{id}', ['uses' => 'IngresoEquipoController@listarPorPermiso']);
$router->get('/ws/ingresoequipo/consultarequiposxfecha/{fecha_inicial}/{fecha_final}', ['uses' => 'IngresoEquipoController@consultarEquiposxFecha']);
$router->get('/ws/ingresoequipo/consultar/{id}', ['uses' => 'IngresoEquipoController@consultar']);
$router->post('/ws/ingresoequipo/insertar', ['uses' => 'IngresoEquipoController@insertar']);
$router->put('/ws/ingresoequipo/actualizar', ['uses' => 'IngresoEquipoController@actualizar']);
$router->delete('/ws/ingresoequipo/eliminar/{id}', ['uses' => 'IngresoEquipoController@eliminar']);

//Sitios
$router->get('/ws/sitio/listar', ['uses' => 'SitioController@listar']);
$router->get('/ws/sitio/consultar/{id}', ['uses' => 'SitioController@consultar']);
$router->post('/ws/sitio/insertar', ['uses' => 'SitioController@insertar']);
$router->post('/ws/sitio/actualizar', ['uses' => 'SitioController@actualizar']);
$router->delete('/ws/sitio/eliminar/{id}', ['uses' => 'SitioController@eliminar']);

//Grupos
$router->get('/ws/grupo/listar', ['uses' => 'GrupoController@listar']);
$router->get('/ws/grupo/consultar/{id}', ['uses' => 'GrupoController@consultar']);
$router->post('/ws/grupo/insertar', ['uses' => 'GrupoController@insertar']);
$router->post('/ws/grupo/actualizar', ['uses' => 'GrupoController@actualizar']);
$router->delete('/ws/grupo/eliminar/{id}', ['uses' => 'GrupoController@eliminar']);

//Roles
$router->get('/ws/rolcuenta/listar',['uses' => 'RolCuentaController@listar']);
$router->get('/ws/rolcuenta/consultar/{id}',['uses' => 'RolCuentaController@consultar']);
$router->post('/ws/rolcuenta/insertar',['uses' => 'RolCuentaController@insertar']);
$router->put('/ws/rolcuenta/actualizar',['uses' => 'RolCuentaController@actualizar']);
$router->delete('/ws/rolcuenta/eliminar/{id}',['uses' => 'RolCuentaController@eliminar']);

//Areas
$router->get('/ws/areausuario/listar',['uses' => 'AreaUsuarioController@listar']);
$router->get('/ws/areausuario/consultar/{id}',['uses' => 'AreaUsuarioController@consultar']);
$router->post('/ws/areausuario/insertar',['uses' => 'AreaUsuarioController@insertar']);
$router->put('/ws/areausuario/actualizar',['uses' => 'AreaUsuarioController@actualizar']);
$router->delete('/ws/areausuario/eliminar/{id}',['uses' => 'AreaUsuarioController@eliminar']);


//Tipos de trasporte
$router->get('/ws/tipotransporte/listar',['uses' => 'TipoTransporteController@listar']);
$router->get('/ws/tipotransporte/consultar/{id}',['uses' => 'TipoTransporteController@consultar']);
$router->post('/ws/tipotransporte/insertar',['uses' => 'TipoTransporteController@insertar']);
$router->put('/ws/tipotransporte/actualizar',['uses' => 'TipoTransporteController@actualizar']);
$router->delete('/ws/tipotransporte/eliminar/{id}',['uses' => 'TipoTransporteController@eliminar']);

//Tipos de correos
$router->get('/ws/tipocorreo/listar',['uses' => 'TipoCorreoController@listar']);
$router->get('/ws/tipocorreo/consultar/{id}',['uses' => 'TipoCorreoController@consultar']);
$router->post('/ws/tipocorreo/insertar',['uses' => 'TipoCorreoController@insertar']);
$router->put('/ws/tipocorreo/actualizar',['uses' => 'TipoCorreoController@actualizar']);
$router->delete('/ws/tipocorreo/eliminar/{id}',['uses' => 'TipoCorreoController@eliminar']);

//trasportadoras
$router->get('/ws/transportadora/listar',['uses' => 'TransportadoraController@listar']);
$router->get('/ws/transportadora/consultar/{id}',['uses' => 'TransportadoraController@consultar']);
$router->post('/ws/transportadora/insertar',['uses' => 'TransportadoraController@insertar']);
$router->put('/ws/transportadora/actualizar',['uses' => 'TransportadoraController@actualizar']);
$router->delete('/ws/transportadora/eliminar/{id}',['uses' => 'TransportadoraController@eliminar']);

//estado de equipos
$router->get('/ws/estadoequipo/listar',['uses' => 'EstadoEquipoController@listar']);
$router->get('/ws/estadoequipo/consultar/{id}',['uses' => 'EstadoEquipoController@consultar']);
$router->post('/ws/estadoequipo/insertar',['uses' => 'EstadoEquipoController@insertar']);
$router->put('/ws/estadoequipo/actualizar',['uses' => 'EstadoEquipoController@actualizar']);
$router->delete('/ws/estadoequipo/eliminar/{id}',['uses' => 'EstadoEquipoController@eliminar']);

//empleados
$router->get('/ws/empleado/listar',['uses' => 'EmpleadoController@listar']);
$router->get('/ws/empleado/listartodos',['uses' => 'EmpleadoController@listarTodos']);
$router->get('/ws/empleado/exportarconpermiso',['uses' => 'EmpleadoController@exportarconPermiso']);
$router->get('/ws/empleado/consultar/{id}',['uses' => 'EmpleadoController@consultar']);
$router->get('/ws/empleado/consultarxusuario/{id}',['uses' => 'EmpleadoController@consultarPorUsuario']);
$router->post('/ws/empleado/insertar',['uses' => 'EmpleadoController@insertar']);
$router->put('/ws/empleado/actualizar',['uses' => 'EmpleadoController@actualizar']);
$router->delete('/ws/empleado/eliminar/{id}',['uses' => 'EmpleadoController@eliminar']);


//visitantes
$router->get('/ws/visitante/listar',['uses' => 'VisitanteController@listar']);
$router->get('/ws/visitante/exportarxpermiso',['uses' => 'VisitanteController@exportarconPermiso']);
$router->get('/ws/visitante/listartodos',['uses' => 'VisitanteController@listarTodos']);
$router->get('/ws/visitante/consultar/{id}',['uses' => 'VisitanteController@consultar']);
$router->get('/ws/visitante/consultarxusuario/{id}',['uses' => 'VisitanteController@consultarPorUsuario']);
$router->post('/ws/visitante/insertar',['uses' => 'VisitanteController@insertar']);
$router->put('/ws/visitante/actualizar',['uses' => 'VisitanteController@actualizar']);
$router->delete('/ws/visitante/eliminar/{id}',['uses' => 'VisitanteController@eliminar']);


//empresas
$router->get('/ws/empresa/listar',['uses' => 'EmpresaController@listar']);
$router->get('/ws/empresa/consultar/{id}',['uses' => 'EmpresaController@consultar']);
$router->post('/ws/empresa/insertar',['uses' => 'EmpresaController@insertar']);
$router->put('/ws/empresa/actualizar',['uses' => 'EmpresaController@actualizar']);
$router->delete('/ws/empresa/eliminar/{id}',['uses' => 'EmpresaController@eliminar']);

//tipo de puertas
$router->get('/ws/puerta/listar',['uses' => 'PuertaController@listar']);
$router->get('/ws/puerta/consultar/{id}',['uses' => 'PuertaController@consultar']);
$router->post('/ws/puerta/insertar',['uses' => 'PuertaController@insertar']);
$router->put('/ws/puerta/actualizar',['uses' => 'PuertaController@actualizar']);
$router->delete('/ws/puerta/eliminar/{id}',['uses' => 'PuertaController@eliminar']);

//tipo de puertas
$router->get('/ws/tipopuerta/listar',['uses' => 'TipoPuertaController@listar']);
$router->get('/ws/tipopuerta/consultar/{id}',['uses' => 'TipoPuertaController@consultar']);
$router->post('/ws/tipopuerta/insertar',['uses' => 'TipoPuertaController@insertar']);
$router->put('/ws/tipopuerta/actualizar',['uses' => 'TipoPuertaController@actualizar']);
$router->delete('/ws/tipopuerta/eliminar/{id}',['uses' => 'TipoPuertaController@eliminar']);

//Marcas de equipos
$router->get('/ws/marcaequipo/listar',['uses' => 'MarcaEquipoController@listar']);
$router->get('/ws/marcaequipo/consultar/{id}',['uses' => 'MarcaEquipoController@consultar']);
$router->post('/ws/marcaequipo/insertar',['uses' => 'MarcaEquipoController@insertar']);
$router->put('/ws/marcaequipo/actualizar',['uses' => 'MarcaEquipoController@actualizar']);
$router->delete('/ws/marcaequipo/eliminar/{id}',['uses' => 'MarcaEquipoController@eliminar']);

//Estado De Herramientas
$router->get('/ws/estadoherramienta/listar',['uses' => 'EstadoHerramientaController@listar']);
$router->get('/ws/estadoherramienta/consultar/{id}',['uses' => 'EstadoHerramientaController@consultar']);
$router->post('/ws/estadoherramienta/insertar',['uses' => 'EstadoHerramientaController@insertar']);
$router->put('/ws/estadoherramienta/actualizar',['uses' => 'EstadoHerramientaController@actualizar']);
$router->delete('/ws/estadoherramienta/eliminar/{id}',['uses' => 'EstadoHerramientaController@eliminar']);

//Usuarios por evento
$router->get('/ws/usuarioevento/listar',['uses' => 'UsuarioEventoController@listar']);
$router->get('/ws/usuarioevento/listarxevento/{evento}',['uses' => 'UsuarioEventoController@listarPorEvento']);
$router->get('/ws/usuarioevento/consultar/{id}',['uses' => 'UsuarioEventoController@consultar']);
$router->post('/ws/usuarioevento/insertar', ['uses' => 'UsuarioEventoController@insertar']);
$router->put('/ws/usuarioevento/actualizar', ['uses' => 'UsuarioEventoController@actualizar']);
$router->delete('/ws/usuarioevento/eliminar/{id}',['uses' => 'UsuarioEventoController@eliminar']);
$router->post('/ws/usuarioevento/cargamasiva', ['uses' => 'UsuarioEventoController@cargaMasiva']);
$router->get('/ws/usuarioevento/listarporevento/{evento}',['uses' => 'UsuarioEventoController@listarPorEvento']);
$router->post('/ws/usuarioevento/validarusuario',['uses' => 'UsuarioEventoController@validarUsuario']);
$router->post('/ws/usuario/consultarpordocumento', ['uses' => 'UsuarioController@consultarPorDocumento']);

//Configuraciones
$router->get('/ws/configuracion/listar',['uses' => 'ConfiguracionController@listar']);
$router->get('/ws/configuracion/consultar/{id}',['uses' => 'ConfiguracionController@consultar']);
$router->post('/ws/configuracion/insertar',['uses' => 'ConfiguracionController@insertar']);
$router->post('/ws/configuracion/actualizar',['uses' => 'ConfiguracionController@actualizar']);
$router->delete('/ws/configuracion/eliminar/{id}',['uses' => 'ConfiguracionController@eliminar']);

//salones
$router->get('/ws/salon/listar',['uses' => 'SalonController@listar']);
$router->get('/ws/salon/consultar/{id}',['uses' => 'SalonController@consultar']);
$router->post('/ws/salon/insertar',['uses' => 'SalonController@insertar']);
$router->put('/ws/salon/actualizar',['uses' => 'SalonController@actualizar']);
$router->delete('/ws/salon/eliminar/{id}',['uses' => 'SalonController@eliminar']);

//contratistas
$router->get('/ws/contratista/listar',['uses' => 'ContratistaController@listar']);
$router->get('/ws/contratista/listartodos',['uses' => 'ContratistaController@listarTodos']);
$router->get('/ws/contratista/consultar/{id}',['uses' => 'ContratistaController@consultar']);
$router->get('/ws/contratista/consultarxusuario/{id}',['uses' => 'ContratistaController@consultarPorUsuario']);
$router->post('/ws/contratista/insertar',['uses' => 'ContratistaController@insertar']);
$router->put('/ws/contratista/actualizar',['uses' => 'ContratistaController@actualizar']);
$router->delete('/ws/contratista/eliminar/{id}',['uses' => 'ContratistaController@eliminar']);

//Contratos
$router->get('/ws/contrato/listar',['uses' => 'ContratoController@listar']);
$router->get('/ws/contrato/listarxcontratista/{id}',['uses' => 'ContratoController@listarPorContratista']);
$router->get('/ws/contrato/consultar/{id}',['uses' => 'ContratoController@consultar']);
$router->post('/ws/contrato/insertar',['uses' => 'ContratoController@insertar']);
$router->put('/ws/contrato/actualizar',['uses' => 'ContratoController@actualizar']);
$router->delete('/ws/contrato/eliminar/{id}',['uses' => 'ContratoController@eliminar']);

// Eventos
$router->get('/ws/evento/listar',['uses' => 'EventoController@listar']);
$router->get('/ws/evento/consultar/{id}',['uses' => 'EventoController@consultar']);
$router->post('/ws/evento/insertar',['uses' => 'EventoController@insertar']);
$router->put('/ws/evento/actualizar',['uses' => 'EventoController@actualizar']);
$router->delete('/ws/evento/eliminar/{id}',['uses' => 'EventoController@eliminar']);
$router->get('/ws/evento/contarevento/{id}',['uses' => 'EventoController@contarevento']);
$router->get('/ws/evento/consultarxevento/{id}',['uses' => 'EventoController@consultarxevento']);

//Cuentas
$router->get('/ws/cuenta/listar', ['uses' => 'CuentaController@listar']);
$router->post('/ws/cuenta/insertar', ['uses' => 'CuentaController@insertar']);
$router->put('/ws/cuenta/actualizar', ['uses' => 'CuentaController@actualizar']);
$router->put('/ws/cuenta/actualizarclave', ['uses' => 'CuentaController@actualizarClave']);
$router->get('/ws/cuenta/consultar/{id}', ['uses' => 'CuentaController@consultar']);
$router->delete('/ws/cuenta/eliminar/{id}', ['uses' => 'CuentaController@eliminar']);

//Controles de acceso
$router->get('/ws/controlacceso/listar',['uses' => 'ControlAccesoController@listar']);
$router->get('/ws/controlacceso/consultar/{id}',['uses' => 'ControlAccesoController@consultar']);
$router->post('/ws/controlacceso/insertar',['uses' => 'ControlAccesoController@insertar']);
$router->put('/ws/controlacceso/actualizar',['uses' => 'ControlAccesoController@actualizar']);
$router->delete('/ws/controlacceso/eliminar/{id}',['uses' => 'ControlAccesoController@eliminar']);

//Turnos
$router->get('/ws/turno/listar',['uses' => 'TurnoController@listar']);
$router->get('/ws/turno/listarporgrupo',['uses' => 'TurnoController@listarporGrupo']);
$router->get('/ws/turno/consultar/{id}',['uses' => 'TurnoController@consultar']);
$router->post('/ws/turno/insertar',['uses' => 'TurnoController@insertar']);
$router->put('/ws/turno/actualizar',['uses' => 'TurnoController@actualizar']);
$router->delete('/ws/turno/eliminar/{id}',['uses' => 'TurnoController@eliminar']);

//Asignaciones de turnos
$router->get('/ws/asignacionturno/listar',['uses' => 'AsignacionTurnoController@listar']);
$router->get('/ws/asignacionturno/listarxusuario/{id}',['uses' => 'AsignacionTurnoController@listarPorUsuario']);
$router->get('/ws/asignacionturno/listarxpermiso/{id}',['uses' => 'AsignacionTurnoController@listarPorPermiso']);
$router->get('/ws/asignacionturno/consultar/{id}',['uses' => 'AsignacionTurnoController@consultar']);
$router->post('/ws/asignacionturno/insertar',['uses' => 'AsignacionTurnoController@insertar']);
$router->put('/ws/asignacionturno/actualizar',['uses' => 'AsignacionTurnoController@actualizar']);
$router->delete('/ws/asignacionturno/eliminar/{id}',['uses' => 'AsignacionTurnoController@eliminar']);

//Excepcines permisos
$router->get('/ws/excepcionpermiso/listar',['uses' => 'ExcepcionPermisoController@listar']);
$router->get('/ws/excepcionpermiso/lixtarxusuario/{id}',['uses' => 'ExcepcionPermisoController@listarPorUsuario']);
$router->get('/ws/excepcionpermiso/listarxpermiso/{id}',['uses' => 'ExcepcionPermisoController@listarPorPermiso']);
$router->get('/ws/excepcionpermiso/consultar/{id}',['uses' => 'ExcepcionPermisoController@consultar']);
$router->post('/ws/excepcionpermiso/insertar',['uses' => 'ExcepcionPermisoController@insertar']);
$router->put('/ws/excepcionpermiso/actualizar',['uses' => 'ExcepcionPermisoController@actualizar']);
$router->delete('/ws/excepcionpermiso/eliminar/{id}',['uses' => 'ExcepcionPermisoController@eliminar']);

//Subsitios
$router->get('/ws/subsitio/listar', ['uses' => 'SubsitioController@listar']);
$router->get('/ws/subsitio/consultar/{id}', ['uses' => 'SubsitioController@consultar']);
$router->get('/ws/subsitio/listarxsitio/{id}', ['uses' => 'SubsitioController@listarPorSitio']);
$router->post('/ws/subsitio/insertar', ['uses' => 'SubsitioController@insertar']);
$router->put('/ws/subsitio/actualizar', ['uses' => 'SubsitioController@actualizar']);
$router->delete('/ws/subsitio/eliminar/{id}', ['uses' => 'SubsitioController@eliminar']);

//Tipos vehiculos
$router->get('/ws/tipovehiculo/listar', ['uses' => 'TipoVehiculoController@listar']);
$router->get('/ws/tipovehiculo/consultar/{id}', ['uses' => 'TipoVehiculoController@consultar']);
$router->post('/ws/tipovehiculo/insertar', ['uses' => 'TipoVehiculoController@insertar']);
$router->put('/ws/tipovehiculo/actualizar', ['uses' => 'TipoVehiculoController@actualizar']);
$router->delete('/ws/tipovehiculo/eliminar/{id}', ['uses' => 'TipoVehiculoController@eliminar']);

// Ingreso de vehículos
$router->get('/ws/ingresovehiculo/listar', ['uses' => 'IngresoVehiculoController@listar']);
$router->get('/ws/ingresovehiculo/listarxpermiso/{id}', ['uses' => 'IngresoVehiculoController@listarPorPermiso']);
$router->get('/ws/ingresovehiculo/consultar/{id}', ['uses' => 'IngresoVehiculoController@consultar']);
$router->post('/ws/ingresovehiculo/insertar', ['uses' => 'IngresoVehiculoController@insertar']);
$router->put('/ws/ingresovehiculo/actualizar', ['uses' => 'IngresoVehiculoController@actualizar']);
$router->delete('/ws/ingresovehiculo/eliminar/{id}', ['uses' => 'IngresoVehiculoController@eliminar']);

//Tipos empleados
$router->get('/ws/tipoempleado/listar', ['uses' => 'TipoEmpleadoController@listar']);
$router->get('/ws/tipoempleado/consultar/{id}', ['uses' => 'TipoEmpleadoController@consultar']);
$router->post('/ws/tipoempleado/insertar', ['uses' => 'TipoEmpleadoController@insertar']);
$router->put('/ws/tipoempleado/actualizar', ['uses' => 'TipoEmpleadoController@actualizar']);
$router->delete('/ws/tipoempleado/eliminar/{id}', ['uses' => 'TipoEmpleadoController@eliminar']);

//Transportes
$router->get('/ws/transporte/listar', ['uses' => 'TransporteController@listar']);
$router->get('/ws/transporte/consultar/{id}', ['uses' => 'TransporteController@consultar']);
$router->post('/ws/transporte/insertar', ['uses' => 'TransporteController@insertar']);
$router->put('/ws/transporte/actualizar', ['uses' => 'TransporteController@actualizar']);
$router->delete('/ws/transporte/eliminar/{id}', ['uses' => 'TransporteController@eliminar']);

// Documento contratista
$router->get('/ws/documentocontratista/listar', ['uses' => 'DocumentoContratistaController@listar']);
$router->get('/ws/documentocontratista/listarxcontratista/{id}', ['uses' => 'DocumentoContratistaController@listarPorContratista']);
$router->get('/ws/documentocontratista/consultar/{id}', ['uses' => 'DocumentoContratistaController@consultar']);
$router->post('/ws/documentocontratista/insertar', ['uses' => 'DocumentoContratistaController@insertar']);
$router->post('/ws/documentocontratista/actualizar', ['uses' => 'DocumentoContratistaController@actualizar']);
$router->delete('/ws/documentocontratista/eliminar/{id}', ['uses' => 'DocumentoContratistaController@eliminar']);

// Tipos documentos contratistas
$router->get('/ws/tipodocumentocontratista/listar', ['uses' => 'TipoDocumentoContratistaController@listar']);
$router->get('/ws/tipodocumentocontratista/consultar/{id}', ['uses' => 'TipoDocumentoContratistaController@consultar']);
$router->post('/ws/tipodocumentocontratista/insertar', ['uses' => 'TipoDocumentoContratistaController@insertar']);
$router->put('/ws/tipodocumentocontratista/actualizar', ['uses' => 'TipoDocumentoContratistaController@actualizar']);
$router->delete('/ws/tipodocumentocontratista/eliminar/{id}', ['uses' => 'TipoDocumentoContratistaController@eliminar']);

//Controladoras

$router->get('/ws/controladora/listar',['uses' => 'ControladoraController@listar']);
$router->get('/ws/controladora/consultar/{id}',['uses' => 'ControladoraController@consultar']);
$router->get('/ws/controladora/consultarpuerta/{id}',['uses' => 'ControladoraController@consultarPuerta']);
$router->post('/ws/controladora/insertar',['uses' => 'ControladoraController@insertar']);
$router->put('/ws/controladora/actualizar',['uses' => 'ControladoraController@actualizar']);
$router->delete('/ws/controladora/eliminar/{id}',['uses' => 'ControladoraController@eliminar']);


//Nomina
$router->get('/ws/nomina/listar', 'NominaController@listar');
$router->get('/ws/nomina/consultar/{fecha_inicial}/{fecha_final}', ['uses' => 'NominaController@consultar']);

// Formatos
$router->get('/ws/formato/listar', ['uses' => 'FormatoController@listar']);
$router->post('/ws/formato/insertar', ['uses' => 'FormatoController@insertar']);

//Neofaces
$router->get('/ws/neoface/listar', ['uses' => 'NeoFaceController@listar']);
$router->get('/ws/neoface/getauthtoken/{id}', ['uses' => 'NeoFaceController@GETAUTHTOKEN']);


// Login
$router->post('/ws/login/login', ['uses' => 'LoginController@login']);

//Dias sin Acceso

$router->get('/ws/reportediasinacceso/ultimoingreso', ['uses' => 'PersonaEventoController@listar']);


// LiveView
$router->get('/ws/liveview/consultarporpuerta/{id}', ['uses' => 'PuertaController@consultar']);

//logs
$router->get('/ws/log/listar', ['uses' => 'LogController@listar']);
$router->get('/ws/log/not', ['uses' => 'LogController@not']);
$router->post('/ws/log/insertar', ['uses' => 'LogController@insertar']);
$router->post('/ws/notificacion/insertar', ['uses' => 'NotificacionController@insertar']);

//camara
$router->get('/ws/camara/consultar/{id}', ['uses' => 'CamaraController@consultar']);
