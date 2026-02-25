<style>
    /* Reutilizar estilos del personal pero adaptados para órdenes */
    .page-header {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
    }

    .page-header h1 {
        color: white;
        margin: 0;
        font-weight: 700;
        font-size: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: linear-gradient(135deg, #2a2a2a, #1a1a1a);
        border: 1px solid #3a3a3a;
        border-radius: 15px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        border-color: #00ff00;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
    }

    .stat-icon.pendiente { background: rgba(255, 193, 7, 0.2); color: #ffc107; }
    .stat-icon.proceso { background: rgba(0, 123, 255, 0.2); color: #007bff; }
    .stat-icon.completado { background: rgba(40, 167, 69, 0.2); color: #28a745; }
    .stat-icon.entregado { background: rgba(0, 255, 0, 0.2); color: #00ff00; }

    .stat-info h3 {
        font-size: 2rem;
        font-weight: 700;
        color: #fff;
        margin: 0;
    }

    .stat-info p {
        font-size: 0.9rem;
        color: #b0b0b0;
        margin: 0;
    }

    /* Filtros */
    .filters-card {
        background: linear-gradient(135deg, #2a2a2a, #1a1a1a);
        border: 1px solid #3a3a3a;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .filters-card .form-label {
        color: #b0b0b0;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .filters-card .form-control,
    .filters-card .form-select {
        background: #1a1a1a;
        border: 2px solid #3a3a3a;
        color: #fff;
        border-radius: 10px;
    }

    .filters-card .form-control:focus,
    .filters-card .form-select:focus {
        border-color: #00ff00;
        box-shadow: 0 0 0 3px rgba(0, 255, 0, 0.1);
    }

    /* Tabla */
    .table-container {
        background: linear-gradient(135deg, #2a2a2a, #1a1a1a);
        border: 1px solid #3a3a3a;
        border-radius: 20px;
        padding: 2rem;
        overflow: hidden;
    }

    #tablaOrdenes {
        border-collapse: separate;
        border-spacing: 0;
    }

    #tablaOrdenes thead th {
        background: linear-gradient(135deg, #2d2d2d 0%, #404040 100%);
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 1rem;
        border: none;
    }

    #tablaOrdenes tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid #3a3a3a;
    }

    #tablaOrdenes tbody tr:hover {
        background: rgba(0, 255, 0, 0.05);
        transform: scale(1.01);
    }

    #tablaOrdenes tbody td {
        padding: 1rem;
        vertical-align: middle;
        color: #fff;
    }

    /* Badges de estado */
    .badge-estado {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-block;
    }

    .badge-pendiente { background: rgba(255, 193, 7, 0.2); color: #ffc107; }
    .badge-en_proceso { background: rgba(0, 123, 255, 0.2); color: #007bff; }
    .badge-completado { background: rgba(40, 167, 69, 0.2); color: #28a745; }
    .badge-entregado { background: rgba(0, 255, 0, 0.2); color: #00ff00; }
    .badge-cancelado { background: rgba(220, 53, 69, 0.2); color: #dc3545; }

    /* Botones de acción */
    .btn-acciones {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        min-width: 45px;
        height: 40px;
    }

    .btn-ver {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
    }

    .btn-ver:hover {
        background: linear-gradient(135deg, #0056b3, #004085);
        transform: translateY(-2px);
    }

    .btn-imprimir {
        background: linear-gradient(135deg, #6c757d, #495057);
        color: white;
    }

    .btn-estado {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: white;
    }

    /* Botón flotante */
    .floating-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 65px;
        height: 65px;
        border-radius: 50%;
        background: linear-gradient(135deg, #00ff00, #00cc00);
        border: none;
        box-shadow: 0 6px 25px rgba(0, 255, 0, 0.4);
        color: #000;
        font-size: 28px;
        z-index: 1000;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .floating-btn:hover {
        transform: translateY(-5px) scale(1.1);
        box-shadow: 0 10px 35px rgba(0, 255, 0, 0.5);
    }
</style>

<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="page-header">
        <h1>
            <i class="bi bi-card-checklist"></i>
            Órdenes de Servicio
        </h1>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon pendiente">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="stat-info">
                <h3 id="stat-pendientes">0</h3>
                <p>Pendientes</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon proceso">
                <i class="bi bi-tools"></i>
            </div>
            <div class="stat-info">
                <h3 id="stat-proceso">0</h3>
                <p>En Proceso</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon completado">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3 id="stat-completado">0</h3>
                <p>Completadas</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon entregado">
                <i class="bi bi-box-arrow-right"></i>
            </div>
            <div class="stat-info">
                <h3 id="stat-entregado">0</h3>
                <p>Entregadas</p>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-card">
        <div class="row g-3">
            <div class="col-md-3">
                <label for="filtro_estado" class="form-label">
                    <i class="bi bi-funnel"></i> Estado
                </label>
                <select id="filtro_estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="en_proceso">En Proceso</option>
                    <option value="completado">Completado</option>
                    <option value="entregado">Entregado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filtro_fecha_desde" class="form-label">
                    <i class="bi bi-calendar"></i> Desde
                </label>
                <input type="date" id="filtro_fecha_desde" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="filtro_fecha_hasta" class="form-label">
                    <i class="bi bi-calendar"></i> Hasta
                </label>
                <input type="date" id="filtro_fecha_hasta" class="form-control">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button id="btnFiltrar" class="btn btn-green w-100">
                    <i class="bi bi-search"></i> Filtrar
                </button>
            </div>
        </div>
    </div>

    <!-- Tabla de órdenes -->
    <div class="table-container">
        <div class="table-responsive">
            <table id="tablaOrdenes" class="table table-hover"></table>
        </div>
    </div>

    <!-- Botón flotante -->
    <a href="/comodin_motors/orden/nueva" class="floating-btn" title="Nueva Orden">
        <i class="bi bi-plus-lg"></i>
    </a>
</div>

<script src="/comodin_motors/build/js/orden/index.js" type="module"></script>