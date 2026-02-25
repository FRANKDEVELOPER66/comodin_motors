<?php

namespace Model;

class Orden extends ActiveRecord
{
    protected static $tabla = 'ordenes_servicio';
    protected static $idTabla = 'id_orden';
    protected static $columnasDB = [
        'numero_orden',
        'id_vehiculo',
        'id_cliente',
        'fecha_orden',
        'hora_ingreso',
        'kilometraje_actual',
        'ingreso_grua',
        'trabajo_realizar',
        'observaciones',
        'proximo_servicio_km',
        'nivel_combustible',
        'estado_orden',
        'costo_total'
    ];

    public $id_orden;
    public $numero_orden;
    public $id_vehiculo;
    public $id_cliente;
    public $fecha_orden;
    public $hora_ingreso;
    public $kilometraje_actual;
    public $ingreso_grua;
    public $trabajo_realizar;
    public $observaciones;
    public $proximo_servicio_km;
    public $nivel_combustible;
    public $estado_orden;
    public $costo_total;

    public function __construct($args = [])
    {
        $this->id_orden = $args['id_orden'] ?? null;
        $this->numero_orden = $args['numero_orden'] ?? '';
        $this->id_vehiculo = $args['id_vehiculo'] ?? null;
        $this->id_cliente = $args['id_cliente'] ?? null;
        $this->fecha_orden = $args['fecha_orden'] ?? date('Y-m-d');
        $this->hora_ingreso = $args['hora_ingreso'] ?? date('H:i:s');
        $this->kilometraje_actual = $args['kilometraje_actual'] ?? 0;
        $this->ingreso_grua = $args['ingreso_grua'] ?? 0;
        $this->trabajo_realizar = $args['trabajo_realizar'] ?? '';
        $this->observaciones = $args['observaciones'] ?? '';
        $this->proximo_servicio_km = $args['proximo_servicio_km'] ?? null;
        $this->nivel_combustible = $args['nivel_combustible'] ?? '1/2';
        $this->estado_orden = $args['estado_orden'] ?? 'pendiente';
        $this->costo_total = $args['costo_total'] ?? 0.00;
    }

