import { Dropdown } from "bootstrap";
import '../scss/app.scss';

document.addEventListener('DOMContentLoaded', (e) => {
    // ⭐ VALIDAR QUE EL ELEMENTO EXISTA ANTES DE USARLO
    const dropdown = document.querySelector('.dropdown-menu');
    if (dropdown) {
        dropdown.style.margin = 0;
    }

    let items = document.querySelectorAll('.nav-link')
    items.forEach(item => {
        if (item.href == location.href) {
            item.classList.add('active')
            if (item.classList.contains('dropdown-item')) {
                item.parentElement.parentElement.previousElementSibling.classList.add('active')
            }
        }
    });
})

document.onreadystatechange = () => {
    switch (document.readyState) {
        case "loading":
            break;
        case "interactive":
            const barInteractive = document.getElementById('bar');
            if (barInteractive) {
                barInteractive.style.width = '35%';
            }
            break;

        case "complete":
            const barComplete = document.getElementById('bar');
            if (barComplete) {
                barComplete.style.width = '100%';
                setTimeout(() => {
                    if (barComplete.parentElement) {
                        barComplete.parentElement.style.display = 'none';
                    }
                }, 1000);
            }
            break;
    }
}

// ============================================
// COMODÍN MOTORS - Sistema de Gestión
// ============================================

class ComodinMotorsApp {
    constructor() {
        // ⭐ VERIFICAR QUE EXISTAN ANTES DE USAR
        this.sidebar = document.getElementById('sidebar');
        this.sidebarToggle = document.getElementById('sidebarToggle');
        this.sidebarClose = document.getElementById('sidebarClose');
        this.sidebarOverlay = document.getElementById('sidebarOverlay');
        this.loader = document.getElementById('loader');
        this.scrollProgressBar = document.getElementById('scrollProgressBar');

        this.init();
    }

    init() {
        // Ocultar loader cuando la página cargue (solo si existe)
        if (this.loader) {
            this.initLoader();
        }

        // ⭐ SOLO INICIALIZAR SI EXISTEN LOS ELEMENTOS
        if (this.sidebar && this.sidebarToggle) {
            this.initSidebar();
            this.initSubmenus();
        }

        if (this.scrollProgressBar) {
            this.initScrollProgress();
        }

        this.initFullscreen();

        if (this.sidebar) {
            this.setActiveLink();
        }

        console.log('🚗 Comodín Motors App Initialized');
    }

    // ============================================
    // LOADER
    // ============================================
    initLoader() {
        window.addEventListener('load', () => {
            setTimeout(() => {
                if (this.loader) {
                    this.loader.classList.add('hidden');
                    setTimeout(() => {
                        this.loader.style.display = 'none';
                    }, 500);
                }
            }, 1500);
        });
    }

    // ============================================
    // SIDEBAR
    // ============================================
    initSidebar() {
        if (this.sidebarToggle) {
            this.sidebarToggle.addEventListener('click', () => {
                this.toggleSidebar();
            });
        }

        if (this.sidebarClose) {
            this.sidebarClose.addEventListener('click', () => {
                this.closeSidebar();
            });
        }

        if (this.sidebarOverlay) {
            this.sidebarOverlay.addEventListener('click', () => {
                this.closeSidebar();
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.sidebar) {
                this.closeSidebar();
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 991 && this.sidebar) {
                this.closeSidebar();
            }
        });
    }

    toggleSidebar() {
        if (!this.sidebar || !this.sidebarOverlay) return;

        this.sidebar.classList.toggle('active');
        this.sidebarOverlay.classList.toggle('active');
        document.body.style.overflow = this.sidebar.classList.contains('active') ? 'hidden' : '';
    }

