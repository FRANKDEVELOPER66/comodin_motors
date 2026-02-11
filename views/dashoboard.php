<!-- Dashboard principal -->
<div class="dashboard-header mb-4">
    <h2 class="mb-1">Bienvenido de nuevo 👋</h2>
    <p class="text-muted">Aquí está el resumen de hoy</p>
</div>

<!-- Métricas principales -->
<div class="row g-4 mb-4">
    <!-- Órdenes del día -->
    <div class="col-lg-3 col-md-6">
        <div class="metric-card">
            <div class="metric-icon bg-green">
                <i class="bi bi-card-checklist"></i>
            </div>
            <div class="metric-content">
                <h3 class="metric-value" data-counter="12">0</h3>
                <p class="metric-label">Órdenes del día</p>
                <span class="metric-change positive">
                    <i class="bi bi-arrow-up"></i> 15% vs ayer
                </span>
            </div>
        </div>
    </div>

    <!-- Vehículos en taller -->
    <div class="col-lg-3 col-md-6">
        <div class="metric-card">
            <div class="metric-icon bg-blue">
                <i class="bi bi-car-front-fill"></i>
            </div>
            <div class="metric-content">
                <h3 class="metric-value" data-counter="8">0</h3>
                <p class="metric-label">Vehículos en taller</p>
                <span class="metric-change neutral">
                    <i class="bi bi-dash"></i> Sin cambios
                </span>
            </div>
        </div>
    </div>

    <!-- Ingresos del día -->
    <div class="col-lg-3 col-md-6">
        <div class="metric-card">
            <div class="metric-icon bg-yellow">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="metric-content">
                <h3 class="metric-value">Q 4,580</h3>
                <p class="metric-label">Ingresos del día</p>
                <span class="metric-change positive">
                    <i class="bi bi-arrow-up"></i> 23% vs ayer
                </span>
            </div>
        </div>
    </div>

    <!-- Órdenes completadas -->
    <div class="col-lg-3 col-md-6">
        <div class="metric-card">
            <div class="metric-icon bg-purple">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="metric-content">
                <h3 class="metric-value" data-counter="5">0</h3>
                <p class="metric-label">Completadas hoy</p>
                <span class="metric-change positive">
                    <i class="bi bi-arrow-up"></i> 10% vs ayer
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Accesos rápidos -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="quick-actions-card">
            <h5 class="mb-3">
                <i class="bi bi-lightning-charge-fill text-green"></i>
                Accesos Rápidos
            </h5>
            <div class="quick-actions-grid">
                <a href="/ordenes/nueva" class="quick-action-btn">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Nueva Orden</span>
                </a>
                <a href="/clientes/buscar" class="quick-action-btn">
                    <i class="bi bi-search"></i>
                    <span>Buscar Cliente</span>
                </a>
                <a href="/vehiculos/nuevo" class="quick-action-btn">
                    <i class="bi bi-car-front"></i>
                    <span>Nuevo Vehículo</span>
                </a>
                <a href="/ordenes/pendientes" class="quick-action-btn">
                    <i class="bi bi-clock-history"></i>
                    <span>Ver Pendientes</span>
                </a>
                <a href="/reportes/ingresos" class="quick-action-btn">
                    <i class="bi bi-graph-up"></i>
                    <span>Reportes</span>
                </a>
                <a href="/tecnicos" class="quick-action-btn">
                    <i class="bi bi-people"></i>
                    <span>Técnicos</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Órdenes recientes y estado -->
<div class="row g-4">
    <!-- Órdenes recientes -->
    <div class="col-lg-8">
        <div class="data-card">
            <div class="card-header">
                <h5>
                    <i class="bi bi-list-ul"></i>
                    Órdenes Recientes
                </h5>
                <a href="/ordenes" class="btn-link">Ver todas →</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>N° Orden</th>
                                <th>Cliente</th>
                                <th>Vehículo</th>
                                <th>Estado</th>
                                <th>Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>#000098</strong></td>
                                <td>Saúl Rivera</td>
                                <td>Changan 2020 - P590LBB</td>
                                <td><span class="badge badge-warning">En Proceso</span></td>
                                <td>Q 1,250.00</td>
                                <td>
                                    <button class="btn-icon" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn-icon" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>#000097</strong></td>
                                <td>María López</td>
                                <td>Toyota Corolla 2018</td>
                                <td><span class="badge badge-success">Completado</span></td>
                                <td>Q 850.00</td>
                                <td>
                                    <button class="btn-icon" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn-icon" title="Imprimir">
                                        <i class="bi bi-printer"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>#000096</strong></td>
                                <td>Juan Pérez</td>
                                <td>Honda Civic 2019</td>
                                <td><span class="badge badge-danger">Pendiente</span></td>
                                <td>Q 2,100.00</td>
                                <td>
                                    <button class="btn-icon" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn-icon" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Estado de órdenes -->
    <div class="col-lg-4">
        <div class="data-card">
            <div class="card-header">
                <h5>
                    <i class="bi bi-pie-chart-fill"></i>
                    Estado de Órdenes
                </h5>
            </div>
            <div class="card-body">
                <div class="status-item">
                    <div class="status-info">
                        <span class="status-dot bg-danger"></span>
                        <span class="status-label">Pendientes</span>
                    </div>
                    <div class="status-value">
                        <strong>4</strong>
                        <div class="progress-mini">
                            <div class="progress-mini-bar bg-danger" style="width: 40%"></div>
                        </div>
                    </div>
                </div>

                <div class="status-item">
                    <div class="status-info">
                        <span class="status-dot bg-warning"></span>
                        <span class="status-label">En Proceso</span>
                    </div>
                    <div class="status-value">
                        <strong>3</strong>
                        <div class="progress-mini">
                            <div class="progress-mini-bar bg-warning" style="width: 30%"></div>
                        </div>
                    </div>
                </div>

                <div class="status-item">
                    <div class="status-info">
                        <span class="status-dot bg-success"></span>
                        <span class="status-label">Completadas</span>
                    </div>
                    <div class="status-value">
                        <strong>5</strong>
                        <div class="progress-mini">
                            <div class="progress-mini-bar bg-success" style="width: 50%"></div>
                        </div>
                    </div>
                </div>

                <div class="status-item mb-0">
                    <div class="status-info">
                        <span class="status-dot bg-info"></span>
                        <span class="status-label">Entregadas</span>
                    </div>
                    <div class="status-value">
                        <strong>2</strong>
                        <div class="progress-mini">
                            <div class="progress-mini-bar bg-info" style="width: 20%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Técnicos activos -->
        <div class="data-card mt-4">
            <div class="card-header">
                <h5>
                    <i class="bi bi-people-fill"></i>
                    Técnicos Activos
                </h5>
            </div>
            <div class="card-body">
                <div class="technician-item">
                    <div class="tech-avatar bg-green">JM</div>
                    <div class="tech-info">
                        <div class="tech-name">Juan Méndez</div>
                        <div class="tech-status">2 órdenes asignadas</div>
                    </div>
                </div>
                <div class="technician-item">
                    <div class="tech-avatar bg-blue">CR</div>
                    <div class="tech-info">
                        <div class="tech-name">Carlos Ruiz</div>
                        <div class="tech-status">1 orden asignada</div>
                    </div>
                </div>
                <div class="technician-item mb-0">
                    <div class="tech-avatar bg-purple">AL</div>
                    <div class="tech-info">
                        <div class="tech-name">Ana López</div>
                        <div class="tech-status">Disponible</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos adicionales para el dashboard */
