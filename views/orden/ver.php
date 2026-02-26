<?php
// Obtener servicios, inventario y daños de la orden
use Model\ServicioRealizado;
use Model\InventarioVehiculo;
use Model\DanoVehiculo;

$servicios = ServicioRealizado::obtenerPorOrden($orden['id_orden'] ?? 0);
$inventario = InventarioVehiculo::obtenerPorOrden($orden['id_orden'] ?? 0);
$danos = DanoVehiculo::obtenerPorOrden($orden['id_orden'] ?? 0);
// DEBUG TEMPORAL
error_log("ID orden: " . ($orden['id_orden'] ?? 'NULL'));
error_log("Daños encontrados: " . count($danos));
error_log(print_r($danos, true));

$estadoClass = [
    'pendiente'  => 'estado-pendiente',
    'en_proceso' => 'estado-proceso',
    'completado' => 'estado-completado',
    'cancelado'  => 'estado-cancelado',
][$orden['estado_orden'] ?? 'pendiente'] ?? 'estado-pendiente';

$estadoLabel = [
    'pendiente'  => 'Pendiente',
    'en_proceso' => 'En Proceso',
    'completado' => 'Completado',
    'cancelado'  => 'Cancelado',
][$orden['estado_orden'] ?? 'pendiente'] ?? 'Pendiente';

// Config de vistas para daños
$vistas = [
    'frontal'           => ['label' => 'Frontal',       'icon' => 'bi-arrow-up-circle',    'img' => '/comodin_motors/public/images/front.png'],
    'trasero'           => ['label' => 'Trasero',       'icon' => 'bi-arrow-down-circle',  'img' => '/comodin_motors/public/images/back.png'],
    'lateral_izquierdo' => ['label' => 'Lat. Izquierdo', 'icon' => 'bi-arrow-left-circle',  'img' => '/comodin_motors/public/images/left.png'],
    'lateral_derecho'   => ['label' => 'Lat. Derecho',  'icon' => 'bi-arrow-right-circle', 'img' => '/comodin_motors/public/images/right.png'],
    'techo'             => ['label' => 'Techo',         'icon' => 'bi-arrow-up-square',    'img' => '/comodin_motors/public/images/top.png'],
];

$danosPorVista = [];
foreach ($danos as $d) {
    $danosPorVista[$d['ubicacion']][] = $d;
}
$vistasConDanos = array_keys($danosPorVista);
$primeraVista   = $vistasConDanos[0] ?? 'frontal';
?>

