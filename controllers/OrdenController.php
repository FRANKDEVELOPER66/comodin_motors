<?php

namespace Controllers;

use Exception;
use Model\Orden;
use Model\Cliente;
use Model\Vehiculo;
use Model\InventarioVehiculo;
use Model\DanoVehiculo;
use Model\Tecnico;
use Model\ServicioRealizado;
use Model\CatalogoServicio;
use MVC\Router;

class OrdenController
{
    /**
     * Vista principal - listado de órdenes
     */
    public static function index(Router $router)
    {
        $ordenes = Orden::obtenerOrdenesCompletas();

        $router->render('orden/index', [  // ← sin 's'
            'ordenes' => $ordenes
        ]);
    }


    /**
     * Vista - Nueva orden
     */
    public static function nueva(Router $router)
    {
        $tecnicos = Tecnico::obtenerActivos();

        $router->render('orden/nueva', [
            'tecnicos' => $tecnicos,
            'script' => 'orden/nueva',
            'titulo' => 'Nueva Orden'
        ]);
    }

    /**
     * Vista - Ver/editar orden
     */
    public static function ver(Router $router)
    {
        $id_orden = $_GET['id'] ?? null;

        if (!$id_orden) {
            header('Location: /ordenes');
            return;
        }

        $orden = Orden::obtenerDetalleCompleto($id_orden);

        if (!$orden) {
            header('Location: /ordenes');
            return;
        }

        $inventario = InventarioVehiculo::obtenerPorOrden($id_orden);
        $danos = DanoVehiculo::obtenerPorOrden($id_orden);
        $tecnicos = Tecnico::obtenerActivos();

        $router->render('ordenes/ver', [
            'orden' => $orden,
            'inventario' => $inventario,
            'danos' => $danos,
            'tecnicos' => $tecnicos
        ]);
    }

    // ============================================
    // API ENDPOINTS
    // ============================================

