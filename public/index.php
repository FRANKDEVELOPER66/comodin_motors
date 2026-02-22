<?php
require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\AppController;
use Controllers\OrdenController;
use Controllers\DashboardController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

// Inicio
$router->get('/', [AppController::class, 'index']);

// Dashboard
$router->get('/dashboard', [DashboardController::class, 'index']);

// ============================================
// VISTAS - Órdenes
// ============================================
$router->get('/orden', [OrdenController::class, 'index']);
$router->get('/orden/nueva', [OrdenController::class, 'nueva']);
$router->get('/orden/ver', [OrdenController::class, 'ver']);

// ============================================
// APIs - Clientes
// ============================================
$router->get('/API/clientes/buscar', [OrdenController::class, 'buscarClienteAPI']);
$router->post('/API/clientes/guardar', [OrdenController::class, 'guardarClienteAPI']);

// ============================================
// APIs - Vehículos
// ============================================
$router->get('/API/vehiculos/cliente', [OrdenController::class, 'obtenerVehiculosAPI']);
$router->post('/API/vehiculos/guardar', [OrdenController::class, 'guardarVehiculoAPI']);

// ============================================
// APIs - Órdenes
// ============================================
$router->post('/API/ordenes/guardar', [OrdenController::class, 'guardarOrdenAPI']);
$router->get('/API/ordenes/buscar', [OrdenController::class, 'buscarAPI']);
$router->post('/API/ordenes/modificar', [OrdenController::class, 'modificarAPI']);
$router->post('/API/ordenes/estado', [OrdenController::class, 'cambiarEstadoAPI']);

// ============================================
// APIs - Servicios (catálogo)
// ============================================
$router->get('/API/servicios/buscar', [OrdenController::class, 'buscarServiciosAPI']);

// Comprueba y valida las rutas
$router->comprobarRutas();
