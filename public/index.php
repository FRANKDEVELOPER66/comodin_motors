<?php 
require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\AppController;
use Controllers\OrdenController;
use Controllers\DashboardController; // ← AGREGAR ESTA LÍNEA

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);

// ❌ ELIMINAR ESTA LÍNEA:
// $router->render('dashboard/index', []); 

// Órdenes
$router->get('/orden', [OrdenController::class, 'index']);
$router->get('/orden/nueva', [OrdenController::class, 'nueva']);
$router->get('/orden/ver', [OrdenController::class, 'ver']);

// APIs de Orden
$router->get('/API/orden/buscar-cliente', [OrdenController::class, 'buscarClienteAPI']);
$router->get('/API/orden/vehiculos', [OrdenController::class, 'obtenerVehiculosAPI']);
$router->post('/API/orden/guardar-cliente', [OrdenController::class, 'guardarClienteAPI']);
$router->post('/API/orden/guardar-vehiculo', [OrdenController::class, 'guardarVehiculoAPI']);
$router->post('/API/orden/guardar', [OrdenController::class, 'guardarOrdenAPI']);
$router->get('/API/orden/buscar', [OrdenController::class, 'buscarAPI']);
$router->post('/API/orden/modificar', [OrdenController::class, 'modificarAPI']);
$router->post('/API/orden/estado', [OrdenController::class, 'cambiarEstadoAPI']);

// Dashboard
$router->get('/dashboard', [DashboardController::class, 'index']);

// Comprueba y valida las rutas
$router->comprobarRutas();