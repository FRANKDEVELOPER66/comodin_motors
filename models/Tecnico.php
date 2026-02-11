<?php

namespace Model;


class Tecnico extends ActiveRecord
{
    protected static $tabla = 'tecnicos';
    protected static $idTabla = 'id_tecnico';
    protected static $columnasDB = [
        'nombre_completo',
        'telefono',
        'especialidad',
        'firma_digital',
        'activo'
    ];

    public $id_tecnico;
    public $nombre_completo;
    public $telefono;
    public $especialidad;
    public $firma_digital;
    public $activo;

    public function __construct($args = [])
    {
        $this->id_tecnico = $args['id_tecnico'] ?? null;
        $this->nombre_completo = $args['nombre_completo'] ?? '';
        $this->telefono = $args['telefono'] ?? null;
        $this->especialidad = $args['especialidad'] ?? null;
        $this->firma_digital = $args['firma_digital'] ?? null;
        $this->activo = $args['activo'] ?? 1;
    }

    /**
     * Obtener técnicos activos
     */
    public static function obtenerActivos()
    {
        $sql = "SELECT * FROM tecnicos 
                WHERE activo = 1 
                ORDER BY nombre_completo";

        return self::fetchArray($sql);
    }
}
