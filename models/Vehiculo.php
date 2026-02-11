<?php

namespace Model;

class Vehiculo extends ActiveRecord
{
    protected static $tabla = 'vehiculos';
    protected static $idTabla = 'id_vehiculo';
    protected static $columnasDB = [
        'id_cliente',
        'marca',
        'modelo',
        'anio',
        'color',
        'placas',
        'numero_serie',
        'kilometraje_inicial',
        'activo'
    ];

    public $id_vehiculo;
    public $id_cliente;
    public $marca;
    public $modelo;
    public $anio;
    public $color;
    public $placas;
    public $numero_serie;
    public $kilometraje_inicial;
    public $activo;

    public function __construct($args = [])
    {
        $this->id_vehiculo = $args['id_vehiculo'] ?? null;
        $this->id_cliente = $args['id_cliente'] ?? null;
        $this->marca = $args['marca'] ?? '';
        $this->modelo = $args['modelo'] ?? '';
        $this->anio = $args['anio'] ?? date('Y');
        $this->color = $args['color'] ?? '';
        $this->placas = $args['placas'] ?? '';
        $this->numero_serie = $args['numero_serie'] ?? null;
        $this->kilometraje_inicial = $args['kilometraje_inicial'] ?? 0;
        $this->activo = $args['activo'] ?? 1;
    }

    /**
     * Obtener vehículos de un cliente
     */
    public static function obtenerPorCliente($id_cliente)
    {
        $sql = "SELECT * FROM vehiculos 
                WHERE id_cliente = ? 
                AND activo = 1
                ORDER BY fecha_registro DESC";
        
        return self::fetchArray($sql, [$id_cliente]);
    }

    /**
     * Buscar vehículo por placas
     */
    public static function buscarPorPlacas($placas)
    {
        $sql = "SELECT v.*, c.nombre as propietario, c.telefono
                FROM vehiculos v
                INNER JOIN clientes c ON v.id_cliente = c.id_cliente
                WHERE v.placas = ? 
                AND v.activo = 1";
        
        $resultado = self::fetchArray($sql, [$placas]);
        return $resultado[0] ?? null;
    }
}