<?php

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes(['register' => false]);

//Route::get('/home', 'HomeController@index')->name('home')->middleware('CheckStatus');
Route::get('/home', 'HomeController@index')->name('home');

################################ Begin Users Routes ######################################
Route::group(['namespace' => 'Users', 'middleware' => 'auth'],function (){

    Route::get('show_users','UserController@index')->name('users.get');
    Route::get('create_user','UserController@create')->name('user.create');
    Route::post('store_user','UserController@store')->name('user.store');
    Route::get('user-edit/{user_id}','UserController@edit')->name('user.edit');
    Route::post('user-update/{user_id}','UserController@update')->name('user.update');

});
################################ End Users Routes ########################################

################################ Begin Roles&Permissions Routes ######################################
Route::group(['namespace' => 'Roles_Permissions', 'middleware' => 'auth'],function (){

    Route::get('show_roles','RoleController@index')->name('roles.get');
    Route::get('create-role','RoleController@create')->name('role.create');
    Route::post('store-role','RoleController@store')->name('role.store');
    Route::get('role/{role_id}','RoleController@edit')->name('role.edit');
    Route::post('role/{role_id}','RoleController@update')->name('role.update');
    Route::get('roles_permission/{role_id}','RoleController@show')->name('rolesPermission.get');
    Route::post('delete-role/{role_id}','RoleController@destroy')->name('role.destroy');

});
################################ Begin Roles&Permissions Routes ########################################

################################ Begin Invoices Routes ######################################
Route::group(['namespace' => 'Invoices'],function (){

    Route::get('invoices','InvoicesController@index')->name('get.invoices');
    Route::get('create_invoices','InvoicesController@create')->name('create.invoices');
    Route::get('get_products/{section_id}','InvoicesController@getProducts')->name('getProducts.invoices'); //uses in ajax
    Route::post('store_invoice','InvoicesController@store')->name('store.invoice');
    Route::get('invoice_details/{invoice_id}','InvoicesDetailsController@getInvoiceDetails')->name('getDetails.invoices');
    Route::get('invoice_attachments/{invoice_id}','InvoicesDetailsController@getInvoiceAttachments')->name('getAttachments.invoices');
    Route::get('view_file/{invoice_number}/{file_name}','InvoicesDetailsController@open_file')->name('file.view');
    Route::get('get_file/{invoice_number}/{file_name}','InvoicesDetailsController@get_file')->name('file.download');
    Route::post('delete_file','InvoicesDetailsController@destroy')->name('file.delete');
    Route::post('add_attachment','InvoicesAttachmentsController@addAttachments')->name('file.newAdd');
    Route::get('edit_invoice/{invoice_id}','InvoicesController@edit')->name('edit.invoice');
    Route::post('update_invoice/{invoice_id}','InvoicesController@update')->name('update.invoice');
    Route::post('delete_invoice','InvoicesController@destroy')->name('delete.invoice');
    Route::get('payment_status/{invoice_id}','InvoicesController@getPaymentStatus')->name('get.paymentStatus');
    Route::post('payment_status_update/{invoice_id}','InvoicesController@getPaymentStatusUpdate')->name('update.paymentStatus');
    Route::get('Invoice_Paid','InvoicesController@Invoice_Paid')->name('invoice.Paid');
    Route::get('Invoice_UnPaid','InvoicesController@Invoice_UnPaid')->name('invoice.UnPaid');
    Route::get('Invoice_Partial','InvoicesController@Invoice_Partial')->name('invoice.Partial');
    Route::get('invoices_archive','InvoiceArchiveController@index')->name('invoiceArchive.get');
    Route::post('invoices_archive_update','InvoiceArchiveController@updateArchive')->name('invoiceArchive.update');
    Route::post('destroy_invoice_archive','InvoiceArchiveController@destroyforce')->name('delete.invoice.archive');
    Route::get('print_invoice/{invoice_id}','InvoicesController@print')->name('invoice.print');
    Route::get('invoices_export', 'InvoicesController@export')->name('invoices.export');
    Route::get('invoices_report','InvoicesReportsController@index')->name('reports.get');
    Route::post('Search_invoices', 'InvoicesReportsController@Search_invoices')->name('searchInvoices');
    Route::get('customers_report','CustomersReportsController@index')->name('reportsCustomers.get');
    Route::post('Search_customers', 'CustomersReportsController@Search_customers')->name('searchCustomers');

    Route::post('save-token','InvoicesController@saveToken')->name('save-token');

    Route::get('getUnreadNotificationsall','InvoicesController@getUnreadNotificationsall')->name('getUnreadNotificationsall'); //all

    Route::get('getUnreadNotifications','InvoicesController@getUnreadNotifications')->name('getUnreadNotifications');
    Route::get('markInvoicesAsRead','InvoicesController@markAsRead')->name('makeInvoiceAsRead');

});
################################ Begin Invoices Routes ######################################

################################ Begin Sections Routes ######################################
Route::group(['namespace' => 'Sections'],function (){

    Route::get('sections','SectionController@index')->name('get.sections');
    Route::post('store_sections','SectionController@store')->name('store.sections');
    Route::post('update_sections','SectionController@update')->name('update.sections');
    Route::post('delete_sections','SectionController@destroy')->name('destroy.sections');

});
################################ Begin Sections Routes ######################################

################################ Begin Products Routes ######################################
Route::group(['namespace' => 'Products'],function (){

    Route::get('products','ProductsController@index')->name('get.products');
    Route::post('store_products','ProductsController@store')->name('store.products');
    Route::post('update_products','ProductsController@update')->name('update.products');
    Route::post('delete_products','ProductsController@destroy')->name('destroy.product');

});
################################ Begin Products Routes ######################################


//Route::group(['middleware' => ['auth']], function() {
//    Route::resource('roles','RoleController');
//    Route::resource('users','UserController');
//});


Route::get('/{page}', 'AdminController@index')->name('index');


