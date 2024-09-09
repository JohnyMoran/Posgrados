<?php

use App\Admin\Controllers\CohorteController;
use App\Admin\Controllers\CoordinadorController;
use App\Admin\Controllers\DocenteController;
use App\Admin\Controllers\EstudianteController;
use App\Admin\Controllers\ProgramaController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use OpenAdmin\Admin\Facades\Admin;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('coordinadors', CoordinadorController::class);
    $router->resource('programas', ProgramaController::class);
    $router->resource('cohortes', CohorteController::class);
    $router->resource('estudiantes', EstudianteController::class);
    $router->resource('docentes', DocenteController::class);

});