    closeSidebar() {
        if (!this.sidebar || !this.sidebarOverlay) return;

        this.sidebar.classList.remove('active');
        this.sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    // ============================================
    // SUBMENUS
    // ============================================
    initSubmenus() {
        const submenuItems = document.querySelectorAll('.has-submenu > .nav-link');

        submenuItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();

                const parent = item.parentElement;
                const wasActive = parent.classList.contains('active');

                document.querySelectorAll('.has-submenu').forEach(sub => {
                    sub.classList.remove('active');
                });

                if (!wasActive) {
                    parent.classList.add('active');
                }
            });
        });

        document.querySelectorAll('.submenu a').forEach(link => {
            if (link.classList.contains('active')) {
                link.closest('.has-submenu').classList.add('active');
            }
        });
    }

    // ============================================
    // SCROLL PROGRESS BAR
    // ============================================
    initScrollProgress() {
        window.addEventListener('scroll', () => {
            const winScroll = document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;

            if (this.scrollProgressBar) {
                this.scrollProgressBar.style.width = scrolled + '%';
            }
        });
    }

    // ============================================
    // PANTALLA COMPLETA
    // ============================================
    initFullscreen() {
        const fullscreenBtn = document.getElementById('fullscreenBtn');

        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', () => {
                this.toggleFullscreen();
            });
        }
    }

    toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                console.error('Error al activar pantalla completa:', err);
            });
        } else {
            document.exitFullscreen();
        }
    }

    // ============================================
    // MARCAR LINK ACTIVO
    // ============================================
    setActiveLink() {
        const currentPath = window.location.pathname;
        const links = document.querySelectorAll('.sidebar-nav a');

        links.forEach(link => {
            link.classList.remove('active');

            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');

                const submenu = link.closest('.submenu');
                if (submenu) {
                    const parentSubmenu = submenu.closest('.has-submenu');
                    if (parentSubmenu) {
                        parentSubmenu.classList.add('active');
                    }
                }
            }
        });
    }
}

// ============================================
// UTILIDADES ADICIONALES
// ============================================

function smoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
}

function initTooltips() {
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

function confirmDelete() {
    document.querySelectorAll('[data-confirm-delete]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm('¿Estás seguro de que deseas eliminar este elemento?')) {
                e.preventDefault();
            }
        });
    });
}

function autoCloseAlerts() {
    if (typeof bootstrap !== 'undefined') {
        const alerts = document.querySelectorAll('.alert-auto-close');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    }
}

function formatCurrency(amount, currency = 'GTQ') {
    return new Intl.NumberFormat('es-GT', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

function formatDate(date, format = 'long') {
    const options = {
        short: { year: 'numeric', month: '2-digit', day: '2-digit' },
        long: { year: 'numeric', month: 'long', day: 'numeric' },
        time: { hour: '2-digit', minute: '2-digit' }
    };

    return new Intl.DateTimeFormat('es-GT', options[format]).format(new Date(date));
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Copiado al portapapeles', 'success');
    });
}

function showToast(message, type = 'info') {
    console.log(`[${type.toUpperCase()}] ${message}`);
}

function initFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');

    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });

        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function () {
                if (this.checkValidity()) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            });
        });
    });
}

function initTableSearch(searchInputId, tableId) {
    const searchInput = document.getElementById(searchInputId);
    const table = document.getElementById(tableId);

    if (searchInput && table) {
        searchInput.addEventListener('keyup', function () {
            const filter = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
}

function previewImage(input, previewId) {
    const file = input.files[0];
    const preview = document.getElementById(previewId);

    if (file && preview) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

function animateCounter(element, target, duration = 2000) {
    let current = 0;
    const increment = target / (duration / 16);

    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = Math.ceil(target);
            clearInterval(timer);
        } else {
            element.textContent = Math.ceil(current);
        }
    }, 16);
}

// Exportar funciones globales
window.ComodinMotors = {
    formatCurrency,
    formatDate,
    copyToClipboard,
    showToast,
    previewImage,
    animateCounter
};

// ============================================
// INICIALIZAR APP
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    new ComodinMotorsApp();

    smoothScroll();
    initTooltips();
    confirmDelete();
    autoCloseAlerts();
    initFormValidation();

    console.log('✅ Todas las funcionalidades cargadas');
});