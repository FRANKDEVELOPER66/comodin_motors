import { Toast, validarFormulario } from "../funciones";
import Swal from "sweetalert2";

// ============================================
// VARIABLES GLOBALES
// ============================================
let clienteSeleccionado = null;
let vehiculoSeleccionado = null;

// ============================================
// ELEMENTOS DEL DOM
// ============================================
const elementos = {
    buscarTelefono: document.getElementById('buscar_telefono'),
    btnBuscarCliente: document.getElementById('btnBuscarCliente'),
    datosCliente: document.getElementById('datosCliente'),
    btnNuevoCliente: document.getElementById('btnNuevoCliente'),
    btnCrearCliente: document.getElementById('btnCrearCliente'),
    idCliente: document.getElementById('id_cliente'),
    clienteNombre: document.getElementById('cliente_nombre'),
    clienteTelefono: document.getElementById('cliente_telefono'),
    clienteEmpresa: document.getElementById('cliente_empresa'),
    clienteDireccion: document.getElementById('cliente_direccion'),
    seccionVehiculo: document.getElementById('seccionVehiculo'),
    vehiculosExistentes: document.getElementById('vehiculosExistentes'),
    listaVehiculos: document.getElementById('listaVehiculos'),
    btnNuevoVehiculo: document.getElementById('btnNuevoVehiculo'),
    formNuevoVehiculo: document.getElementById('formNuevoVehiculo'),
    idVehiculo: document.getElementById('id_vehiculo'),
    nivelCombustible: document.getElementById('nivel_combustible'),
    fuelLevel: document.getElementById('fuelLevel'),
    resumenCliente: document.getElementById('resumen_cliente'),
    resumenTelefono: document.getElementById('resumen_telefono'),
    resumenVehiculo: document.getElementById('resumen_vehiculo'),
    resumenPlacas: document.getElementById('resumen_placas'),
    resumenKm: document.getElementById('resumen_km'),
    resumenCombustible: document.getElementById('resumen_combustible'),
    formularioOrden: document.getElementById('formularioOrden'),
    btnGuardarOrden: document.getElementById('btnGuardarOrden')
};

// ============================================
// INICIALIZACIÓN
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    inicializarEventos();
    inicializarCombustible();
    console.log('✅ Sistema de Nueva Orden Inicializado');
});

function inicializarEventos() {
    if (elementos.btnBuscarCliente) {
        elementos.btnBuscarCliente.addEventListener('click', buscarCliente);
    }

    if (elementos.buscarTelefono) {
        elementos.buscarTelefono.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') { e.preventDefault(); buscarCliente(); }
        });
    }

    if (elementos.btnCrearCliente) {
        elementos.btnCrearCliente.addEventListener('click', crearCliente);
    }

    if (elementos.btnNuevoVehiculo) {
        elementos.btnNuevoVehiculo.addEventListener('click', mostrarFormularioNuevoVehiculo);
    }

    const btnAgregarManual = document.getElementById('btnAgregarServicioManual');
    if (btnAgregarManual) {
        btnAgregarManual.addEventListener('click', agregarServicioManual);
    }

    ['cliente_nombre', 'cliente_telefono', 'marca', 'modelo', 'anio', 'placas'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', actualizarResumen);
    });

    document.getElementById('kilometraje_actual')?.addEventListener('input', (e) => {
        if (elementos.resumenKm) {
            elementos.resumenKm.textContent = e.target.value ? `${parseInt(e.target.value).toLocaleString()} km` : '-';
        }
    });

    if (elementos.formularioOrden) {
        elementos.formularioOrden.addEventListener('submit', guardarOrden);
    }

    const inputBuscar = document.getElementById('buscarServicio');
    if (inputBuscar) {
        inputBuscar.addEventListener('input', async (e) => {
            const termino = e.target.value.trim();
            if (termino.length < 2) return;

            const response = await fetch(`/comodin_motors/API/servicios/buscar?q=${encodeURIComponent(termino)}`);
            const data = await response.json();

            const resultados = document.getElementById('resultadosServicios');
            resultados.innerHTML = '';

            if (data.codigo === 1 && data.datos.length > 0) {
                resultados.classList.add('show');
                data.datos.forEach(servicio => {
                    const item = document.createElement('div');
                    item.className = 'autocomplete-item';
                    item.innerHTML = `
                        <div class="servicio-descripcion">${servicio.descripcion}</div>
                        <div class="servicio-precio">Q ${servicio.precio_sugerido}</div>
                    `;
                    item.addEventListener('click', () => {
                        agregarServicioATabla(servicio);
                        resultados.classList.remove('show');
                        inputBuscar.value = '';
                    });
                    resultados.appendChild(item);
                });
            } else {
                resultados.classList.remove('show');
            }
        });
    }
}

