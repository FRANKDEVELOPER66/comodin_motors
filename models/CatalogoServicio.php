<?php

namespace Model;

class CatalogoServicio extends ActiveRecord
{
    protected static $tabla = 'catalogo_servicios';
    protected static $idTabla = 'id_catalogo_servicio';
    protected static $columnasDB = [
        'codigo',
        'descripcion',
        'precio_sugerido',
        'categoria',
        'activo'
    ];

    public $id_catalogo_servicio;
    public $codigo;
    public $descripcion;
    public $precio_sugerido;
    public $categoria;
    public $activo;

    public function __construct($args = [])
    {
        $this->id_catalogo_servicio = $args['id_catalogo_servicio'] ?? null;
        $this->codigo = $args['codigo'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->precio_sugerido = $args['precio_sugerido'] ?? 0.00;
        $this->categoria = $args['categoria'] ?? 'otro';
        $this->activo = $args['activo'] ?? 1;
    }

    /**
     * Buscar servicios por término
     */
    public static function buscar($termino)
    {
        $termino = '%' . $termino . '%';
        $sql = "SELECT * FROM " . self::$tabla . " 
                WHERE activo = 1 
                AND (codigo LIKE ? OR descripcion LIKE ?)
                ORDER BY codigo
                LIMIT 10";

        return self::fetchArray($sql, [$termino, $termino]);
    }

    /**
     * Obtener todos los servicios activos
     */
    public static function obtenerActivos()
    {
        $sql = "SELECT * FROM " . self::$tabla . " 
                WHERE activo = 1 
                ORDER BY categoria, descripcion";

        return self::fetchArray($sql);
    }

    /**
     * Obtener por categoría
     */
    public static function obtenerPorCategoria($categoria)
    {
        $sql = "SELECT * FROM " . self::$tabla . " 
                WHERE activo = 1 AND categoria = ?
                ORDER BY descripcion";

        return self::fetchArray($sql, [$categoria]);
    }
}
