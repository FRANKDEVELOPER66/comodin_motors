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
        $sql = "CALL sp_generar_numero_orden(@numero)";
        self::$db->query($sql);
        
        $resultado = self::$db->query("SELECT @numero as numero_orden");
        $fila = $resultado->fetch_assoc();
        
        return $fila['numero_orden'];
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
}