// ============================================
// BÚSQUEDA Y GESTIÓN DE CLIENTES
// ============================================
async function buscarCliente() {
    const telefono = elementos.buscarTelefono.value.trim();

    if (!telefono) {
        Toast.fire({ icon: 'warning', title: 'Ingrese un teléfono' });
        return;
    }

    try {
        const response = await fetch(`/comodin_motors/API/clientes/buscar?telefono=${encodeURIComponent(telefono)}`);
        const data = await response.json();

        if (data.codigo === 1 && data.datos && data.datos.length > 0) {
            mostrarDatosCliente(data.datos[0]);
            cargarVehiculosCliente(data.datos[0].id_cliente);
        } else {
            mostrarFormularioNuevoCliente(telefono);
        }
    } catch (error) {
        console.error('Error al buscar cliente:', error);
        Toast.fire({ icon: 'error', title: 'Error al buscar cliente' });
    }
}

function mostrarDatosCliente(cliente) {
    clienteSeleccionado = cliente;

    elementos.idCliente.value = cliente.id_cliente;
    elementos.clienteNombre.value = cliente.nombre;
    elementos.clienteTelefono.value = cliente.telefono;
    elementos.clienteEmpresa.value = cliente.empresa || '';
    elementos.clienteDireccion.value = cliente.direccion || '';
    elementos.clienteNombre.readOnly = true;
    elementos.clienteTelefono.readOnly = true;

    elementos.datosCliente.style.display = 'block';
    elementos.btnNuevoCliente.style.display = 'none';
    elementos.seccionVehiculo.style.display = 'block';

    actualizarResumen();
    Toast.fire({ icon: 'success', title: 'Cliente encontrado', timer: 1500 });
}

function mostrarFormularioNuevoCliente(telefono) {
    elementos.idCliente.value = '';
    elementos.clienteNombre.value = '';
    elementos.clienteTelefono.value = telefono;
    elementos.clienteEmpresa.value = '';
    elementos.clienteDireccion.value = '';
    elementos.clienteNombre.readOnly = false;
    elementos.clienteTelefono.readOnly = false;

    elementos.datosCliente.style.display = 'block';
    elementos.btnNuevoCliente.style.display = 'block';
    elementos.seccionVehiculo.style.display = 'none';

    Toast.fire({ icon: 'info', title: 'Cliente no encontrado. Complete los datos.' });
}

async function crearCliente() {
    const nombre = elementos.clienteNombre.value.trim();
    const telefono = elementos.clienteTelefono.value.trim();
    const empresa = elementos.clienteEmpresa.value.trim();
    const direccion = elementos.clienteDireccion.value.trim();

    if (!nombre || !telefono) {
        Toast.fire({ icon: 'warning', title: 'Complete nombre y teléfono' });
        return;
    }

    try {
        const formData = new FormData();
        formData.append('nombre', nombre);
        formData.append('telefono', telefono);
        formData.append('empresa', empresa);
        formData.append('direccion', direccion);

        const response = await fetch('/comodin_motors/API/clientes/guardar', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.codigo === 1) {
            elementos.idCliente.value = data.id_cliente;
            clienteSeleccionado = { id_cliente: data.id_cliente, nombre, telefono };

            elementos.clienteNombre.readOnly = true;
            elementos.clienteTelefono.readOnly = true;
            elementos.btnNuevoCliente.style.display = 'none';
            elementos.seccionVehiculo.style.display = 'block';

            actualizarResumen();
            mostrarFormularioNuevoVehiculo();

            Toast.fire({ icon: 'success', title: 'Cliente creado exitosamente' });
        } else {
            Toast.fire({ icon: 'error', title: data.mensaje || 'Error al crear cliente' });
        }
    } catch (error) {
        console.error('Error al crear cliente:', error);
        Toast.fire({ icon: 'error', title: 'Error al crear cliente' });
    }
}

