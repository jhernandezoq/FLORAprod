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

Route::get('/', 'HomeController@VerificacionModal')->name('welcome');

Auth::routes();

Route::GET('/resetinside', 'UserController@resetinside')->name('password.resetinside');
Route::POST('/updatepassword', 'UserController@updatepasswordinside')->name('password.change');

Route::GET('/cambiocontraseña/{id}/{error}', 'UserController@cambiocontraseña')->name('cambiocontraseña');

Route::POST('/updatepasswordfinal', 'UserController@updatepasswordfinal')->name('updatepasswordfinal');

Route::GET('/salidalogin', 'UserController@salidalogin')->name('salidalogin');
Route::POST('/actualizacionpassword', 'UserController@actualizacionpassword')->name('actualizacionpassword');

Route::get('/invoice/{id}/log', 'InvoiceController@log')->name('invoice.log');
Route::get('/invoices', 'InvoiceController@index')->name('invoices');
Route::get('/invoice/pending', 'InvoiceController@listPendingInvoices')->name('invoice.pending');
Route::get('/invoice/notify/{invoice}', 'InvoiceController@notify')->name('notify');
Route::post('/invoice/edit', 'InvoiceController@edit')->name('invoice.edit');
Route::get('/invoice/{id}/{id_user}', 'InvoiceController@show')->name('invoice.show');
Route::POST('/invoice/gestionequivalents', 'InvoiceController@gestionequivalents')->name('invoice.gestionequivalents');
Route::POST('/invoice/gestionequivalentsini', 'InvoiceController@gestionequivalents')->name('invoice.gestionequivalents');
Route::get('/invoice', 'InvoiceController@create')->name('invoice.create');
Route::post('/invoice', 'InvoiceController@store')->name('invoice.store');
Route::post('/invoice/report', 'InvoiceController@report')->name('invoice.report');
Route::GET('/invoice/aprobacion_masiva', 'InvoiceController@aprobacion_masiva');
Route::GET('/invoice/resolutions', 'InvoiceController@resolutionscreate')->name('invoice.resolutions');
Route::GET('/invoice/resolutionsinit', 'InvoiceController@resolutionsinit')->name('invoice.resolutionsinit');
Route::POST('/invoice/resolutionsinactive', 'InvoiceController@resolutionsinactive')->name('invoice.resolutionsinactive');
Route::POST('/invoice/resolutionskeep', 'InvoiceController@resolutionsstore')->name('invoice.resolutionsstore');
Route::POST('/invoice/logequivalent', 'InvoiceController@logequivalent')->name('invoice.logequivalent');
Route::POST('/invoice/loggestionequivalents', 'InvoiceController@loggestionequivalents');
Route::GET('/invoice/equivalents', 'InvoiceController@equivalents')->name('invoice.equivalents');
Route::POST('/pdf/equivalentepdf', 'InvoiceController@imprimir')->name('invoice.imprimir');


Route::post('/log', 'LogController@store')->name('log.store');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/procesos', 'HomeController@process')->name('process');
Route::get('/autogestion', 'HomeController@autogestion')->name('autogestion');
Route::get('/invoice/equivalente', 'InvoiceController@equivalente')->name('invoice.equivalente');
Route::get('/yo', 'HomeController@yo')->name('yo');
Route::get('/servicio', 'TicketController@index')->name('service');
Route::get('/informes', 'HomeController@informes')->name('informes');
Route::get('/informesrtc', 'HomeController@informesrtc')->name('informes.rtc');
Route::get('/admin', 'HomeController@admin')->name('admin');
Route::get('/admindata', 'HomeController@admindata')->name('admindata');
Route::POST('/adminpermissions', 'HomeController@adminpermissions')->name('admin.permissions');

Route::GET('/empleados', 'HomeController@empleados')->name('admin.empleados');
Route::POST('/actualizacionjefes', 'HomeController@actualizacionjefes');
Route::GET('/descontinuados', 'HomeController@descontinuados');
Route::POST('/actualizacionempleadossalientes', 'HomeController@actualizacionempleadossalientes');
//se agregan rutas de actualizacion de campos
Route::GET('/actualizacioncamposempleados', 'HomeController@actualizacioncamposempleados');
Route::POST('/actualizacioncamposempleadosfinal', 'HomeController@actualizacioncamposempleadosfinal');
//
Route::GET('/empleadosnuevos', 'HomeController@empleadosnuevos');
Route::POST('/actualizacionempleadosnuevos', 'HomeController@actualizacionempleadosnuevos');
Route::GET('/procesoterminado', 'HomeController@procesoterminado');