    /**
     * Generar siguiente número de orden
     */
    public static function generarNumeroOrden()
    {
        $db = self::$db;
        $stmt = $db->query("SELECT MAX(CAST(numero_orden AS UNSIGNED)) as ultimo FROM ordenes_servicio");
        $row  = $stmt->fetch(\PDO::FETCH_ASSOC);
        $siguiente = (intval($row['ultimo'] ?? 0)) + 1;
        return str_pad($siguiente, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Obtener todas las órdenes con datos relacionados
     */
    public static function obtenerOrdenesCompletas($filtros = [])
    {
        $sql = "SELECT 
                    o.*,
                    c.nombre AS cliente_nombre,
                    c.telefono AS cliente_telefono,
                    c.empresa AS cliente_empresa,
                    v.marca,
                    v.modelo,
                    v.anio,
                    v.color,
                    v.placas
                FROM ordenes_servicio o
                INNER JOIN clientes c ON o.id_cliente = c.id_cliente
                INNER JOIN vehiculos v ON o.id_vehiculo = v.id_vehiculo
                WHERE 1=1";

        $params = [];

        // Aplicar filtros opcionales
        if (!empty($filtros['estado'])) {
            $sql .= " AND o.estado_orden = ?";
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND o.fecha_orden >= ?";
            $params[] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND o.fecha_orden <= ?";
            $params[] = $filtros['fecha_hasta'];
        }

        $sql .= " ORDER BY o.fecha_orden DESC, o.hora_ingreso DESC";

        return self::fetchArray($sql, $params);
    }

    /**
     * Obtener órdenes del día
     */
    public static function obtenerOrdenesDelDia()
    {
        $sql = "SELECT 
                    o.*,
                    c.nombre AS cliente_nombre,
                    c.telefono AS cliente_telefono,
                    v.placas,
                    v.marca,
                    v.modelo
                FROM ordenes_servicio o
                INNER JOIN clientes c ON o.id_cliente = c.id_cliente
                INNER JOIN vehiculos v ON o.id_vehiculo = v.id_vehiculo
                WHERE o.fecha_orden = CURDATE()
                ORDER BY o.hora_ingreso DESC";

        return self::fetchArray($sql);
    }

    /**
     * Obtener órdenes pendientes
     */
    public static function obtenerOrdenesPendientes()
    {
        $sql = "SELECT * FROM vista_ordenes_completas 
                WHERE estado_orden IN ('pendiente', 'en_proceso')
                ORDER BY fecha_orden DESC, hora_ingreso ASC";

        return self::fetchArray($sql);
    }

    /**
     * Obtener detalle completo de una orden
     */
    public static function obtenerDetalleCompleto($id_orden)
    {
        $sql = "SELECT 
                    o.*,
                    c.nombre AS cliente_nombre,
                    c.telefono AS cliente_telefono,
                    c.empresa AS cliente_empresa,
                    c.direccion AS cliente_direccion,
                    c.email AS cliente_email,
                    v.marca,
                    v.modelo,
                    v.anio,
                    v.color,
                    v.placas,
                    v.numero_serie,
                    v.kilometraje_inicial
                FROM ordenes_servicio o
                INNER JOIN clientes c ON o.id_cliente = c.id_cliente
                INNER JOIN vehiculos v ON o.id_vehiculo = v.id_vehiculo
                WHERE o.id_orden = ?";

        $resultado = self::fetchArray($sql, [$id_orden]);
        return $resultado[0] ?? null;
    }

    /**
     * Contar órdenes por estado(s)
     */
    public static function contarPorEstado($estados = [])
    {
        if (empty($estados)) {
            return 0;
        }

        // Escapar valores manualmente para evitar SQL injection
        $estadosEscapados = array_map(function ($estado) {
            return self::$db->quote($estado);
        }, $estados);

        $estadosStr = implode(',', $estadosEscapados);

        $sql = "SELECT COUNT(*) as total 
            FROM ordenes_servicio 
            WHERE estado_orden IN ($estadosStr)";

        $resultado = self::consultarSQL($sql);
        return $resultado ? intval($resultado[0]->total) : 0;
    }

    /**
     * Contar completadas hoy
     */
    public static function contarCompletadasHoy()
    {
        $sql = "SELECT COUNT(*) as total 
                FROM ordenes_servicio 
                WHERE estado_orden = 'completado' 
                AND DATE(fecha_orden) = CURDATE()";

        $resultado = self::fetchFirst($sql);
        return $resultado ? intval($resultado['total']) : 0;
    }

    /**
     * Contar órdenes por fecha
     */
    public static function contarPorFecha($fecha)
    {
        $fecha = self::$db->quote($fecha);
        $sql = "SELECT COUNT(*) as total 
            FROM ordenes_servicio 
            WHERE DATE(fecha_orden) = $fecha";

        $resultado = self::consultarSQL($sql);
        return $resultado ? intval($resultado[0]->total) : 0;
    }

    /**
     * Sumar ingresos por fecha
     */
    public static function sumarIngresosPorFecha($fecha)
    {
        $fecha = self::$db->quote($fecha);
        $sql = "SELECT SUM(costo_total) as total 
            FROM ordenes_servicio 
            WHERE DATE(fecha_orden) = $fecha";

        $resultado = self::consultarSQL($sql);
        return $resultado && $resultado[0]->total ? floatval($resultado[0]->total) : 0;
    }

    /**
     * Contar completadas por fecha
     */
    public static function contarCompletadasPorFecha($fecha)
    {
        $fecha = self::$db->quote($fecha);
        $sql = "SELECT COUNT(*) as total 
            FROM ordenes_servicio 
            WHERE estado_orden = 'completado' 
            AND DATE(fecha_orden) = $fecha";

        $resultado = self::consultarSQL($sql);
        return $resultado ? intval($resultado[0]->total) : 0;
    }

    /**
     * Obtener órdenes recientes
     */
    public static function obtenerRecientes($limite = 5)
    {
        $sql = "SELECT 
                o.*,
                c.nombre AS cliente_nombre,
                v.marca,
                v.modelo,
                v.anio,
                v.placas
            FROM ordenes_servicio o
            INNER JOIN clientes c ON o.id_cliente = c.id_cliente
            INNER JOIN vehiculos v ON o.id_vehiculo = v.id_vehiculo
            ORDER BY o.fecha_orden DESC, o.hora_ingreso DESC
            LIMIT " . intval($limite);

        return self::fetchArray($sql);
    }
}
