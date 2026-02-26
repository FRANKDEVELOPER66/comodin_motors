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
    /**
     * Vista - Ver/editar orden
     */
    public static function ver(Router $router)
    {
        $id_orden = $_GET['id'] ?? null;

        if (!$id_orden) {
            header('Location: /comodin_motors/orden');
            return;
        }

        $orden = Orden::obtenerDetalleCompleto($id_orden);

        if (!$orden) {
            header('Location: /comodin_motors/orden');
            return;
        }

        $tecnicos = Tecnico::obtenerActivos();

        $router->render('orden/ver', [
            'orden'     => $orden,
            'tecnicos'  => $tecnicos
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
                'detalle' => $e->getMessage(),
                'post_recibido' => $_POST  // ← temporal para debug
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * API - Guardar orden completa
     */
    public static function guardarOrdenAPI()
    {
        header('Content-Type: application/json; charset=UTF-8');

        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            throw new \Exception("PHP Error [$errno]: $errstr en $errfile:$errline");
        });

        try {
            if (empty($_POST['id_cliente'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'id_cliente no recibido',
                    'post' => $_POST
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $_POST['trabajo_realizar'] = htmlspecialchars($_POST['trabajo_realizar'] ?? '');
            $_POST['observaciones']    = htmlspecialchars($_POST['observaciones'] ?? '');

            Orden::getDB()->beginTransaction();

            // 0. Si no hay id_vehiculo, crear el vehículo primero
            if (empty($_POST['id_vehiculo'])) {
                $vehiculo                     = new Vehiculo();
                $vehiculo->id_cliente         = $_POST['id_cliente'];
                $vehiculo->marca              = $_POST['marca']             ?? '';
                $vehiculo->modelo             = $_POST['modelo']            ?? '';
                $vehiculo->anio               = $_POST['anio']              ?? date('Y');
                $vehiculo->color              = $_POST['color']             ?? '';
                $vehiculo->placas             = $_POST['placas']            ?? '';
                $vehiculo->numero_serie       = $_POST['numero_serie']      ?? '';
                $vehiculo->kilometraje_inicial = $_POST['kilometraje_actual'] ?? 0;
                $vehiculo->activo             = 1;

                $resV = $vehiculo->guardar();

                if (empty($resV['id'])) {
                    throw new \Exception('No se pudo crear el vehículo (id vacío)');
                }

                $_POST['id_vehiculo'] = $resV['id'];
            }

            // 1. Generar número de orden
            $numero_orden        = Orden::generarNumeroOrden();
            $_POST['numero_orden'] = $numero_orden;

            // 2. Crear orden
            $orden           = new Orden($_POST);
            $resultado_orden = $orden->crear();
            $id_orden        = $resultado_orden['id'];

            if (empty($id_orden)) {
                throw new \Exception('No se pudo crear la orden (id vacío)');
            }

            // 3. Guardar inventario
            if (!empty($_POST['inventario'])) {
                $inventario_data             = $_POST['inventario'];
                $inventario_data['id_orden'] = $id_orden;
                $inventario                  = new InventarioVehiculo($inventario_data);
                $inventario->crear();
            }

            // 4. Guardar daños
            $danos = json_decode($_POST['danos'] ?? '[]', true);
            // DEBUG TEMPORAL
            error_log("JSON daños recibido: " . ($_POST['danos'] ?? 'VACÍO'));
            error_log("Daños decodificados: " . count($danos ?? []));
            if (!empty($danos) && is_array($danos)) {
                foreach ($danos as $dano_data) {
                    $dano_data['id_orden'] = $id_orden;

                    // ← AGREGAR ESTAS LÍNEAS
                    $dano_data['coordenada_x'] = floatval($dano_data['x'] ?? $dano_data['coordenada_x'] ?? 0);
                    $dano_data['coordenada_y'] = floatval($dano_data['y'] ?? $dano_data['coordenada_y'] ?? 0);
                    $dano_data['ubicacion'] = $dano_data['ubicacion'] ?? 'frontal';
                    $dano_data['descripcion'] = $dano_data['descripcion'] ?? '';
                    $dano_data['tipo_dano'] = $dano_data['tipo_dano'] ?? 'otro';
                    // ← FIN

                    $dano = new DanoVehiculo($dano_data);
                    $dano->crear();
                }
            }

            // 5. Guardar servicios
            $servicios = json_decode($_POST['servicios'] ?? '[]', true);
            if (!empty($servicios) && is_array($servicios)) {
                foreach ($servicios as $servicio_data) {
                    $servicio_data['id_orden'] = $id_orden;
                    $servicio                  = new ServicioRealizado($servicio_data);
                    $servicio->crear();
                }
            }

            // 6. Actualizar costo_total
            $total_servicios = ServicioRealizado::obtenerTotalPorOrden($id_orden);
            $stmt = Orden::getDB()->prepare(
                "UPDATE ordenes_servicio SET costo_total = ? WHERE id_orden = ?"
            );
            $stmt->execute([$total_servicios, $id_orden]);

            Orden::getDB()->commit();

            http_response_code(200);
            echo json_encode([
                'codigo'       => 1,
                'mensaje'      => 'Orden creada exitosamente',
                'numero_orden' => $numero_orden,
                'id_orden'     => $id_orden
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            // Rollback solo si hay transacción activa
            try {
                Orden::getDB()->rollback();
            } catch (\Exception $ex) {
            }

            http_response_code(500);
            echo json_encode([
                'codigo'  => 0,
                'mensaje' => 'Error al guardar la orden',
                'detalle' => $e->getMessage(),
                'post'    => $_POST          // ← quitar en producción
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