Route::get('/users/{user}', 'UserController@show')->name('user.show');
Route::post('/users/{user}', 'UserController@update')->name('user.update');


//*
//Ticket
//**//
Route::get('/ticket/search', 'TicketController@search')->name('ticket.search');
Route::get('/ticket/pending', 'TicketController@pending')->name('ticket.pending');
Route::get('/ticket/{id}/solution', 'TicketController@solution')->name('ticket.solution');
Route::post('/ticket/{id}/solve', 'TicketController@solve')->name('ticket.solve');

Route::get('/ticket/{id}/solutiona', 'TicketController@solutiona')->name('ticket.solutiona');
Route::post('/ticket/{id}/accept', 'TicketController@accept')->name('ticket.accept');

Route::get('/ticket/{id}/solutiond', 'TicketController@solutiond')->name('ticket.solutiond');
Route::post('/ticket/{id}/deny', 'TicketController@deny')->name('ticket.deny');

Route::get('/ticket/{id}/close', 'TicketController@close')->name('ticket.close');
Route::post('/ticket/{id}/finalize', 'TicketController@finalize')->name('ticket.finalize');

Route::get('/ticket', 'TicketController@create')->name('ticket.create');
Route::post('/ticket', 'TicketController@store')->name('ticket.store');
Route::get('/ticket/{id}', 'TicketController@show')->name('ticket.show');




//*
//TicketLog
//**//
Route::post('/ticketlog', 'TicketLogController@store')->name('ticketlog.store');
Route::POST('/edit', 'TicketLogController@edit')->name('ticketlog.edit');
Route::GET('/preedit', 'TicketLogController@preedit')->name('ticketlog.preedit');

//*Performance
//**
Route::get('/performance/self', 'PerformanceController@self')->name('performance.self');

Route::post('/performance/storeSelfAssessment', 'PerformanceController@storeSelfAssessment')->name('performance.storeSelfAssessment');

Route::get('/performance/team', 'PerformanceController@myTeam')->name('performance.team');

Route::get('/performance/assessment/{person}', 'PerformanceController@assessment')->name('performance.assessment');

Route::get('/performance/assessment2', 'PerformanceController@assessment2')->name('performance.assessment');

Route::post('/performance/storeAssessment', 'PerformanceController@storeAssessment')->name('performance.storeAssessment');

Route::get('/performance/report', 'PerformanceController@report')->name('performance.report');

Route::post('/performance/seeker', 'PerformanceController@seeker');
Route::get('/performance/export', 'PerformanceController@export');

Route::get('/performance/history/{id_user}', 'PerformanceController@history');
Route::POST('/performance/userhistory','PerformanceController@userhistory');

Route::GET('/performance/enable', 'PerformanceController@enable')->name('performance.enable');

Route::POST('/performance/storeEnable', 'PerformanceController@storeEnable')->name('performance.storeEnable');

Route::GET('/performance/users', 'PerformanceController@users')->name('performance.users');

Route::POST('/performance/editUser', 'PerformanceController@editUser')->name('performance.editUser');

Route::POST('/performance/edit', 'PerformanceController@edit')->name('performance.edit');

Route::GET('/performance/edition/{id}', 'PerformanceController@edition')->name('performance.edition');

Route::GET('/performance/missing', 'PerformanceController@missing')->name('performance.missing');

Route::GET('/performance/reportmissing', 'PerformanceController@reportmissing')->name('performance.reportmissing');

Route::get('/performance/exportmissing', 'PerformanceController@exportmissing');

Route::POST('/performance/files', 'PerformanceController@files');


//Reports

Route::GET('/reports/find', 'ReportController@find')->name('find');
Route::GET('/reports/editinvoices/{id}', 'ReportController@editinvoices');
Route::POST('/reports/editioninvoicesself', 'ReportController@editinvoicesself');
Route::POST('/reports/finaleditinvoicesself', 'ReportController@finaleditinvoicesself');
Route::POST('/reports/finaleditinvoices', 'ReportController@finaleditinvoices');
Route::GET('/reports/deleteinvoices/{id}', 'ReportController@deleteinvoices');
Route::POST('/reports/deleteinvoicesself', 'ReportController@deleteinvoicesself');
Route::POST('/reports/lookinginvoices', 'ReportController@lookinginvoices');
Route::POST('/reports/lookinginvoicesself', 'ReportController@lookinginvoicesself');
Route::POST('/reports/usersfinder', 'ReportController@usersfinder');
Route::GET('/reports/lookingtickets', 'ReportController@lookingtickets');
Route::GET('/report/invoices', 'ReportController@reportsInvoices')->name('reports.invoices');
Route::GET('/report/invocesself', 'ReportController@reportsInvoicesUsers')->name('invoice.self');
Route::GET('/report/tickets', 'ReportController@reportsTickets')->name('reports.tickets');
Route::POST('/report/permissionUser', 'ReportController@permissionUser')->name('reports.permissionUser');
Route::GET('/reports/users', 'ReportController@Users')->name('reports.users');