.dashboard-header h2 {
    color: var(--bs-white);
    font-weight: 700;
}

.metric-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border: 1px solid #3a3a3a;
    border-radius: 12px;
    padding: 25px;
    display: flex;
    gap: 20px;
    align-items: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.metric-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 255, 0, 0.2);
}

.metric-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
}

.metric-icon.bg-green { background: rgba(0, 255, 0, 0.15); color: #00ff00; }
.metric-icon.bg-blue { background: rgba(0, 123, 255, 0.15); color: #007bff; }
.metric-icon.bg-yellow { background: rgba(255, 193, 7, 0.15); color: #ffc107; }
.metric-icon.bg-purple { background: rgba(128, 0, 255, 0.15); color: #8000ff; }

.metric-value {
    font-size: 32px;
    font-weight: 700;
    color: #fff;
    margin: 0;
}

.metric-label {
    color: #b0b0b0;
    font-size: 14px;
    margin: 5px 0;
}

.metric-change {
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.metric-change.positive { color: #00ff00; }
.metric-change.negative { color: #ff4444; }
.metric-change.neutral { color: #b0b0b0; }

.quick-actions-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border: 1px solid #3a3a3a;
    border-radius: 12px;
    padding: 25px;
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.quick-action-btn {
    background: rgba(0, 255, 0, 0.1);
    border: 1px solid rgba(0, 255, 0, 0.3);
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    text-decoration: none;
    color: #00ff00;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.quick-action-btn:hover {
    background: #00ff00;
    color: #000;
    transform: scale(1.05);
}

.quick-action-btn i {
    font-size: 28px;
}

.data-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border: 1px solid #3a3a3a;
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    padding: 20px 25px;
    border-bottom: 1px solid #3a3a3a;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h5 {
    margin: 0;
    color: #fff;
    font-weight: 600;
}

.btn-link {
    color: #00ff00;
    text-decoration: none;
    font-size: 14px;
}

.card-body {
    padding: 25px;
}

.table {
    color: #fff;
    margin: 0;
}

.table th {
    border-bottom: 1px solid #3a3a3a;
    color: #b0b0b0;
    font-weight: 600;
    padding: 15px;
}

.table td {
    border-bottom: 1px solid #2a2a2a;
    padding: 15px;
}

.table tbody tr:hover {
    background: rgba(0, 255, 0, 0.05);
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-success { background: rgba(0, 255, 0, 0.2); color: #00ff00; }
.badge-warning { background: rgba(255, 193, 7, 0.2); color: #ffc107; }
.badge-danger { background: rgba(255, 68, 68, 0.2); color: #ff4444; }
.badge-info { background: rgba(0, 123, 255, 0.2); color: #007bff; }

.btn-icon {
    background: none;
    border: none;
    color: #b0b0b0;
    font-size: 18px;
    cursor: pointer;
    padding: 5px;
    transition: color 0.3s ease;
}

.btn-icon:hover {
    color: #00ff00;
}

.status-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.status-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.status-label {
    color: #b0b0b0;
    font-size: 14px;
}

.status-value {
    text-align: right;
}

.status-value strong {
    color: #fff;
    font-size: 18px;
}

.progress-mini {
    width: 100px;
    height: 4px;
    background: #2a2a2a;
    border-radius: 10px;
    margin-top: 5px;
}

.progress-mini-bar {
    height: 100%;
    border-radius: 10px;
}

.technician-item {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.tech-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: #000;
}

.tech-name {
    color: #fff;
    font-weight: 600;
    font-size: 14px;
}

.tech-status {
    color: #b0b0b0;
    font-size: 12px;
}

.text-green {
    color: #00ff00 !important;
}
</style>

<script>
// Animar contadores
document.addEventListener('DOMContentLoaded', () => {
    const counters = document.querySelectorAll('[data-counter]');
    counters.forEach(counter => {
        const target = parseInt(counter.dataset.counter);
        window.ComodinMotors.animateCounter(counter, target, 2000);
    });
});
</script>