// ============================================
// GESTIÓN DE VEHÍCULOS
// ============================================
async function cargarVehiculosCliente(id_cliente) {
    try {
        const response = await fetch(`/comodin_motors/API/vehiculos/cliente?id_cliente=${id_cliente}`);
        const data = await response.json();

        if (data.codigo === 1 && data.datos && data.datos.length > 0) {
            mostrarVehiculosExistentes(data.datos);
        } else {
            mostrarFormularioNuevoVehiculo();
        }
    } catch (error) {
        console.error('Error al cargar vehículos:', error);
        mostrarFormularioNuevoVehiculo();
    }
}

function mostrarVehiculosExistentes(vehiculos) {
    elementos.listaVehiculos.innerHTML = '';

    vehiculos.forEach(v => {
        const card = document.createElement('div');
        card.className = 'col-md-6';
        card.innerHTML = `
            <div class="vehicle-card" data-id="${v.id_vehiculo}">
                <h5 class="text-white mb-2">
                    <i class="bi bi-car-front"></i> ${v.marca} ${v.modelo}
                </h5>
                <p class="text-muted mb-1">
                    <i class="bi bi-calendar3"></i> ${v.anio} | 
                    <i class="bi bi-palette"></i> ${v.color}
                </p>
                <p class="mb-0" style="color: #00ff00; font-weight: 700;">
                    <i class="bi bi-credit-card"></i> ${v.placas}
                </p>
            </div>
        `;

        card.querySelector('.vehicle-card').addEventListener('click', () => {
            seleccionarVehiculo(v, card.querySelector('.vehicle-card'));
        });

        elementos.listaVehiculos.appendChild(card);
    });

    elementos.vehiculosExistentes.style.display = 'block';
    elementos.formNuevoVehiculo.style.display = 'none';
}

function seleccionarVehiculo(vehiculo, cardElement) {
    vehiculoSeleccionado = vehiculo;

    document.querySelectorAll('.vehicle-card').forEach(c => c.classList.remove('selected'));
    cardElement.classList.add('selected');

    elementos.idVehiculo.value = vehiculo.id_vehiculo;
    document.getElementById('marca').value = vehiculo.marca;
    document.getElementById('modelo').value = vehiculo.modelo;
    document.getElementById('anio').value = vehiculo.anio;
    document.getElementById('color').value = vehiculo.color;
    document.getElementById('placas').value = vehiculo.placas;
    document.getElementById('numero_serie').value = vehiculo.numero_serie || '';

    actualizarResumen();
    Toast.fire({ icon: 'success', title: 'Vehículo seleccionado', timer: 1500 });
}

function mostrarFormularioNuevoVehiculo() {
    elementos.vehiculosExistentes.style.display = 'none';
    elementos.formNuevoVehiculo.style.display = 'block';
    elementos.idVehiculo.value = '';
    document.getElementById('marca').value = '';
    document.getElementById('modelo').value = '';
    document.getElementById('anio').value = new Date().getFullYear();
    document.getElementById('color').value = '';
    document.getElementById('placas').value = '';
    document.getElementById('numero_serie').value = '';
}

// ============================================
// NIVEL DE COMBUSTIBLE
// ============================================
function inicializarCombustible() {
    if (elementos.nivelCombustible) {
        elementos.nivelCombustible.addEventListener('change', actualizarCombustible);
        actualizarCombustible();
    }
}

function actualizarCombustible() {
    const nivel = elementos.nivelCombustible.value;
    const porcentajes = { 'E': 0, '1/4': 25, '1/2': 50, '3/4': 75, 'F': 100 };

    if (elementos.fuelLevel) {
        elementos.fuelLevel.style.width = `${porcentajes[nivel] ?? 50}%`;
    }
    if (elementos.resumenCombustible) {
        elementos.resumenCombustible.textContent = nivel;
    }
}

// ============================================
// SERVICIOS Y REPUESTOS
// ============================================
function agregarServicioManual() {
    const input = document.getElementById('buscarServicio');
    const descripcion = input?.value.trim();

    agregarServicioATabla({
        descripcion: descripcion || 'Servicio manual',
        precio_sugerido: 0
    });

    if (input) input.value = '';
}

