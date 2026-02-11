<?php

namespace Model;

class Cliente extends ActiveRecord
{
    protected static $tabla = 'clientes';
    protected static $idTabla = 'id_cliente';
    protected static $columnasDB = [
        'nombre',
        'telefono',
        'empresa',
        'direccion',
        'email',
        'activo'
    ];

    public $id_cliente;
    public $nombre;
    public $telefono;
    public $empresa;
    public $direccion;
    public $email;
    public $activo;

    public function __construct($args = [])
    {
        $this->id_cliente = $args['id_cliente'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->empresa = $args['empresa'] ?? null;
        $this->direccion = $args['direccion'] ?? null;
        $this->email = $args['email'] ?? null;
        $this->activo = $args['activo'] ?? 1;
    }

    /**
     * Buscar clientes por teléfono
     */
    public static function buscarPorTelefono($telefono)
    {
        $sql = "SELECT * FROM clientes 
                WHERE telefono LIKE ? 
                AND activo = 1
                ORDER BY nombre";
        
        $param = '%' . $telefono . '%';
        return self::fetchArray($sql, [$param]);
    }

    /**
     * Buscar clientes por nombre
     */
    public static function buscarPorNombre($nombre)
    {
        $sql = "SELECT * FROM clientes 
                WHERE nombre LIKE ? 
                AND activo = 1
                ORDER BY nombre";
        
        $param = '%' . $nombre . '%';
        return self::fetchArray($sql, [$param]);
    }

    /**
     * Obtener todos los clientes activos
     */
    public static function obtenerActivos()
    {
        $sql = "SELECT * FROM clientes 
                WHERE activo = 1 
                ORDER BY nombre";
        
        return self::fetchArray($sql);
    }
}