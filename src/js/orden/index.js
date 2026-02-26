import { Toast } from "../funciones";
import Swal from "sweetalert2";

// ============================================
// INICIALIZACIÓN
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    cargarOrdenes();
    cargarEstadisticas();
    inicializarFiltros();
    console.log('✅ Sistema de Órdenes Inicializado');
});

// ============================================
// CARGAR ESTADÍSTICAS
// ============================================
async function cargarEstadisticas() {
    try {
        const response = await fetch('/comodin_motors/API/ordenes/buscar');
        const data = await response.json();

        if (data.codigo === 1 && data.datos) {
            const ordenes = data.datos;
            document.getElementById('stat-pendientes').textContent =
                ordenes.filter(o => o.estado_orden === 'pendiente').length;
            document.getElementById('stat-proceso').textContent =
                ordenes.filter(o => o.estado_orden === 'en_proceso').length;
            document.getElementById('stat-completado').textContent =
                ordenes.filter(o => o.estado_orden === 'completado').length;
            document.getElementById('stat-entregado').textContent =
                ordenes.filter(o => o.estado_orden === 'entregado').length;
        }
    } catch (error) {
        console.error('Error al cargar estadísticas:', error);
    }
}

// ============================================
// CARGAR ÓRDENES EN TABLA
// ============================================
async function cargarOrdenes(filtros = {}) {
    const tabla = document.getElementById('tablaOrdenes');
    tabla.innerHTML = `
        <thead>
            <tr>
                <th>N° Orden</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>Placas</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tablaBody">
            <tr><td colspan="8" class="text-center" style="color:#666; padding:2rem;">
                <i class="bi bi-hourglass-split" style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
                Cargando órdenes...
            </td></tr>
        </tbody>`;

    try {
        let url = '/comodin_motors/API/ordenes/buscar';
        const params = new URLSearchParams();
        if (filtros.estado) params.append('estado', filtros.estado);
        if (filtros.fecha_desde) params.append('fecha_desde', filtros.fecha_desde);
        if (filtros.fecha_hasta) params.append('fecha_hasta', filtros.fecha_hasta);
        if (params.toString()) url += '?' + params.toString();

        const response = await fetch(url);
        const data = await response.json();

        const tbody = document.getElementById('tablaBody');

        if (data.codigo === 1 && data.datos && data.datos.length > 0) {
            tbody.innerHTML = '';
            data.datos.forEach(orden => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <a href="/comodin_motors/orden/ver?id=${orden.id_orden}"
                           style="color:#00ff00; font-weight:700; font-family:monospace; text-decoration:none;">
                            #${orden.numero_orden}
                        </a>
                    </td>
                    <td>${orden.cliente_nombre ?? '-'}</td>
                    <td>${(orden.marca ?? '') + ' ' + (orden.modelo ?? '') + ' ' + (orden.anio ?? '')}</td>
                    <td style="color:#00ff00; font-weight:600;">${orden.placas ?? '-'}</td>
                    <td>${orden.fecha_orden ? new Date(orden.fecha_orden).toLocaleDateString('es-GT') : '-'}</td>
                    <td><span class="badge-estado badge-${orden.estado_orden ?? 'pendiente'}">
                        ${estadoLabel(orden.estado_orden)}
                    </span></td>
                    <td>Q ${parseFloat(orden.costo_total ?? 0).toFixed(2)}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="/comodin_motors/orden/ver?id=${orden.id_orden}"
                               class="btn-acciones btn-ver" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button class="btn-acciones btn-estado" title="Cambiar estado"
                                onclick="cambiarEstado(${orden.id_orden}, '${orden.estado_orden}')">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            tbody.innerHTML = `
                <tr><td colspan="8" class="text-center" style="color:#666; padding:3rem;">
                    <i class="bi bi-inbox" style="font-size:3rem; display:block; margin-bottom:1rem; color:#3a3a3a;"></i>
                    No hay órdenes registradas
                </td></tr>`;
        }
    } catch (error) {
        console.error('Error al cargar órdenes:', error);
        document.getElementById('tablaBody').innerHTML = `
            <tr><td colspan="8" class="text-center" style="color:#ff4444; padding:2rem;">
                Error al cargar las órdenes
            </td></tr>`;
    }
}

// ============================================
// FILTROS
// ============================================
function inicializarFiltros() {
    const btnFiltrar = document.getElementById('btnFiltrar');
    if (btnFiltrar) {
        btnFiltrar.addEventListener('click', () => {
            const filtros = {
                estado: document.getElementById('filtro_estado')?.value || '',
                fecha_desde: document.getElementById('filtro_fecha_desde')?.value || '',
                fecha_hasta: document.getElementById('filtro_fecha_hasta')?.value || ''
            };
            cargarOrdenes(filtros);
            cargarEstadisticas();
        });
    }
}

// ============================================
// CAMBIAR ESTADO
// ============================================
window.cambiarEstado = async (id_orden, estadoActual) => {
    const estados = [
        { value: 'pendiente', label: 'Pendiente' },
        { value: 'en_proceso', label: 'En Proceso' },
        { value: 'completado', label: 'Completado' },
        { value: 'entregado', label: 'Entregado' },
        { value: 'cancelado', label: 'Cancelado' }
    ];

    const opcionesHtml = estados
        .filter(e => e.value !== estadoActual)
        .map(e => `<option value="${e.value}">${e.label}</option>`)
        .join('');

    const { value: nuevoEstado } = await Swal.fire({
        title: 'Cambiar estado',
        html: `<select id="nuevo-estado" class="swal2-select"
                    style="background:#2a2a2a; border:1px solid #3a3a3a; color:#fff;">
                ${opcionesHtml}
               </select>`,
        confirmButtonText: 'Cambiar',
        cancelButtonText: 'Cancelar',
        showCancelButton: true,
        confirmButtonColor: '#00ff00',
        background: '#1a1a1a',
        color: '#fff',
        preConfirm: () => document.getElementById('nuevo-estado').value
    });

    if (!nuevoEstado) return;

    try {
        const formData = new FormData();
        formData.append('id_orden', id_orden);
        formData.append('estado', nuevoEstado);

        const response = await fetch('/comodin_motors/API/ordenes/estado', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.codigo === 1) {
            Toast.fire({ icon: 'success', title: 'Estado actualizado' });
            cargarOrdenes();
            cargarEstadisticas();
        } else {
            Toast.fire({ icon: 'error', title: data.mensaje || 'Error al cambiar estado' });
        }
    } catch (error) {
        Toast.fire({ icon: 'error', title: 'Error al cambiar estado' });
    }
};

// ============================================
// HELPERS
// ============================================
function estadoLabel(estado) {
    const labels = {
        pendiente: 'Pendiente',
        en_proceso: 'En Proceso',
        completado: 'Completado',
        entregado: 'Entregado',
        cancelado: 'Cancelado'
    };
    return labels[estado] ?? estado;
}

console.log('✅ index.js de órdenes cargado');