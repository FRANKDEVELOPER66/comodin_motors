<?php

namespace Model;

class InventarioVehiculo extends ActiveRecord
{
    protected static $tabla = 'inventario_vehiculo';
    protected static $idTabla = 'id_inventario';
    protected static $columnasDB = [
        'id_orden',
        'gato',
        'herramientas',
        'triangulos',
        'tapetes',
        'llanta_refaccion',
        'extintor',
        'antena',
        'emblemas',
        'tapones_rueda',
        'cables',
        'estereo',
        'encendedor',
        'otros'
    ];

    public $id_inventario;
    public $id_orden;
    public $gato;
    public $herramientas;
    public $triangulos;
    public $tapetes;
    public $llanta_refaccion;
    public $extintor;
    public $antena;
    public $emblemas;
    public $tapones_rueda;
    public $cables;
    public $estereo;
    public $encendedor;
    public $otros;

    public function __construct($args = [])
    {
        $this->id_inventario = $args['id_inventario'] ?? null;
        $this->id_orden = $args['id_orden'] ?? null;
        $this->gato = $args['gato'] ?? 0;
        $this->herramientas = $args['herramientas'] ?? 0;
        $this->triangulos = $args['triangulos'] ?? 0;
        $this->tapetes = $args['tapetes'] ?? 0;
        $this->llanta_refaccion = $args['llanta_refaccion'] ?? 0;
        $this->extintor = $args['extintor'] ?? 0;
        $this->antena = $args['antena'] ?? 0;
        $this->emblemas = $args['emblemas'] ?? 0;
        $this->tapones_rueda = $args['tapones_rueda'] ?? 0;
        $this->cables = $args['cables'] ?? 0;
        $this->estereo = $args['estereo'] ?? 0;
        $this->encendedor = $args['encendedor'] ?? 0;
        $this->otros = $args['otros'] ?? null;
    }

    /**
     * Obtener inventario de una orden
     */
    public static function obtenerPorOrden($id_orden)
    {
        $sql = "SELECT * FROM inventario_vehiculo WHERE id_orden = ?";
        $resultado = self::fetchArray($sql, [$id_orden]);
        return $resultado[0] ?? null;
    }
}