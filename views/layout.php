<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= asset('images/1.png') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('build/styles.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Comodín Motors</title>
</head>

<body>
    <!-- ============================================ -->
    <!-- LOADER ANIMADO -->
    <!-- ============================================ -->
    <div id="loader" class="loader-wrapper">
        <div class="loader-container">
            <!-- Logo animado -->
            <div class="loader-logo">
                <img src="<?= asset('./images/1.png') ?>" alt="Comodín Motors">
            </div>

            <!-- Animación de engranajes -->
            <div class="gears">
                <div class="gear gear-1">
                    <i class="bi bi-gear-fill"></i>
                </div>
                <div class="gear gear-2">
                    <i class="bi bi-gear-fill"></i>
                </div>
            </div>

            <!-- Barra de progreso -->
            <div class="loader-progress">
                <div class="loader-progress-bar"></div>
            </div>

            <!-- Texto -->
            <div class="loader-text">
                <span class="loading-dots">Cargando</span>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- SIDEBAR LATERAL -->
    <!-- ============================================ -->
    <aside id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="<?= asset('./images/1.png') ?>" alt="Logo">
                <span class="sidebar-title">Comodín Motors</span>
            </div>
            <button id="sidebarClose" class="sidebar-close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-list">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="/comodin_motors/dashboard" class="nav-link active">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Órdenes -->
                <li class="nav-item has-submenu">
                    <a href="#" class="nav-link">
                        <i class="bi bi-card-checklist"></i>
                        <span>Órdenes</span>
                        <i class="bi bi-chevron-down arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="/comodin_motors/orden"><i class="bi bi-list-ul"></i> Ver todas</a></li>
                        <li><a href="/comodin_motors/orden/nueva"><i class="bi bi-plus-circle"></i> Nueva orden</a></li>
                        <li><a href="/comodin_motors/orden/pendientes"><i class="bi bi-clock-history"></i> Pendientes</a></li>
                        <li><a href="/comodin_motors/orden/proceso"><i class="bi bi-tools"></i> En proceso</a></li>
                        <li><a href="/comodin_motors/orden/completadas"><i class="bi bi-check-circle"></i> Completadas</a></li>
                    </ul>
                </li>

                <!-- Clientes -->
                <li class="nav-item has-submenu">
                    <a href="#" class="nav-link">
                        <i class="bi bi-person-square"></i>
                        <span>Clientes</span>
                        <i class="bi bi-chevron-down arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="/clientes"><i class="bi bi-people"></i> Ver todos</a></li>
                        <li><a href="/clientes/nuevo"><i class="bi bi-person-plus"></i> Nuevo cliente</a></li>
                        <li><a href="/clientes/buscar"><i class="bi bi-search"></i> Buscar</a></li>
                    </ul>
                </li>

                <!-- Vehículos -->
                <li class="nav-item has-submenu">
                    <a href="#" class="nav-link">
                        <i class="bi bi-car-front-fill"></i>
                        <span>Vehículos</span>
                        <i class="bi bi-chevron-down arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="/vehiculos"><i class="bi bi-list-ul"></i> Ver todos</a></li>
                        <li><a href="/vehiculos/nuevo"><i class="bi bi-plus-circle"></i> Nuevo vehículo</a></li>
                        <li><a href="/vehiculos/mantenimiento"><i class="bi bi-wrench"></i> Próximos servicios</a></li>
                    </ul>
                </li>

                <!-- Técnicos -->
                <li class="nav-item has-submenu">
                    <a href="#" class="nav-link">
                        <i class="bi bi-wrench-adjustable"></i>
                        <span>Técnicos</span>
                        <i class="bi bi-chevron-down arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="/tecnicos"><i class="bi bi-people"></i> Ver todos</a></li>
                        <li><a href="/tecnicos/nuevo"><i class="bi bi-person-plus"></i> Nuevo técnico</a></li>
                        <li><a href="/tecnicos/asignaciones"><i class="bi bi-diagram-3"></i> Asignaciones</a></li>
                    </ul>
                </li>

                <!-- Reportes -->
                <li class="nav-item has-submenu">
                    <a href="#" class="nav-link">
                        <i class="bi bi-graph-up"></i>
                        <span>Reportes</span>
                        <i class="bi bi-chevron-down arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="/reportes/ingresos"><i class="bi bi-cash-stack"></i> Ingresos</a></li>
                        <li><a href="/reportes/servicios"><i class="bi bi-tools"></i> Servicios</a></li>
                        <li><a href="/reportes/clientes"><i class="bi bi-people"></i> Top clientes</a></li>
                        <li><a href="/reportes/tecnicos"><i class="bi bi-person-check"></i> Rendimiento</a></li>
                    </ul>
                </li>

                <li class="nav-divider"></li>

                <!-- Configuración -->
                <li class="nav-item has-submenu">
                    <a href="#" class="nav-link">
                        <i class="bi bi-gear"></i>
                        <span>Configuración</span>
                        <i class="bi bi-chevron-down arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="/configuracion/general"><i class="bi bi-sliders"></i> General</a></li>
                        <li><a href="/configuracion/taller"><i class="bi bi-building"></i> Datos del taller</a></li>
                    </ul>
                </li>

                <!-- Usuarios -->
                <li class="nav-item has-submenu">
                    <a href="#" class="nav-link">
                        <i class="bi bi-people-fill"></i>
                        <span>Usuarios</span>
                        <i class="bi bi-chevron-down arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="/usuarios"><i class="bi bi-list-ul"></i> Ver todos</a></li>
                        <li><a href="/usuarios/nuevo"><i class="bi bi-person-plus"></i> Nuevo usuario</a></li>
                        <li><a href="/usuarios/permisos"><i class="bi bi-shield-lock"></i> Permisos</a></li>
                    </ul>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="user-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="user-info">
                    <div class="user-name">Admin</div>
                    <div class="user-role">Administrador</div>
                </div>
            </div>
            <a href="/logout" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i>
                Cerrar sesión
            </a>
        </div>
    </aside>

    <!-- ============================================ -->
    <!-- OVERLAY para cerrar sidebar en móvil -->
    <!-- ============================================ -->
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <!-- ============================================ -->
    <!-- TOPBAR (barra superior) -->
    <!-- ============================================ -->
    <header class="topbar">
        <button id="sidebarToggle" class="sidebar-toggle">
            <i class="bi bi-list"></i>
        </button>

        <div class="topbar-title">
            <h1><?= $titulo ?? 'Dashboard' ?></h1>
        </div>

        <div class="topbar-actions">
            <!-- Notificaciones -->
            <button class="topbar-btn" title="Notificaciones">
                <i class="bi bi-bell"></i>
                <span class="badge">3</span>
            </button>

            <!-- Búsqueda rápida -->
            <button class="topbar-btn" title="Búsqueda rápida">
                <i class="bi bi-search"></i>
            </button>

            <!-- Pantalla completa -->
            <button class="topbar-btn" id="fullscreenBtn" title="Pantalla completa">
                <i class="bi bi-arrows-fullscreen"></i>
            </button>

            <!-- Usuario -->
            <div class="topbar-user">
                <img src="https://ui-avatars.com/api/?name=Admin&background=00ff00&color=000" alt="User">
            </div>
        </div>
    </header>

    <!-- ============================================ -->
    <!-- CONTENIDO PRINCIPAL -->
    <!-- ============================================ -->
    <main class="main-content">
        <div class="container-fluid">
            <?php echo $contenido; ?>
        </div>
    </main>

    <!-- ============================================ -->
    <!-- FOOTER -->
    <!-- ============================================ -->
    <footer class="main-footer">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?= date('Y') ?> Comodín Motors - Todos los derechos reservados</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Desarrollado por <strong>Frankd Developer</strong></p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Barra de progreso de scroll -->
    <div class="scroll-progress">
        <div class="scroll-progress-bar" id="scrollProgressBar"></div>
    </div>

    <script src="<?= asset('build/js/app.js') ?>"></script>
    <?php if (isset($script)): ?>
        <script src="<?= asset('build/js/' . $script . '.js') ?>"></script>
    <?php endif; ?>
</body>

</html>