function agregarServicioATabla(servicio) {
    const tbody = document.getElementById('tablaServicios');
    const emptyRow = tbody.querySelector('.empty-state');
    if (emptyRow) emptyRow.remove();

    const index = tbody.rows.length;
    const tr = document.createElement('tr');
    tr.setAttribute('data-index', index);
    tr.innerHTML = `
        <td>${index + 1}</td>
        <td><input type="text" class="servicio-descripcion" value="${servicio.descripcion}"></td>
        <td><input type="number" class="servicio-cantidad" value="1" min="1"></td>
        <td><input type="number" class="servicio-precio" value="${servicio.precio_sugerido || 0}" min="0" step="0.01"></td>
        <td class="servicio-subtotal">Q ${parseFloat(servicio.precio_sugerido || 0).toFixed(2)}</td>
        <td>
            <button type="button" class="btn-eliminar-servicio" onclick="eliminarServicio(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;

    tr.querySelectorAll('.servicio-cantidad, .servicio-precio').forEach(input => {
        input.addEventListener('input', () => calcularSubtotal(tr));
    });

    tbody.appendChild(tr);
    calcularTotal();
}

function calcularSubtotal(fila) {
    const cantidad = parseFloat(fila.querySelector('.servicio-cantidad').value) || 0;
    const precio = parseFloat(fila.querySelector('.servicio-precio').value) || 0;
    fila.querySelector('.servicio-subtotal').textContent = `Q ${(cantidad * precio).toFixed(2)}`;
    calcularTotal();
}

function calcularTotal() {
    let total = 0;
    document.querySelectorAll('#tablaServicios tr[data-index]').forEach(fila => {
        const cantidad = parseFloat(fila.querySelector('.servicio-cantidad')?.value) || 0;
        const precio = parseFloat(fila.querySelector('.servicio-precio')?.value) || 0;
        total += cantidad * precio;
    });
    document.getElementById('totalServicios').textContent = `Q ${total.toFixed(2)}`;
}

function obtenerServiciosParaGuardar() {
    const servicios = [];
    document.querySelectorAll('#tablaServicios tr[data-index]').forEach(fila => {
        const descripcion = fila.querySelector('.servicio-descripcion')?.value?.trim();
        const cantidad = fila.querySelector('.servicio-cantidad')?.value;
        const precio = fila.querySelector('.servicio-precio')?.value;
        if (descripcion) {
            servicios.push({
                descripcion,
                cantidad: parseInt(cantidad) || 1,
                costo: parseFloat(precio) || 0
            });
        }
    });
    return servicios;
}

window.eliminarServicio = (btn) => {
    btn.closest('tr').remove();
    calcularTotal();
    const tbody = document.getElementById('tablaServicios');
    if (tbody.rows.length === 0) {
        tbody.innerHTML = `
            <tr class="empty-state">
                <td colspan="6" class="text-center" style="color: #666; padding: 2rem;">
                    <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                    No hay servicios agregados
                </td>
            </tr>`;
    }
};

// ============================================
// ACTUALIZAR RESUMEN
// ============================================
function actualizarResumen() {
    if (elementos.resumenCliente) elementos.resumenCliente.textContent = elementos.clienteNombre?.value || '-';
    if (elementos.resumenTelefono) elementos.resumenTelefono.textContent = elementos.clienteTelefono?.value || '-';

    const marca = document.getElementById('marca')?.value || '';
    const modelo = document.getElementById('modelo')?.value || '';
    const anio = document.getElementById('anio')?.value || '';
    if (elementos.resumenVehiculo) elementos.resumenVehiculo.textContent = marca ? `${marca} ${modelo} ${anio}` : '-';
    if (elementos.resumenPlacas) elementos.resumenPlacas.textContent = document.getElementById('placas')?.value || '-';
}

// ============================================
// GUARDAR ORDEN
// ============================================
async function guardarOrden(e) {
    e.preventDefault();

    if (!elementos.idCliente.value) {
        Swal.fire({ icon: 'warning', title: 'Cliente requerido', text: 'Debe seleccionar o crear un cliente' });
        return;
    }

    if (!elementos.idVehiculo.value && !document.getElementById('marca').value) {
        Swal.fire({ icon: 'warning', title: 'Vehículo requerido', text: 'Debe seleccionar o crear un vehículo' });
        return;
    }

    if (!validarFormulario(elementos.formularioOrden, [
        'id_cliente', 'id_vehiculo', 'numero_serie', 'proximo_servicio_km',
        'cliente_empresa', 'cliente_direccion', 'observaciones',
        'inventario_otros', 'buscarServicio'
    ])) {
        Swal.fire({ icon: 'warning', title: 'Campos incompletos', text: 'Complete todos los campos obligatorios' });
        return;
    }

    elementos.btnGuardarOrden.disabled = true;

    Swal.fire({
        title: 'Guardando orden...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        const formData = new FormData(elementos.formularioOrden);
        formData.set('id_cliente', elementos.idCliente.value);
        formData.set('id_vehiculo', elementos.idVehiculo.value);

        // ── DAÑOS: leer del hidden input que maneja el sistema de imágenes ──
        const danosHidden = document.getElementById('danosHidden');
        const danosJson = danosHidden?.value ?? '[]';
        formData.set('danos', danosJson); // set en lugar de append para no duplicar

        console.log('Daños que se envían:', danosJson);

        // Si no hay vehículo seleccionado, crear uno nuevo primero
        if (!formData.get('id_vehiculo') || formData.get('id_vehiculo') === '') {
            const vehiculoData = new FormData();
            vehiculoData.append('id_cliente', formData.get('id_cliente'));
            vehiculoData.append('marca', formData.get('marca'));
            vehiculoData.append('modelo', formData.get('modelo'));
            vehiculoData.append('anio', formData.get('anio'));
            vehiculoData.append('color', formData.get('color'));
            vehiculoData.append('placas', formData.get('placas'));
            vehiculoData.append('numero_serie', formData.get('numero_serie') || '');
            vehiculoData.append('kilometraje_inicial', formData.get('kilometraje_actual'));

            const respV = await fetch('/comodin_motors/API/vehiculos/guardar', {
                method: 'POST',
                body: vehiculoData
            });
            const dataV = await respV.json();

            if (dataV.codigo === 1) {
                formData.set('id_vehiculo', dataV.id_vehiculo);
            } else {
                throw new Error(dataV.detalle || dataV.mensaje || 'Error al crear vehículo');
            }
        }

        // Agregar servicios
        formData.set('servicios', JSON.stringify(obtenerServiciosParaGuardar()));

        const response = await fetch('/comodin_motors/API/ordenes/guardar', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        console.log('Respuesta ordenes/guardar:', data);

        if (data.codigo === 1) {
            const result = await Swal.fire({
                icon: 'success',
                title: '¡Orden creada exitosamente!',
                html: `
                    <p>Número de orden:</p>
                    <strong style="color:#00ff00; font-size:2rem;">${data.numero_orden}</strong>
                `,
                showCancelButton: true,
                confirmButtonText: '<i class="bi bi-eye"></i> Ver orden',
                cancelButtonText: '<i class="bi bi-plus-circle"></i> Nueva orden',
                confirmButtonColor: '#00ff00',
                cancelButtonColor: '#6c757d',
                background: '#1a1a1a',
                color: '#fff'
            });

            if (result.isConfirmed) {
                window.location.href = `/comodin_motors/orden/ver?id=${data.id_orden}`;
            } else {
                window.location.reload();
            }
        } else {
            throw new Error(data.detalle || data.mensaje || 'Error al guardar');
        }

    } catch (error) {
        console.error('Error al guardar orden:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error al guardar',
            text: error.message || 'No se pudo guardar la orden',
            confirmButtonColor: '#ff4444',
            background: '#1a1a1a',
            color: '#fff'
        });
    } finally {
        elementos.btnGuardarOrden.disabled = false;
    }
}

// ============================================
// SISTEMA DE DAÑOS CON IMÁGENES
// ============================================
(function () {
    let vistaActual = 'frontal';
    let pendingClick = null;
    let danos = [];
    let pinCounter = 0;

    const container = document.getElementById('diagramContainer');
    const carImage = document.getElementById('carImage');
    const modal = document.getElementById('modalDano');
    const damageList = document.getElementById('damageList');
    const damageItems = document.getElementById('damageItems');
    const danoCount = document.getElementById('danoCount');

    if (!container) return; // salir si no está la sección de daños

    // ── Cambio de vista ────────────────────────────────────────
    document.querySelectorAll('.vista-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.vista-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            vistaActual = tab.dataset.vista;
            carImage.src = tab.dataset.img;
            carImage.alt = tab.textContent.trim();
            renderPins();
        });
    });

    // ── Click en diagrama ──────────────────────────────────────
    container.addEventListener('click', (e) => {
        if (e.target.closest('.damage-pin')) return;

        const rect = container.getBoundingClientRect();
        const pctX = ((e.clientX - rect.left) / rect.width) * 100;
        const pctY = ((e.clientY - rect.top) / rect.height) * 100;

        pendingClick = {
            pctX: parseFloat(pctX.toFixed(2)),
            pctY: parseFloat(pctY.toFixed(2))
        };

        document.getElementById('tipoDanoInput').value = 'rayón';
        document.getElementById('descripcionDanoInput').value = '';
        modal.style.display = 'flex';
        setTimeout(() => document.getElementById('descripcionDanoInput').focus(), 100);
    });

    // ── Confirmar daño ─────────────────────────────────────────
    document.getElementById('btnConfirmarDano').addEventListener('click', () => {
        const tipo = document.getElementById('tipoDanoInput').value;
        const desc = document.getElementById('descripcionDanoInput').value.trim();

        if (!desc) {
            document.getElementById('descripcionDanoInput').style.borderColor = '#ff4444';
            return;
        }
        document.getElementById('descripcionDanoInput').style.borderColor = '';

        pinCounter++;
        danos.push({
            id: pinCounter,
            ubicacion: vistaActual,
            tipo_dano: tipo,
            descripcion: desc,
            coordenada_x: pendingClick.pctX,
            coordenada_y: pendingClick.pctY
        });

        modal.style.display = 'none';
        pendingClick = null;
        renderPins();
        renderDamageList();
    });

    // ── Cancelar modal ─────────────────────────────────────────
    document.getElementById('btnCancelarDano').addEventListener('click', () => {
        modal.style.display = 'none';
        pendingClick = null;
    });

    document.getElementById('descripcionDanoInput').addEventListener('keydown', e => {
        if (e.key === 'Enter') document.getElementById('btnConfirmarDano').click();
        if (e.key === 'Escape') document.getElementById('btnCancelarDano').click();
    });

    // ── Renderizar pins ────────────────────────────────────────
    function renderPins() {
        container.querySelectorAll('.damage-pin').forEach(p => p.remove());

        danos.filter(d => d.ubicacion === vistaActual).forEach(d => {
            const pin = document.createElement('div');
            pin.className = 'damage-pin';
            pin.dataset.id = d.id;
            pin.style.left = d.coordenada_x + '%';
            pin.style.top = d.coordenada_y + '%';
            pin.innerHTML = `${d.id}<span class="pin-tooltip">${d.tipo_dano}: ${d.descripcion}</span>`;

            pin.addEventListener('click', (e) => {
                e.stopPropagation();
                if (confirm(`¿Eliminar daño #${d.id}?`)) {
                    danos = danos.filter(x => x.id !== d.id);
                    renderPins();
                    renderDamageList();
                }
            });

            container.appendChild(pin);
        });
    }

    // ── Renderizar lista ───────────────────────────────────────
    function renderDamageList() {
        danoCount.textContent = danos.length;
        damageList.style.display = danos.length ? 'block' : 'none';

        const vistaLabel = {
            frontal: 'Frontal',
            trasero: 'Trasero',
            lateral_izquierdo: 'Lat. Izq',
            lateral_derecho: 'Lat. Der',
            techo: 'Techo'
        };

        damageItems.innerHTML = danos.map(d => `
            <div class="damage-list-item">
                <span class="fw-bold" style="color:#888; font-size:0.8rem; min-width:20px">#${d.id}</span>
                <span class="dano-vista">${vistaLabel[d.ubicacion] ?? d.ubicacion}</span>
                <span class="dano-tipo">${d.tipo_dano}</span>
                <span class="dano-desc">${d.descripcion}</span>
                <button type="button" class="btn-remove-dano" onclick="removeDano(${d.id})" title="Eliminar">
                    <i class="bi bi-trash3"></i>
                </button>
            </div>
        `).join('');

        syncDanosHidden();
    }

    // ── Eliminar daño desde la lista ───────────────────────────
    window.removeDano = function (id) {
        danos = danos.filter(d => d.id !== id);
        renderPins();
        renderDamageList();
    };

    // ── Sync con formulario ────────────────────────────────────
    function syncDanosHidden() {
        let hidden = document.getElementById('danosHidden');
        if (!hidden) {
            hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'danos';
            hidden.id = 'danosHidden';
            document.getElementById('formularioOrden').appendChild(hidden);
        }
        hidden.value = JSON.stringify(danos);
    }



    // ============================================
    // FUEL DRAG SLIDER
    // ============================================
    (function initFuelSlider() {
        const STEPS = ['E', '1/4', '1/2', '3/4', 'F'];
        const PERCENTAGES = [0, 25, 50, 75, 100]; // % de llenado visual

        const track = document.getElementById('fuelTrack');
        const fill = document.getElementById('fuelFill');
        const thumb = document.getElementById('fuelThumb');
        const hidden = document.getElementById('nivel_combustible');
        const display = document.getElementById('fuelValueDisplay');

        if (!track || !fill || !thumb || !hidden) return;

        let currentIndex = 2; // arranca en 1/2
        let isDragging = false;
        let startX = 0;

        function getSnapPositions() {
            const w = track.offsetWidth;
            return PERCENTAGES.map(p => (p / 100) * w);
        }

        function applyIndex(index, animate = true) {
            currentIndex = Math.max(0, Math.min(4, index));
            const positions = getSnapPositions();
            const pos = positions[currentIndex];
            const pct = PERCENTAGES[currentIndex];

            if (!animate) {
                fill.style.transition = 'none';
                thumb.style.transition = 'none';
            } else {
                fill.style.transition = 'width 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)';
                thumb.style.transition = 'left 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)';
            }

            fill.style.width = pct + '%';
            thumb.style.left = pos + 'px';
            hidden.value = STEPS[currentIndex];
            display.textContent = STEPS[currentIndex];

            // Color del display según nivel
            const colors = ['#ff4444', '#ff8800', '#ffcc00', '#aaee00', '#00ff00'];
            display.style.color = colors[currentIndex];

            // Actualizar resumen si existe
            const resumenCombustible = document.getElementById('resumen_combustible');
            if (resumenCombustible) resumenCombustible.textContent = STEPS[currentIndex];
        }

        function getNearestIndex(x) {
            const positions = getSnapPositions();
            let nearest = 0;
            let minDist = Infinity;
            positions.forEach((pos, i) => {
                const dist = Math.abs(x - pos);
                if (dist < minDist) { minDist = dist; nearest = i; }
            });
            return nearest;
        }

        // Click directo en el track
        track.addEventListener('click', (e) => {
            if (isDragging) return;
            const rect = track.getBoundingClientRect();
            const x = e.clientX - rect.left;
            applyIndex(getNearestIndex(x));
        });

        // Drag en el thumb
        thumb.addEventListener('mousedown', (e) => {
            isDragging = true;
            startX = e.clientX;
            thumb.classList.add('dragging');
            e.preventDefault();
        });

        // Touch support
        thumb.addEventListener('touchstart', (e) => {
            isDragging = true;
            startX = e.touches[0].clientX;
            thumb.classList.add('dragging');
        }, { passive: true });

        document.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            const rect = track.getBoundingClientRect();
            const x = Math.max(0, Math.min(e.clientX - rect.left, track.offsetWidth));
            // Mover thumb libre mientras arrastra (sin snap)
            fill.style.transition = 'none';
            thumb.style.transition = 'none';
            fill.style.width = (x / track.offsetWidth * 100) + '%';
            thumb.style.left = x + 'px';
        });

        document.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            const rect = track.getBoundingClientRect();
            const x = Math.max(0, Math.min(e.touches[0].clientX - rect.left, track.offsetWidth));
            fill.style.transition = 'none';
            thumb.style.transition = 'none';
            fill.style.width = (x / track.offsetWidth * 100) + '%';
            thumb.style.left = x + 'px';
        }, { passive: true });

        document.addEventListener('mouseup', (e) => {
            if (!isDragging) return;
            isDragging = false;
            thumb.classList.remove('dragging');
            const rect = track.getBoundingClientRect();
            const x = Math.max(0, Math.min(e.clientX - rect.left, track.offsetWidth));
            applyIndex(getNearestIndex(x)); // snap al soltar
        });

        document.addEventListener('touchend', (e) => {
            if (!isDragging) return;
            isDragging = false;
            thumb.classList.remove('dragging');
            const rect = track.getBoundingClientRect();
            const x = Math.max(0, Math.min(e.changedTouches[0].clientX - rect.left, track.offsetWidth));
            applyIndex(getNearestIndex(x));
        });

        // Init con 1/2
        window.addEventListener('load', () => applyIndex(2, false));
        // También por si el layout ya está listo
        setTimeout(() => applyIndex(2, false), 50);
    })();

    // Init
    syncDanosHidden();
})();

console.log('✅ Script nueva.js cargado correctamente');