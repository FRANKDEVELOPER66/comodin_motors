<?php

namespace Controllers;

use Model\Orden;
use MVC\Router;

class DashboardController
{
    public static function index(Router $router)
    {
        // Obtener estadísticas del día
        $hoy = date('Y-m-d');

        // Órdenes del día - con manejo de null
        $ordenesHoy = Orden::obtenerOrdenesDelDia() ?? [];
        $totalOrdenesHoy = count($ordenesHoy);

        // Calcular ingresos del día
        $ingresosHoy = 0;
        foreach ($ordenesHoy as $orden) {
            $ingresosHoy += floatval($orden['costo_total'] ?? 0);
        }

        // Vehículos en taller (órdenes en proceso o pendientes)
        $vehiculosEnTaller = Orden::contarPorEstado(['pendiente', 'en_proceso']) ?? 0;

        // Completadas hoy
        $completadasHoy = Orden::contarCompletadasHoy() ?? 0;

        // Órdenes recientes (últimas 5)
        $ordenesRecientes = Orden::obtenerRecientes(5) ?? [];

        // Comparación con ayer
        $ayer = date('Y-m-d', strtotime('-1 day'));
        $ordenesAyer = Orden::contarPorFecha($ayer) ?? 0;
        $porcentajeOrdenesVsAyer = $ordenesAyer > 0
            ? round((($totalOrdenesHoy - $ordenesAyer) / $ordenesAyer) * 100)
            : 0;

        $ingresosAyer = Orden::sumarIngresosPorFecha($ayer) ?? 0;
        $porcentajeIngresosVsAyer = $ingresosAyer > 0
            ? round((($ingresosHoy - $ingresosAyer) / $ingresosAyer) * 100)
            : 0;

        $completadasAyer = Orden::contarCompletadasPorFecha($ayer) ?? 0;
        $porcentajeCompletadasVsAyer = $completadasAyer > 0
            ? round((($completadasHoy - $completadasAyer) / $completadasAyer) * 100)
            : 0;

        // Estadísticas por estado
        $pendientes = Orden::contarPorEstado(['pendiente']) ?? 0;
        $enProceso = Orden::contarPorEstado(['en_proceso']) ?? 0;
        $completadas = Orden::contarPorEstado(['completado']) ?? 0;

        // RENDERIZAR LA VISTA CON TODAS LAS VARIABLES
        $router->render('dashboard/index', [
            'totalOrdenesHoy' => $totalOrdenesHoy,
            'ingresosHoy' => $ingresosHoy,
            'vehiculosEnTaller' => $vehiculosEnTaller,
            'completadasHoy' => $completadasHoy,
            'porcentajeOrdenesVsAyer' => $porcentajeOrdenesVsAyer,
            'porcentajeIngresosVsAyer' => $porcentajeIngresosVsAyer,
            'porcentajeCompletadasVsAyer' => $porcentajeCompletadasVsAyer,
            'ordenesRecientes' => $ordenesRecientes,
            'pendientes' => $pendientes,
            'enProceso' => $enProceso,
            'completadas' => $completadas
        ]);
    }
}