Route::GET('/report/permission', 'ReportController@permission')->name('permission.data');

Route::POST('/report/permissionedit', 'ReportController@permissionedit')->name('permission.dataedit');

Route::POST('/report/permissioneditfinder', 'ReportController@permissioneditfinder')->name('permission.dataeditfinder');
Route::POST('/report/permissionfinder', 'ReportController@permissionfinder')->name('permission.finder');

Route::GET('/reports/directorio', 'ReportController@directorio')->name('directorio');

Route::POST('/reports/directoryfinder', 'ReportController@directoryfinder')->name('directoryfinder');

Route::GET('/reports/invoiceslist', 'ReportController@invoiceslist')->name('invoiceslist');

Route::POST('/reports/invoicesfinder', 'ReportController@invoicesfinder')->name('invoicesfinder');

Route::GET('/reports/equivalente', 'ReportController@equivalente')->name('reports.equivalente');

Route::POST('/reports/equivalentedit', 'ReportController@equivalentedit')->name('reports.equivalentedit');

Route::POST('/reports/equivalentupdate', 'ReportController@equivalentupdate')->name('reports.equivalentupdate');

//informe rappi
Route::GET('/reports/newrappi', 'ReportController@newrappi')->name('reports.rappi');

//informe compras

Route::GET('/reports/compras', 'ReportController@compras')->name('reports.compras');


// Factura equivalente
Route::POST('/flujoanticipos', 'ReportController@flujoanticipos');

Route::POST('/costcenter', 'ReportController@costcenter');

Route::POST('/adjuntosfiles', 'ReportController@adjuntosfiles');

Route::POST('/adjuntosfilesequivalentes', 'InvoiceController@adjuntosfilesequivalentes');

Route::POST('/load_data_invoices', 'ReportController@load_data_invoices');

Route::POST('/load_data_anticipos', 'ReportController@load_data_anticipos');

Route::POST('/load_data_invoices_report', 'ReportController@load_data_invoices_report');

Route::POST('/updatedata', 'HomeController@updatedata')->name('admin.updatedata');

Route::POST('/updateuserdata', 'HomeController@updateuserdata');

Route::GET('/Updateselfuserdata', 'HomeController@UpdateSelfUserData')->name('admin.updateself');

Route::GET('/performance/usersdata', 'HomeController@UpdateUsersData')->name('performance.usersdata');

Route::POST('/updateothersusersdata', 'HomeController@UpdateDataOthersUsers')->name('admin.updateothersusers');


///


//Anticipos

Route::POST('/reports/anticiposfinder', 'ReportController@anticiposfinder')->name('anticiposfinder');

Route::GET('/reports/anticipo', 'ReportController@anticipo')->name('reports.anticipo');



Route::GET('anticipos/anticipos', 'InvoiceController@anticipos')->name('anticipos.anticipos');
Route::POST('anticipos/save', 'InvoiceController@save')->name('anticipos.save');
Route::GET('anticipos/gestion', 'InvoiceController@gestion')->name('anticipos.gestion');
//Route::GET('anticipos/{id_user}', 'InvoiceController@gestioncorreo');
//Route::GET('anticipos/{id}/{id_user}', 'InvoiceController@gestionar');

Route::GET('anticipos/{id}/{id_user}/aceptar', 'InvoiceController@gestionaraceptar');
Route::GET('anticipos/{id}/{id_user}/{id_user_proceso}/aceptar', 'InvoiceController@gestionaraceptar');

//Route::GET('anticipos/{id}/{id_user}/rechazar', 'InvoiceController@gestionarrechazar');
Route::POST('anticipos/rechazar', 'InvoiceController@gestionarrechazar');


Route::GET('anticipos/pagar', 'InvoiceController@pagar')->name('anticipos.pagar');
Route::GET('anticipos/pagar/{id_user}', 'InvoiceController@pagar');
Route::GET('anticipos/{id}/{id_user}/pagar', 'InvoiceController@pagado');
//Route::GET('anticipos/{id}/{id_user}/{id_user_proceso}/pagar', 'InvoiceController@pagadocorreo');
Route::GET('anticipos/pagaranticipos', 'InvoiceController@pagaranticipos')->name('anticipos.pagaranticipos');


