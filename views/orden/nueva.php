<style>
    /* Tabs de vistas */
    .vista-tabs {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .vista-tab {
        background: #2a2a2a;
        border: 2px solid #3a3a3a;
        color: #b0b0b0;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .vista-tab:hover {
        border-color: #00ff00;
        color: #00ff00;
    }

    .vista-tab.active {
        background: rgba(0, 255, 0, 0.15);
        border-color: #00ff00;
        color: #00ff00;
    }

    /* Contenedor del diagrama */
    .diagram-wrapper {
        background: #1a1a1a;
        border: 2px solid #3a3a3a;
        border-radius: 15px;
        padding: 1.5rem;
        text-align: center;
    }

    .diagram-container {
        position: relative;
        display: inline-block;
        cursor: crosshair;
        max-width: 500px;
        width: 100%;
    }

    .diagram-container img {
        width: 100%;
        height: auto;
        border-radius: 10px;
        pointer-events: none;
        user-select: none;
        /* Quita el fondo blanco de las imágenes PNG */
        mix-blend-mode: normal;
        filter: drop-shadow(0 0 10px rgba(0, 255, 0, 0.15));
    }

    .diagram-hint {
        color: #555;
        font-size: 0.85rem;
        margin-top: 0.75rem;
        margin-bottom: 0;
    }

    /* Markers de daño sobre la imagen */
    .damage-pin {
        position: absolute;
        width: 28px;
        height: 28px;
        background: radial-gradient(circle, #ff6666, #cc0000);
        border: 3px solid #fff;
        border-radius: 50%;
        transform: translate(-50%, -50%);
        cursor: pointer;
        animation: pinPulse 2s infinite;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        color: #fff;
        font-weight: 700;
        transition: transform 0.2s;
    }

    .damage-pin:hover {
        transform: translate(-50%, -50%) scale(1.3);
    }

    .damage-pin .pin-tooltip {
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

    .damage-pin:hover .pin-tooltip {
        display: block;
    }

    @keyframes pinPulse {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(255, 68, 68, 0.6);
        }

        50% {
            box-shadow: 0 0 0 8px rgba(255, 68, 68, 0);
        }
    }

    /* Modal para describir daño */
    .dano-modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .dano-modal-content {
        background: #1e1e1e;
        border: 2px solid #00ff00;
        border-radius: 15px;
        padding: 2rem;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 20px 60px rgba(0, 255, 0, 0.15);
    }

    /* Lista de daños */
    .damage-list-item {
        background: #1a1a1a;
        border: 1px solid #3a3a3a;
        border-radius: 10px;
        padding: 0.9rem 1rem;
        margin-bottom: 0.6rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: border-color 0.2s;
    }

    .damage-list-item:hover {
        border-color: #ff4444;
    }

    .damage-list-item .dano-vista {
        background: rgba(0, 255, 0, 0.1);
        color: #00ff00;
        border: 1px solid rgba(0, 255, 0, 0.3);
        padding: 0.2rem 0.6rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        white-space: nowrap;
        text-transform: uppercase;
    }

    .damage-list-item .dano-tipo {
        background: rgba(255, 68, 68, 0.15);
        color: #ff8080;
        border: 1px solid rgba(255, 68, 68, 0.3);
        padding: 0.2rem 0.6rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .damage-list-item .dano-desc {
        color: #ddd;
        flex: 1;
        font-size: 0.9rem;
    }

    .btn-remove-dano {
        background: none;
        border: none;
        color: #ff4444;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 5px;
        transition: background 0.2s;
        font-size: 1.1rem;
    }

    .btn-remove-dano:hover {
        background: rgba(255, 68, 68, 0.15);
    }





    /* Tabla de servicios */
    .table-servicios {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-top: 1rem;
    }

    .table-servicios thead th {
        background: linear-gradient(135deg, #2d2d2d 0%, #404040 100%);
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        padding: 1rem 0.75rem;
        border: none;
        text-align: left;
    }

    .table-servicios tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid #3a3a3a;
    }

    .table-servicios tbody tr:not(.empty-state):hover {
        background: rgba(0, 255, 0, 0.05);
    }

    .table-servicios tbody td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
        color: #fff;
    }

    .table-servicios tfoot {
        background: #1a1a1a;
        border-top: 2px solid #00ff00;
    }

    .table-servicios tfoot td {
        padding: 1.5rem 0.75rem;
        font-size: 1.2rem;
        color: #00ff00;
    }

    .table-servicios input[type="number"],
    .table-servicios input[type="text"] {
        background: #2a2a2a;
        border: 1px solid #3a3a3a;
        color: #fff;
        padding: 0.5rem;
        border-radius: 5px;
        width: 100%;
    }

    .table-servicios input[type="number"]:focus,
    .table-servicios input[type="text"]:focus {
        border-color: #00ff00;
        outline: none;
    }

    .btn-eliminar-servicio {
        background: linear-gradient(135deg, #ff4444, #cc0000);
        border: none;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-eliminar-servicio:hover {
        background: linear-gradient(135deg, #cc0000, #990000);
        transform: translateY(-2px);
    }

    /* Autocomplete */
    .autocomplete-results {
        position: absolute;
        z-index: 1000;
        background: #2a2a2a;
        border: 2px solid #00ff00;
        border-radius: 10px;
        max-height: 300px;
        overflow-y: auto;
        width: calc(66.666% - 1rem);
        margin-top: 0.5rem;
        display: none;
    }

    .autocomplete-results.show {
        display: block;
    }

    .autocomplete-item {
        padding: 1rem;
        cursor: pointer;
        border-bottom: 1px solid #3a3a3a;
        transition: all 0.2s ease;
    }

    .autocomplete-item:last-child {
        border-bottom: none;
    }

    .autocomplete-item:hover {
        background: rgba(0, 255, 0, 0.1);
    }

    .autocomplete-item .servicio-codigo {
        color: #00ff00;
        font-weight: 700;
        font-size: 0.9rem;
    }

    .autocomplete-item .servicio-descripcion {
        color: #fff;
        margin: 0.25rem 0;
    }

    .autocomplete-item .servicio-precio {
        color: #ffc107;
        font-size: 0.9rem;
    }

    /* Contenedor principal con fondo oscuro */
    .orden-container {
        background: #0a0a0a;
        min-height: 100vh;
        padding: 2rem 0;
    }

    /* Tarjetas con efecto moderno */
    .section-card {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        border: 1px solid #3a3a3a;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #00ff00;
    }

    .section-header h3 {
        color: #fff;
        margin: 0;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-header i {
        color: #00ff00;
        font-size: 1.8rem;
    }

    /* Inputs modernos */
    .form-label {
        color: #b0b0b0;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label i {
        color: #00ff00;
    }

    .form-control,
    .form-select {
        background: #2a2a2a;
        border: 2px solid #3a3a3a;
        color: #fff;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        background: #1a1a1a;
        border-color: #00ff00;
        box-shadow: 0 0 0 3px rgba(0, 255, 0, 0.1);
        color: #fff;
    }

    .form-control::placeholder {
        color: #666;
    }

    /* Botones */
    .btn-green {
        background: linear-gradient(135deg, #00ff00, #00cc00);
        border: none;
        color: #000;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 700;
        transition: all 0.3s ease;
    }

    .btn-green:hover {
        background: linear-gradient(135deg, #00cc00, #009900);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 255, 0, 0.3);
        color: #000;
    }

    .btn-outline-green {
        background: transparent;
        border: 2px solid #00ff00;
        color: #00ff00;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-outline-green:hover {
        background: #00ff00;
        color: #000;
    }

    /* Selector de vehículos */
    .vehicle-card {
        background: #2a2a2a;
        border: 2px solid #3a3a3a;
        border-radius: 15px;
        padding: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .vehicle-card:hover {
        border-color: #00ff00;
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(0, 255, 0, 0.2);
    }

    .vehicle-card.selected {
        border-color: #00ff00;
        background: rgba(0, 255, 0, 0.1);
    }

    .vehicle-card.selected::before {
        content: '\f26b';
        /* Bootstrap icon check-circle-fill */
        font-family: 'bootstrap-icons';
        position: absolute;
        top: 10px;
        right: 10px;
        color: #00ff00;
        font-size: 1.5rem;
    }

    /* Indicador de combustible */
    .fuel-gauge {
        width: 100%;
        height: 40px;
        background: #2a2a2a;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        border: 2px solid #3a3a3a;
    }

    .fuel-level {
        height: 100%;
        background: linear-gradient(90deg, #ff4444 0%, #ffaa00 50%, #00ff00 100%);
        transition: width 0.3s ease;
        border-radius: 18px;
    }

    .fuel-labels {
        display: flex;
        justify-content: space-between;
        margin-top: 0.5rem;
        font-size: 0.85rem;
        color: #b0b0b0;
    }

    /* Checkboxes del inventario */
    .inventory-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .inventory-item {
        background: #2a2a2a;
        border: 2px solid #3a3a3a;
        border-radius: 10px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .inventory-item:hover {
        border-color: #00ff00;
    }

    .inventory-item input[type="checkbox"] {
        width: 24px;
        height: 24px;
        cursor: pointer;
    }

    .inventory-item input[type="checkbox"]:checked {
        accent-color: #00ff00;
    }

    .inventory-item label {
        color: #fff;
        margin: 0;
        cursor: pointer;
        flex: 1;
    }

    /* Diagrama de vehículo */
    .vehicle-diagram {
        background: #2a2a2a;
        border: 2px solid #3a3a3a;
        border-radius: 15px;
        padding: 2rem;
        position: relative;
        min-height: 400px;
    }

    #carCanvas {
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
        display: block;
        cursor: crosshair;
    }

    .damage-marker {
        position: absolute;
        width: 30px;
        height: 30px;
        background: #ff4444;
        border: 3px solid #fff;
        border-radius: 50%;
        transform: translate(-50%, -50%);
        cursor: pointer;
        transition: all 0.3s ease;
        animation: pulse 2s infinite;
    }

    .damage-marker:hover {
        transform: translate(-50%, -50%) scale(1.2);
    }

    @keyframes damagePulse {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(255, 68, 68, 0.7);
        }

        50% {
            box-shadow: 0 0 0 10px rgba(255, 68, 68, 0);
        }
    }

    .damage-list {
        margin-top: 1.5rem;
    }

    .damage-item {
        background: #1a1a1a;
        border: 1px solid #3a3a3a;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .damage-info {
        flex: 1;
    }

    .damage-badge {
        background: #ff4444;
        color: #fff;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    /* Resumen de la orden */
    .order-summary {
        background: #1a1a1a;
        border: 2px solid #00ff00;
        border-radius: 15px;
        padding: 2rem;
        position: sticky;
        top: 100px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #3a3a3a;
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .summary-label {
        color: #b0b0b0;
        font-weight: 600;
    }

    .summary-value {
        color: #00ff00;
        font-weight: 700;
    }

    .order-number {
        font-size: 2rem;
        color: #00ff00;
        font-weight: 900;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    /* Radios personalizados */
    .form-check-input[type="radio"] {
        width: 20px;
        height: 20px;
        border: 2px solid #3a3a3a;
        background: #2a2a2a;
    }

    .form-check-input[type="radio"]:checked {
        background-color: #00ff00;
        border-color: #00ff00;
    }

    .form-check-label {
        color: #fff;
        margin-left: 0.5rem;
    }

    /* Badges de estado */
    .badge-nuevo {
        background: linear-gradient(135deg, #00ff00, #00cc00);
        color: #000;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.9rem;
    }

    /* Botón flotante de guardar */
    .floating-save {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
    }

    .floating-save .btn {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        font-size: 2rem;
        box-shadow: 0 10px 40px rgba(0, 255, 0, 0.4);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .orden-container {
            padding: 1rem 0;
        }

        .section-card {
            padding: 1.5rem;
        }

        .order-summary {
            position: static;
            margin-top: 2rem;
        }
    }

    .fuel-drag-wrapper {
        user-select: none;
        margin-top: 0.5rem;
    }

    .fuel-track {
        position: relative;
        width: 100%;
        height: 44px;
        background: #1a1a1a;
        border: 2px solid #3a3a3a;
        border-radius: 22px;
        cursor: pointer;
        overflow: visible;
    }

    .fuel-fill {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        border-radius: 20px;
        background: linear-gradient(90deg, #ff4444 0%, #ffaa00 50%, #00ff00 100%);
        background-size: 600px 100%;
        /* fijo para que el gradiente no escale */
        transition: width 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        pointer-events: none;
    }

    .fuel-thumb {
        position: absolute;
        top: 50%;
        display: none; /* ← simplemente ocúltalo */
        transform: translate(-50%, -50%);
        width: 28px;
        height: 28px;
        background: #fff;
        border: 3px solid #00ff00;
        border-radius: 50%;
        box-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
        cursor: grab;
        transition: left 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
            transform 0.2s ease,
            box-shadow 0.2s ease;
        z-index: 2;
    }

    .fuel-thumb:active,
    .fuel-thumb.dragging {
        cursor: grabbing;
        transform: translate(-50%, -50%) scale(1.25);
        box-shadow: 0 0 20px rgba(0, 255, 0, 0.8);
    }

    .fuel-snap-markers {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 0;
        pointer-events: none;
    }

    .fuel-snap-dot {
        width: 6px;
        height: 6px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        flex-shrink: 0;
        /* Posicionamos los 5 dots en los extremos y centro */
    }

    /* Primer y último dot en los bordes */
    .fuel-snap-dot:first-child {
        margin-left: 0px;
    }

    .fuel-snap-dot:last-child {
        margin-right: 0px;
    }

    .fuel-labels {
        display: flex;
        justify-content: space-between;
        margin-top: 0.5rem;
        font-size: 0.85rem;
        color: #b0b0b0;
        padding: 0 2px;
    }

    .fuel-value-display {
        text-align: center;
        margin-top: 0.5rem;
        font-size: 1.1rem;
        font-weight: 700;
        color: #00ff00;
        letter-spacing: 1px;
        min-height: 1.5rem;
        transition: all 0.2s ease;
    }
</style>

<div class="orden-container">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="section-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="text-white mb-0">
                                <i class="bi bi-file-earmark-plus text-green"></i>
                                Nueva Orden de Servicio
                            </h2>
                            <p class="text mb-0 mt-2">Complete los datos del vehículo y servicio</p>
                        </div>
                        <div class="text-end">
                            <span class="badge-nuevo">
                                <i class="bi bi-lightning-fill"></i>
                                NUEVA
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="formularioOrden">
            <!-- Hidden fields siempre presentes -->
            <input type="hidden" name="id_cliente" id="id_cliente">
            <input type="hidden" name="id_vehiculo" id="id_vehiculo">
            <div class="row">
                <!-- Columna izquierda - Formulario -->
                <div class="col-lg-8">

                    <!-- SECCIÓN 1: DATOS DEL CLIENTE -->
                    <div class="section-card">
                        <div class="section-header">
                            <h3>
                                <i class="bi bi-person-circle"></i>
                                Datos del Cliente
                            </h3>
                        </div>

                        <!-- Búsqueda de cliente -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="buscar_telefono" class="form-label">
                                    <i class="bi bi-telephone"></i> Buscar por teléfono
                                </label>
                                <input type="text" id="buscar_telefono" class="form-control"
                                    placeholder="Ingrese teléfono del cliente...">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" id="btnBuscarCliente" class="btn btn-green w-100">
                                    <i class="bi bi-search"></i> Buscar
                                </button>
                            </div>
                        </div>

                        <!-- Datos del cliente encontrado/nuevo -->
                        <div id="datosCliente" style="display:none;">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="cliente_nombre" class="form-label">
                                        <i class="bi bi-person-fill"></i> Nombre completo *
                                    </label>
                                    <input type="text" id="cliente_nombre" name="cliente_nombre"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cliente_telefono" class="form-label">
                                        <i class="bi bi-telephone-fill"></i> Teléfono *
                                    </label>
                                    <input type="text" id="cliente_telefono" name="cliente_telefono"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cliente_empresa" class="form-label">
                                        <i class="bi bi-building"></i> Empresa (opcional)
                                    </label>
                                    <input type="text" id="cliente_empresa" name="cliente_empresa"
                                        class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cliente_direccion" class="form-label">
                                        <i class="bi bi-geo-alt-fill"></i> Dirección
                                    </label>
                                    <input type="text" id="cliente_direccion" name="cliente_direccion"
                                        class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Botón para nuevo cliente -->
                        <div id="btnNuevoCliente" style="display:none;">
                            <button type="button" id="btnCrearCliente" class="btn btn-outline-green">
                                <i class="bi bi-person-plus"></i> Crear Nuevo Cliente
                            </button>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: DATOS DEL VEHÍCULO -->
                    <div class="section-card" id="seccionVehiculo" style="display:none;">
                        <div class="section-header">
                            <h3>
                                <i class="bi bi-car-front"></i>
                                Datos del Vehículo
                            </h3>
                        </div>

                        <!-- Selector de vehículos existentes -->
                        <div id="vehiculosExistentes" style="display:none;">
                            <p class="text-muted mb-3">Seleccione un vehículo del cliente:</p>
                            <div id="listaVehiculos" class="row g-3">
                                <!-- Se llenará dinámicamente -->
                            </div>
                            <div class="mt-3">
                                <button type="button" id="btnNuevoVehiculo" class="btn btn-outline-green">
                                    <i class="bi bi-plus-circle"></i> Agregar Nuevo Vehículo
                                </button>
                            </div>
                        </div>

                        <!-- Formulario de nuevo vehículo -->
                        <div id="formNuevoVehiculo" style="display:none;">

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="marca" class="form-label">
                                        <i class="bi bi-car-front"></i> Marca *
                                    </label>
                                    <input type="text" name="marca" id="marca" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="modelo" class="form-label">
                                        <i class="bi bi-tag"></i> Modelo *
                                    </label>
                                    <input type="text" name="modelo" id="modelo" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="anio" class="form-label">
                                        <i class="bi bi-calendar3"></i> Año *
                                    </label>
                                    <input type="number" name="anio" id="anio" class="form-control"
                                        min="1900" max="2030" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="color" class="form-label">
                                        <i class="bi bi-palette"></i> Color *
                                    </label>
                                    <input type="text" name="color" id="color" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="placas" class="form-label">
                                        <i class="bi bi-credit-card"></i> Placas *
                                    </label>
                                    <input type="text" name="placas" id="placas" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="numero_serie" class="form-label">
                                        <i class="bi bi-upc-scan"></i> N° Serie/VIN
                                    </label>
                                    <input type="text" name="numero_serie" id="numero_serie" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 3: DETALLES DE LA ORDEN -->
                    <div class="section-card">
                        <div class="section-header">
                            <h3>
                                <i class="bi bi-tools"></i>
                                Detalles del Servicio
                            </h3>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha_orden" class="form-label">
                                    <i class="bi bi-calendar-event"></i> Fecha de ingreso *
                                </label>
                                <input type="date" name="fecha_orden" id="fecha_orden"
                                    class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="hora_ingreso" class="form-label">
                                    <i class="bi bi-clock"></i> Hora de ingreso *
                                </label>
                                <input type="time" name="hora_ingreso" id="hora_ingreso"
                                    class="form-control" value="<?= date('H:i') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kilometraje_actual" class="form-label">
                                    <i class="bi bi-speedometer2"></i> Kilometraje actual *
                                </label>
                                <input type="number" name="kilometraje_actual" id="kilometraje_actual"
                                    class="form-control" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="proximo_servicio_km" class="form-label">
                                    <i class="bi bi-arrow-right-circle"></i> Próximo servicio (km)
                                </label>
                                <input type="number" name="proximo_servicio_km" id="proximo_servicio_km"
                                    class="form-control" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-truck"></i> ¿Ingresó en grúa?
                                </label>
                                <div class="d-flex gap-4 mt-2">
                                    <div class="form-check">
                                        <input type="radio" name="ingreso_grua" value="1"
                                            id="grua_si" class="form-check-input">
                                        <label for="grua_si" class="form-check-label">Sí</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="ingreso_grua" value="0"
                                            id="grua_no" class="form-check-input" checked>
                                        <label for="grua_no" class="form-check-label">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nivel_combustible" class="form-label">
                                    <i class="bi bi-fuel-pump"></i> Nivel de combustible
                                </label>
                                <!-- Campo hidden que guarda el valor real para el form -->
                                <input type="hidden" name="nivel_combustible" id="nivel_combustible" value="1/2">

                                <!-- Barra drag con snap -->
                                <div class="fuel-drag-wrapper">
                                    <div class="fuel-track" id="fuelTrack">
                                        <div class="fuel-fill" id="fuelFill"></div>
                                        <div class="fuel-thumb" id="fuelThumb"></div>
                                        <!-- Marcadores de snap -->
                                        <div class="fuel-snap-markers">
                                            <span class="fuel-snap-dot" data-index="0"></span>
                                            <span class="fuel-snap-dot" data-index="1"></span>
                                            <span class="fuel-snap-dot" data-index="2"></span>
                                            <span class="fuel-snap-dot" data-index="3"></span>
                                            <span class="fuel-snap-dot" data-index="4"></span>
                                        </div>
                                    </div>
                                    <!-- Etiquetas -->
                                    <div class="fuel-labels">
                                        <span>E</span>
                                        <span>1/4</span>
                                        <span>1/2</span>
                                        <span>3/4</span>
                                        <span>F</span>
                                    </div>
                                    <!-- Valor actual visible -->
                                    <div class="fuel-value-display" id="fuelValueDisplay">1/2</div>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="trabajo_realizar" class="form-label">
                                    <i class="bi bi-list-task"></i> Trabajo a realizar *
                                </label>
                                <textarea name="trabajo_realizar" id="trabajo_realizar"
                                    class="form-control" rows="5"
                                    placeholder="Describa detalladamente el trabajo que se realizará en el vehículo..."
                                    required></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="observaciones" class="form-label">
                                    <i class="bi bi-chat-left-text"></i> Observaciones
                                </label>
                                <textarea name="observaciones" id="observaciones"
                                    class="form-control" rows="3"
                                    placeholder="Observaciones adicionales..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 4: INVENTARIO DEL VEHÍCULO -->
                    <div class="section-card">
                        <div class="section-header">
                            <h3>
                                <i class="bi bi-list-check"></i>
                                Inventario del Vehículo
                            </h3>
                        </div>

                        <p class="text-muted mb-3">Marque los items que el vehículo trae consigo:</p>

                        <div class="inventory-grid">
                            <div class="inventory-item">
                                <input type="checkbox" name="inventario[gato]" id="inv_gato" value="1">
                                <label for="inv_gato">
                                    <i class="bi bi-tools"></i> Gato
                                </label>
                            </div>
                            <div class="inventory-item">
                                <input type="checkbox" name="inventario[herramientas]" id="inv_herramientas" value="1">
                                <label for="inv_herramientas">
                                    <i class="bi bi-wrench"></i> Herramientas
                                </label>
                            </div>
                            <div class="inventory-item">
                                <input type="checkbox" name="inventario[triangulos]" id="inv_triangulos" value="1">
                                <label for="inv_triangulos">
                                    <i class="bi bi-triangle"></i> Triángulos
                                </label>
                            </div>
                            <div class="inventory-item">
                                <input type="checkbox" name="inventario[tapetes]" id="inv_tapetes" value="1">
                                <label for="inv_tapetes">
                                    <i class="bi bi-square"></i> Tapetes
                                </label>
                            </div>
                            <div class="inventory-item">
                                <input type="checkbox" name="inventario[llanta_refaccion]" id="inv_llanta" value="1">
                                <label for="inv_llanta">
                                    <i class="bi bi-circle"></i> Llanta refacción
                                </label>
                            </div>
                            <div class="inventory-item">
                                <input type="checkbox" name="inventario[extintor]" id="inv_extintor" value="1">
                                <label for="inv_extintor">
                                    <i class="bi bi-fire"></i> Extintor
                                </label>
                            </div>
                            <div class="inventory-item">
                                <input type="checkbox" name="inventario[antena]" id="inv_antena" value="1">
                                <label for="inv_antena">
                                    <i class="bi bi-broadcast"></i> Antena
                                </label>
                            </div>
                            <div class="inventory-item">
                                <input type="checkbox" name="inventario[emblemas]" id="inv_emblemas" value="1">
                                <label for="inv_emblemas">
                                    <i class="bi bi-badge-tm"></i> Emblemas
                                </label>
                            </div>
                            <div class="inventory-item">
                                <input type="checkbox" name="inventario[tapones_rueda]" id="inv_tapones" value="1">
                                <label for="inv_tapones">
                                    <i class="bi bi-circle-fill"></i> Tapones rueda
                                </label>
                            </div>
                            <div class="inventory-item">
                                <input type="checkbox" name="inventario[cables]" id="inv_cables" value="1">
                                <label for="inv_cables">
                                    <i class="bi bi-lightning"></i> Cables
                                </label>
                            </div>
                            <div class="inventory-item">
                                <input type="checkbox" name="inventario[estereo]" id="inv_estereo" value="1">
                                <label for="inv_estereo">
                                    <i class="bi bi-music-note-beamed"></i> Estéreo
                                </label>
                            </div>
                            <div class="inventory-item">
                                <input type="checkbox" name="inventario[encendedor]" id="inv_encendedor" value="1">
                                <label for="inv_encendedor">
                                    <i class="bi bi-fire"></i> Encendedor
                                </label>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <label for="inventario_otros" class="form-label">
                                    <i class="bi bi-plus-circle"></i> Otros items
                                </label>
                                <input type="text" name="inventario[otros]" id="inventario_otros"
                                    class="form-control"
                                    placeholder="Especifique otros items que trae el vehículo...">
                            </div>
                        </div>
                    </div>


                    <!-- SECCIÓN: SERVICIOS Y MANO DE OBRA -->
                    <div class="section-card">
                        <div class="section-header">
                            <h3>
                                <i class="bi bi-tools"></i>
                                Servicios y Repuestos
                            </h3>
                        </div>

                        <!-- Búsqueda de servicios -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label">
                                    <i class="bi bi-search"></i> Buscar servicio o agregar manualmente
                                </label>
                                <input type="text" id="buscarServicio" class="form-control"
                                    placeholder="Escriba para buscar en el catálogo o presione Enter para agregar...">
                                <div id="resultadosServicios" class="autocomplete-results"></div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" id="btnAgregarServicioManual" class="btn btn-outline-green w-100">
                                    <i class="bi bi-plus-circle"></i> Agregar Manual
                                </button>
                            </div>
                        </div>

                        <!-- Tabla de servicios agregados -->
                        <div class="table-responsive">
                            <table class="table-servicios">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th style="width: 45%">Descripción</th>
                                        <th style="width: 12%">Cantidad</th>
                                        <th style="width: 15%">Precio Unit.</th>
                                        <th style="width: 15%">Subtotal</th>
                                        <th style="width: 8%">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaServicios">
                                    <tr class="empty-state">
                                        <td colspan="6" class="text-center" style="color: #666; padding: 2rem;">
                                            <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                                            No hay servicios agregados
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="total-row">
                                        <td colspan="4" class="text-end"><strong>TOTAL:</strong></td>
                                        <td colspan="2"><strong id="totalServicios">Q 0.00</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- SECCIÓN 5: DAÑOS DEL VEHÍCULO — Reemplaza tu sección actual -->
                    <!-- ============================================================ -->
                    <div class="section-card">
                        <div class="section-header">
                            <h3>
                                <i class="bi bi-exclamation-triangle"></i>
                                Daños Preexistentes del Vehículo
                            </h3>
                        </div>

                        <div class="alert alert-info" style="background: rgba(0,123,255,0.1); border: 1px solid rgba(0,123,255,0.3); color: #b0b0b0;">
                            <i class="bi bi-info-circle"></i>
                            Seleccione una vista y haga clic sobre el vehículo para marcar daños preexistentes.
                        </div>

                        <!-- Tabs de vistas -->
                        <div class="vista-tabs">
                            <button type="button" class="vista-tab active" data-vista="frontal" data-img="/comodin_motors/public/images/front.png">
                                <i class="bi bi-arrow-up-circle"></i> Frontal
                            </button>
                            <button type="button" class="vista-tab" data-vista="trasero" data-img="/comodin_motors/public/images/back.png">
                                <i class="bi bi-arrow-down-circle"></i> Trasero
                            </button>
                            <button type="button" class="vista-tab" data-vista="lateral_izquierdo" data-img="/comodin_motors/public/images/left.png">
                                <i class="bi bi-arrow-left-circle"></i> Lat. Izquierdo
                            </button>
                            <button type="button" class="vista-tab" data-vista="lateral_derecho" data-img="/comodin_motors/public/images/rigth.png">
                                <i class="bi bi-arrow-right-circle"></i> Lat. Derecho
                            </button>
                            <button type="button" class="vista-tab" data-vista="techo" data-img="/comodin_motors/public/images/top.png">
                                <i class="bi bi-arrow-up-square"></i> Techo
                            </button>
                        </div>

                        <!-- Contenedor del diagrama -->
                        <div class="diagram-wrapper">
                            <div class="diagram-container" id="diagramContainer">
                                <img id="carImage" src="/comodin_motors/public/images/front.png" alt="Vista frontal" draggable="false">
                                <!-- Los markers se insertan aquí dinámicamente -->
                            </div>
                            <p class="diagram-hint"><i class="bi bi-cursor"></i> Haz clic en el vehículo para marcar un daño</p>
                        </div>

                        <!-- Modal para describir el daño -->
                        <div id="modalDano" class="dano-modal" style="display:none;">
                            <div class="dano-modal-content">
                                <h5 class="text-white mb-3"><i class="bi bi-exclamation-triangle text-danger"></i> Describir Daño</h5>
                                <div class="mb-3">
                                    <label class="form-label">Tipo de daño *</label>
                                    <select id="tipoDanoInput" class="form-select">
                                        <option value="rayón">Rayón</option>
                                        <option value="abolladura">Abolladura</option>
                                        <option value="cristal_roto">Cristal roto</option>
                                        <option value="faltante">Faltante</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Descripción *</label>
                                    <input type="text" id="descripcionDanoInput" class="form-control"
                                        placeholder="Ej: Rayón en puerta delantera..." maxlength="255">
                                </div>
                                <div class="d-flex gap-2 justify-content-end">
                                    <button type="button" id="btnCancelarDano" class="btn btn-outline-green">
                                        Cancelar
                                    </button>
                                    <button type="button" id="btnConfirmarDano" class="btn btn-green">
                                        <i class="bi bi-check-lg"></i> Guardar Daño
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de daños registrados -->
                        <div id="damageList" class="damage-list mt-4" style="display:none;">
                            <h5 class="text-white mb-3">
                                <i class="bi bi-list-ul"></i> Daños registrados
                                <span id="danoCount" class="badge ms-2" style="background:#ff4444; font-size:0.85rem;">0</span>
                            </h5>
                            <div id="damageItems"></div>
                        </div>
                    </div>

                </div> <!-- Fin col-lg-8 -->

                <!-- COLUMNA DERECHA: RESUMEN -->
                <div class="col-lg-4">
                    <div class="order-summary">
                        <div class="text-center mb-4">
                            <div class="order-number" id="numeroOrden">
                                <span style="font-size: 1rem; color: #b0b0b0;">N° Orden</span><br>
                                <span style="color: #00ff00;">---</span>
                            </div>
                        </div>

                        <div class="summary-item">
                            <span class="summary-label">
                                <i class="bi bi-person"></i> Cliente:
                            </span>
                            <span class="summary-value" id="resumen_cliente">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">
                                <i class="bi bi-telephone"></i> Teléfono:
                            </span>
                            <span class="summary-value" id="resumen_telefono">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">
                                <i class="bi bi-car-front"></i> Vehículo:
                            </span>
                            <span class="summary-value" id="resumen_vehiculo">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">
                                <i class="bi bi-credit-card"></i> Placas:
                            </span>
                            <span class="summary-value" id="resumen_placas">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">
                                <i class="bi bi-speedometer2"></i> Kilometraje:
                            </span>
                            <span class="summary-value" id="resumen_km">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">
                                <i class="bi bi-fuel-pump"></i> Combustible:
                            </span>
                            <span class="summary-value" id="resumen_combustible">1/2</span>
                        </div>
                        <div class="summary-item" style="border-bottom: none;">
                            <span class="summary-label">
                                <i class="bi bi-hourglass-split"></i> Estado:
                            </span>
                            <span class="badge" style="background: rgba(255, 193, 7, 0.2); color: #ffc107;">
                                Pendiente
                            </span>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-green w-100" id="btnGuardarOrden">
                                <i class="bi bi-save-fill"></i> Guardar Orden
                            </button>
                            <a href="/comodin_motors/dashboard" class="btn btn-outline-green w-100 mt-2">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>

                        <div class="mt-3 text-center" style="font-size: 0.85rem; color: #666;">
                            <i class="bi bi-info-circle"></i>
                            Al guardar se generará el número de orden automáticamente
                        </div>
                    </div>
                </div>

            </div> <!-- Fin row principal -->
        </form>

    </div> <!-- Fin container-fluid -->
</div> <!-- Fin orden-container -->