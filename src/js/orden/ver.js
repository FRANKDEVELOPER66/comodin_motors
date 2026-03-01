import { Toast } from "../funciones";  // ← importar Toast igual que index.js
import Swal from "sweetalert2";

// ── CAMBIAR ESTADO ────────────────────────────────────────────
window.cambiarEstado = async (id_orden, estado) => {
    const labels = {
        en_proceso: 'En Proceso',
        completado: 'Completada',
        entregado: 'Entregada',
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
                title: '¡Listo!',
                text: `Estado cambiado a "${labels[estado]}"`,
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#00ff00',
                background: '#1a1a1a',
                color: '#fff'
            });

            // ✅ Marcar que debe saltar el loader
            sessionStorage.setItem('skipLoader', '1');
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
};

// ── ACTUALIZAR EL DOM SIN RECARGAR ────────────────────────────
function actualizarEstadoDOM(nuevoEstado) {
    const clases = {
        pendiente: 'estado-pendiente',
        en_proceso: 'estado-proceso',
        completado: 'estado-completado',
        entregado: 'estado-completado', // mismo estilo verde
        cancelado: 'estado-cancelado'
    };
    const labels = {
        pendiente: 'Pendiente',
        en_proceso: 'En Proceso',
        completado: 'Completado',
        entregado: 'Entregado',
        cancelado: 'Cancelado'
    };

    // Actualizar badge
    const badge = document.querySelector('.estado-badge');
    if (badge) {
        badge.className = 'estado-badge ' + (clases[nuevoEstado] ?? 'estado-pendiente');
        badge.textContent = labels[nuevoEstado] ?? nuevoEstado;
    }

    // Flujo de botones: ocultar todos y mostrar solo el siguiente
    const botonesConfig = {
        en_proceso: '[onclick*="en_proceso"]',
        completado: '[onclick*="completado"]',
        entregado: '[onclick*="entregado"]',
        cancelado: '[onclick*="cancelado"]'
    };

    // Ocultar todos primero
    Object.values(botonesConfig).forEach(selector => {
        const btn = document.querySelector(selector);
        if (btn) btn.style.display = 'none';
    });

    // Mostrar solo el siguiente según el nuevo estado
    const siguiente = {
        en_proceso: 'completado',
        completado: 'entregado',
        entregado: null,          // ya no hay siguiente
        cancelado: null
    };

    const proximoEstado = siguiente[nuevoEstado];
    if (proximoEstado) {
        const btnSiguiente = document.querySelector(botonesConfig[proximoEstado]);
        if (btnSiguiente) btnSiguiente.style.display = '';
    }

    // Cancelar solo se oculta si entregado o cancelado
    if (nuevoEstado === 'entregado' || nuevoEstado === 'cancelado') {
        const btnCancelar = document.querySelector(botonesConfig['cancelado']);
        if (btnCancelar) btnCancelar.style.display = 'none';
    }
}

// ── DAÑOS: tabs + pins ────────────────────────────────────────
const container = document.getElementById('verDiagramContainer');
const carImage = document.getElementById('verCarImage');
const damageList = document.getElementById('verDamageList');

if (container && carImage) {
    const raw = container.dataset.danos;
    if (raw) {
        const todosLosDanos = JSON.parse(raw);
        let vistaActual = container.dataset.vista;

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
            if (!damageList) return;
            const filtrados = todosLosDanos.filter(d => d.ubicacion === vistaActual);
            if (!filtrados.length) { damageList.innerHTML = ''; return; }
            damageList.innerHTML = filtrados.map((d, i) => `
                <div class="ver-dano-item">
                    <div class="ver-dano-num">${i + 1}</div>
                    <span class="ver-dano-tipo">${d.tipo_dano}</span>
                    <span class="ver-dano-desc">${d.descripcion}</span>
                </div>
            `).join('');
        }

        if (carImage.complete) {
            renderPins();
            renderList();
        } else {
            carImage.addEventListener('load', () => {
                renderPins();
                renderList();
            });
        }
    }
}

console.log('✅ ver.js cargado');