Route::GET('anticipos/historial', 'InvoiceController@historial')->name('anticipos.historial');
Route::GET('anticipos/historialnew', 'InvoiceController@historialnew')->name('anticipos.historialnew');
//Route::GET('anticipos/historial/{id_user}', 'InvoiceController@historialcorreo');



//Legalizacion

Route::GET('anticipos/legalizacion', 'InvoiceController@legalizacion')->name('anticipos.legalizacion');
Route::GET('anticipos/legalizar_final/{id}', 'InvoiceController@legalizar_final')->name('anticipos.legalizar_final');
Route::POST('legalizacion/save', 'InvoiceController@legalizacionsave');

Route::GET('legalizacion/gestion', 'InvoiceController@legalizaciongestion')->name('legalizacion.gestion');
Route::GET('legalizacion/legalizacion_cerrr', 'InvoiceController@legalizacioncerrar')->name('legalizacion.cierre');

Route::GET('anticipos/{id}/{id_user}/aceptarlegalizacion', 'InvoiceController@gestionaraceptarlegalizacion');

//Route::GET('anticipos/{id}/{id_user}/rechazarlegalizacion', 'InvoiceController@gestionarrechazarlegalizacion');
Route::POST('anticipos/rechazarlegalizacion', 'InvoiceController@gestionarrechazarlegalizacion');


Route::GET('anticipos/{id}/{id_user}/cerrarlegalizacion', 'InvoiceController@gestionarcerrarlegalizacion');
Route::GET('anticipos/finalizarlegalizacion', 'InvoiceController@gestionarfinalizarlegalizacion')->name('legalizacion.finalizar');
Route::GET('anticipos/{id}/{id_user}/finalizarlegalizacion', 'InvoiceController@gestionarfinalizacionlegalizacion');


Route::GET('anticipos/{id}/{id_user}/rechazarcierrelegalizacion', 'InvoiceController@gestionarrechazarcierrelegalizacion');

Route::POST('/anticipos/anticipos-log', 'InvoiceController@anticiposLog');
Route::POST('/adjuntosfilesanticipos', 'InvoiceController@adjuntosfilesanticipos');

Route::POST('/adjuntosfileslegalizaciones', 'InvoiceController@adjuntosfileslegalizaciones');



//ANTICIPOS CORREO

Route::GET('anticipos/correo/{id_user}', 'InvoiceController@gestioncorreo');
Route::GET('anticipos/pagaranticiposcorreo/{id_user}', 'InvoiceController@pagaranticiposcorreo')->name('anticipos.pagaranticiposcorreo');
Route::GET('anticipos/historialcorreo/{id_user}', 'InvoiceController@historialcorreo')->name('anticipos.historialcorreo');
Route::GET('legalizacion/gestioncorreo/{id_user}', 'InvoiceController@legalizaciongestioncorreo')->name('legalizacion.gestioncorreo');


// Departamentos
Route::GET('/document/search-depto', 'DocumentController@search_deptos');
// Ciudades
Route::GET('/document/search-city', 'DocumentController@search_cities');

// Proveedores
Route::GET('/document/search-supplier', 'DocumentController@search_supplier');
// Unidades de medida
Route::GET('/document/search-quantity-unit', 'DocumentController@search_quantity_unit');

// Resoluciones DIAN
Route::GET('/dianresolution', 'DianResolutionController@index')->name('dianresolution.index');
Route::GET('/dianresolution/create', 'DianResolutionController@create')->name('dianresolution.create');
Route::GET('/dianresolution/{id}', 'DianResolutionController@create')->name('dianresolution.create');
Route::POST('/dianresolution/store', 'DianResolutionController@store');


// Documents Soporte/Notas
Route::GET('/document', 'DocumentController@index')->name('document.index');
Route::GET('/document/create', 'DocumentController@create')->name('document.create');
Route::GET('/document/show/{id}', 'DocumentController@show')->name('document.show');
Route::GET('/document/{id}', 'DocumentController@create')->name('document.create');
Route::POST('/document/store', 'DocumentController@store');
Route::POST('/document/edit', 'DocumentController@edit');
Route::POST('/document/get-item', 'DocumentController@get_item');
Route::POST('/document/del-item', 'DocumentController@destroy');
Route::POST('/document/update', 'DocumentController@update');
Route::POST('/document/send-data', 'DocumentController@send_data');


Route::get('/testss-informatioon-validateee', 'TestController@index');


// Documents Soporte/Nota de ajuste

