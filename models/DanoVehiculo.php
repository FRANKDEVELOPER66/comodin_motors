<?php

namespace Model;

class DanoVehiculo extends ActiveRecord
{
    protected static $tabla = 'danos_vehiculo';
    protected static $idTabla = 'id_dano';
    protected static $columnasDB = [
        'id_orden',
        'ubicacion',
        'descripcion',
        'tipo_dano',
        'coordenada_x',
        'coordenada_y',
        'foto_url'
    ];

    public $id_dano;
    public $id_orden;
    public $ubicacion;
    public $descripcion;
    public $tipo_dano;
    public $coordenada_x;
    public $coordenada_y;
    public $foto_url;

    public function __construct($args = [])
    {
        $this->id_dano = $args['id_dano'] ?? null;
        $this->id_orden = $args['id_orden'] ?? null;
        $this->ubicacion = $args['ubicacion'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->tipo_dano = $args['tipo_dano'] ?? 'otro';
        $this->coordenada_x = $args['coordenada_x'] ?? null;
        $this->coordenada_y = $args['coordenada_y'] ?? null;
        $this->foto_url = $args['foto_url'] ?? null;
    }

    /**
     * Obtener daños de una orden
     */
    public static function obtenerPorOrden($id_orden)
    {
        $sql = "SELECT * FROM danos_vehiculo 
                WHERE id_orden = ?
                ORDER BY id_dano";
        
        return self::fetchArray($sql, [$id_orden]);
    }
}