    /**
     * API - Buscar cliente por teléfono
     */
    public static function buscarClienteAPI()
    {
        header('Content-Type: application/json; charset=UTF-8');

        $telefono = $_GET['telefono'] ?? '';

        if (empty($telefono)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Teléfono no proporcionado'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            $clientes = Cliente::buscarPorTelefono($telefono);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Búsqueda exitosa',
                'datos' => $clientes
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar cliente',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * API - Obtener vehículos de un cliente
     */
    public static function obtenerVehiculosAPI()
    {
        header('Content-Type: application/json; charset=UTF-8');

        $id_cliente = $_GET['id_cliente'] ?? null;

        if (!$id_cliente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de cliente no proporcionado'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            $vehiculos = Vehiculo::obtenerPorCliente($id_cliente);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Vehículos encontrados',
                'datos' => $vehiculos
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener vehículos',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * API - Guardar cliente nuevo
     */
    public static function guardarClienteAPI()
    {
        header('Content-Type: application/json; charset=UTF-8');

        $_POST['nombre'] = htmlspecialchars($_POST['nombre']);
        $_POST['telefono'] = htmlspecialchars($_POST['telefono']);
        $_POST['empresa'] = htmlspecialchars($_POST['empresa'] ?? '');

        try {
            $cliente = new Cliente($_POST);
            $resultado = $cliente->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Cliente guardado exitosamente',
                'id_cliente' => $resultado['id']
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar cliente',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * API - Guardar vehículo nuevo
     */
    public static function guardarVehiculoAPI()
    {
        header('Content-Type: application/json; charset=UTF-8');

        $_POST['marca'] = htmlspecialchars($_POST['marca']);
        $_POST['modelo'] = htmlspecialchars($_POST['modelo']);
        $_POST['placas'] = htmlspecialchars($_POST['placas']);

        try {
            $vehiculo = new Vehiculo($_POST);
            $resultado = $vehiculo->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Vehículo guardado exitosamente',
                'id_vehiculo' => $resultado['id']
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar vehículo',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * API - Guardar orden completa
     */
    public static function guardarOrdenAPI()
    {
        header('Content-Type: application/json; charset=UTF-8');

        // Sanitizar datos
        $_POST['trabajo_realizar'] = htmlspecialchars($_POST['trabajo_realizar']);
        $_POST['observaciones'] = htmlspecialchars($_POST['observaciones'] ?? '');

        try {
            // Iniciar transacción
            Orden::getDB()->beginTransaction();

            // 1. Generar número de orden
            $numero_orden = Orden::generarNumeroOrden();
            $_POST['numero_orden'] = $numero_orden;

            // 2. Crear orden
            $orden = new Orden($_POST);
            $resultado_orden = $orden->crear();
            $id_orden = $resultado_orden['id'];

            // 3. Guardar inventario del vehículo
            if (!empty($_POST['inventario'])) {
                $inventario_data = $_POST['inventario'];
                $inventario_data['id_orden'] = $id_orden;

                $inventario = new InventarioVehiculo($inventario_data);
                $inventario->crear();
            }

            // 4. Guardar daños del vehículo
            if (!empty($_POST['danos']) && is_array($_POST['danos'])) {
                foreach ($_POST['danos'] as $dano_data) {
                    $dano_data['id_orden'] = $id_orden;
                    $dano = new DanoVehiculo($dano_data);
                    $dano->crear();
                }
            }

            // 5. Guardar servicios realizados
            if (!empty($_POST['servicios']) && is_string($_POST['servicios'])) {
                $servicios = json_decode($_POST['servicios'], true);

                if ($servicios && is_array($servicios)) {
                    foreach ($servicios as $servicio_data) {
                        $servicio_data['id_orden'] = $id_orden;

                        // Crear objeto ServicioRealizado
                        $servicio = new ServicioRealizado($servicio_data);

                        // El método crear() calculará automáticamente el subtotal
                        $servicio->crear();
                    }
                }
            }

            // 6. Actualizar costo_total de la orden
            if (isset($id_orden)) {
                $total_servicios = ServicioRealizado::obtenerTotalPorOrden($id_orden);

                $sql = "UPDATE ordenes_servicio SET costo_total = ? WHERE id_orden = ?";
                $stmt = Orden::getDB()->prepare($sql);
                $stmt->execute([$total_servicios, $id_orden]);
            }

            // Commit de la transacción
            Orden::getDB()->commit();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Orden creada exitosamente',
                'numero_orden' => $numero_orden,
                'id_orden' => $id_orden
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            // Rollback en caso de error
            Orden::getDB()->rollback();

            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar la orden',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * API - Buscar órdenes
     */
    public static function buscarAPI()
    {
        header('Content-Type: application/json; charset=UTF-8');

        try {
            $filtros = [];

            if (!empty($_GET['estado'])) {
                $filtros['estado'] = $_GET['estado'];
            }

            if (!empty($_GET['fecha_desde'])) {
                $filtros['fecha_desde'] = $_GET['fecha_desde'];
            }

            if (!empty($_GET['fecha_hasta'])) {
                $filtros['fecha_hasta'] = $_GET['fecha_hasta'];
            }

            $ordenes = Orden::obtenerOrdenesCompletas($filtros);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Datos encontrados',
                'datos' => $ordenes
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar órdenes',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * API - Modificar orden
     */
    public static function modificarAPI()
    {
        header('Content-Type: application/json; charset=UTF-8');

        $id = filter_var($_POST['id_orden'], FILTER_SANITIZE_NUMBER_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de orden no válido'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            $orden = Orden::find($id);

            if (!$orden) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Orden no encontrada'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $_POST['trabajo_realizar'] = htmlspecialchars($_POST['trabajo_realizar']);
            $_POST['observaciones'] = htmlspecialchars($_POST['observaciones'] ?? '');

            $orden->sincronizar($_POST);
            $orden->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Orden modificada exitosamente'
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar orden',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * API - Cambiar estado de orden
     */
    public static function cambiarEstadoAPI()
    {
        header('Content-Type: application/json; charset=UTF-8');

        $id = filter_var($_POST['id_orden'], FILTER_SANITIZE_NUMBER_INT);
        $nuevo_estado = $_POST['estado'] ?? '';

        $estados_validos = ['pendiente', 'en_proceso', 'completado', 'entregado', 'cancelado'];

        if (!$id || !in_array($nuevo_estado, $estados_validos)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Datos no válidos'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            $orden = Orden::find($id);

            if (!$orden) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Orden no encontrada'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $orden->estado_orden = $nuevo_estado;
            $orden->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Estado actualizado exitosamente'
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al cambiar estado',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    /**
     * API - Buscar servicios en catálogo
     */
    public static function buscarServiciosAPI()
    {
        header('Content-Type: application/json; charset=UTF-8');

        $termino = $_GET['q'] ?? '';

        if (empty($termino)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Término de búsqueda no proporcionado'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            $servicios = CatalogoServicio::buscar($termino);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Búsqueda exitosa',
                'datos' => $servicios
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar servicios',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
}