Route::GET('/ajuste', 'DocumentController@ajuste')->name('document.ajuste');
Route::GET('/document/ajuste/{id}', 'DocumentController@ajuste_create')->name('ajuste.create');
Route::POST('/ajuste/save', 'DocumentController@ajuste_save')->name('document.ajuste_save');


// Legalización gastos

Route::GET('/anticipos/gastos', 'InvoiceController@gastos')->name('legalizacion.gastos');
Route::POST('gastos/save', 'InvoiceController@gastos_save');
Route::POST('/costcenterlegalizacion', 'InvoiceController@costcenterlegalizacion');
Route::GET('/anticipos/gastosgestion', 'InvoiceController@gastosgestion')->name('legalizacion.gastosgestion');
Route::GET('/anticipos/gastosgestionuser/{id_user}', 'InvoiceController@gastosgestionuser');
Route::POST('/adjuntosfilesgastos', 'InvoiceController@adjuntosfilesgastos');
Route::POST('/flujogastos', 'InvoiceController@flujogastos');
Route::GET('anticipos/{id}/aceptargastos', 'InvoiceController@aceptargastos');
Route::GET('anticipos/{id}/{id_user}/aceptargastosuser', 'InvoiceController@aceptargastosuser');
Route::GET('reports/gastos', 'ReportController@gastos')->name('reports.gastos');
Route::GET('anticipos/historialgastos', 'InvoiceController@historialgastos')->name('historial.gastos');
Route::GET('anticipos/gastospagos', 'InvoiceController@gastospagos')->name('legalizacion.gastospagos');
Route::GET('anticipos/{id}/pagogasto', 'InvoiceController@pagogasto');
Route::POST('anticipos/rechazarlegalizaciongastos', 'InvoiceController@rechazarlegalizaciongastos');
Route::POST('anticipos/rechazarlegalizaciongastosuser', 'InvoiceController@rechazarlegalizaciongastosuser');
Route::POST('/adjuntosdistribuciongastos', 'InvoiceController@adjuntosdistribuciongastos');


//Devoluciones

Route::GET('/invoice/devoluciones','InvoiceController@devoluciones')->name('invoices.devoluciones');
Route::POST('devoluciones/save', 'InvoiceController@devoluciones_save');
Route::GET('/invoice/gestiondevoluciones', 'InvoiceController@gestiondevoluciones')->name('devoluciones.gestion');
Route::POST('/devoluciones-log', 'InvoiceController@devolucionesLog');
Route::POST('/adjuntosfilesdevoluciones', 'InvoiceController@adjuntosfilesdevoluciones');
Route::POST('devoluciones/rechazar', 'InvoiceController@devolucionesrrechazar');
Route::GET('devoluciones/{id}/aceptar', 'InvoiceController@gestionaraceptardevolucion');
Route::GET('/reports/devoluciones', 'ReportController@devoluciones')->name('reports.devoluciones');
Route::GET('/invoices/historialdevoluciones', 'InvoiceController@historialdevoluciones')->name('historial.devoluciones');
Route::POST('/datosdevoluciones', 'InvoiceController@datosdevoluciones');
Route::POST('devoluciones/update', 'InvoiceController@devoluciones_update');
Route::POST('devoluciones/aceptar', 'InvoiceController@devolucionesaceptar');

//Facturación Web Service

Route::GET('/invoice/distribution_suppliers','InvoiceController@distribution_suppliers')->name('distributions.suppliers');
Route::POST('/usersdistributions', 'InvoiceController@usersdistributions');
Route::POST('/flowapprovers', 'InvoiceController@flowapprovers');
Route::GET('/invoice/unassigned','InvoiceController@unassigned')->name('invoices.unassigned');
Route::GET('takeinvoice/{id_factura}','InvoiceController@takeinvoice');
Route::GET('invoice/faccosto','InvoiceController@faccosto')->name('invoices.faccosto');
Route::GET('/invoice/facosto/{id}/{id_user}', 'InvoiceController@facosto');
Route::post('/logfaccosto', 'InvoiceController@logfaccosto');
Route::GET('invoice/compras','InvoiceController@fcompras')->name('invoices.compras');
Route::POST('/invoicesdistributionsupdate', 'InvoiceController@invoicesdistributionsupdate');
Route::POST('/invoices/invoicesfinderunassigned', 'InvoiceController@invoicesfinderunassigned');
Route::POST('/invoices/invoicesfindercosto', 'InvoiceController@invoicesfindercosto');
Route::GET('/invoices/siningreso', 'InvoiceController@siningreso')->name('invoices.siningreso');


//Radian
Route::POST('/rechazofactura', 'InvoiceController@rechazofactura');

