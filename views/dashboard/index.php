<style>
    /* DashboardStyles */
    .dashboard-container {
        padding: 2rem;
    }

    .dashboard-header {
        margin-bottom: 2rem;
    }

    .dashboard-header h2 {
        color: #fff;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .dashboard-header p {
        color: #b0b0b0;
        margin: 0;
    }

    /* Metric Cards */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .metric-card {
        background: linear-gradient(135deg, #2a2a2a, #1a1a1a);
        border: 1px solid #3a3a3a;
        border-radius: 15px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.3s ease, border-color 0.3s ease;
    }

    .metric-card:hover {
        transform: translateY(-5px);
        border-color: #00ff00;
    }

    .metric-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
    }

    .metric-icon.green {
        background: rgba(0, 255, 0, 0.2);
        color: #00ff00;
    }

    .metric-icon.blue {
        background: rgba(0, 123, 255, 0.2);
        color: #007bff;
    }

    .metric-icon.yellow {
        background: rgba(255, 193, 7, 0.2);
        color: #ffc107;
    }

    .metric-icon.purple {
        background: rgba(128, 0, 255, 0.2);
        color: #8000ff;
    }

    .metric-content h3 {
        font-size: 2rem;
        font-weight: 700;
        color: #fff;
        margin: 0;
    }

    .metric-content p {
        color: #b0b0b0;
        font-size: 0.9rem;
        margin: 0.25rem 0;
    }

    .metric-change {
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .metric-change.positive {
        color: #00ff00;
    }

    .metric-change.negative {
        color: #ff4444;
    }

    .metric-change.neutral {
        color: #b0b0b0;
    }

    /* Quick Actions */
    .quick-actions {
        background: linear-gradient(135deg, #2a2a2a, #1a1a1a);
        border: 1px solid #3a3a3a;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .quick-actions h5 {
        color: #fff;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
    }

    .action-btn {
        background: rgba(0, 255, 0, 0.1);
        border: 1px solid rgba(0, 255, 0, 0.3);
        border-radius: 10px;
        padding: 1.25rem;
        text-align: center;
        text-decoration: none;
        color: #00ff00;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }

    .action-btn:hover {
        background: #00ff00;
        color: #000;
        transform: scale(1.05);
    }

    .action-btn i {
        font-size: 2rem;
    }

    /* Recent Orders Table */
    .recent-orders {
        background: linear-gradient(135deg, #2a2a2a, #1a1a1a);
        border: 1px solid #3a3a3a;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .recent-orders h5 {
        color: #fff;
        margin-bottom: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .view-all {
        color: #00ff00;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .orders-table {
        width: 100%;
        color: #fff;
    }

    .orders-table th {
        color: #b0b0b0;
        font-weight: 600;
        text-align: left;
        padding: 1rem 0.5rem;
        border-bottom: 1px solid #3a3a3a;
    }

    .orders-table td {
        padding: 1rem 0.5rem;
        border-bottom: 1px solid #2a2a2a;
    }

    .orders-table tr:hover {
        background: rgba(0, 255, 0, 0.05);
    }

    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-badge.pendiente {
        background: rgba(255, 193, 7, 0.2);
        color: #ffc107;
    }

    .status-badge.en_proceso {
        background: rgba(0, 123, 255, 0.2);
        color: #007bff;
    }

    .status-badge.completado {
        background: rgba(0, 255, 0, 0.2);
        color: #00ff00;
    }

    /* Status Chart */
    .status-chart {
        background: linear-gradient(135deg, #2a2a2a, #1a1a1a);
        border: 1px solid #3a3a3a;
        border-radius: 15px;
        padding: 1.5rem;
    }

    .status-chart h5 {
        color: #fff;
        margin-bottom: 1rem;
    }

    .status-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #3a3a3a;
    }

    .status-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .status-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #b0b0b0;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .status-dot.red {
        background: #ff4444;
    }

    .status-dot.yellow {
        background: #ffc107;
    }

    .status-dot.green {
        background: #00ff00;
    }

    .status-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #fff;
    }
</style>

<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <h2>Dashboard</h2>
        <p>Aquí está el resumen de hoy</p>
    </div>

    <!-- Métricas principales -->
    <div class="metrics-grid">
        <!-- Órdenes del día -->
        <div class="metric-card">
            <div class="metric-icon green">
                <i class="bi bi-card-checklist"></i>
            </div>
            <div class="metric-content">
                <h3><?= $totalOrdenesHoy ?></h3>
                <p>Órdenes del día</p>
                <span class="metric-change <?= $porcentajeOrdenesVsAyer >= 0 ? 'positive' : 'negative' ?>">
                    <i class="bi bi-arrow-<?= $porcentajeOrdenesVsAyer >= 0 ? 'up' : 'down' ?>"></i>
                    <?= abs($porcentajeOrdenesVsAyer) ?>% vs ayer
                </span>
            </div>
        </div>

        <!-- Vehículos en taller -->
        <div class="metric-card">
            <div class="metric-icon blue">
                <i class="bi bi-car-front-fill"></i>
            </div>
            <div class="metric-content">
                <h3><?= $vehiculosEnTaller ?></h3>
                <p>Vehículos en taller</p>
                <span class="metric-change neutral">
                    <i class="bi bi-dash"></i> Sin cambios
                </span>
            </div>
        </div>

        <!-- Ingresos del día -->
        <div class="metric-card">
            <div class="metric-icon yellow">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="metric-content">
                <h3>Q <?= number_format($ingresosHoy, 0) ?></h3>
                <p>Ingresos del día</p>
                <span class="metric-change <?= $porcentajeIngresosVsAyer >= 0 ? 'positive' : 'negative' ?>">
                    <i class="bi bi-arrow-<?= $porcentajeIngresosVsAyer >= 0 ? 'up' : 'down' ?>"></i>
                    <?= abs($porcentajeIngresosVsAyer) ?>% vs ayer
                </span>
            </div>
        </div>

        <!-- Completadas hoy -->
        <div class="metric-card">
            <div class="metric-icon purple">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="metric-content">
                <h3><?= $completadasHoy ?></h3>
                <p>Completadas hoy</p>
                <span class="metric-change <?= $porcentajeCompletadasVsAyer >= 0 ? 'positive' : 'negative' ?>">
                    <i class="bi bi-arrow-<?= $porcentajeCompletadasVsAyer >= 0 ? 'up' : 'down' ?>"></i>
                    <?= abs($porcentajeCompletadasVsAyer) ?>% vs ayer
                </span>
            </div>
        </div>
    </div>

    <!-- Accesos rápidos -->
    <div class="quick-actions">
        <h5>
            <i class="bi bi-lightning-charge-fill"></i>
            Accesos Rápidos
        </h5>
        <div class="actions-grid">
            <a href="/comodin_motors/orden/nueva" class="action-btn">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Nueva Orden</span>
            </a>
            <a href="#" class="action-btn">
                <i class="bi bi-search"></i>
                <span>Buscar Cliente</span>
            </a>
            <a href="#" class="action-btn">
                <i class="bi bi-car-front"></i>
                <span>Nuevo Vehículo</span>
            </a>
            <a href="/comodin_motors/orden" class="action-btn">
                <i class="bi bi-clock-history"></i>
                <span>Ver Pendientes</span>
            </a>
            <a href="#" class="action-btn">
                <i class="bi bi-graph-up"></i>
                <span>Reportes</span>
            </a>
            <a href="#" class="action-btn">
                <i class="bi bi-people"></i>
                <span>Técnicos</span>
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Órdenes recientes -->
        <div class="col-lg-8">
            <div class="recent-orders">
                <h5>
                    <span><i class="bi bi-list-ul"></i> Órdenes Recientes</span>
                    <a href="/comodin_motors/orden" class="view-all">Ver todas →</a>
                </h5>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>N° Orden</th>
                            <th>Cliente</th>
                            <th>Vehículo</th>
                            <th>Estado</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ordenesRecientes)): ?>
                            <?php foreach ($ordenesRecientes as $orden): ?>
                                <tr>
                                    <td><strong style="color: #00ff00;">#<?= $orden['numero_orden'] ?></strong></td>
                                    <td><?= htmlspecialchars($orden['cliente_nombre']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($orden['marca']) ?> <?= htmlspecialchars($orden['modelo']) ?> <?= $orden['anio'] ?><br>
                                        <small style="color: #00ff00;"><?= $orden['placas'] ?></small>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= $orden['estado_orden'] ?>">
                                            <?php
                                            $estados = [
                                                'pendiente' => 'Pendiente',
                                                'en_proceso' => 'En Proceso',
                                                'completado' => 'Completado',
                                                'entregado' => 'Entregado'
                                            ];
                                            echo $estados[$orden['estado_orden']] ?? $orden['estado_orden'];
                                            ?>
                                        </span>
                                    </td>
                                    <td>Q <?= number_format($orden['costo_total'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #b0b0b0; padding: 2rem;">
                                    No hay órdenes registradas
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Estado de órdenes -->
        <div class="col-lg-4">
            <div class="status-chart">
                <h5>
                    <i class="bi bi-pie-chart-fill"></i>
                    Estado de Órdenes
                </h5>
                <div class="status-item">
                    <div class="status-label">
                        <span class="status-dot red"></span>
                        <span>Pendientes</span>
                    </div>
                    <span class="status-value"><?= $pendientes ?></span>
                </div>
                <div class="status-item">
                    <div class="status-label">
                        <span class="status-dot yellow"></span>
                        <span>En Proceso</span>
                    </div>
                    <span class="status-value"><?= $enProceso ?></span>
                </div>
                <div class="status-item">
                    <div class="status-label">
                        <span class="status-dot green"></span>
                        <span>Completadas</span>
                    </div>
                    <span class="status-value"><?= $completadas ?></span>
                </div>
            </div>
        </div>
    </div>
</div>