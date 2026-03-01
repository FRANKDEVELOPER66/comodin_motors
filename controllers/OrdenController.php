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

        $router->render('orden/index', [
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
            'script'   => 'orden/nueva',
            'titulo'   => 'Nueva Orden'
        ]);
    }

    /**
     * Vista - Ver orden
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

        $tecnicos   = Tecnico::obtenerActivos();
        $servicios  = ServicioRealizado::obtenerPorOrden($id_orden);
        $inventario = InventarioVehiculo::obtenerPorOrden($id_orden);
        $danos      = DanoVehiculo::obtenerPorOrden($id_orden);

        $router->render('orden/ver', [
            'orden'      => $orden,
            'tecnicos'   => $tecnicos,
            'servicios'  => $servicios,
            'inventario' => $inventario,
            'danos'      => $danos
        ]);
    }

    /**
     * PDF - Generar orden de servicio con wkhtmltopdf
     * Ruta: /comodin_motors/orden/pdf?id=XX
     */
    public static function pdf(Router $router)
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

        $servicios  = ServicioRealizado::obtenerPorOrden($id_orden);
        $inventario = InventarioVehiculo::obtenerPorOrden($id_orden);
        $danos      = DanoVehiculo::obtenerPorOrden($id_orden);

        $html = self::generarHTMLPdf($orden, $servicios, $inventario, $danos);

        // ── Guardar HTML en archivo temporal ─────────────────────────────
        $tmpHtml = tempnam(sys_get_temp_dir(), 'orden_') . '.html';
        $tmpPdf  = tempnam(sys_get_temp_dir(), 'orden_') . '.pdf';
        file_put_contents($tmpHtml, $html);

        // ── Ejecutar wkhtmltopdf ──────────────────────────────────────────
        $cmd = sprintf(
            'wkhtmltopdf --page-size Letter --margin-top 8mm --margin-bottom 8mm ' .
                '--margin-left 12mm --margin-right 12mm ' .
                '--enable-local-file-access ' .
                '--print-media-type ' .
                '--disable-smart-shrinking ' .
                '--zoom 1.0 ' .
                '--dpi 150 ' .
                '%s %s 2>&1',
            escapeshellarg($tmpHtml),
            escapeshellarg($tmpPdf)
        );

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($tmpPdf) || filesize($tmpPdf) === 0) {
            // Limpiar temporales
            @unlink($tmpHtml);
            @unlink($tmpPdf);

            http_response_code(500);
            echo '<h2>Error al generar PDF</h2>';
            echo '<p>Código de retorno: ' . $returnCode . '</p>';
            echo '<pre>' . htmlspecialchars(implode("\n", $output)) . '</pre>';
            echo '<hr><h3>Para instalar wkhtmltopdf en Docker:</h3>';
            echo '<code>apt-get update && apt-get install -y wkhtmltopdf</code>';
            exit;
        }

        // ── Enviar PDF al navegador ───────────────────────────────────────
        $numero = $orden['numero_orden'] ?? $id_orden;
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="Orden_' . $numero . '.pdf"');
        header('Content-Length: ' . filesize($tmpPdf));
        header('Cache-Control: private, max-age=0, must-revalidate');

        readfile($tmpPdf);

        // Limpiar temporales
        @unlink($tmpHtml);
        @unlink($tmpPdf);
        exit;
    }

    /**
     * Genera el HTML — ahora puede usar CSS moderno completo
     * porque wkhtmltopdf usa WebKit real
     */
    private static function generarHTMLPdf(array $orden, $servicios, $inventario, $danos): string
    {
        // ── Paleta ────────────────────────────────────────────────────────
        $NEGRO       = '#0d0d0d';
        $VERDE       = '#1a7a3c';
        $VERDE_CLARO = '#e8f5ed';
        $GRIS_OSC    = '#2c2c2c';
        $GRIS_MED    = '#555555';
        $GRIS_SUV    = '#888888';
        $GRIS_LINE   = '#cccccc';
        $GRIS_BG     = '#f4f4f4';
        $BLANCO      = '#ffffff';
        $ROJO        = '#b91c1c';
        $AMBAR       = '#b45309';
        $AMBAR_BG    = '#fffbeb';

        // ── Logo ──────────────────────────────────────────────────────────
        $logoPath = $_SERVER['DOCUMENT_ROOT'] . '/comodin_motors/public/images/1.png';
        $logoHtml = file_exists($logoPath)
            ? "<img src='file://{$logoPath}' style='height:100px; width:auto; display:block;' />"
            : "<span style='color:#ffffff; font: size 40px;px; font-weight:bold;'>&#9889;</span>";

        // ── Mapa vista → archivo ──────────────────────────────────────────
        $vistaArchivo = [
            'frontal'     => 'front.png',
            'front'       => 'front.png',
            'trasero'     => 'back.png',
            'back'        => 'back.png',
            'lateral'     => 'left.png',
            'lateral_izq' => 'left.png',
            'lateral_der' => 'rigth.png',
            'left'        => 'left.png',
            'right'       => 'rigth.png',
            'techo'       => 'top.png',
            'top'         => 'top.png',
        ];

        // ── Estado ────────────────────────────────────────────────────────
        $estados = [
            'pendiente'  => ['bg' => '#fef3c7', 'color' => '#92400e', 'label' => 'PENDIENTE'],
            'en_proceso' => ['bg' => '#dbeafe', 'color' => '#1e40af', 'label' => 'EN PROCESO'],
            'completado' => ['bg' => '#d1fae5', 'color' => '#065f46', 'label' => 'COMPLETADO'],
            'entregado'  => ['bg' => '#e0e7ff', 'color' => '#3730a3', 'label' => 'ENTREGADO'],
            'cancelado'  => ['bg' => '#fee2e2', 'color' => '#991b1b', 'label' => 'CANCELADO'],
        ];
        $est      = $estados[$orden['estado_orden'] ?? 'pendiente'] ?? $estados['pendiente'];
        $estBg    = $est['bg'];
        $estColor = $est['color'];
        $estLabel = $est['label'];

        // ── Combustible ───────────────────────────────────────────────────
        $fuelMap   = ['E' => 0, '1/4' => 25, '1/2' => 50, '3/4' => 75, 'F' => 100];
        $fuelPct   = $fuelMap[$orden['nivel_combustible'] ?? '1/2'] ?? 50;
        $fuelColor = $fuelPct <= 25 ? '#dc2626' : ($fuelPct <= 50 ? '#ea580c' : $VERDE);
        $fuelRest  = 100 - $fuelPct;

        // ── Datos ─────────────────────────────────────────────────────────
        $numero           = htmlspecialchars($orden['numero_orden'] ?? '---');
        $fecha            = date('d/m/Y', strtotime($orden['fecha_orden'] ?? 'now'));
        $hora             = substr($orden['hora_ingreso'] ?? '', 0, 5);
        $grua             = ($orden['ingreso_grua'] ?? 0) ? 'SÍ' : 'NO';
        $proxKm           = !empty($orden['proximo_servicio_km']) ? number_format($orden['proximo_servicio_km']) . ' km' : '—';
        $clienteNombre    = htmlspecialchars($orden['cliente_nombre']    ?? '—');
        $clienteTelefono  = htmlspecialchars($orden['cliente_telefono']  ?? '—');
        $clienteEmpresa   = htmlspecialchars($orden['cliente_empresa']   ?? '—');
        $clienteDireccion = htmlspecialchars($orden['cliente_direccion'] ?? '—');
        $vehiculo         = htmlspecialchars(trim(($orden['marca'] ?? '') . ' ' . ($orden['modelo'] ?? '') . ' ' . ($orden['anio'] ?? '')));
        $color            = htmlspecialchars($orden['color']        ?? '—');
        $placas           = strtoupper(htmlspecialchars($orden['placas'] ?? '—'));
        $serie            = htmlspecialchars($orden['numero_serie'] ?? '—');
        $km               = number_format($orden['kilometraje_actual'] ?? 0) . ' km';
        $combustible      = htmlspecialchars($orden['nivel_combustible'] ?? '—');
        $trabajo          = nl2br(htmlspecialchars($orden['trabajo_realizar'] ?? ''));
        $obs              = htmlspecialchars($orden['observaciones'] ?? '');

        // ── Helper: celda ─────────────────────────────────────────────────
        $cel = function (string $label, string $value, int $span = 1, string $vColor = '')
        use ($NEGRO, $GRIS_SUV, $GRIS_LINE, $GRIS_BG): string {
            $vc = $vColor ?: $NEGRO;
            return
                "<td colspan='{$span}' style='padding:0; border:1px solid {$GRIS_LINE}; vertical-align:top;'>" .
                "<table width='100%' style='border-collapse:collapse;'>" .
                "<tr><td style='background:{$GRIS_BG}; padding:3px 7px; font-size:16px;" .
                "color:{$GRIS_SUV}; text-transform:uppercase; letter-spacing:0.8px;" .
                "border-bottom:1px solid {$GRIS_LINE};'>{$label}</td></tr>" .
                "<tr><td style='padding:5px 7px; font-size:20px; font-weight:bold; color:{$vc};'>{$value}</td></tr>" .
                "</table></td>";
        };

        // ── Helper: título sección ────────────────────────────────────────
        $secTit = function (string $icon, string $titulo, string $acento = '')
        use ($NEGRO, $VERDE, $BLANCO): string {
            $ac = $acento ?: $VERDE;
            return
                "<table width='100%' style='border-collapse:collapse; margin-bottom:10px;'><tr>" .
                "<td style='background:{$NEGRO}; border-left:6px solid {$ac};" .
                "padding:6px 12px; font-size:16px; font-weight:bold;" .
                "color:{$BLANCO}; text-transform:uppercase; letter-spacing:1.5px;'>" .
                "{$icon} &nbsp; {$titulo}</td></tr></table>";
        };

        // ── Servicios ─────────────────────────────────────────────────────
        $totalCalculado = 0;
        $serviciosRows  = '';
        if (!empty($servicios)) {
            foreach ($servicios as $i => $s) {
                $sub             = floatval($s['subtotal'] ?? (floatval($s['costo'] ?? 0) * intval($s['cantidad'] ?? 1)));
                $totalCalculado += $sub;
                $rowBg = ($i % 2 === 0) ? $BLANCO : $GRIS_BG;
                $serviciosRows .=
                    "<tr style='background:{$rowBg};'>" .
                    "<td style='padding:7px 8px; border:1px solid {$GRIS_LINE}; text-align:center; font-size:16px; color:{$GRIS_SUV}; width:4%;'>" . ($i + 1) . "</td>" .
                    "<td style='padding:7px 10px; border:1px solid {$GRIS_LINE}; font-size:18px; color:{$NEGRO}; width:52%;'>" . htmlspecialchars($s['descripcion'] ?? '') . "</td>" .
                    "<td style='padding:7px 8px; border:1px solid {$GRIS_LINE}; text-align:center; font-size:16px; color:{$NEGRO}; width:10%;'>" . intval($s['cantidad'] ?? 1) . "</td>" .
                    "<td style='padding:7px 8px; border:1px solid {$GRIS_LINE}; text-align:right; font-size:16px; color:{$GRIS_MED}; width:17%;'>Q " . number_format(floatval($s['costo'] ?? 0), 2) . "</td>" .
                    "<td style='padding:7px 10px; border:1px solid {$GRIS_LINE}; text-align:right; font-size:18px; font-weight:bold; color:{$VERDE}; width:17%;'>Q " . number_format($sub, 2) . "</td>" .
                    "</tr>";
            }
        } else {
            $serviciosRows =
                "<tr><td colspan='5' style='padding:16px; text-align:center; color:{$GRIS_SUV};" .
                "font-size:12px; border:1px solid {$GRIS_LINE}; background:{$GRIS_BG}; font-style:italic;'>Sin servicios registrados</td></tr>";
        }
        $totalFinal = number_format(floatval($orden['costo_total'] ?? $totalCalculado), 2);

        // ── Inventario ────────────────────────────────────────────────────
        $invItems = [
            'gato'             => 'Gato',
            'herramientas'  => 'Herramientas',
            'triangulos'       => 'Triángulos',
            'tapetes'       => 'Tapetes',
            'llanta_refaccion' => 'Llanta refac.',
            'extintor'      => 'Extintor',
            'antena'           => 'Antena',
            'emblemas'      => 'Emblemas',
            'tapones_rueda'    => 'Tapones rueda',
            'cables'        => 'Cables',
            'estereo'          => 'Estéreo',
            'encendedor'    => 'Encendedor',
        ];
        $inventarioSeccion = '';
        if (!empty($inventario)) {
            $presentes = [];
            foreach ($invItems as $key => $label) {
                if (!empty($inventario[$key]) && $inventario[$key] == 1) $presentes[] = $label;
            }
            if (!empty($presentes)) {
                $chips = '';
                foreach ($presentes as $item) {
                    $chips .= "<span style='display:inline-block; background:{$VERDE_CLARO}; color:{$VERDE};" .
                        "border:1px solid #86efac; padding:4px 12px; margin:3px 4px;" .
                        "font-size:15px; font-weight:bold;'>&#10003; &nbsp;{$item}</span>";
                }
                if (!empty($inventario['otros'])) {
                    $chips .= "<span style='display:inline-block; background:{$GRIS_BG}; color:{$GRIS_MED};" .
                        "border:1px solid {$GRIS_LINE}; padding:4px 12px; margin:3px 4px;" .
                        "font-size:15px;'>+ " . htmlspecialchars($inventario['otros']) . "</span>";
                }
                $inventarioSeccion =
                    "<div style='margin-bottom:22px;'>" .
                    $secTit('&#9745;', 'Inventario del Vehículo — Artículos Presentes') .
                    "<div style='border:1px solid {$GRIS_LINE}; padding:10px 8px; background:{$BLANCO}; line-height:2;'>{$chips}</div>" .
                    "</div>";
            }
        }

        // ── Colores por tipo de daño ──────────────────────────────────────
        $tipColor = [
            'faltante'     => '#dc2626',
            'rayon'        => '#ea580c',
            'rayón'        => '#ea580c',
            'abollon'      => '#ca8a04',
            'abollón'      => '#ca8a04',
            'cristal_roto' => '#2563eb',
            'golpe'        => '#9333ea',
            'otro'         => '#6b7280',
        ];

        // ── DAÑOS — CSS moderno con position:absolute ─────────────────────
        // wkhtmltopdf usa WebKit real, soporta position:absolute perfectamente.
        // La imagen se muestra con <img> normal y los círculos van encima.
        $danosSeccion = '';
        if (!empty($danos)) {

            $vistas = [];
            foreach ($danos as $idx => $d) {
                $ub = strtolower($d['ubicacion'] ?? 'frontal');
                if (!isset($vistaArchivo[$ub])) {
                    if (strpos($ub, 'lat')  !== false)                                  $ub = 'lateral';
                    elseif (strpos($ub, 'fron') !== false)                                  $ub = 'frontal';
                    elseif (strpos($ub, 'tras') !== false || strpos($ub, 'back') !== false) $ub = 'trasero';
                    elseif (strpos($ub, 'tec')  !== false || strpos($ub, 'top')  !== false) $ub = 'techo';
                    else $ub = 'frontal';
                }
                $vistas[$ub][] = array_merge($d, ['_num' => $idx + 1]);
            }

            $vistaLabel = [
                'frontal'     => 'Vista Frontal',
                'trasero'     => 'Vista Trasera',
                'lateral'     => 'Vista Lateral Izq.',
                'lateral_izq' => 'Vista Lateral Izq.',
                'lateral_der' => 'Vista Lateral Der.',
                'techo'       => 'Vista Superior (Techo)',
            ];

            $vistasData = [];
            foreach ($vistas as $vista => $puntos) {
                $archivo = $vistaArchivo[$vista] ?? null;
                if (!$archivo) continue;

                // wkhtmltopdf necesita file:// para rutas locales
                $imgPath    = $_SERVER['DOCUMENT_ROOT'] . '/comodin_motors/public/images/' . $archivo;
                $imgFileUrl = 'file://' . $imgPath;
                if (!file_exists($imgPath)) continue;

                // Puntos con position:absolute — WebKit los soporta perfectamente
                $puntosDivs = '';
                foreach ($puntos as $p) {
                    $cx    = floatval($p['coordenada_x'] ?? 50);
                    $cy    = floatval($p['coordenada_y'] ?? 50);
                    $num   = $p['_num'];
                    $tipo  = strtolower(str_replace([' ', '-'], '_', $p['tipo_dano'] ?? 'otro'));
                    $clr   = $tipColor[$tipo] ?? $tipColor['otro'];

                    $puntosDivs .=
                        "<div style='" .
                        "position:absolute;" .
                        "left:{$cx}%;" .
                        "top:{$cy}%;" .
                        "transform:translate(-50%, -50%);" .
                        "width:26px; height:26px;" .
                        "background:{$clr};" .
                        "color:#ffffff;" .
                        "border-radius:50%;" .
                        "border:2.5px solid #ffffff;" .
                        "font-size:12px; font-weight:bold;" .
                        "text-align:center; line-height:21px;" .
                        "box-shadow: 0 2px 6px rgba(0,0,0,0.5);" .
                        "font-family:Arial,sans-serif;" .
                        "z-index:10;" .
                        "'>{$num}</div>";
                }

                // Contenedor relativo: imagen + puntos
                $imgBloque =
                    "<div style='position:relative; display:inline-block; width:100%;'>" .
                    "<img src='{$imgFileUrl}' style='width:100%; height:auto; display:block;' />" .
                    $puntosDivs .
                    "</div>";

                // Leyenda
                $leyenda = "<table width='100%' style='border-collapse:collapse; margin-top:6px; border-top:2px solid {$GRIS_LINE};'>";
                foreach ($puntos as $p) {
                    $tipo   = strtolower(str_replace([' ', '-'], '_', $p['tipo_dano'] ?? 'otro'));
                    $hexCol = $tipColor[$tipo] ?? $tipColor['otro'];
                    $desc   = htmlspecialchars($p['descripcion'] ?? '');
                    $tipLbl = strtoupper(htmlspecialchars($p['tipo_dano'] ?? 'otro'));
                    $num    = $p['_num'];
                    $leyenda .=
                        "<tr>" .
                        "<td style='padding:4px 6px; text-align:center; width:26px; border-bottom:1px solid {$GRIS_LINE};'>" .
                        "<span style='display:inline-block; background:{$hexCol}; color:#fff;" .
                        "width:20px; height:20px; border-radius:50%; font-size:12px; font-weight:bold;" .
                        "text-align:center; line-height:20px;'>{$num}</span></td>" .
                        "<td style='padding:4px 6px; border-bottom:1px solid {$GRIS_LINE}; width:85px;'>" .
                        "<span style='background:{$hexCol}22; color:{$hexCol}; border:1px solid {$hexCol};" .
                        "padding:2px 6px; font-size:10px; font-weight:bold; text-transform:uppercase;'>{$tipLbl}</span></td>" .
                        "<td style='padding:4px 6px; border-bottom:1px solid {$GRIS_LINE}; font-size:12px; color:{$NEGRO};'>{$desc}</td>" .
                        "</tr>";
                }
                $leyenda .= "</table>";

                $vistasData[] = [
                    'label'     => $vistaLabel[$vista] ?? ucfirst($vista),
                    'imgBloque' => $imgBloque,
                    'leyenda'   => $leyenda,
                ];
            }

            // Grid 2 columnas con flexbox (wkhtmltopdf soporta flexbox básico)
            $cols = '';
            foreach ($vistasData as $v) {
                $cols .=
                    "<div style='width:25%; display:inline-block; vertical-align:top; margin:0 1%;'>" .
                    "<div style='background:{$GRIS_BG}; border:1px solid {$GRIS_LINE}; border-bottom:none;" .
                    "padding:5px 8px; font-size:18px; font-weight:bold; color:{$GRIS_MED};" .
                    "text-transform:uppercase; letter-spacing:1px;'>" . $v['label'] . "</div>" .
                    "<div style='border:1px solid {$GRIS_LINE}; padding:6px; background:{$BLANCO};'>" .
                    $v['imgBloque'] .
                    $v['leyenda'] .
                    "</div></div>";
            }

            $cntDanos = count($danos);
            $plural   = $cntDanos > 1 ? 's' : '';
            $danosSeccion =
                "<div style='margin-bottom:22px;'>" .
                $secTit('&#9888;', "Daños Preexistentes ({$cntDanos} registrado{$plural})", $ROJO) .
                "<div style='font-size:0;'>{$cols}</div>" .  // font-size:0 elimina espacio entre inline-block
                "</div>";
        }

        // ── Observaciones ─────────────────────────────────────────────────
        $obsBloque = '';
        if ($obs) {
            $obsBloque =
                "<tr><td style='padding:0; border:1px solid {$GRIS_LINE};'>" .
                "<table width='100%' style='border-collapse:collapse;'>" .
                "<tr><td style='background:{$AMBAR_BG}; padding:3px 7px; font-size:8px; color:{$AMBAR};" .
                "text-transform:uppercase; letter-spacing:0.8px; border-bottom:1px solid #fde68a;'>Observaciones</td></tr>" .
                "<tr><td style='padding:6px 8px; font-size:11px; color:{$NEGRO};'>{$obs}</td></tr>" .
                "</table></td></tr>";
        }

        // ── Barra combustible ─────────────────────────────────────────────
        $fuelBar =
            "<td colspan='4' style='padding:6px 8px; border:1px solid {$GRIS_LINE};'>" .
            "<table width='100%' style='margin-bottom:3px;'><tr>" .
            "<td style='font-size:8px; color:{$GRIS_SUV}; text-align:left;   width:20%;'>E</td>" .
            "<td style='font-size:8px; color:{$GRIS_SUV}; text-align:center; width:20%;'>1/4</td>" .
            "<td style='font-size:8px; color:{$GRIS_SUV}; text-align:center; width:20%;'>1/2</td>" .
            "<td style='font-size:8px; color:{$GRIS_SUV}; text-align:center; width:20%;'>3/4</td>" .
            "<td style='font-size:8px; color:{$GRIS_SUV}; text-align:right;  width:20%;'>F</td>" .
            "</tr></table>" .
            "<table width='100%' style='border-collapse:collapse; background:#e5e7eb; border:1px solid {$GRIS_LINE}; height:10px;'><tr>" .
            "<td style='background:{$fuelColor}; width:{$fuelPct}%; height:10px; padding:0;'></td>" .
            "<td style='width:{$fuelRest}%; height:10px; padding:0;'></td>" .
            "</tr></table></td>";

        // ═══════════════════════════════════════════════════════════════════
        // HTML FINAL — CSS moderno completo (wkhtmltopdf lo soporta todo)
        // ═══════════════════════════════════════════════════════════════════
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: Arial, sans-serif; background:#ffffff; color:{$NEGRO}; font-size:16px; }
  @page { margin: 0; }
</style>
</head>
<body>
HTML;

        // HEADER
        $html .=
            "<table width='100%' style='border-collapse:collapse;'><tr>" .
            "<td style='background:{$NEGRO}; padding:16px 20px; width:60%; vertical-align:middle;'>" .
            "<table style='border-collapse:collapse;'><tr>" .
            "<td style='vertical-align:middle; padding-right:12px;'>{$logoHtml}</td>" .
            "<td style='vertical-align:middle; border-left:2px solid {$VERDE}; padding-left:12px;'>" .
            "<div style='color:{$BLANCO}; font-size:28px; font-weight:bold; letter-spacing:3px; text-transform:uppercase;'>COMODIN MOTORS</div>" .
            "<div style='color:{$GRIS_LINE}; font-size:14px; letter-spacing:2px; margin-top:3px; text-transform:uppercase;'>Centro Automotriz &nbsp;·&nbsp; Calidad que Impulsa tu Confianza</div>" .
            "</td></tr></table></td>" .
            "<td style='background:{$VERDE}; padding:14px 20px; width:40%; vertical-align:middle; text-align:right;'>" .
            "<div style='color:{$BLANCO}; font-size:18px; text-transform:uppercase; letter-spacing:2px; margin-bottom:4px;'>Orden de Servicio</div>" .
            "<div style='color:{$BLANCO}; font-size:26px; font-weight:bold; letter-spacing:2px;'>#{$numero}</div>" .
            "<div style='margin-top:8px;'><span style='background:{$estBg}; color:{$estColor}; padding:3px 14px; font-size:14px; font-weight:bold; text-transform:uppercase;'>{$estLabel}</span></div>" .
            "</td></tr><tr>" .
            "<td colspan='2' style='background:{$GRIS_OSC}; padding:5px 20px;'>" .
            "<table width='100%' style='border-collapse:collapse;'><tr>" .
            "<td style='color:{$GRIS_LINE}; font-size:18px;'>" .
            "FECHA: <b style='color:{$BLANCO};'>{$fecha}</b> &nbsp;&nbsp; " .
            "HORA: <b style='color:{$BLANCO};'>{$hora}</b> &nbsp;&nbsp; " .
            "INGRESÓ EN GRÚA: <b style='color:{$BLANCO};'>{$grua}</b>" .
            "</td>" .
            "<td style='text-align:right; color:{$GRIS_LINE}; font-size:16px;'>" .
            "PRÓXIMO SERVICIO: <b style='color:{$VERDE};'>{$proxKm}</b>" .
            "</td></tr></table></td></tr></table>";

        // DIVISOR
        $html .=
            "<div style='height:8px; background:{$VERDE}; margin-bottom:2px;'></div>" .
            "<div style='height:2px; background:{$GRIS_BG}; margin-bottom:18px;'></div>";

        // CLIENTE / VEHÍCULO
        $html .=
            "<table width='100%' style='border-collapse:collapse; margin-bottom:18px;'><tr>" .
            "<td colspan='4' style='background:{$NEGRO}; border-left:6px solid {$VERDE}; padding:6px 12px; font-size:18px; font-weight:bold; color:{$BLANCO}; text-transform:uppercase; letter-spacing:1.5px;'>Datos del Cliente</td>" .
            "<td style='width:10px; padding:0;'></td>" .
            "<td colspan='4' style='background:{$NEGRO}; border-left:6px solid {$VERDE}; padding:6px 12px; font-size:18px; font-weight:bold; color:{$BLANCO}; text-transform:uppercase; letter-spacing:1.5px;'>Datos del Vehículo</td>" .
            "</tr><tr>" .
            $cel('Nombre', $clienteNombre, 2) . $cel('Teléfono', $clienteTelefono, 2) .
            "<td style='width:14px; padding:0;'></td>" .
            $cel('Vehículo', $vehiculo, 2) . $cel('Color', $color) . $cel('Placas', $placas, 1, $VERDE) .
            "</tr><tr>" .
            $cel('Empresa', $clienteEmpresa, 2) . $cel('Dirección', $clienteDireccion, 2) .
            "<td style='width:10px; padding:0;'></td>" .
            $cel('N° Serie', $serie, 2) . $cel('Kilometraje', $km) . $cel('Combustible', $combustible, 1, $fuelColor) .
            "</tr><tr><td colspan='4'></td>" .
            "<td style='width:10px; padding:0;'></td>" .
            $fuelBar .
            "</tr></table>";

        // TRABAJO
        $html .=
            "<div style='margin-bottom:18px;'>" . $secTit('', 'Trabajo a Realizar') .
            "<table width='100%' style='border-collapse:collapse;'><tr>" .
            "<td style='border:1px solid {$GRIS_LINE}; padding:10px 12px; font-size:18px; line-height:1.8;'>{$trabajo}</td>" .
            "</tr>{$obsBloque}</table></div>";

        // SERVICIOS
        $html .=
            "<div style='margin-bottom:22px;'>" . $secTit('', 'Servicios y Repuestos') .
            "<table width='100%' style='border-collapse:collapse;'>" .
            "<thead><tr style='background:{$GRIS_BG};'>" .
            "<th style='padding:8px; border:1px solid {$GRIS_LINE}; font-size:15px; color:{$GRIS_MED}; width:4%; text-align:center; text-transform:uppercase;'>#</th>" .
            "<th style='padding:8px 10px; border:1px solid {$GRIS_LINE}; font-size:15px; color:{$GRIS_MED}; width:52%; text-align:left; text-transform:uppercase;'>Descripción</th>" .
            "<th style='padding:8px; border:1px solid {$GRIS_LINE}; font-size:15px; color:{$GRIS_MED}; width:10%; text-align:center; text-transform:uppercase;'>Cant.</th>" .
            "<th style='padding:8px; border:1px solid {$GRIS_LINE}; font-size:15px; color:{$GRIS_MED}; width:17%; text-align:right; text-transform:uppercase;'>P. Unit.</th>" .
            "<th style='padding:8px 10px; border:1px solid {$GRIS_LINE}; font-size:15px; color:{$GRIS_MED}; width:17%; text-align:right; text-transform:uppercase;'>Subtotal</th>" .
            "</tr></thead><tbody>{$serviciosRows}</tbody>" .
            "<tfoot><tr>" .
            "<td colspan='4' style='padding:10px; border:1px solid {$GRIS_LINE}; text-align:right; background:{$GRIS_BG}; font-size:17px; font-weight:bold; text-transform:uppercase;'>Total a Pagar:</td>" .
            "<td style='padding:10px; border:2px solid {$VERDE}; text-align:right; background:{$VERDE_CLARO}; font-size:19px; font-weight:bold; color:{$VERDE};'>Q {$totalFinal}</td>" .
            "</tr></tfoot></table></div>";

        $html .= $inventarioSeccion;
        $html .= $danosSeccion;

        // FIRMAS
        $html .=
            "<table width='100%' style='border-collapse:collapse; margin-top:80px;'><tr>" .
            "<td width='50%' style='padding:130px 16px 0 0;'>" .
            "<div style='height:50px; border-bottom:1px solid {$GRIS_MED};'></div>" .
            "<div style='font-size:18px; color:{$GRIS_MED}; text-transform:uppercase; margin-top:5px;'>Firma y nombre del cliente</div>" .
            "<div style='font-size:18px; font-weight:bold; margin-top:2px;'>{$clienteNombre}</div></td>" .
            "<td width='50%' style='padding:130px 0 0 16px;'>" .
            "<div style='height:50px; border-bottom:1px solid {$GRIS_MED};'></div>" .
            "<div style='font-size:18px; color:{$GRIS_MED}; text-transform:uppercase; margin-top:5px;'>Firma del técnico / Recibido</div>" .
            "<div style='font-size:20px; font-weight:bold; margin-top:2px;'>Carlos Emilio Urízar</div></td>" .
            "</tr></table>";

        // AVISO + PIE
        $fechaGen = date('d/m/Y H:i');
        $html .=
            "<div style='margin-top:16px; background:{$AMBAR_BG}; border:1px solid #fde68a; border-left:4px solid #f59e0b; padding:8px 12px;'>" .
            "<span style='font-size:20px; color:{$AMBAR};'><b>AVISO:</b> Los daños preexistentes fueron verificados al momento del ingreso. " .
            "Comodín Motors no se responsabiliza por daños no reportados en esta orden. " .
            "Tiempo de entrega sujeto a disponibilidad de repuestos.</span></div>" .
            "<table width='100%' style='margin-top:10px; border-top:1px solid {$GRIS_LINE}; padding-top:6px;'><tr>" .
            "<td style='font-size:13px; color:{$GRIS_SUV};'>Generado: {$fechaGen} &nbsp;·&nbsp; Comodín Motors</td>" .
            "<td style='text-align:right; font-size:13px; color:{$GRIS_SUV};'>Orden #{$numero} &nbsp;·&nbsp; Sistema de Gestión de Órdenes</td>" .
            "</tr></table>";

        $html .= '</body></html>';
        return $html;
    }
    // ============================================
    // API ENDPOINTS
    // ============================================

    public static function buscarClienteAPI()
    {
        header('Content-Type: application/json; charset=UTF-8');
        $telefono = $_GET['telefono'] ?? '';
        if (empty($telefono)) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Teléfono no proporcionado'], JSON_UNESCAPED_UNICODE);
            return;
        }
        try {
            $clientes = Cliente::buscarPorTelefono($telefono);
            echo json_encode(['codigo' => 1, 'mensaje' => 'Búsqueda exitosa', 'datos' => $clientes], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al buscar cliente', 'detalle' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function obtenerVehiculosAPI()
    {
        header('Content-Type: application/json; charset=UTF-8');
        $id_cliente = $_GET['id_cliente'] ?? null;
        if (!$id_cliente) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID de cliente no proporcionado'], JSON_UNESCAPED_UNICODE);
            return;
        }
        try {
            $vehiculos = Vehiculo::obtenerPorCliente($id_cliente);
            echo json_encode(['codigo' => 1, 'mensaje' => 'Vehículos encontrados', 'datos' => $vehiculos], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al obtener vehículos', 'detalle' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function guardarClienteAPI()
    {
        header('Content-Type: application/json; charset=UTF-8');
        $_POST['nombre']  = htmlspecialchars($_POST['nombre']  ?? '');
        $_POST['telefono'] = htmlspecialchars($_POST['telefono'] ?? '');
        $_POST['empresa'] = htmlspecialchars($_POST['empresa']  ?? '');
        try {
            $cliente   = new Cliente($_POST);
            $resultado = $cliente->crear();
            echo json_encode(['codigo' => 1, 'mensaje' => 'Cliente guardado exitosamente', 'id_cliente' => $resultado['id']], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al guardar cliente', 'detalle' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function guardarVehiculoAPI()
    {
        header('Content-Type: application/json; charset=UTF-8');
        $_POST['marca']   = htmlspecialchars($_POST['marca']   ?? '');
        $_POST['modelo']  = htmlspecialchars($_POST['modelo']  ?? '');
        $_POST['placas']  = htmlspecialchars($_POST['placas']  ?? '');
        try {
            $vehiculo  = new Vehiculo($_POST);
            $resultado = $vehiculo->crear();
            echo json_encode(['codigo' => 1, 'mensaje' => 'Vehículo guardado exitosamente', 'id_vehiculo' => $resultado['id']], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al guardar vehículo', 'detalle' => $e->getMessage(), 'post_recibido' => $_POST], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function guardarOrdenAPI()
    {
        header('Content-Type: application/json; charset=UTF-8');

        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            throw new \Exception("PHP Error [$errno]: $errstr en $errfile:$errline");
        });

        try {
            if (empty($_POST['id_cliente'])) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'id_cliente no recibido', 'post' => $_POST], JSON_UNESCAPED_UNICODE);
                return;
            }

            $_POST['trabajo_realizar'] = htmlspecialchars($_POST['trabajo_realizar'] ?? '');
            $_POST['observaciones']    = htmlspecialchars($_POST['observaciones']    ?? '');

            Orden::getDB()->beginTransaction();

            // 0. Crear vehículo si no existe
            if (empty($_POST['id_vehiculo'])) {
                $vehiculo                      = new Vehiculo();
                $vehiculo->id_cliente          = $_POST['id_cliente'];
                $vehiculo->marca               = $_POST['marca']              ?? '';
                $vehiculo->modelo              = $_POST['modelo']             ?? '';
                $vehiculo->anio                = $_POST['anio']               ?? date('Y');
                $vehiculo->color               = $_POST['color']              ?? '';
                $vehiculo->placas              = $_POST['placas']             ?? '';
                $vehiculo->numero_serie        = $_POST['numero_serie']       ?? '';
                $vehiculo->kilometraje_inicial = $_POST['kilometraje_actual'] ?? 0;
                $vehiculo->activo              = 1;
                $resV = $vehiculo->guardar();
                if (empty($resV['id'])) throw new \Exception('No se pudo crear el vehículo (id vacío)');
                $_POST['id_vehiculo'] = $resV['id'];
            }

            // 1. Número de orden
            $numero_orden          = Orden::generarNumeroOrden();
            $_POST['numero_orden'] = $numero_orden;

            // 2. Crear orden
            $orden           = new Orden($_POST);
            $resultado_orden = $orden->crear();
            $id_orden        = $resultado_orden['id'];
            if (empty($id_orden)) throw new \Exception('No se pudo crear la orden (id vacío)');

            // 3. Inventario
            if (!empty($_POST['inventario'])) {
                $inventario_data             = $_POST['inventario'];
                $inventario_data['id_orden'] = $id_orden;
                $inventario                  = new InventarioVehiculo($inventario_data);
                $inventario->crear();
            }

            // 4. Daños
            $danos = json_decode($_POST['danos'] ?? '[]', true);
            error_log("JSON daños recibido: " . ($_POST['danos'] ?? 'VACÍO'));
            error_log("Daños decodificados: " . count($danos ?? []));
            if (!empty($danos) && is_array($danos)) {
                foreach ($danos as $dano_data) {
                    $dano_data['id_orden']     = $id_orden;
                    $dano_data['coordenada_x'] = floatval($dano_data['coordenada_x'] ?? $dano_data['x'] ?? 0);
                    $dano_data['coordenada_y'] = floatval($dano_data['coordenada_y'] ?? $dano_data['y'] ?? 0);
                    $dano_data['ubicacion']    = $dano_data['ubicacion']   ?? 'frontal';
                    $dano_data['descripcion']  = $dano_data['descripcion'] ?? '';
                    $dano_data['tipo_dano']    = $dano_data['tipo_dano']   ?? 'otro';
                    $dano = new DanoVehiculo($dano_data);
                    $dano->crear();
                }
            }

            // 5. Servicios
            $servicios = json_decode($_POST['servicios'] ?? '[]', true);
            if (!empty($servicios) && is_array($servicios)) {
                foreach ($servicios as $servicio_data) {
                    $servicio_data['id_orden'] = $id_orden;
                    $servicio = new ServicioRealizado($servicio_data);
                    $servicio->crear();
                }
            }

            // 6. Actualizar costo total
            $total_servicios = ServicioRealizado::obtenerTotalPorOrden($id_orden);
            $stmt = Orden::getDB()->prepare("UPDATE ordenes_servicio SET costo_total = ? WHERE id_orden = ?");
            $stmt->execute([$total_servicios, $id_orden]);

            Orden::getDB()->commit();

            echo json_encode([
                'codigo'       => 1,
                'mensaje'      => 'Orden creada exitosamente',
                'numero_orden' => $numero_orden,
                'id_orden'     => $id_orden
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            try {
                Orden::getDB()->rollback();
            } catch (\Exception $ex) {
            }
            http_response_code(500);
            echo json_encode([
                'codigo'  => 0,
                'mensaje' => 'Error al guardar la orden',
                'detalle' => $e->getMessage(),
                'post'    => $_POST
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function buscarAPI()
    {
        header('Content-Type: application/json; charset=UTF-8');
        try {
            $filtros = [];
            if (!empty($_GET['estado']))      $filtros['estado']      = $_GET['estado'];
            if (!empty($_GET['fecha_desde'])) $filtros['fecha_desde'] = $_GET['fecha_desde'];
            if (!empty($_GET['fecha_hasta'])) $filtros['fecha_hasta'] = $_GET['fecha_hasta'];
            $ordenes = Orden::obtenerOrdenesCompletas($filtros);
            echo json_encode(['codigo' => 1, 'mensaje' => 'Datos encontrados', 'datos' => $ordenes], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al buscar órdenes', 'detalle' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function modificarAPI()
    {
        header('Content-Type: application/json; charset=UTF-8');
        $id = filter_var($_POST['id_orden'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID de orden no válido'], JSON_UNESCAPED_UNICODE);
            return;
        }
        try {
            $orden = Orden::find($id);
            if (!$orden) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Orden no encontrada'], JSON_UNESCAPED_UNICODE);
                return;
            }
            $_POST['trabajo_realizar'] = htmlspecialchars($_POST['trabajo_realizar'] ?? '');
            $_POST['observaciones']    = htmlspecialchars($_POST['observaciones']    ?? '');
            $orden->sincronizar($_POST);
            $orden->actualizar();
            echo json_encode(['codigo' => 1, 'mensaje' => 'Orden modificada exitosamente'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al modificar orden', 'detalle' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function cambiarEstadoAPI()
    {
        header('Content-Type: application/json; charset=UTF-8');
        $id           = filter_var($_POST['id_orden'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $nuevo_estado = $_POST['estado'] ?? '';
        $estados_validos = ['pendiente', 'en_proceso', 'completado', 'entregado', 'cancelado'];
        if (!$id || !in_array($nuevo_estado, $estados_validos)) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Datos no válidos'], JSON_UNESCAPED_UNICODE);
            return;
        }
        try {
            $orden = Orden::find($id);
            if (!$orden) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Orden no encontrada'], JSON_UNESCAPED_UNICODE);
                return;
            }
            $orden->estado_orden = $nuevo_estado;
            $orden->actualizar();
            echo json_encode(['codigo' => 1, 'mensaje' => 'Estado actualizado exitosamente'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al cambiar estado', 'detalle' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function buscarServiciosAPI()
    {
        header('Content-Type: application/json; charset=UTF-8');
        $termino = $_GET['q'] ?? '';
        if (empty($termino)) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Término de búsqueda no proporcionado'], JSON_UNESCAPED_UNICODE);
            return;
        }
        try {
            $servicios = CatalogoServicio::buscar($termino);
            echo json_encode(['codigo' => 1, 'mensaje' => 'Búsqueda exitosa', 'datos' => $servicios], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al buscar servicios', 'detalle' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }
}
