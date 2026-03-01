<?php

namespace Model;

class ServicioRealizado extends ActiveRecord
{
    protected static $tabla = 'servicios_realizados';
    protected static $idTabla = 'id_servicio_realizado';
    protected static $columnasDB = [
        'id_orden',
        'descripcion',
        'costo',
        'precio_unitario',
        'subtotal',
        'cantidad',
        'tipo'
    ];

    public $id_servicio_realizado;
    public $id_orden;
    public $descripcion;
    public $costo;
    public $precio_unitario;
    public $subtotal;
    public $cantidad;
    public $tipo;

    public function __construct($args = [])
    {
        $this->id_servicio_realizado = $args['id_servicio_realizado'] ?? null;
        $this->id_orden              = $args['id_orden']    ?? null;
        $this->descripcion           = $args['descripcion'] ?? '';
        $this->cantidad              = $args['cantidad']    ?? 1;
        $this->tipo                  = $args['tipo']        ?? 'servicio';

        // ← aceptar 'costo' o 'precio_unitario' indistintamente
        $this->precio_unitario = floatval(
            $args['precio_unitario'] ?? $args['costo'] ?? 0.00
        );
        $this->costo    = $this->precio_unitario;
        $this->subtotal = $args['subtotal'] ?? 0.00;
    }

    /**
     * Validaciones antes de guardar
     */
    public function validar()
    {
        if (!$this->descripcion) {
            self::$alertas['error'][] = 'La descripción del servicio es obligatoria';
        }

        if (!$this->cantidad || $this->cantidad <= 0) {
            self::$alertas['error'][] = 'La cantidad debe ser mayor a 0';
        }

        if (!$this->precio_unitario || $this->precio_unitario < 0) {
            self::$alertas['error'][] = 'El precio unitario no puede ser negativo';
        }

        return self::$alertas;
    }

    /**
     * Calcular subtotal antes de guardar
     */
    public function calcularSubtotal()
    {
        $this->subtotal = floatval($this->cantidad) * floatval($this->precio_unitario);
        // Mantener compatibilidad con campo costo
        $this->costo = $this->precio_unitario;
        return $this->subtotal;
    }

    /**
     * Sobrescribir crear para calcular subtotal automáticamente
     */
    public function crear()
    {
        $this->calcularSubtotal();
        return parent::crear();
    }

    /**
     * Sobrescribir actualizar para recalcular subtotal
     */
    public function actualizar()
    {
        $this->calcularSubtotal();
        return parent::actualizar();
    }

    /**
     * Obtener servicios de una orden
     */
    public static function obtenerPorOrden($id_orden)
    {
        $sql = "SELECT * FROM " . self::$tabla . " 
                WHERE id_orden = ? 
                ORDER BY id_servicio_realizado";

        return self::fetchArray($sql, [$id_orden]);
    }

    /**
     * Obtener total de servicios de una orden
     */
    public static function obtenerTotalPorOrden($id_orden)
    {
        $sql = "SELECT SUM(subtotal) as total 
                FROM " . self::$tabla . " 
                WHERE id_orden = ?";

        $resultado = self::fetchFirst($sql, [$id_orden]);
        return $resultado ? floatval($resultado['total']) : 0.00;
    }

    /**
     * Eliminar todos los servicios de una orden
     */
    public static function eliminarPorOrden($id_orden)
    {
        $sql = "DELETE FROM " . self::$tabla . " WHERE id_orden = ?";
        $stmt = self::getDB()->prepare($sql);
        return $stmt->execute([$id_orden]);
    }

    /**
     * Contar servicios de una orden
     */
    public static function contarPorOrden($id_orden)
    {
        $sql = "SELECT COUNT(*) as total 
                FROM " . self::$tabla . " 
                WHERE id_orden = ?";

        $resultado = self::fetchFirst($sql, [$id_orden]);
        return $resultado ? intval($resultado['total']) : 0;
    }
}