<style>
    .ver-orden {
        background: #0a0a0a;
        min-height: 100vh;
        padding: 2rem 0;
    }

    .orden-header {
        background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
        border: 1px solid #3a3a3a;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .orden-numero {
        font-size: 2.5rem;
        font-weight: 900;
        color: #00ff00;
        font-family: monospace;
    }

    .orden-numero span {
        color: #b0b0b0;
        font-size: 1rem;
        display: block;
        font-family: inherit;
    }

    .orden-header .text-muted {
        color: #aaaaaa !important;
        font-size: 0.95rem;
    }

    .orden-header .text-muted i {
        color: #00ff00;
    }

    .estado-pendiente {
        background: rgba(255, 193, 7, 0.25);
        color: #ffe082;
        border: 1px solid rgba(255, 193, 7, 0.6);
    }

    .estado-proceso {
        background: rgba(0, 123, 255, 0.15);
        color: #007bff;
        border: 1px solid rgba(0, 123, 255, 0.3);
    }

    .estado-completado {
        background: rgba(0, 255, 0, 0.15);
        color: #00ff00;
        border: 1px solid rgba(0, 255, 0, 0.3);
    }

    .estado-cancelado {
        background: rgba(255, 68, 68, 0.15);
        color: #ff4444;
        border: 1px solid rgba(255, 68, 68, 0.3);
    }

    .estado-badge {
        padding: 0.6rem 1.5rem;
        border-radius: 30px;
        font-weight: 700;
        font-size: 1rem;
    }

    .info-card {
        background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
        border: 1px solid #3a3a3a;
        border-radius: 20px;
        padding: 1.75rem;
        margin-bottom: 1.5rem;
        height: 100%;
    }

    .info-card-title {
        color: #00ff00;
        font-weight: 700;
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #3a3a3a;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        color: #888;
        font-size: 0.9rem;
    }

    .info-value {
        color: #fff;
        font-weight: 600;
        text-align: right;
        max-width: 60%;
    }

    .section-card {
        background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
        border: 1px solid #3a3a3a;
        border-radius: 20px;
        padding: 1.75rem;
        margin-bottom: 1.5rem;
    }

    .section-title {
        color: #fff;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #00ff00;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-title i {
        color: #00ff00;
    }

    /* Tabla servicios */
    .tabla-servicios {
        width: 100%;
        border-collapse: collapse;
    }

    .tabla-servicios th {
        background: #2a2a2a;
        color: #b0b0b0;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 0.75rem 1rem;
        text-align: left;
    }

    .tabla-servicios td {
        padding: 0.9rem 1rem;
        color: #fff;
        border-bottom: 1px solid #2a2a2a;
    }

    .tabla-servicios tr:last-child td {
        border-bottom: none;
    }

    .tabla-servicios tr:hover td {
        background: rgba(0, 255, 0, 0.03);
    }

    .tabla-total {
        background: #1a1a1a;
        border-top: 2px solid #00ff00;
    }

    .tabla-total td {
        padding: 1rem;
        color: #00ff00;
        font-weight: 700;
        font-size: 1.1rem;
    }

    /* Inventario */
    .inv-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 0.75rem;
    }

    .inv-item {
        background: #2a2a2a;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .inv-item.presente {
        border: 1px solid rgba(0, 255, 0, 0.3);
        color: #00ff00;
    }

    .inv-item.ausente {
        border: 1px solid #3a3a3a;
        color: #555;
        text-decoration: line-through;
    }

    /* Acciones */
    .btn-accion {
        padding: 0.6rem 1.25rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-green {
        background: linear-gradient(135deg, #00ff00, #00cc00);
        color: #000;
    }

    .btn-green:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 255, 0, 0.3);
    }

    .btn-outline {
        background: transparent;
        border: 2px solid #00ff00;
        color: #00ff00;
    }

    .btn-outline:hover {
        background: #00ff00;
        color: #000;
    }

    .btn-danger {
        background: rgba(255, 68, 68, 0.15);
        border: 1px solid rgba(255, 68, 68, 0.3);
        color: #ff4444;
    }

    .btn-danger:hover {
        background: rgba(255, 68, 68, 0.3);
    }

    .btn-blue {
        background: rgba(0, 123, 255, 0.15);
        border: 1px solid rgba(0, 123, 255, 0.3);
        color: #007bff;
    }

    .btn-blue:hover {
        background: rgba(0, 123, 255, 0.3);
    }

    .trabajo-box {
        background: #2a2a2a;
        border: 1px solid #3a3a3a;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        color: #ddd;
        line-height: 1.6;
        white-space: pre-wrap;
    }

    .fuel-bar {
        height: 12px;
        background: #2a2a2a;
        border-radius: 6px;
        overflow: hidden;
        margin-top: 0.5rem;
    }

    .fuel-fill {
        height: 100%;
        background: linear-gradient(90deg, #ff4444, #ffaa00, #00ff00);
        border-radius: 6px;
        transition: width 0.5s;
    }

    /* ── DAÑOS ── */
    .ver-vista-tabs {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .ver-vista-tab {
        background: #2a2a2a;
        border: 2px solid #3a3a3a;
        color: #b0b0b0;
        padding: 0.45rem 0.9rem;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.82rem;
        font-weight: 600;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .ver-vista-tab:hover {
        border-color: #00ff00;
        color: #00ff00;
    }

    .ver-vista-tab.active {
        background: rgba(0, 255, 0, 0.12);
        border-color: #00ff00;
        color: #00ff00;
    }

    .tab-count {
        background: #ff4444;
        color: #fff;
        font-size: 0.7rem;
        padding: 0.1rem 0.45rem;
        border-radius: 10px;
        font-weight: 700;
    }

    .ver-diagram-wrapper {
        background: #1a1a1a;
        border: 2px solid #3a3a3a;
        border-radius: 15px;
        padding: 1.5rem;
        text-align: center;
    }

    .ver-diagram-container {
        position: relative;
        display: inline-block;
        max-width: 480px;
        width: 100%;
    }

    .ver-diagram-container img {
        width: 100%;
        height: auto;
        border-radius: 10px;
        pointer-events: none;
        user-select: none;
        filter: drop-shadow(0 0 8px rgba(0, 255, 0, 0.12));
        transition: opacity 0.15s ease;
    }

    .ver-damage-pin {
        position: absolute;
        width: 30px;
        height: 30px;
        background: radial-gradient(circle, #ff6666, #cc0000);
        border: 3px solid #fff;
        border-radius: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        color: #fff;
        font-weight: 700;
        cursor: default;
        animation: pinPulseVer 2.5s infinite;
        z-index: 10;
    }

    .ver-damage-pin .ver-pin-tooltip {
        display: none;
        position: absolute;
        bottom: 130%;
        left: 50%;
        transform: translateX(-50%);
        background: #1a1a1a;
        border: 1px solid #ff4444;
        color: #fff;
        padding: 0.4rem 0.75rem;
        border-radius: 6px;
        font-size: 0.78rem;
        white-space: nowrap;
        z-index: 20;
        pointer-events: none;
    }

    .ver-damage-pin:hover .ver-pin-tooltip {
        display: block;
    }

    @keyframes pinPulseVer {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(255, 68, 68, 0.5);
        }

        50% {
            box-shadow: 0 0 0 7px rgba(255, 68, 68, 0);
        }
    }

    .ver-dano-item {
        background: #2a2a2a;
        border: 1px solid #3a3a3a;
        border-radius: 10px;
        padding: 0.8rem 1rem;
        margin-bottom: 0.6rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .ver-dano-num {
        background: #ff4444;
        color: #fff;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    .ver-dano-tipo {
        background: rgba(255, 68, 68, 0.15);
        color: #ff8080;
        border: 1px solid rgba(255, 68, 68, 0.3);
        padding: 0.15rem 0.6rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        white-space: nowrap;
        text-transform: capitalize;
    }

    .ver-dano-desc {
        color: #ddd;
        font-size: 0.9rem;
        flex: 1;
    }
</style>

<div class="ver-orden">
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="orden-header">
            <div>
                <div class="orden-numero">
                    <span>Orden de Servicio</span>
                    #<?= htmlspecialchars($orden['numero_orden'] ?? '---') ?>
                </div>
                <div class="text-muted mt-1">
                    <i class="bi bi-calendar3"></i>
                    <?= date('d/m/Y', strtotime($orden['fecha_orden'] ?? 'now')) ?>
                    &nbsp;&nbsp;
                    <i class="bi bi-clock"></i>
                    <?= substr($orden['hora_ingreso'] ?? '', 0, 5) ?>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <span class="estado-badge <?= $estadoClass ?>"><?= $estadoLabel ?></span>
                <div class="d-flex gap-2">
                    <a href="/comodin_motors/orden/pdf?id=<?= $orden['id_orden'] ?>"
                        class="btn-accion btn-outline" target="_blank">
                        <i class="bi bi-file-earmark-pdf"></i> Ver PDF
                    </a>
                    <a href="/comodin_motors/orden" class="btn-accion btn-outline">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- COLUMNA IZQUIERDA -->
            <div class="col-lg-8">

                <!-- CLIENTE Y VEHÍCULO -->
                <div class="row mb-0">
                    <div class="col-md-6 mb-3">
                        <div class="info-card">
                            <div class="info-card-title"><i class="bi bi-person-circle"></i> Cliente</div>
                            <div class="info-row">
                                <span class="info-label">Nombre</span>
                                <span class="info-value"><?= htmlspecialchars($orden['cliente_nombre'] ?? '-') ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Teléfono</span>
                                <span class="info-value"><?= htmlspecialchars($orden['cliente_telefono'] ?? '-') ?></span>
                            </div>
                            <?php if (!empty($orden['cliente_empresa'])): ?>
                                <div class="info-row">
                                    <span class="info-label">Empresa</span>
                                    <span class="info-value"><?= htmlspecialchars($orden['cliente_empresa']) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($orden['cliente_direccion'])): ?>
                                <div class="info-row">
                                    <span class="info-label">Dirección</span>
                                    <span class="info-value"><?= htmlspecialchars($orden['cliente_direccion']) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-card">
                            <div class="info-card-title"><i class="bi bi-car-front"></i> Vehículo</div>
                            <div class="info-row">
                                <span class="info-label">Vehículo</span>
                                <span class="info-value"><?= htmlspecialchars(($orden['marca'] ?? '') . ' ' . ($orden['modelo'] ?? '') . ' ' . ($orden['anio'] ?? '')) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Color</span>
                                <span class="info-value"><?= htmlspecialchars($orden['color'] ?? '-') ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Placas</span>
                                <span class="info-value" style="color:#00ff00;"><?= htmlspecialchars($orden['placas'] ?? '-') ?></span>
                            </div>
                            <?php if (!empty($orden['numero_serie'])): ?>
                                <div class="info-row">
                                    <span class="info-label">N° Serie</span>
                                    <span class="info-value"><?= htmlspecialchars($orden['numero_serie']) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="info-row">
                                <span class="info-label">Kilometraje</span>
                                <span class="info-value"><?= number_format($orden['kilometraje_actual'] ?? 0) ?> km</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Combustible</span>
                                <span class="info-value"><?= htmlspecialchars($orden['nivel_combustible'] ?? '-') ?></span>
                            </div>
                            <?php $fuelPct = ['E' => 0, '1/4' => 25, '1/2' => 50, '3/4' => 75, 'F' => 100][$orden['nivel_combustible'] ?? '1/2'] ?? 50; ?>
                            <div class="fuel-bar">
                                <div class="fuel-fill" style="width:<?= $fuelPct ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TRABAJO A REALIZAR -->
                <div class="section-card">
                    <div class="section-title"><i class="bi bi-list-task"></i> Trabajo a Realizar</div>
                    <div class="trabajo-box"><?= nl2br(htmlspecialchars($orden['trabajo_realizar'] ?? '')) ?></div>
                    <?php if (!empty($orden['observaciones'])): ?>
                        <div class="mt-3">
                            <div class="info-label mb-2"><i class="bi bi-chat-left-text"></i> Observaciones</div>
                            <div class="trabajo-box"><?= nl2br(htmlspecialchars($orden['observaciones'])) ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- SERVICIOS -->
                <div class="section-card">
                    <div class="section-title"><i class="bi bi-tools"></i> Servicios y Repuestos</div>
                    <?php if (!empty($servicios)): ?>
                        <div class="table-responsive">
                            <table class="tabla-servicios">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Descripción</th>
                                        <th>Cant.</th>
                                        <th>Precio Unit.</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($servicios as $i => $s): ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td><?= htmlspecialchars($s['descripcion'] ?? '') ?></td>
                                            <td><?= $s['cantidad'] ?? 1 ?></td>
                                            <td>Q <?= number_format(floatval($s['costo'] ?? 0), 2) ?></td>
                                            <td>Q <?= number_format(floatval($s['subtotal'] ?? ($s['costo'] * $s['cantidad'])), 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="tabla-total">
                                        <td colspan="4" class="text-end"><strong>TOTAL:</strong></td>
                                        <td><strong>Q <?= number_format(floatval($orden['costo_total'] ?? 0), 2) ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">No hay servicios registrados</p>
                    <?php endif; ?>
                </div>

                <!-- INVENTARIO -->
                <?php if (!empty($inventario)): ?>
                    <div class="section-card">
                        <div class="section-title"><i class="bi bi-list-check"></i> Inventario del Vehículo</div>
                        <div class="inv-grid">
                            <?php
                            $items = [
                                'gato' => 'Gato',
                                'herramientas' => 'Herramientas',
                                'triangulos' => 'Triángulos',
                                'tapetes' => 'Tapetes',
                                'llanta_refaccion' => 'Llanta refacción',
                                'extintor' => 'Extintor',
                                'antena' => 'Antena',
                                'emblemas' => 'Emblemas',
                                'tapones_rueda' => 'Tapones rueda',
                                'cables' => 'Cables',
                                'estereo' => 'Estéreo',
                                'encendedor' => 'Encendedor'
                            ];
                            foreach ($items as $key => $label):
                                $presente = !empty($inventario[$key]) && $inventario[$key] == 1;
                            ?>
                                <div class="inv-item <?= $presente ? 'presente' : 'ausente' ?>">
                                    <i class="bi bi-<?= $presente ? 'check-circle-fill' : 'x-circle' ?>"></i>
                                    <?= $label ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (!empty($inventario['otros'])): ?>
                            <div class="mt-3 text-muted">
                                <i class="bi bi-plus-circle"></i> Otros: <?= htmlspecialchars($inventario['otros']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- DAÑOS -->
                <?php if (!empty($danos)): ?>
                    <div class="section-card">
                        <div class="section-title">
                            <i class="bi bi-exclamation-triangle"></i> Daños Preexistentes
                            <span style="background:#ff4444; color:#fff; font-size:0.78rem; padding:0.2rem 0.6rem; border-radius:10px; margin-left:0.25rem;">
                                <?= count($danos) ?> daño<?= count($danos) > 1 ? 's' : '' ?>
                            </span>
                        </div>

                        <!-- Tabs — solo vistas con daños -->
                        <div class="ver-vista-tabs">
                            <?php foreach ($vistas as $key => $v):
                                if (!isset($danosPorVista[$key])) continue;
                                $cnt = count($danosPorVista[$key]);
                            ?>
                                <button type="button"
                                    class="ver-vista-tab <?= $key === $primeraVista ? 'active' : '' ?>"
                                    data-vista="<?= $key ?>"
                                    data-img="<?= $v['img'] ?>">
                                    <i class="bi <?= $v['icon'] ?>"></i>
                                    <?= $v['label'] ?>
                                    <span class="tab-count"><?= $cnt ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <!-- Imagen con pins -->
                        <div class="ver-diagram-wrapper">
                            <div class="ver-diagram-container" id="verDiagramContainer">
                                <img id="verCarImage"
                                    src="<?= $vistas[$primeraVista]['img'] ?>"
                                    alt="Vista <?= $vistas[$primeraVista]['label'] ?>"
                                    draggable="false">
                            </div>
                            <p style="text-align:center; color:#555; font-size:0.82rem; margin-top:0.5rem; margin-bottom:0;">
                                <i class="bi bi-cursor"></i> Pasa el cursor sobre un pin para ver el detalle
                            </p>
                        </div>

                        <!-- Lista de daños de la vista activa -->
                        <div id="verDamageList" class="mt-3"></div>
                    </div>
                <?php endif; ?>

            </div><!-- fin col-lg-8 -->

            <!-- COLUMNA DERECHA -->
            <div class="col-lg-4">
                <div class="info-card" style="position:sticky; top:100px;">
                    <div class="info-card-title"><i class="bi bi-receipt"></i> Resumen</div>
                    <div class="info-row">
                        <span class="info-label">N° Orden</span>
                        <span class="info-value" style="color:#00ff00; font-family:monospace;">#<?= htmlspecialchars($orden['numero_orden'] ?? '') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Fecha</span>
                        <span class="info-value"><?= date('d/m/Y', strtotime($orden['fecha_orden'] ?? 'now')) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Grúa</span>
                        <span class="info-value"><?= ($orden['ingreso_grua'] ?? 0) ? 'Sí' : 'No' ?></span>
                    </div>
                    <?php if (!empty($orden['proximo_servicio_km'])): ?>
                        <div class="info-row">
                            <span class="info-label">Próx. servicio</span>
                            <span class="info-value"><?= number_format($orden['proximo_servicio_km']) ?> km</span>
                        </div>
                    <?php endif; ?>
                    <div class="info-row" style="border-top: 2px solid #00ff00; margin-top: 0.5rem; padding-top: 1rem;">
                        <span class="info-label" style="font-size:1.1rem; color:#fff;">TOTAL</span>
                        <span class="info-value" style="font-size:1.5rem; color:#00ff00;">Q <?= number_format(floatval($orden['costo_total'] ?? 0), 2) ?></span>
                    </div>

                    <!-- Cambiar estado -->
                    <div class="mt-4">
                        <div class="info-label mb-2">Cambiar Estado:</div>
                        <div class="d-grid gap-2">
                            <?php if (($orden['estado_orden'] ?? '') !== 'en_proceso'): ?>
                                <button class="btn-accion btn-blue w-100 justify-content-center"
                                    onclick="cambiarEstado(<?= $orden['id_orden'] ?>, 'en_proceso')">
                                    <i class="bi bi-play-circle"></i> Marcar En Proceso
                                </button>
                            <?php endif; ?>
                            <?php if (($orden['estado_orden'] ?? '') !== 'completado'): ?>
                                <button class="btn-accion btn-green w-100 justify-content-center"
                                    onclick="cambiarEstado(<?= $orden['id_orden'] ?>, 'completado')">
                                    <i class="bi bi-check-circle"></i> Marcar Completada
                                </button>
                            <?php endif; ?>
                            <?php if (($orden['estado_orden'] ?? '') !== 'cancelado'): ?>
                                <button class="btn-accion btn-danger w-100 justify-content-center"
                                    onclick="cambiarEstado(<?= $orden['id_orden'] ?>, 'cancelado')">
                                    <i class="bi bi-x-circle"></i> Cancelar Orden
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mt-3 d-grid gap-2">
                        <a href="/comodin_motors/orden/pdf?id=<?= $orden['id_orden'] ?>"
                            class="btn-accion btn-outline w-100 justify-content-center text-decoration-none" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i> Ver PDF
                        </a>
                        <a href="/comodin_motors/orden/nueva" class="btn-accion btn-outline w-100 justify-content-center text-decoration-none">
                            <i class="bi bi-plus-circle"></i> Nueva Orden
                        </a>
                    </div>
                </div>
            </div>

        </div><!-- fin row -->
    </div>
</div>

<script>
    // ── DAÑOS: tabs + pins ────────────────────────────────────────
    <?php if (!empty($danos)): ?>
            (function() {
                const todosLosDanos = <?= json_encode(array_map(fn($d) => [
                                            'ubicacion'   => $d['ubicacion']   ?? 'frontal',
                                            'tipo_dano'   => $d['tipo_dano']   ?? 'otro',
                                            'descripcion' => $d['descripcion'] ?? '',
                                            'x'           => floatval($d['coordenada_x'] ?? 0),
                                            'y'           => floatval($d['coordenada_y'] ?? 0),
                                        ], $danos), JSON_UNESCAPED_UNICODE) ?>;

                const container = document.getElementById('verDiagramContainer');
                const carImage = document.getElementById('verCarImage');
                const damageList = document.getElementById('verDamageList');
                let vistaActual = '<?= $primeraVista ?>';

                // Cambio de tab
                document.querySelectorAll('.ver-vista-tab').forEach(tab => {
                    tab.addEventListener('click', () => {
                        document.querySelectorAll('.ver-vista-tab').forEach(t => t.classList.remove('active'));
                        tab.classList.add('active');
                        vistaActual = tab.dataset.vista;

                        carImage.style.opacity = '0';
                        setTimeout(() => {
                            carImage.src = tab.dataset.img;
                            carImage.style.opacity = '1';
                        }, 150);

                        renderPins();
                        renderList();
                    });
                });

                function renderPins() {
                    container.querySelectorAll('.ver-damage-pin').forEach(p => p.remove());
                    todosLosDanos
                        .filter(d => d.ubicacion === vistaActual)
                        .forEach((d, i) => {
                            const pin = document.createElement('div');
                            pin.className = 'ver-damage-pin';
                            pin.style.left = d.x + '%';
                            pin.style.top = d.y + '%';
                            pin.innerHTML = `${i + 1}<span class="ver-pin-tooltip">${d.tipo_dano}: ${d.descripcion}</span>`;
                            container.appendChild(pin);
                        });
                }

                function renderList() {
                    const filtrados = todosLosDanos.filter(d => d.ubicacion === vistaActual);
                    if (!filtrados.length) {
                        damageList.innerHTML = '';
                        return;
                    }
                    damageList.innerHTML = filtrados.map((d, i) => `
            <div class="ver-dano-item">
                <div class="ver-dano-num">${i + 1}</div>
                <span class="ver-dano-tipo">${d.tipo_dano}</span>
                <span class="ver-dano-desc">${d.descripcion}</span>
            </div>
        `).join('');
                }

                // Init: esperar a que cargue la imagen
                if (carImage.complete) {
                    renderPins();
                    renderList();
                } else carImage.addEventListener('load', () => {
                    renderPins();
                    renderList();
                });
            })();
    <?php endif; ?>

    // ── CAMBIAR ESTADO ────────────────────────────────────────────
    async function cambiarEstado(id_orden, estado) {
        const labels = {
            en_proceso: 'En Proceso',
            completado: 'Completada',
            cancelado: 'Cancelada'
        };

        const confirmado = await Swal.fire({
            title: `¿Cambiar a "${labels[estado]}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'No',
            confirmButtonColor: '#00ff00',
            background: '#1a1a1a',
            color: '#fff'
        });

        if (!confirmado.isConfirmed) return;

        try {
            const formData = new FormData();
            formData.append('id_orden', id_orden);
            formData.append('estado', estado);

            const response = await fetch('/comodin_motors/API/ordenes/estado', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.codigo === 1) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Estado actualizado',
                    timer: 1500,
                    showConfirmButton: false,
                    background: '#1a1a1a',
                    color: '#fff'
                });
                location.reload();
            } else {
                throw new Error(data.mensaje);
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message,
                background: '#1a1a1a',
                color: '#fff'
            });
        }
    }
</script>